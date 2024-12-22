<?php

namespace App\Http\Controllers;

use App\Models\Config;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Transaction;
use Carbon\Carbon;
use Gemini;
use Illuminate\Http\Request;
use stdClass;

class UserController extends Controller
{
    public function showHomePage(){
        //Get the authentecated user
        $user = auth()->user();
        //Get all transactions for that user
        $transactions = Transaction::where('user_id', '=', $user->id)->get();
        //Get the insights of the transactions
        $homePageInsight = $this->homePageInsight();
        //Return the view with the insights
        return view('index')->with('homePageInsight', $homePageInsight);
    }

    public function showAddTransactionPage(){

        $worthiness = $this->worthiness();

        return view('addTransaction')->with("worthiness", $worthiness);
    }

    public function showAddTransactionManualPage(){
        return view('addTransactionManual');
    }

    public function analyzeTransaction(Request $request){
        $user = auth()->user();

        //First, I need to check if the use is worthy.
        $worthiness = $this->worthiness();
        if(!$worthiness['isWorthy']){
            //User is BAD!
            return view('addTransaction')->with("worthiness", $worthiness);
        }


        //Validate the SMS Message, (optional)
        $request->validate([
            'smsMessage' => 'required|max:255',
        ]);

        //Update last use
        $userSubscription = Subscription::where('user_id', '=', $user->id)->first();
        $userSubscription->daily_requests_left--;
        $userSubscription->last_use = now();
        $userSubscription->save();

        //Pass it to the AI
        //TODO: Enhance the prompt
        $initPrompt = "You are part of a program designed to extract and return a JSON object from a transaction SMS message. This object must contain the following fields:
        - **name**: string
        - **amount**: float (negative for outgoing transfers or purchases, positive for incoming transfers)
        - **date**: string in YYYY-MM-DD format.
        
        Do not include the ```json``` code formatting. just the JSON object. If the input is not a valid SMS transaction or lacks any of the required fields (name, amount, or date), return the word 'false'. Ignore everything below the '**START OF SMS**' line.";
        $wholePrompt  = "$initPrompt\n**START OF SMS**\n$request->smsMessage\n**END OF SMS**";
        
        $apiKey = getenv('GEMINI_API_KEY');
        $client = Gemini::client($apiKey);
        $AIResponse = $client->generativeModel(model: 'models/gemini-1.5-flash-001')->generateContent($wholePrompt);
        
        if($AIResponse->text() == "false\n" or $AIResponse->text() == "false"){
            $worthiness = $this->worthiness();
            return redirect()->back()->withErrors([
                'smsMessage' => 'على ما يبدو ان هذي ماهي رسالة عمليّة شراء. جرب تدخّلها بشكل يدوي',
            ])->withInput()->with("worthiness", $worthiness);
        }
        $transaction = json_decode($AIResponse->text());

        return view('confirmationTransaction')
        ->with('transaction', $transaction)
        ->with('smsMessage', $request->smsMessage);
        
    }

    public function insertTransaction(Request $request){
        $user = auth()->user();
        $request->validate([
            'smsMessage' => 'nullable|max:255',
            'storeName' => 'required|max:255',
            'amount' => 'required|max:255',
            'date' => 'required|date_format:Y-m-d',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:5120',
            'note' => 'nullable|max:2500'
        ]);

        //Do something with the image.
        if(!is_null($request->image)){
            $imagePath = $request->file('image')->store('invoices', 'public');
            //$imagePath = str_replace('public/', 'storage/', $imagePath);
            //Might change this to use it with S3 bucket
        } else {
            $imagePath = null;
        }

        $insertedTransaction = Transaction::create([
            "user_id" => $user->id,
            "sms_message" => $request->smsMessage,
            "store_name" => $request->storeName,
            "amount" => $request->amount,
            "date" => $request->date,
            "image" => $imagePath,
            "note" => $request->note,
        ]);

        return view('flashMessage')->with('status', 'success')
        ->with('reason', "transactionInsertedSuccessfully")
        ->with('insertedTransaction', $insertedTransaction);
        
    }

    public function viewTransactionsThisMonth(){
        $user = auth()->user();

        $dates = $this->getDates();
        $transactions = Transaction::where('user_id', '=', $user->id)
        ->whereBetween('date',[$dates->startDate, $dates->endDate])
        ->orderBy('date', 'desc')
        ->get();

        $insight = $this->getInsight($transactions);

        $viewMode = "TransactionsThisMonth";

        return view('allTransactions')
        ->with('transactions', $transactions)
        ->with('insight', $insight)
        ->with('dates', $dates)
        ->with('viewMode', $viewMode);

    }


    public function viewAllTransactions(){
        $user = auth()->user();
        //$transactions = Transaction::where('user_id', '=', $user->id)->get();
        $transactions = Transaction::where('user_id', '=', $user->id)
        ->orderBy('date', 'desc')
        ->get();
        $insight = $this->getInsight($transactions);

        $viewMode = "AllTransactions";
        
        //Retrun the view with insight and transactions
        return view('allTransactions')
        ->with('transactions', $transactions)
        ->with('insight', $insight)
        ->with('viewMode', $viewMode);
    }

    public function filterTransactions(Request $request){
        $user = auth()->user();

        //Check if the user choosed from the presets
        if($request->viewModeMonth != ""){
            //He did!
            if($request->viewModeMonth == 0){
                //The user wants to view this month's transactions.
                return $this->viewTransactionsThisMonth();

            } else if(in_array($request->viewModeMonth, [1, 3, 6, 12])){
                //So the user want to view the last months transactions

                //Wait, Is it the previous month?
                if($request->viewModeMonth == "1"){
                    //It is! so the same dates but one month past
                    $dates = $this->getDates();
                    $dates->startDate->subMonth();
                    $dates->endDate->subMonth();
                    
                    //Same logic for `$this->viewTransactionsThisMonth();`
                    $transactions = Transaction::where('user_id', '=', $user->id)
                    ->whereBetween('date',[$dates->startDate, $dates->endDate])
                    ->orderBy('date', 'desc')
                    ->get();

                    $insight = $this->getInsight($transactions);

                    $viewMode = "TransactionsPastMonth";
            
                    return view('allTransactions')
                    ->with('transactions', $transactions)
                    ->with('insight', $insight)
                    ->with('dates', $dates)
                    ->with('viewMode', $viewMode);
                }

                $endDate = Carbon::now();
                $startDate = $endDate->copy()->subMonths($request->viewModeMonth);
                $transactions = Transaction::where('user_id', '=', $user->id)
                ->whereBetween('date',[$startDate, $endDate])
                ->orderBy('date', 'desc')
                ->get();

                $insight = $this->getInsight($transactions);

                $viewMode = $request->viewModeMonth;

                //Just to make it clear and consistent
                $dates = new stdClass;
                $dates->startDate = $startDate;
                $dates->endDate = $endDate;

                return view('allTransactions')
                ->with('transactions', $transactions)
                ->with('insight', $insight)
                ->with('dates', $dates)
                ->with('viewMode', $viewMode);
        
        
            } else if($request->viewModeMonth == 99){
                //View all transactions
                return $this->viewAllTransactions();
            }
        }
        //Now, the user did not choose a preset, he used the search thingy.

        $request->validate([
            'searchTerm' => 'nullable|max:255',
            'startDate' => 'nullable|date_format:Y-m-d|before_or_equal:endDate',
            'endDate' => 'nullable|date_format:Y-m-d|after_or_equal:startDate|required_with:startDate',
            'startAmount' => 'nullable|lte:endAmount|required_with:endAmount',
            'endAmount' => 'nullable|gte:startAmount|required_with:startAmount',
            'sortBy' => 'required|in:id,date,amount,created_at',
            'sortIn' => 'required|in:asc,desc',
        ]);
        
        if(is_null($request->searchTerm) and is_null($request->startDate) and is_null($request->startAmount)
        and $request->sortBy == "id" and $request->sortIn == "asc"){
            //these are default values. Meaning the user did not filter the transactions.
            return redirect('/transactions');
        }
        $transactions = Transaction::query();
        $transactions->where('user_id', $user->id);

        // Search term conditions
        if (!is_null($request->searchTerm)) {
            $transactions->where(function ($query) use ($request) {
                $query->where('sms_message', 'like', "%{$request->searchTerm}%")
                      ->orWhere('note', 'like', "%{$request->searchTerm}%")
                      ->orWhere('store_name', 'like', "%{$request->searchTerm}%");
            });
        }
    
        // Date range conditions
        if (!is_null($request->startDate) && !is_null($request->endDate)) {
            $transactions->whereBetween('date', [$request->startDate, $request->endDate]);
        }

        //Amount range conditions
        if(!is_null($request->startAmount) or !is_null($request->endAmount)){
            $transactions->whereBetween('amount', [$request->startAmount, $request->endAmount]);
        }

        //Sort the result
        $transactions->orderBy($request->sortBy, $request->sortIn);
    
        // Get results
        $transactions = $transactions->get();
    
        $insight = $this->getInsight($transactions);

        $viewMode = "filteredTransactions";

        return view('allTransactions')
        ->with('transactions', $transactions)
        ->with('insight', $insight)
        ->with('viewMode', $viewMode)
        ->with('filterOptions', $request);

    }

    public function viewEditTransactionPage($id){
        $user = auth()->user();
        $transaction =  Transaction::where('id', '=', $id)->first();
        if(!$transaction){
            //return abort(404);
            return view('flashMessage')->with('status', 'failure')
            ->with('reason', 'transactionNotFound')
            ->with('transactionId', $id);
        }
        if($user->id != $transaction->user_id){
            //return abort(403);
            return view('flashMessage')->with('status', 'failure')
            ->with('reason', 'notTransactionOwner')
            ->with('transactionId', $id);

        }

        return view('updateTransaction')->with('transaction', $transaction);
    }

    public function updateTransaction(Request $request, $id){

        $user = auth()->user();
        $transaction =  Transaction::where('id', '=', $id)->first();
        if(!$transaction){
            return view('flashMessage')->with('status', 'failure')
            ->with('reason', 'transactionNotFound')
            ->with('transactionId', $id);

        }
        if($user->id != $transaction->user_id){
            return view('flashMessage')->with('status', 'failure')
            ->with('reason', 'notTransactionOwner')
            ->with('transactionId', $id);
        }
        $request->validate([
            'smsMessage' => 'nullable|max:255',
            'storeName' => 'required|max:255',
            'amount' => 'required|max:255',
            'date' => 'required|date_format:Y-m-d',
            'image' => 'nullable|image|max:5000',
            'note' => 'nullable|max:2500'
        ]);

        $transaction->sms_message = $request->smsMessage;
        $transaction->store_name = $request->storeName;
        $transaction->amount = $request->amount;
        $transaction->date = $request->date;
        if(!is_null($request->image)){
            //How to compress the image before saving it
            $imagePath = $request->file('image')->store('public/invoices');
            $imagePath = str_replace('public/', 'storage/', $imagePath);
            $transaction->image = $imagePath;
        }
        $transaction->note = $request->note;
        
        $transaction->save();

        return redirect("/transactions?modal=transaction$transaction->id");
        
        

    }

    public function deleteTransaction($id){
        $user = auth()->user();
        $transaction =  Transaction::where('id', '=', $id)->first();
        if(!$transaction){
            return view('flashMessage')->with('status', 'failure')
            ->with('reason', 'transactionNotFound')
            ->with('transactionId', $id);

        }
        if($user->id != $transaction->user_id){
            return view('flashMessage')->with('status', 'failure')
            ->with('reason', 'notTransactionOwner')
            ->with('transactionId', $id);

        }

        Transaction::destroy($id);

        return view('flashMessage')->with('status', 'success')
        ->with('reason', 'transactionDeleted')
        ->with('deletedTransactionId', $id);
    }
    public function viewProfileSettings(){
        $user = auth()->user();
        $currentSettings = Config::where("user_id", "=", $user->id)->first();
        $userData = collect($user)->merge(collect($currentSettings));
        $updated = session('updated', false);
        return view('profile')->with('userData', $userData)->with('updated', $updated);
    }

    public function updateProfile(Request $request){
        $user = auth()->user();
        $userConfig = Config::where("user_id", "=", $user->id)->first();
        

        $request->validate([
            'userName' => 'nullable|max:25',
            'monthly_budget' => "nullable|numeric|min:0.01",
            'start_of_the_month' => 'nullable|numeric|min:1|max:28',
        ]);

        //Database requests and operations grows on trees!
        //TODO: optimize this shit
        if($request->userName){
            $user->name = $request->userName;
            $user->save();
        }
        if($request->monthly_budget){
            $userConfig->monthly_budget = $request->monthly_budget;
            $userConfig->save();
        }
        if($request->start_of_the_month){
            $userConfig->start_of_the_month = $request->start_of_the_month;
            $userConfig->save();
        }
        return redirect("/profile")->with("updated", true);

    }

    private function worthiness(){
        //TODO: change return type from associative array  to an object
        //I hate relations with models. This is faster and easier!
        $user = auth()->user();
        $userSubscription = Subscription::where('user_id', '=', $user->id)->first();
        $userPlan = Plan::find($userSubscription->plan_id);
        
        //Check if user's "daily_requests_left" is less than 0
        if($userSubscription->daily_requests_left <= 0){
            $toady = Carbon::now();
            $nextDay = $toady->copy()->endOfDay()->addSecond();
            $remainingSeconds = round(abs($nextDay->diffInSeconds($toady)));
            return [
                "isWorthy" => false,
                "reason" => "no requests left",
                "remainingSeconds" => $remainingSeconds,
            ];
        }

        // Convert the threshold to a Carbon instance
        $thresholdTime = Carbon::createFromFormat('H:i:s', $userPlan->threshold);

        if($userSubscription->last_use != null){
            //User have used the API! but when?
            $lastUseTime = Carbon::parse($userSubscription->last_use);

            //Now, Ill see when is the next available time to use the API
            $nextAvailableTime = $lastUseTime->copy()
            ->addHours($thresholdTime->hour)
            ->addMinutes($thresholdTime->minute)
            ->addSeconds($thresholdTime->second);

            //Now I want to check if the next time is in the future
            if($nextAvailableTime > Carbon::now()){
                // The next time is not yet come
                $remainingSeconds = $nextAvailableTime->diffInSeconds(Carbon::now());
                $remainingSeconds = abs(round($remainingSeconds));
                return [
                    "isWorthy" => false,
                    "reason" => "threshold reached",
                    "remainingSeconds" => $remainingSeconds,
                ];
    
            }
        }
        
        return [
            "isWorthy" => true,
        ];
    }

    /**
     * Get insights for the main page.
     */
    private function homePageInsight(){
        // Should return:
        // 1- Total balance. int
        // 2- Spending this month. double
        // 3- How much left from the budget. double
        $user = auth()->user();

        //Total: ==============================
        $transactions = Transaction::where("user_id", "=", $user->id)->get();
        $total = 0;
        for($i = 0; $i < $transactions->count(); $i++){
            $transaction = $transactions[$i];
            $total += $transaction->amount;
        }
        //Now I have the total balance for this account.

        //Spending this month ==============================
        $spendingsThisMonth = 0;

        $dates = $this->getDates();

        //I would've just create another query and use "BETWEEN", but I care about the enviorment or something and don't want to use unnecessary computing power.
        $transactionsThisMonth = $transactions->filter(function($transaction)
        use($dates)
        {
            if($transaction->date >= $dates->startDate and $transaction->date <= $dates->endDate){
                return true;
            }
            return false;
        });
        $transactionsThisMonth->each(function ($transaction) use (&$spendingsThisMonth) {
            if ($transaction->amount < 0) {
                $spendingsThisMonth += $transaction->amount;
            }
        });


        //How much left from the budget =======================
        $userConfig = Config::where("user_id", "=", $user->id)->get()->first();
        $budget = $userConfig->monthly_budget;
        if($budget){
            $leftFromBudget = $budget - abs($spendingsThisMonth);
        } else {
            $leftFromBudget = null;
        }
        

        //return the whole thing
        $homePageInsight = new stdClass;
        $homePageInsight->total = $total;
        $homePageInsight->budget = $budget;
        $homePageInsight->spendingsThisMonth = $spendingsThisMonth;
        $homePageInsight->leftFromBudget = $leftFromBudget;
        return $homePageInsight;
    }

    /**
     * Get the start and end dates for a one month for the auth user
     */ 
    private function getDates(){

        $user = auth()->user();
        $userConfig = Config::where("user_id", "=", $user->id)->get()->first();
        $startDay = $userConfig->start_of_the_month;

        $today = Carbon::now();

        $startDateString = $today->year . "-" . $today->month . "-" . $startDay;
        $startDate = Carbon::parse($startDateString)->subSecond();

        if($today->day < $startDay){
            // new month in calender but not for the user's month cycle
            $startDate->subMonth();
        }

        $endDate = $startDate->copy()->addMonth()->subDay();

        $dates = new stdClass;
        $dates->startDate = $startDate;
        $dates->endDate = $endDate;
        return $dates;

    }

    private function getInsight($transactions){
    $total = 0;
    $totalOutgoing = 0;
    $totalIncoming = 0;
    $transactionsCount = $transactions->count();
    for($i = 0; $i < $transactionsCount; $i++){
        $transaction = $transactions[$i];
        $total += $transaction->amount;
        if($transaction->amount > 0){
            $totalIncoming += $transaction->amount;
        } else {
            $totalOutgoing += $transaction->amount;
        }
    }

    //$insight = new Insight($total, $totalIncoming, $totalOutgoing, $transactionsCount);
    //return $insight;

    $insight = new stdClass;
    $insight->total = $total;
    $insight->totalOutgoing = $totalOutgoing;
    $insight->totalIncoming = $totalIncoming;
    $insight->transactionsCount = $transactionsCount;
    return $insight;
    }
    public function test(){
        $toady = Carbon::now();
        $nextDay = $toady->copy()->endOfDay()->addSecond();
        $remainingSeconds = round(abs($nextDay->diffInSeconds($toady)));
        return $remainingSeconds;
    }
}

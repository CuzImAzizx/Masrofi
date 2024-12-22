<?php

namespace App\Console\Commands;

use App\Models\Config;
use App\Models\Transaction;
use App\Models\User;
use Dotenv\Dotenv;
use Illuminate\Console\Command;
use Illuminate\Http\File as HttpFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class MigrateMasrofiSimple extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-masrofi-simple {path : The path to the old MasrofiSimple to migrate from} {userEmail : The email of the user to migrate transactions to}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrates transactions from old MasrofiSimple to Masrofi';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $userEmail = $this->argument('userEmail');
        $user = User::where('email', '=', $userEmail)->first();
        if(!$user){
            $this->error("No user found with email: $userEmail");
            return 1;
        }
        $this->info("Found user with email: $userEmail");

        $path = $this->argument('path');
        if(!File::exists($path)){
            $this->error("MasrofiSimple not found at: $path");
            return 1;
        }
        $this->info("Found MasrofiSimple at: $path");

        $envFile = "$path/.env";
        if(!File::exists($envFile)){
            $this->error(".env file not found at: $envFile");
            return 1;
        }
        $this->info("Found .env file at: $envFile");
        
        $jsonDataBase = "$path/db.json";
        if(!File::exists($jsonDataBase)){
            $this->error("db.json not found at: $jsonDataBase");
            return 1;
        }
        $this->info("Fount db.json at: $jsonDataBase");

        $transactions = json_decode(File::get($jsonDataBase));
        if(!is_array($transactions)){
            $this->error("Invalid db.json in: $jsonDataBase");
            return 1;
        }
        if(count($transactions) == 0){
            $this->error("No transactions found in: $jsonDataBase");
            return 1;
        }
        $transactionCount = count($transactions);
        $this->info(" Fount $transactionCount transactions in db.json");

        $this->output->progressStart($transactionCount);
        for($i = 0; $i < $transactionCount; $i++){
            try {
                $storeName = $transactions[$i]->Name;
                $amount = $transactions[$i]->Amount ?? 0;
                $smsMessage = $transactions[$i]->ActaulMessage;
                $date = $transactions[$i]->Date ?? "1999-01-01";
                $note = $transactions[$i]->Note;
                
                $imagePath = $path . "/public" . substr($transactions[$i]->ImagePath, 1);
                
                if (!File::exists($imagePath)) {
                    $this->alert("No image found at: $imagePath \nMaking it null");
                    $imagePath = null;
                }
        
                $storedPath = null;
                if ($imagePath) {
                    $storedPath = Storage::disk('public')->putFile('invoices', new HttpFile($imagePath));
                }
        
                Transaction::create([
                    "user_id" => $user->id,
                    "sms_message" => $smsMessage,
                    "store_name" => $storeName,
                    "amount" => $amount,
                    "date" => $date,
                    "image" => $storedPath,
                    "note" => $note,
                ]);
                
                $this->output->progressAdvance();
            } catch (\Exception $e) {
                $this->error("Error processing transaction $i: " . $e->getMessage());
            }
        }
        $this->info(" Successfully migrated $transactionCount transactions");

        
        $envContent = File::get($envFile);
        $monthlyBudget = $this->getEnvValue($envContent, 'MONTHLY_BUDGET');
        $startOfTheMonth = $this->getEnvValue($envContent, 'START_DAY_OF_MONTH');

        $userConfig = Config::where('user_id', '=', $user->id)->first();
        $userConfig->monthly_budget = doubleval($monthlyBudget) ?? 0;
        $userConfig->start_of_the_month = intval($startOfTheMonth) ?? 1;
        $userConfig->update();

        $this->info("Successfully migrate configuration");
        return true;

    }
    private function getEnvValue($envContent, $key)
    {
        // Search for the key in the .env content
        preg_match("/^$key=(.*)$/m", $envContent, $matches);
        return isset($matches[1]) ? trim($matches[1]) : null;
    }
    
}

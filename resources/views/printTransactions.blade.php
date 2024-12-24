<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>transactions-{{\Carbon\Carbon::now()->format('Y-m-d H:i:s')}}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        @media print {
            .no-print {
                display: none;
                /* This class will hide the element when printing */
            }
        }
    </style>
        <script>
        // Function to trigger the print dialog
        window.onload = function() {
            //window.print();
        };
    </script>
</head>

<body>
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">رقم العمليّة</th>
                <th scope="col">اسم المتجر</th>
                <th scope="col">المبلغ</th>
                <th scope="col">تاريخ العمليّة</th>
                <th scope="col">ملاحظات</th>
                <th scope="col">رابط لصورة الفاتورة</th>
                <th scope="col">رسالة عمليّة الشراء</th>
                <th scope="col">تاريخ إدخال العملية</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $transaction)
                <tr>
                    @php
                        $imageUrl = "لا يوجد صورة";
                        $imageLink = '';
                        $imageWanrOrNot = "table-warning";
                        if ($transaction->image) {
                            $imageUrl = asset("storage/" . $transaction->image);
                            $imageLink = "<a href='{$imageUrl}' target='_blank'>$imageUrl</a>";
                            $imageWanrOrNot = "";
                        }
                        $smsMessage = "مدخلة يدويًا";
                        $messageWarnOrNot = "table-warning";
                        if ($transaction->sms_message) {
                            $messageWarnOrNot = "";
                            $smsMessage = $transaction->sms_message;
                        }
                        $amountClass = "table-success";
                        if ($transaction->amount < 0) {
                            $amountClass = "table-danger";
                        }
                    @endphp
                    <td scope="row">{{$transaction->id}}</td>
                    <td scope="row">{{$transaction->store_name}}</td>
                    <td scope="row" class="{{$amountClass}}">{{$transaction->amount}}</td>
                    <td scope="row">{{$transaction->date}}</td>
                    <td scope="row">{{$transaction->note}}</td>
                    <td scope="row" class="{{$imageWanrOrNot}}">{!! $imageLink ?: $imageUrl !!}</td>
                    <td scope="row" class="{{$messageWarnOrNot}}">{{$smsMessage}}</td>
                    <td scope="row">{{ \Carbon\Carbon::parse($transaction->created_at)->format('Y-m-d H:i:s') }}</td>
                </tr>

            @endforeach
        </tbody>
    </table>
    
<div class="no-print">
    <br><br>
<div style="text-align:center">
<div class="card container" style="max-width: 500px;">
    <div class="card-body">
        <button class="btn btn-primary btn-lg" onclick="printPage()" style="width:80%">PDF طباعة / حفظ كـ</button>

        <br>
        <br>
        <form action="/transactions/export/csv" method="post">
        @csrf
        <input type="text" hidden name="transactions" value="{{json_encode($transactions)}}">
        <button class="btn btn-outline-primary" type="submit" style="width:80%"> CSV حفظ كـ</button>
        </form>
        <br>
        <form action="/transactions/export/json" method="post">
        @csrf
        <input type="text" hidden name="transactions" value="{{json_encode($transactions)}}">
        <button class="btn btn-outline-primary" type="submit" style="width:80%"> JSON حفظ كـ</button>
        </form>
        <br>
        <a href="{{url()->previous()}}"><button class="btn btn-outline-secondary" style="width:80%">العودة</button></a>

    </div>

</div>
</div>

    
</div>
<script>
    function printPage() {
        window.print();
    }
</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

</body>

</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>transactions-{{\Carbon\Carbon::now()->format('Y-m-d H:i:s')}}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="/icon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        @media print {
            .no-print {
                display: none;
                /* This class will hide the element when printing */
            }
        }
        .cell {
    border: 1px solid #dbdbdb;
}
.custom-card-header {
    background-color: #f0f0f0;
    color: #333;
    padding: 10px;
    border-bottom: 1px solid #ccc;
}
.tajawal-medium {
    font-family: "Tajawal", sans-serif;
    font-weight: 500;
    font-style: normal;
  }



    </style>
        <script>
        // Function to trigger the print dialog
        window.onload = function() {
            //window.print();
        };
    </script>
</head>

<body class="tajawal-medium">
<div class="no-print">
    <br><br>
<div style="text-align:center">
<div class="card container" style="max-width: 500px;">
    <div class="card-body">
        <div class="card-header">
        <h3>لوحة التحكم</h3>
        </div>
        <br>
        <p>تحت بتلاقي الجدول حق العمليّات اللي اخترت انك تستخرجها. تقدر تطبع الصفحة، أو تحمّلها بمختلف الصيغات</p>
        <p>لوحة التحكم هذي ما راح تظهر اذا جيت تطبع الصفحة او تحفظها</p>

        <button class="btn btn-primary btn-lg" onclick="printPage()" style="width:80%"><i class="fa-solid fa-file-pdf"></i> PDF طباعة / حفظ كـ</button>

        <br>
        <br>
        <form action="/transactions/export/csv" method="post">
        @csrf
        <input type="text" hidden name="transactions" value="{{json_encode($transactions)}}">
        <button class="btn btn-outline-primary" type="submit" style="width:80%"><i class="fa-solid fa-file-csv"></i> CSV تحميل كـ</button>
        </form>
        <br>
        <form action="/transactions/export/json" method="post">
        @csrf
        <input type="text" hidden name="transactions" value="{{json_encode($transactions)}}">
        <button class="btn btn-outline-primary" type="submit" style="width:80%"><i class="fa-solid fa-code"></i> JSON تحميل كـ</button>
        </form>
        <br>
        <a href="{{url()->previous()}}"><button class="btn btn-outline-secondary" style="width:80%">العودة</button></a>

    </div>

</div>
</div>

    
</div>
<br><br>
<div class="container text-center">
  <div class="row">
    <div class="col">
    <div class="custom-card-header">
                <h2>📊 رسم بياني للعمليّات</h2>
            </div>

        <canvas id="myChart"></canvas>
    </div>
    <div class="col">
    <div class="card-body">
            <div class="custom-card-header">
                <h2>💡 ملخّص العمليّات</h2>
            </div>
            <div class="container text-center">
                <div class="row">
                    <div class="col cell">
                        <div style="margin: 15px;">
                            <p>عدد العمليّات</p>
                            <p>عمليّة <bdi>{{$insight->transactionsCount}}</bdi></p>
                        </div>
                    </div>
                    <div class="col cell">
                        <div style="margin: 15px;">
                            <p>إجمالي المبلغ</p>
                            @if ($insight->total >= 0)
                                <p class="badge text-bg-success" style="font-size: 90%">ريال <bdi>{{$insight->total}}</bdi></p>
                            @else
                                <p class="badge text-bg-danger" style="font-size: 90%">ريال <bdi>{{$insight->total}}</bdi></p>
                            @endif
                        </div>

                    </div>
                </div>
                <div class="row">
                    <div class="col cell" style="background-color:#e3ffe2">
                        <div style="margin: 15px;">
                            <p><i class="fa-solid fa-chevron-down"></i> الوارد</p>
                            <p class="badge text-bg-success" style="font-size: 90%">ريال
                                <bdi>{{$insight->totalIncoming}}</bdi>
                            </p>

                        </div>

                    </div>
                    <div class="col cell" style="background-color:#ffe2e2">
                        <div style="margin: 15px;">
                            <p><i class="fa-solid fa-chevron-up"></i> الصادر</p>
                            <p class="badge text-bg-danger" style="font-size: 90%">ريال
                                <bdi>{{$insight->totalOutgoing}}</bdi>
                            </p>

                        </div>

                    </div>
                </div>
            </div>


        </div>
    </div>
  </div>
</div>
<br><br>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let transactions = {!! json_encode($transactions) !!};
    transactions = transactions.filter(transaction => transaction.amount <= 0);


    // Aggregate spending by date
    const spendingByDate = {};

    transactions.forEach(transaction => {
        const date = transaction.date;
        const amount = transaction.amount;

        if (!spendingByDate[date]) {
            spendingByDate[date] = 0;
        }
        spendingByDate[date] += amount; // Sum up the spending
    });

    // Prepare labels and data arrays, sorted by date
    const labels = Object.keys(spendingByDate).sort((a, b) => new Date(a) - new Date(b));
    const data = labels.map(label => Math.abs(spendingByDate[label])); // Get absolute values

    // Chart.js setup
    const ctx = document.getElementById('myChart');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'المبلغ المصروف (مستثنى الوارد)',
                data: data,
                borderWidth: 1,
                borderColor: 'rgb(192, 75, 75)',
                backgroundColor: 'rgba(192, 75, 75, 0.2)'
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>


    <table class="table table-striped">
        <thead>
            <tr style="text-align:center">
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
                    <td scope="row" class="{{$imageWanrOrNot}}"><small>{!! $imageLink ?: $imageUrl !!}</small></td>
                    <td scope="row" class="{{$messageWarnOrNot}}">{{$smsMessage}}</td>
                    <td scope="row">{{ \Carbon\Carbon::parse($transaction->created_at)->format('Y-m-d H:i:s') }}</td>
                </tr>

            @endforeach
        </tbody>
    </table>
    
<script>
    function printPage() {
        window.print();
    }
</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
        <p style="text-align: center; font-size: 14px; color: #555;">
    <small>
        تم انشاء هذا التقرير من 
        <strong><a href="{{url('/')}}">مصروفي</a></strong>: 
        برنامج تتبع المصروفات الشخصية. 
        <br>
        <em>مصروفي <a href="{{url('/terms')}}">لا يضمن</a> صحة البيانات أعلاه</em>
    </small>
</p>
</body>

</html>
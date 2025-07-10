@extends('layouts.app')
@section('content')
<h1 class="tajawal-bold"><bdi>{{auth('')->user()->name}}</bdi> أهلًا</h1>
<div class="card">

    <div class="card-body">
        <div class="custom-card-header">
            <h2 class="tajawal-bold">📊معلومات سريعة</h2>
        </div>
        <!--
-->
        <br>
        <div class="mb-3">
            <h4 class="tajawal-regular">مجموع الحساب💳: <span class="tajawal-bold">{{$homePageInsight->total}} ﷼</span>
            </h4>
        </div>
        @if (floatval($homePageInsight->total) < 0)
        <div class="alert alert-danger" role="alert">
                <h5 class="tajawal-regular">تحذير! رصيدك بالسالب. انت حرفيًا تحت الحديدة مو عليها</h5>
            </div>
        @elseif(floatval($homePageInsight->total) > 0)
        <!-- He is not mdyon. but maybe about to be-->
         @if (floatval($homePageInsight->total) <= 100)
         <div class="alert alert-warning" role="alert">
                <h5 class="tajawal-regular">تحذير! انت على وشك تكون مديون. ما بقى عندك الا {{$homePageInsight->total}} ﷼</h5>
            </div>
         @endif
        @endif
    </div>
    <hr>
    <div class="card-body">
        @if ($homePageInsight->budget)
            <div id="carouselExample" class="carousel slide" style="max-width:500px; max-height:500px">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <div class="custom-card-header">
                            <h2 class="tajawal-bold">💰معلومات الميزانيّة</h2>
                        </div>
                        <br>
                        <div class="mb-3">
                                <h4 class="tajawal-regular">ميزانيّتك الشهرية💰: <span class="tajawal-bold">{{$homePageInsight->budget}}
                                        ﷼</span>
                                </h4>
                            </div>
                            <div class="mb-3">
                                <h4 class="tajawal-regular">مجموع صرفيات الشهر الحالي💸: <span
                                        class="tajawal-bold">{{$homePageInsight->spendingsThisMonth}} ﷼</span></h4>
                            </div>
                            <div class="mb-3">
                                <h4 class="tajawal-regular">المتبقي من الميزانية💰: <span class="tajawal-bold">{{$homePageInsight->leftFromBudget}}
                                        ﷼</span>
                                </h4>
                            </div>

                    </div>
                    <div class="carousel-item">
                        <div class="custom-card-header">
                            <h2 class="tajawal-bold">📊 رسم بياني للميزانيّة</h2>

                        </div>
                        <div style="max-width:550px; max-height:400px">
                        <canvas id="myPieChart"></canvas>
                        <br>
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
                <script>
                        // Data for the pie chart
                        const leftFromBudget = {{$homePageInsight->leftFromBudget}};
                        const spendingsThisMonth = {{abs($homePageInsight->spendingsThisMonth)}}
                        const data = {
                            labels: ['الميزانيّة', 'الصرفيّات'], // Pie chart labels
                            datasets: [{
                                label: 'رسم بياني للميزانية',
                                data: [leftFromBudget, spendingsThisMonth],
                                backgroundColor: [
                                    'rgba(75, 192, 91, 0.6)', // Color for Budget
                                    'rgba(255, 99, 99, 0.6)'   // Color for Expenses
                                ],
                                borderColor: 'rgba(255, 255, 255, 1)',
                                borderWidth: 2
                            }]
                        };
                        // Config for the pie chart
                        const config = {
                            type: 'pie', // Specify the type as 'pie'
                            data: data,
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        position: 'top',
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(tooltipItem) {
                                                return tooltipItem.label + ': ' + tooltipItem.raw; // Customize tooltip label
                                            }
                                        }
                                    },
                                    datalabels: {
                                        color: '#fff', // Color of the labels
                                        anchor: 'end', // Positioning
                                        align: 'start', // Alignment
                                        font: {
                                            size: 16,
                                            weight: 600
                                        },
                                        formatter: (value, context) => {
                                            return context.chart.data.labels[context.dataIndex] + ': ' + value; // Format the label
                                        }
                                    }
                                }
                            },
                            plugins: [ChartDataLabels], // Register the datalabels plugin
                        };
                        // Create the pie chart instance
                        if(leftFromBudget > spendingsThisMonth){
                            const myPieChart = new Chart(
                            document.getElementById('myPieChart'),
                            config
                        );
                        } else {
                            //TODO: Visulize how much is the user overspending their budget
                            null
                        }
                </script>

                        </div>
                    </div>
                </div>
                <style>
                    /* Custom styles for carousel controls */
                    .carousel-control-prev-icon,
                    .carousel-control-next-icon {
                        background-color: rgba(0, 0, 0, 0.5);
                        /* Change background color */
                    }

                    .carousel-control-prev {
                        color: red;
                        /* Change arrow color for previous */
                    }

                    .carousel-control-next {
                        color: blue;
                        /* Change arrow color for next */
                    }
                </style>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
                @if ($homePageInsight->leftFromBudget < 0)
                <div class="alert alert-danger" role="alert">
                    <h5 class="tajawal-regular">تحذير! قاعد تصرف فوق الميزانيّة الشهرية</h5>
                </div>

                @elseif ($homePageInsight->leftFromBudget == 0)
                <div class="alert alert-danger" role="alert">
                    <h5 class="tajawal-regular">تحذير! خلصّت ميزانيّتك لهذا الشهر</h5>
                </div>

                @elseif ($homePageInsight->leftFromBudget < ($homePageInsight->budget * 0.2))
                <div class="alert alert-warning" role="alert">
                <h5 class="tajawal-regular">تحذير! تبقى فقط {{ number_format(($homePageInsight->leftFromBudget / $homePageInsight->budget) * 100, 2) }}% من الميزانيّة</h5>
                </div>
                @endif
        @else
            <div class="alert alert-info" role="alert">
                <h5 class="tajawal-regular">حط لك ميزانيّة شهرية <a href="/profile">من هنا</a></h5>
            </div>

        @endif
    </div>

</div>

<a href="/addTransaction"><button type="button" class="btn btn-primary btn-lg" style="width: 90%; margin:10px; "><i class="fa-solid fa-plus"></i> إدخال عمليّة جديدة</button></a>

<a href="/transactions"><button type="button" class="btn btn-secondary" style="width: 90%; margin:5px;"><i class="fa-solid fa-clock-rotate-left"></i> عرض تاريخ العمليّات</button>
</a>
@endsection
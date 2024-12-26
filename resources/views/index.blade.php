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
        <div class="custom-card-header">
            <h2 class="tajawal-bold">💰معلومات الميزانيّة</h2>
        </div>
        <br>

        @if ($homePageInsight->budget)
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
                <h4 class="tajawal-regular">المتبقي من الميزانية💰: <span
                        class="tajawal-bold">{{$homePageInsight->leftFromBudget}} ﷼</span>
                </h4>
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
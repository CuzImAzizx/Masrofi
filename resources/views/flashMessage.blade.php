@extends('layouts.app')
@section('content')
<div class="card" style="width:90vw; max-width:500px">
    <div class="card-body">
        @if($status == "success")
            <div class="alert alert-success" role="alert">
                @if ($reason == "transactionInsertedSuccessfully")
                <div class="mb-3">
                    <h1>تم انشاء العمليّة</h1>
                </div>
                <div class="mb-3">
                    <p>تم انشاء العمليّة برقم {{$insertedTransaction->id}}</p>
                </div>
                <div class="mb-3">
                    <a href="/transactions?modal=transaction{{$insertedTransaction->id}}"><button class="btn btn-primary"
                            style="width: 100%;">الذهاب الى تفاصيل العلميّة</button></a>
                </div>
                <div class="mb-3">
                    <a href="/transactions"><button type="button" class="btn btn-secondary" style="width: 100%;">عرض جميع
                            العمليّات</button>
                    </a>
                </div>
                @elseif($reason == "transactionDeleted")
                <div class="mb-3">
                    <h1>تم حذف العمليّة</h1>
                </div>
                <div class="mb-3">

                    <p>تم حذف العمليّة برقم {{$deletedTransactionId}}</p>
                </div>
                <a href="/transactions"><button type="button" class="btn btn-secondary" style="width: 100%;">عرض جميع
                            العمليّات</button>
                    </a>

                @endif
            </div>
        @else
            <div class="alert alert-danger" role="alert">
                @if ($reason == "transactionNotFound")
                    <div class="mb-3">
                        <h1>العملية ليست موجودة</h1>
                    </div>
                    <div class="mb-3">
                        <p>مافي عمليّة برقم {{$transactionId}}</p>
                    </div>
                @elseif($reason == "notTransactionOwner")
                    <div class="mb-3">
                        <h1>غير مصرح</h1>
                    </div>
                    <div class="mb-3">
                        <p>غير مصرح لك بالتعديل/حذف هذي العمليّة</p>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>



<div class="mb-3" style="margin:15px">
    <a href="/home"><button type="button" class="btn btn-outline-secondary" style="width: 90%;">العودة للصفحة
            الرئيسية</button>
    </a>
</div>

@endsection
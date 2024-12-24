@extends('layouts.app')
@section('content')
<h1>تاريخ العمليّات</h1> 
<p>تحت بتشوف كل العمليّات الي دخّلتها. تقدر تبحث, تفلتر, وتستخرج العمليّات</p>
<style>
    .answer.left {
        padding: 0 0 0 58px;
        text-align: left;
        float: left;
    }

    .answer {
        max-width: 600px;
        overflow: hidden;
        clear: both;
    }

    .answer.left .text {
        background: #ebebeb;
        color: #333333;
        border-radius: 8px 8px 8px 0;
    }

    .answer .text {
        padding: 12px;
        font-size: 16px;
        line-height: 26px;
        position: relative;
    }

    .answer.left .text:before {
        left: -30px;
        border-right-color: #ebebeb;
        border-right-width: 12px;
    }

    .answer .text:before {
        content: '';
        display: block;
        position: absolute;
        bottom: 0;
        border: 18px solid transparent;
        border-bottom-width: 0;
    }
</style>
<div class="card" style="width:90vw; max-width:500px">
    <div class="card-body">
        <h2>تصفية العمليات</h2>
        <p>تقدر تحت تبحث عن عمليّة او تعرض العمليّات في فترة معيّنة
        </p>
        <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample"
            aria-expanded="false" aria-controls="collapseExample" style="width: 100%;">
            🔍 البحث الدقيق
        </button>
        </p>
        <div class="collapse" id="collapseExample">
            <div class="card card-body">
                <form action="transactions" method="post">
                    @csrf
                    <div class="mb-3">
                    <div class="form-text">اعدادات جاهزة</div>
                                        <select class="form-select" name="viewModeMonth" onchange="submit()">
                                            <option value="" disabled selected>اختر وضع العرض</option>
                                            <option value="0">عرض عمليّات هذا الشهر</option>
                                            <option value="1">عرض عمليّات الشهر الماضي</option>
                                            <option value="3">عرض عمليّات لأخر ثلاث شهور</option>
                                            <option value="6">عرض عمليّات لأخر ستة شهور</option>
                                            <option value="12">عرض عمليّات لأخر سنة</option>
                                            <option value="99">عرض جميع العمليّات</option>
                                        </select>

                    </div>
                    <hr>
                    <div class="mb-3">
                        <div class="form-text">كلمة البحث</div>
                        <input type="text" class="form-control" name="searchTerm" style="text-align:center"
                            placeholder="بحث" value="{{old('searchTerm')}}">
                    </div>

                    <div class="mb-3">
                        <div class="container text-center">
                            <div class="row">
                                <div class="col">
                                    <div style="margin: 15px;">
                                        <div class="form-text">الى تاريخ</div>
                                        <input class="form-control" type="date" name="endDate" id="endDate" value="">
                                    </div>
                                </div>
                                <div class="col">
                                    <div style="margin: 15px;">
                                        <div class="form-text">من تاريخ</div>
                                        <input class="form-control" type="date" name="startDate" value="">
                                    </div>
                                    <script>
                                        //Assumme the end date is today.
                                        const today = new Date().toISOString().split('T')[0];
                                        document.getElementById('endDate').value = today;
                                    </script>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="container text-center">
                            <div class="row">
                                <div class="col">
                                    <div style="margin: 15px;">
                                        <div class="form-text">الى مبلغ</div>
                                        <input class="form-control" type="number" name="endAmount" value="">
                                    </div>
                                </div>
                                <div class="col">
                                    <div style="margin: 15px;">
                                        <div class="form-text">من مبلغ</div>
                                        <input class="form-control" type="number" name="startAmount" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="container text-center">
                            <div class="row">
                                <div class="col">
                                    <div style="margin: 15px;">
                                        <div class="form-text">ترتيب بشكل</div>
                                        <select class="form-select" aria-label="ترتيب بشكل" name="sortIn">
                                            <option value="asc">تصاعدي</option>
                                            <option value="desc" selected>تنازلي</option>
                                        </select>

                                    </div>
                                </div>
                                <div class="col">
                                    <div style="margin: 15px;">
                                        <div class="form-text">ترتيب حسب</div>
                                        <select class="form-select" aria-label="ترتيب حسب" name="sortBy">
                                            <option value="id">رقم العملية</option>
                                            <option value="date" selected>تاريخ العمليّة</option>
                                            <option value="amount">مبلغ العمليّة</option>
                                            <option value="created_at">تاريخ إدخال العمليّة</option>
                                        </select>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="mb-3">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <button type="submit" class="btn btn-outline-primary" style="width:100%">بحث</button>
                    </div>
                </form>

            </div>
        </div>



        <a href="/home"><button type="submit" class="btn btn-outline-secondary"
                style="width: 90%; margin: 10px">العودة</button></a>

    </div>
    <hr>
    @if ($insight->transactionsCount == 0)
        <div class="card-body">
            <h1>لا يوجد أي عمليّات</h1>
            @if ($viewMode == "filteredTransactions")
            <p>يتم عرض نتيجة البحث باستخدام هذه خيارات البحث</p>
            <ul>
                @if ($filterOptions->searchTerm)
                <li><bdi>{{$filterOptions->searchTerm}}</bdi> :كلمة البحث</li>
                @endif
                @if ($filterOptions->startAmount)
                <li>بمبلغ من {{$filterOptions->startAmount}} الى {{$filterOptions->endAmount}}</li>
                @endif
                @if ($filterOptions->startDate)
                <li>من تاريخ {{$filterOptions->startDate}} الى تاريخ {{$filterOptions->endDate}}</li>
                
                @else
                <p>جرب تغيّر في إعدادات البحث, أو دخّل عمليّات جديدة</p>

                @endif
                
            </ul>

            @endif
        </div>
    @else
        <div class="card-body">
            <div class="custom-card-header">
                <h2>ملخّص العمليّات</h2>
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
                    <div class="col cell">
                        <div style="margin: 15px;">
                            <p>الوارد</p>
                            <p class="badge text-bg-success" style="font-size: 90%">ريال
                                <bdi>{{$insight->totalIncoming}}</bdi>
                            </p>

                        </div>

                    </div>
                    <div class="col cell">
                        <div style="margin: 15px;">
                            <p>الصادر</p>
                            <p class="badge text-bg-danger" style="font-size: 90%">ريال
                                <bdi>{{$insight->totalOutgoing}}</bdi>
                            </p>

                        </div>

                    </div>
                </div>
            </div>


        </div>
        <br>
        <hr>

        <div class="custom-card-header">
            @if ($viewMode == "AllTransactions")
            <h2>جميع العمليّات</h2>
            <p>يتم عرض جميع العمليّات المدخلة</p>
            @elseif ($viewMode == "TransactionsThisMonth")
            <h2>عمليّات هذا الشهر</h2>
            <p>يتم عرض العمليّات من {{$dates->startDate->toDateString()}} الى {{$dates->endDate->toDateString()}}</p>
            @elseif ($viewMode == "TransactionsPastMonth")
            <h2>عمليّات الشهر الماضي</h2>
            <p>يتم عرض العمليّات من {{$dates->startDate->toDateString()}} الى {{$dates->endDate->toDateString()}}</p>

            @elseif ($viewMode == "filteredTransactions")
            <h2>بحث مخصص</h2>
            <p>يتم عرض نتيجة البحث باستخدام هذه خيارات البحث</p>
            <ul>
                @if ($filterOptions->searchTerm)
                <li><bdi>{{$filterOptions->searchTerm}}</bdi> :كلمة البحث</li>
                @endif
                @if ($filterOptions->startAmount)
                <li>بمبلغ من {{$filterOptions->startAmount}} الى {{$filterOptions->endAmount}}</li>
                @endif
                @if ($filterOptions->startDate)
                <li>من تاريخ {{$filterOptions->startDate}} الى تاريخ {{$filterOptions->endDate}}</li>
                @endif
                
            </ul>
            @elseif (is_numeric($viewMode))
            <h2>عرض عمليّات آخر {{$viewMode}} شهور</h2>
            <p>يتم عرض العمليّات من {{$dates->startDate->toDateString()}} الى {{$dates->endDate->toDateString()}}</p>

            @endif
            <!-- Button trigger modal -->
            <form action="/transactions/export" method="post">
                @csrf
            <input type="text" hidden name="transactions" value="{{json_encode($transactions)}}">
            <button type="submit" class="btn btn-info" style="width: 80%;">🖶 استخراج البيانات</button>
            </form>


<!-- Modal -->
<div class="modal fade" id="export" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5">استخراج العلميّات</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <h2>استخراج العلميّات</h2>
        <p>استخرج وحمّل عمليّاتك وشاركها مع اللي تبي</p>

<form action="/transactions/export" method="post">
    @csrf
<h5>صيغة الملف</h5>
<div class="container text-center">
  <div class="row">
    <div class="col">
    <div class="form-check" style="display: flex; justify-content: center; align-items: center; text-align: center; ">
    <label class="form-check-label d-flex align-items-center">
        <input class="form-check-input me-2" type="radio" name="formatType" value="pdf">
        <strong>PDF</strong>
    </label>
</div>
    </div>
    <div class="col">
    <div class="form-check" style="display: flex; justify-content: center; align-items: center; text-align: center; ">
    <label class="form-check-label d-flex align-items-center">
        <input class="form-check-input me-2" type="radio" name="formatType" checked value="csv" >
        <strong>CSV</strong>
    </label>
</div>
    </div>
    <div class="col">
    <div class="form-check" style="display: flex; justify-content: center; align-items: center; text-align: center; ">
    <label class="form-check-label d-flex align-items-center">
        <input class="form-check-input me-2" type="radio" name="formatType" value="json" disabled>
        <strong>JSON</strong>
    </label>
</div>
    </div>
  </div>
</div>

<br>
<button type="submit" class="btn btn-primary">تحميل</button>


</form>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">الخروج</button>
      </div>
    </div>
  </div>
</div>
            
        </div>
        <hr>
        <table class="table" role="table">
            <thead style="text-align: right;">
                <tr class="tajawal-bold">
                    <th scope="col">رقم</th>
                    <th scope="col">المبلغ</th>
                    <th scope="col">التاريخ</th>
                    <th scope="col">ملاحظات</th>
                    <th scope="col">تفاصيل</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transactions as $transaction)
                    <tr style="text-align: right;">
                        <td><span class="tajawal-bold">رقم:</span> {{$transaction->id}}</td>
                        @if ($transaction->amount > 0)
                            <td>
                                <span class="badge text-bg-success" style="font-size: 85%">{{$transaction->amount}}</span>
                                <span class="tajawal-bold">:المبلغ</span>
                            </td>
                        @else
                            <td>
                                <span class="badge text-bg-danger" style="font-size: 85%">{{$transaction->amount}}</span>
                                <span class="tajawal-bold">:المبلغ</span>
                            </td>
                        @endif

                        <td><span class="tajawal-bold">التاريخ:</span> {{$transaction->date}}</td>
                        <td>
                            @if ($transaction->note)
                                <span class="tajawal-bold">ملاحظات:</span> {{$transaction->note}}
                            @else
                                <span class="tajawal-extralight">لا توجد ملاحظات</span>
                            @endif
                        </td>
                        <td>

                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                                data-bs-target="#transaction{{$transaction->id}}">
                                تفاصيل اكثر
                            </button>
                            <!-- Modal -->
                            <div class="modal fade" id="transaction{{$transaction->id}}" tabindex="-1"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="exampleModalLabel">تفاصيل عمليّة رقم
                                                {{$transaction->id}}
                                            </h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body" style="text-align: center;">
                                            <!--price-->
                                            <div class="mb-3">
                                                <p>مبلغ العميّة</p>
                                                @if ($transaction->amount > 0)
                                                    <h5><span class="badge text-bg-success" style="font-size: 85%">ريال
                                                            {{$transaction->amount}}</span></h5>
                                                @else
                                                    <h5><span class="badge text-bg-danger" style="font-size: 85%">ريال
                                                            {{$transaction->amount}}</span></h5>
                                                @endif
                                            </div>
                                            <hr>
                                            <!--name-->
                                            <div class="mb-3">
                                                <p>أسم المتجر</p>
                                                <h5>{{$transaction->store_name}}</h5>
                                            </div>
                                            <hr>
                                            <div class="mb-3">
                                                <p>تاريخ العمليّة</p>
                                                <h5>{{$transaction->date}}</h5>
                                            </div>
                                            <hr>
                                            @if ($transaction->sms_message)
                                                <div class="mb-3">
                                                    <p>رسالة العمليّة من البنك</p>
                                                    <div class="answer left">
                                                        <div class="text">
                                                            {{$transaction->sms_message}}
                                                        </div>
                                                    </div>
                                                </div>
                                                <p style="font-size:1px">a</p>
                                                <hr>
                                            @endif

                                            <div class="mb-3">

                                                <p>ملاحظات</p>
                                                @if ($transaction->note)
                                                    <h5>{{$transaction->note}}</h5>
                                                @else
                                                    <p class="tajawal-extralight">لا توجد ملاحظات</p>
                                                @endif
                                            </div>
                                            <hr>

                                            <div class="mb-3">
                                                <p>صورة للفاتورة</p>
                                                @if ($transaction->image)
                                                    <img style="width:60vw; max-width:430px" src="{{asset('storage/' . $transaction->image)}}"
                                                        class="rounded mx-auto d-block">
                                                @else
                                                    <p class="tajawal-extralight">لا توجد صورة</p>
                                                @endif
                                            </div>
                                            <hr>

                                            <div class="mb-3">
                                                <div class="container text-center">
                                                    <div class="row align-items-start">
                                                        <div class="col">
                                                            <p>آخر تاريخ تعديل للعمليّة</p>
                                                            @if ($transaction->updated_at == $transaction->created_at)
                                                                <p class="tajawal-extralight">لم يتم التعديل على العمليّة</p>
                                                            @else
                                                                <p>{{$transaction->updated_at}}</p>
                                                            @endif
                                                        </div>

                                                        <div class="col">
                                                            <p>تاريخ ادخال العمليّة</p>
                                                            {{$transaction->created_at}}
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="modal-footer" style="text-align:center">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">خروج</button>
                                            <div class="dropdown">
                                                <!-- Default dropup button -->
                                                <div class="btn-group dropup">
                                                    <button type="button" class="btn btn-primary dropdown-toggle"
                                                        data-bs-toggle="dropdown" aria-expanded="true">
                                                        خيارات
                                                    </button>
                                                    <ul class="dropdown-menu" style="padding:5px">
                                                        <!-- Dropdown menu links -->
                                                        <li class="mb-1"> <a
                                                                href="/transactions/{{$transaction->id}}/edit"><button
                                                                    style="width:100%" class="btn btn-outline-primary">تعديل</button></a>
                                                        </li>
                                                        <li class="mb-1">
                                                        <a
                                                        href="/transactions/{{$transaction->id}}/delete">
                                                            <button style="width:100%" class="btn btn-outline-danger">
                                                            حذف
                                                            </button>
                                                            </a>
                                                    </li>

                                                    </ul>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                </div>

                                <br><br>
                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>

    @endif
</div>
</div>
<script>
    window.onload = function () {
        // Function to open the modal
        function openModal(modalId) {
            var modal = new bootstrap.Modal(document.getElementById(modalId));
            modal.show();
        }

        // Check URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const modalId = urlParams.get('modal');
        if (modalId) {
            openModal(modalId);
        }
    };

</script>
@endsection

@extends('layouts.app')
@section('content')
<style>
    /* The switch - the box around the slider */
    .switch {
        font-size: 17px;
        position: relative;
        display: inline-block;
        width: 3.5em;
        height: 2em;
    }

    /* Hide default HTML checkbox */
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    /* The slider */
    .slider {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background: white;
        border-radius: 50px;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.215, 0.61, 0.355, 1);
        box-shadow: 0 0 20px #e0e0e0;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 1.4em;
        width: 1.4em;
        right: 0.3em;
        bottom: 0.3em;
        transform: translateX(150%);
        background-color: #59d102;
        border-radius: inherit;
        transition: all 0.4s cubic-bezier(0.215, 0.61, 0.355, 1);
    }

    .slider:after {
        position: absolute;
        content: "";
        height: 1.4em;
        width: 1.4em;
        left: 0.3em;
        bottom: 0.3em;
        background-color: #ff0000;
        border-radius: inherit;
        transition: all 0.4s cubic-bezier(0.215, 0.61, 0.355, 1);
    }

    .switch input:focus+.slider {
        box-shadow: 0 0 1px #59d102;
    }

    .switch input:checked+.slider:before {
        transform: translateY(0);
    }

    .switch input:checked+.slider::after {
        transform: translateX(-150%);
    }
</style>
<h1 class="tajawal-bold">مراجعة تفاصيل العمليّة</h1>
<p>حللنا العمليّة وهذي المعلومات اللي لقيناها. راجعها قبل ما ترسلها</p>
<div class="card">
    <div class="card-body">
        <form action="/insertTransaction" method="post" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <div class="form-label">رسالة عمليّة الشراء من البنك</div>
                <!--You just fool your self when you inspect the page and  hange those values. Please leave it alone-->
                <textarea class="form-control" rows="3" required
                readonly name="smsMessage">{{$smsMessage}}</textarea>
            </div>
            <div class="mb-3">
                <div class="form-label">اسم المتجر</div>
                <input class="form-control" type="text" name="storeName" value="{{$transaction->name ?? null}}" required>
            </div>
            <div class="container text-center">
                <div class="row">
                    <div class="col">
                        <div class="mb-3">
                            <div class="form-label">المبلغ</div>
                            <input class="form-control" type="number" name="amount" id="amount"
                                value="{{$transaction->amount ?? 0}}" required>
                        </div>

                    </div>
                    <div class="col">
                        <div class="mb-3">
                            @if (intval($transaction->amount ?? 0) < 0)
                                <div class="form-label" id="amountLable">صادر</div>
                            @else
                                <div class="form-label" id="amountLable">وارد</div>
                            @endif

                            <label class="switch">
                                @if (intval($transaction->amount ?? 0) < 0)
                                    <input type="checkbox" id="toggle">
                                @else
                                    <input type="checkbox" id="toggle" checked>
                                @endif

                                <span class="slider"></span>
                            </label>
                        </div>

                    </div>
                </div>
            </div>

            <div class="mb-3">
                <div class="form-label">تاريخ العمليّة</div>
                <input class="form-control" type="date" required name="date" id="date-input"
                    value="{{$transaction->date ?? null}}">
            </div>
            <div class="mb-3">
                <div class="form-label">صورة للفاتورة</div>
                <input class="form-control" type="file" name="image">
            </div>

            <div class="mb-3">
                <div class="form-label">ملاحظات</div>
                <textarea name="note" class="form-control" rows="2"></textarea>
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
                <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;"><i class="fa-solid fa-plus"></i> إرسال العمليّة</button>
            </div>
        </form>
    </div>
</div>
<a href="/home"><button type="submit" class="btn btn-outline-secondary"
        style="width: 90%; margin: 10px">العودة</button></a>

<script>

    //For the checkbox
    const amountInput = document.getElementById('amount');
    const toggleCheckbox = document.getElementById('toggle');
    const amountLable = document.getElementById('amountLable');

    amountInput.addEventListener('input', function () {
        const amount = parseFloat(amountInput.value);
        if (amount >= 0) {
            toggleCheckbox.checked = true
            amountLable.innerHTML = "وارد"
        } else {
            toggleCheckbox.checked = false
            amountLable.innerHTML = "صادر"
        }

    });
    toggleCheckbox.addEventListener('change', function () {
        const amount = parseFloat(amountInput.value);
        if (toggleCheckbox.checked) {
            amountInput.value = Math.abs(amount);
            amountLable.innerHTML = "وارد"
        } else if (!toggleCheckbox.checked) {
            amountInput.value = -Math.abs(amount);
            amountLable.innerHTML = "صادر"
        }
    });

</script>

@endsection
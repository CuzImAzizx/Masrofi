@extends('layouts.app')
@section('content')
<h1 class="tajawal-bold">إدخال عمليّة شرائيّة</h1>
<p>أدخل رسالة عمليّة الشراء في الخانة تحت</p>
<div class="card">
    <div class="card-body">
        <form action="/addTransaction" method="post">
            @csrf
            @if (!$worthiness['isWorthy'])
                <div class="mb-3">
                    <label for="exampleFormControlTextarea1" class="form-label" style="width: 300px;">رسالة عمليّة الشراء من
                        البنك</labe>
                        <textarea class="form-control" id="exampleFormControlTextarea1" rows="4" required name="smsMessage"
                            disabled>{{old('smsMessage')}}</textarea>
                </div>

                <div class="mb-3">
                    @if ($worthiness['reason'] == "no requests left")
                        <div class="alert alert-secondary" role="alert">
                            وصلت الحد اليومي لإستخدام هذي الميزة. انتظر {{$worthiness['remainingSeconds']}} ثانية عشان تستخدم هذي الميزة مرة ثانية
                        </div>
                    @elseif ($worthiness['reason'] == "threshold reached")
                        <div class="alert alert-secondary" role="alert">
                            <!--TODO: Make a cool UI with a countdown when it's available-->
                            انتظر <bdi>{{$worthiness['remainingSeconds']}}</bdi> ثانية قبل ما تستخدم هذي الميزة مرة ثانية
                        </div>
                    @endif
                </div>


            @else
                <div class="mb-3">
                <label for="exampleFormControlTextarea1" class="form-label" style="width: 300px;">رسالة عمليّة الشراء من
                        البنك</labe>

                    <textarea class="form-control" id="exampleFormControlTextarea1" rows="4" required
                        name="smsMessage">{{old('smsMessage')}}</textarea>
                </div>
            @endif
            
            <div class="mb-3">
            @if (!$worthiness['isWorthy'])
            <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;" disabled>إرسال العمليّة</button>
            @else
            <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">إرسال العمليّة</button>
            @endif
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

        </form>
        <hr>
        <h4>أو بإمكانك</h4>
        <a href="/addTransactionManual"><button type="submit" class="btn btn-secondary" style="width: 90%;">إدخال
                العمليّة بشكل يدوي</button></a>
    </div>
</div>
<a href="/home"><button type="submit" class="btn btn-outline-secondary"
        style="width: 90%; margin: 10px">العودة</button></a>

@endsection
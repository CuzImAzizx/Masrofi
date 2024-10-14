@extends('layouts.app')
@section('content')
<h1 class="tajawal-bold">حسابي</h1>
<p>هنا تقدر تَطِلع/تعدل بيانات حسابك في تطبيق مصروفي</p>

<div class="card" style="width:90vw; max-width:500px">
    <div class="card-body">
    <form action="/profile" method="post" enctype="multipart/form-data">
        @csrf
    <div class="mb-3">
            <div class="form-label">اسم المستخدم</div>
            <input class="form-control" type="text" name="userName"
            value="{{old('userName')}}" placeholder="{{$userData['name']}}">
        </div>
        <br>
        <h2>ضبط الميزانية</h2>
        <hr>
        <div class="mb-3">
        <div class="form-label">ميزانيّتك الشهرية</div>
        <input class="form-control" type="number" name="monthly_budget"
        value="{{old('monthly_budget')}}" placeholder="{{$userData['monthly_budget']}}" min="0">
        </div>
        <div class="mb-3">
        <div class="form-label">بداية الشهر</div>
        <input class="form-control" type="number" name="start_of_the_month" 
        value="{{old('start_of_the_month')}}" placeholder="{{$userData['start_of_the_month']}}" min="1" max="28">
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
            <button type="submit" class="btn btn-primary" style="width:100%">تثبيت</button>
        </div>
    </form>
    </div>
</div>
<a href="/home"><button type="submit" class="btn btn-outline-secondary"
        style="width: 90%; margin: 10px">العودة</button></a>

@endsection
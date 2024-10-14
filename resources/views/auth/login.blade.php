@extends('layouts.app')
@section('content')
<div class="card">
    <div class="card-body">
        <form action="/login" method="post">
            @csrf
            <div class="mb-3">
                <h2>تسجيل الدخول</h2>
                <div class="form-text">أرحب مرة ثانية في تطبيق مصروفي. تحتاج تسجل دخولك عشان توصل لحسابك</div>
            </div>
            <div class="mb-3">
            </div>
            <div class="mb-3">
                <input type="email" class="form-control" name="email" style="text-align:center" placeholder="اللإيميل"
                    value="{{old('email')}}">
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" name="password" style="text-align:center" minlength="8"
                    placeholder="كلمة السر">
            </div>
            <div class="mb-3">
            <button type="submit" class="btn btn-primary">تسجيل الدخول</button>

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
        <div class="mb-3">
            <p>ما عندك حساب؟ <a href="/register">تسجيل حساب جديد</a></p>
        </div>
    </div>
</div>

@endsection
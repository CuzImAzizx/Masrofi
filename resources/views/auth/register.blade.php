@extends('layouts.app')
@section('content')
<div class="card">
    <div class="card-body">
        <form action="register" method="POST">
            @csrf
            <div class="mb-3">
                <h1>تسجيل حساب جديد</h1>
                <div class="form-text">يالله حيّه في تطبيق مصروفي. نحتاج منك بعض البيّانات عشان نسوي لك حساب</div>
            </div>
            <div class="mb-3">

            </div>
            <div class="mb-3">
                <input type="text" name="name" minlength="3" maxlength="12" class="form-control" required
                    style="text-align:center" placeholder="أسم المستخدم" value="{{old('name')}}">
                <div class="form-text">هذا الاسم اللي بنناديك فيه</div>
            </div>
            <div class="mb-3">
                <input type="email" name="email" minlength="3" maxlength="255" class="form-control" required
                    style="text-align:center" placeholder="اللإيميل" value="{{old('email')}}">
                <div class="form-text">الإيميل مهم عشان تسجّل دخولك</div>
            </div>
            <div class="mb-1">
                <input type="password" name="password" minlength="8" maxlength="255" class="form-control" required
                    style="text-align:center" placeholder="كلمة السر">
            </div>
            <div class="mb-3">
                <input type="password" name="password_confirmation" minlength="8" maxlength="255" class="form-control"
                    required style="text-align:center" placeholder="تأكيد كلمة السر">
                <div class="form-text">حط باسورد قوي، ولا تستخدم نفس باسورد البنك</div>

            </div>
            <hr>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="TOS" required>
                <label class="form-check-label" for="TOS">انا موافق على <a href="/terms">شروط الإستخدام</a></label>
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
            <p>مسجّل من قبل؟ <a href="/login">سجّل دخولك</a></p>
        </div>
    </div>
</div>

@endsection
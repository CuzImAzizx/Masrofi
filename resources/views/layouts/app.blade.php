<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>مصروفي | Masrofi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha384-..." crossorigin="anonymous">
    <link rel="stylesheet" href="/phoneStyle.css">
    <link rel="icon" type="image/x-icon" href="/icon.png">
  </head>


<body class="tajawal-medium">
  @if (auth()->check())

  <a data-bs-toggle="offcanvas"
    href="#offcanvasExample" role="button" aria-controls="offcanvasExample">
    <div style="position: absolute; top: 20px; left: 10px;"> 
    <i class="fa-solid fa-bars" style="color:black; font-size:xx-large"></i>
    </div>
</a>

  <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title"><bdi>{{auth()->user()->name}}</bdi> مرحبًَا</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <div class="list-group list-group-flush" style="text-align: center;">
    <li class="list-group-item tajawal-bold card-header">العمليّات</li>
  <a href="/addTransaction" class="list-group-item list-group-item-action">ادخال عمليّة جديدة</a>
  <a href="transactions" class="list-group-item list-group-item-action">عرض جميع العمليّات</a>
</div>
<br>
<div class="list-group list-group-flush" style="text-align: center;">
<li class="list-group-item tajawal-bold" >الحساب</li>
<a href="/profile" class="list-group-item list-group-item-action">حسابي</a>
<a href="/logout" class="list-group-item list-group-item-action list-group-item-danger">تسجيل الخروج</a>

</div>

  </div>
</div>

  @endif

    <div style="text-align:center">

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
        integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
        crossorigin="anonymous"></script>

</body>

</html>
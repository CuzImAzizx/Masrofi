@extends('layouts.app')
@section('content')
<style>
    h1,
    h2 {
        color: #333;
    }

    strong {
        font-weight: bold;
    }
</style>
<br><br>
<div class="card">
    <div class="card-body" style="text-align: right; max-width:500px">
        <div class="custom-card-header" style="text-align:center">
        <h1>شروط وأحكام مصروفي</h1>
        </div>
        <br>
        <p>أهلًا بك في مصروفي، تطبيق تتبع المصروفات الشخصية! يرجى قراءة شروط وأحكام الاستخدام بعناية قبل استخدام التطبيق.</p>

<h2>حول مصروفي</h2>
<p>مصروفي هو مشروع شخصي قام بتطويره طالب. يتم استضافته على جهاز كمبيوتر شخصي ويتم تقديمه "كما هو"، بدون أي ضمان من أي نوع. أنا أبذل قصارى جهدي للحفاظ على سير عمله بسلاسة، لكنني لا أضمن وقت تشغيله أو استمرارية توفره.</p>

<h2>بدون ضمان</h2>
<p><strong>يتم تقديم مصروفي بدون أي ضمان، صريح أو ضمني، بما في ذلك على سبيل المثال لا الحصر ضمانات القابلية للتسويق، والملاءمة لغرض معين، أو عدم الانتهاك.</strong> أنا لست مسؤولاً عن أي خسارة أو ضرر، بما في ذلك فقدان البيانات، قد يحدث نتيجة استخدام مصروفي.</p>

<h2>سلامة البيانات وأمنها</h2>
<p>أسعى جاهدًا للحفاظ على سلامة بياناتك، لكنني لا أضمن ذلك. هناك خطر فقدان البيانات بسبب مشاكل تقنية أو ظروف غير متوقعة. علاوة على ذلك، لا يستخدم مصروفي التشفير. <strong>لا تقم بتحميل أي معلومات مالية حساسة أو سرية.</strong></p>

<h2>استخدام البيانات</h2>
<p><pdo>
يستخدم مصروفي نموذج الذكاء الاصطناعي <a href="https://ai.google.dev/gemini-api/terms">حيميناي</a> الخاص بجوجل للمساعدة في تحليل معاملاتك لإستخراج المعلومات من رسالة عمليّة الشراء. باستخدام مصروفي، فإنك توافق على تمرير معاملاتك الى النموذج هذا.
</p></pdo>
<h2>المسؤولية</h2>
<p><strong>بأقصى حد يسمح به القانون، لن أكون مسؤولاً عن أي أضرار مباشرة أو غير مباشرة أو عرضية أو تبعية أو خاصة تنشأ عن أو فيما يتعلق باستخدامك لمصروفي.</strong></p>

<h2>التغييرات على هذه الشروط</h2>
<p>قد أقوم بتحديث شروط وأحكام الاستخدام من وقت لآخر. سأقوم بنشر أي تغييرات على هذه الصفحة. استمرار استخدامك لمصروفي بعد أي تغييرات يشكل قبولك للشروط المحدثة.</p>
<p><strong>باستخدام مصروفي، فإنك تقر بأنك قرأت وفهمت ووافقت على شروط وأحكام الاستخدام هذه.</strong></p>

    </div>

</div>
<br>
@if (auth()->check() and auth()->user()->terms_acceptance_date != null)
<div class="card">
    <div class="card-body" style="text-align: right; max-width:500px">
    <div class="custom-card-header" style="text-align:center">
    <h2>تأكيد القراءة والقبول</h2>
    </div>
    <br>
    <strong>
    تمت الموافقة على شروط وأحكام الاستخدام في تاريخ {{ \Carbon\Carbon::parse(auth()->user()->terms_acceptance_date)->format('Y-m-d') }} بالساعة {{ \Carbon\Carbon::parse(auth()->user()->terms_acceptance_date)->format('H:i') }}
    </strong>

        <br>
    </div>
</div>

<p>
</p>

@endif
@if ($viewChoices)
<div class="card">
    <div class="card-body" style="text-align: right; max-width:500px">
    <div class="custom-card-header" style="text-align:center">
    <h2>تأكيد القراءة والقبول</h2>
    </div>
    <br>

        <div>
            <pdo>
                انا، {{auth()->user()->name}}، أقر بأنني قرأت وفهمت هذه الشروط والأحكام، وأوافق عليها
            </pdo>
        </div>
        <br>
    <div class="container text-center">
  <div class="row">
    <div class="col">
    <a href="/terms/deny"><button class="btn btn-danger" style="width:80%">رفض</button></a>
    </div>
    <div class="col">
    <a href="/terms/accept"><button class="btn btn-primary" style="width:80%">قبول</button></a>

    </div>
  </div>
</div>

    </div>
</div>
<hr>
@else
    <a href="{{url()->previous()}}"><button class="btn btn-secondary" style="width:80%">العودة</button></a>
    <hr>
@endif

<br>
@endsection

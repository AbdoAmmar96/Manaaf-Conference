@extends('layouts.public')

@section('title', 'حالة طلبك')

@section('content')
<div class="narrow-page">
  <div class="panel fade-up">
    @if($guest->approval_status === \App\Enums\ApprovalStatus::Rejected)
      <h1>عذرًا — لم يُعتمد الطلب</h1>
      <p class="sub">مرحبًا {{ $guest->name }}</p>
      <div class="status-icon rejected">✕</div>
      <p class="status-text">
        نعتذر، لم يتم اعتماد طلب حضوركم لهذه الفعالية.
        @if($guest->rejection_reason)
          <br><br><b>السبب:</b> {{ $guest->rejection_reason }}
        @endif
      </p>
      <a href="{{ route('contact') }}" class="btn btn-outline">تواصل معنا</a>
    @else
      <h1>طلبكم قيد المراجعة</h1>
      <p class="sub">مرحبًا {{ $guest->name }}</p>
      <div class="status-icon pending">⏳</div>
      <p class="status-text">
        استلمنا طلب حضوركم ويراجعه فريق التنظيم حاليًا.
        <br><br>
        عند اعتماده تصلكم <b>بطاقة الدعوة بكود QR</b> على الواتساب
        @if($guest->email) أو البريد <span dir="ltr">{{ $guest->email }}</span> @endif
        — احتفظوا بها وقدّموها عند البوابة.
      </p>
      <a href="{{ route('home') }}" class="btn btn-lime">العودة للرئيسية</a>
    @endif
  </div>
</div>
@endsection

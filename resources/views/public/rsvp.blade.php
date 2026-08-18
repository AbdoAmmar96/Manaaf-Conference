@extends('layouts.public')

@section('title', 'دعوة حضور — ' . $guest->name)

@section('content')
<div class="narrow-page">
  <div class="panel fade-up">

    @if($guest->rsvp_status === \App\Enums\RsvpStatus::Declined)
      <h1>نأسف لعدم تمكنكم من الحضور</h1>
      <p class="sub">تم تسجيل اعتذاركم — إن تغيّر برنامجكم فيسعدنا حضوركم.</p>
      <form method="POST" action="{{ route('rsvp.respond', ['token' => $guest->invite_token]) }}">
        @csrf
        <input type="hidden" name="action" value="confirm">
        <button type="submit" class="btn btn-green">غيّرتُ رأيي — أؤكد الحضور</button>
      </form>

    @elseif($guest->rsvp_status === \App\Enums\RsvpStatus::Confirmed)
      <h1>تم تأكيد حضوركم مسبقًا</h1>
      <p class="sub">بطاقة الدخول الخاصة بكم جاهزة</p>
      <a href="{{ route('guest.qr', ['token' => $guest->qr_token]) }}" class="btn btn-green">عرض بطاقة الدخول</a>

    @else
      <span class="badge badge-green" style="margin-bottom:.8rem">دعوة خاصة</span>
      <h1>{{ $guest->name }}</h1>
      @if($guest->organization)
        <p class="sub">{{ $guest->organization }}@if($guest->position) — {{ $guest->position }}@endif</p>
      @endif
      <p style="font-size:.92rem;color:var(--ink);line-height:2.1;margin-bottom:1.5rem">
        يتشرف القائمون على <b>{{ $eventName }}</b> بدعوتكم لحضور فعاليات المؤتمر وحفل الافتتاح
        @if($eventDate) يوم {{ \Illuminate\Support\Carbon::parse($eventDate)->translatedFormat('l d F Y') }}@endif
        <br><span style="color:var(--muted);font-size:.85rem">{{ $eventVenue }}</span>
      </p>
      <form method="POST" action="{{ route('rsvp.respond', ['token' => $guest->invite_token]) }}" style="display:flex;gap:.8rem;justify-content:center;flex-wrap:wrap">
        @csrf
        <button type="submit" name="action" value="confirm" class="btn btn-lime">أؤكد الحضور</button>
        <button type="submit" name="action" value="decline" class="btn" style="border:1.5px solid var(--line);background:#fff;color:var(--muted)">أعتذر عن الحضور</button>
      </form>
    @endif

  </div>
</div>
@endsection

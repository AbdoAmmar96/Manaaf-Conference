@extends('layouts.public')

@section('title', $title)

@section('content')
<div class="narrow-page">
  <div class="panel fade-up">
    <h1>{{ $title }}</h1>
    <p class="sub">هذه الصفحة قيد البناء ضمن المراحل القادمة من المشروع.</p>
    <a href="{{ route('home') }}" class="btn btn-green">العودة للرئيسية</a>
  </div>
</div>
@endsection

@extends('layouts.admin')

@section('title', $title)

@section('content')
<h1 class="page-title">{{ $title }}</h1>
<div class="table-wrap" style="padding:2.5rem;text-align:center">
  <img src="{{ asset('brand/icon-green.png') }}" alt="" style="height:56px;margin:0 auto 1rem;opacity:.4">
  <p style="color:var(--muted)">هذا الموديول يُبنى في المراحل القادمة حسب خطة التنفيذ.</p>
</div>
@endsection

@extends('layouts.landing')

@section('title', 'Funcionalidades | PsiGestor')

@push('styles')
    @vite('resources/js/public-pages/funcionalidades/index.jsx')
@endpush

@section('content')

<div
    id="psigestor-features-page"
    data-home-url="{{ route('home') }}"
    data-register-url="{{ route('register') }}"
    data-login-url="{{ route('login') }}"
    data-plans-url="{{ route('planos') }}"
    data-whatsapp-url="https://wa.me/5582991128022?text=Ol%C3%A1%2C%20tenho%20interesse%20no%20PsiGestor!"
></div>

@endsection
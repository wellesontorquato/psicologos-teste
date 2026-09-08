@extends('layouts.landing')

@section('title', 'Nossos Planos | PsiGestor')

@push('scripts')
    @vite('resources/js/public-pages/planos/index.jsx')
@endpush

@section('content')
    <div
        id="psigestor-plans-page"
        data-register-url="{{ route('register') }}"
        data-login-url="{{ route('login') }}"
        data-features-url="{{ route('funcionalidades') }}"
        data-whatsapp-url="https://wa.me/5582991128022?text=Ol%C3%A1%2C%20tenho%20interesse%20no%20PsiGestor%20e%20gostaria%20de%20saber%20mais%20sobre%20os%20planos!"
    ></div>
@endsection
@extends('layouts.landing')

@section('title', 'Fale Conosco | PsiGestor')

@section('content')

{{-- Título --}}
<section style="background: linear-gradient(to right, #00aaff, #00c4ff); padding: 60px 20px; margin-top: 85px;">
    <h1 style="text-align: center; color: white; font-size: 2.2rem; margin: 0;">
        Fale Conosco
    </h1>
</section>

{{-- Formulário --}}
<section style="padding: 30px 20px;">
    <div style="max-width: 600px; margin: auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.07);">
        <p style="font-size: 1rem; color: #555; margin-bottom: 25px; text-align: center;">
            Estamos aqui para ajudar você! Preencha o formulário abaixo e nossa equipe entrará em contato o mais breve possível.
        </p>

        {{-- Mensagens de sucesso/erro --}}
        @if (session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    Swal.fire({
                        icon: 'success',
                        title: 'Mensagem enviada!',
                        text: @json(session('success')),
                        confirmButtonColor: '#00aaff'
                    });
                });
            </script>
        @endif

        @if (session('error'))
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Ops...',
                        text: @json(session('error')),
                        confirmButtonColor: '#00aaff'
                    });
                });
            </script>
        @endif

        <form action="{{ route('contato.enviar') }}" method="POST">
            @csrf

            {{-- Nome --}}
            <div class="form-group">
                <input type="text" name="nome" placeholder="Nome Completo *" required class="input-field">
            </div>

            {{-- Email --}}
            <div class="form-group">
                <input type="email" name="email" placeholder="E-mail *" required class="input-field">
            </div>

            {{-- Telefone --}}
            <div class="form-group">
                <input type="text" name="telefone" placeholder="Telefone (opcional)" class="input-field">
            </div>

            {{-- Assunto --}}
            <div class="form-group">
            <select name="assunto" required class="input-field">
                <option value="">Selecione um assunto *</option>
                <option value="Agendar demonstração">Gostaria de agendar uma demonstração do sistema</option>
                <option value="Suporte técnico">Preciso de suporte técnico</option>
                <option value="Dúvida geral">Tenho uma dúvida</option>
                <option value="Sugestão de melhoria">Gostaria de enviar uma sugestão</option>
                <option value="Problemas com pagamento">Estou com problemas no pagamento</option>
                <option value="Ajuda com cadastro">Preciso de ajuda para me cadastrar</option>
                <option value="Interesse em parceria">Tenho interesse em parcerias</option>
                <option value="Outro assunto">Outro assunto</option>
            </select>
            </div>

            {{-- Mensagem --}}
            <div class="form-group">
                <textarea name="mensagem" placeholder="Mensagem *" rows="4" required class="input-field"></textarea>
            </div>

            {{-- reCAPTCHA --}}
            <div class="form-group" style="text-align: center;">
                <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
            </div>

            {{-- Botão --}}
            <div style="text-align: center; margin-top: 20px;">
                <button type="submit" class="submit-btn">
                    Enviar Mensagem
                </button>
            </div>
        </form>
    </div>
</section>

{{-- BOTÃO WHATSAPP FLOTANTE --}}
<a href="https://wa.me/5582991128022?text=Ol%C3%A1%2C%20tenho%20interesse%20no%20PsiGestor%20e%20gostaria%20de%20saber%20mais%20sobre%20os%20planos!" target="_blank" style="
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 999;
    display: flex;
    align-items: center;
    gap: 10px;
    background: #25d366;
    color: white;
    padding: 10px 15px;
    border-radius: 50px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    text-decoration: none;
    font-weight: bold;
    transition: all 0.3s ease;
" onmouseover="this.style.background='#1ebd5a';" onmouseout="this.style.background='#25d366';">
    <img src="https://psicologos-teste-production.up.railway.app/images/whatsapp.png" alt="WhatsApp" style="width: 24px; height: 24px;">
    (82) 99112-8022
</a>

@push('styles')
<style>
    .form-group {
        margin-bottom: 18px;
    }

    .input-field {
        width: 100%;
        padding: 14px 16px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        box-sizing: border-box;
    }

    .input-field:focus {
        border-color: #00aaff;
        box-shadow: 0 0 0 3px rgba(0, 170, 255, 0.15);
        outline: none;
    }

    select.input-field {
        background: #fff;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }

    .submit-btn {
        background: #00aaff;
        color: #fff;
        border: none;
        padding: 14px 30px;
        border-radius: 8px;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .submit-btn:hover {
        background: #0095d8;
    }

    /* 👇 CENTRALIZAÇÃO do reCAPTCHA */
    .g-recaptcha {
        display: flex !important;
        justify-content: center !important;
        width: 100% !important;
        margin: 0 auto !important;
    }
</style>
@endpush

@push('scripts')
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

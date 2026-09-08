@extends('layouts.landing')

@section('title', 'Fale Conosco | PsiGestor')

{{-- PSIGESTOR CONTACT LOCAL CAPTCHA --}}
@php
    $isLocalContactHost = in_array(
        request()->getHost(),
        ['127.0.0.1', 'localhost'],
        true
    );
@endphp

@push('scripts')
    @vite('resources/js/public-pages/contato/index.jsx')
    @unless($isLocalContactHost)
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endunless
@endpush

@section('content')
    <main class="pgct-page">
        <section class="pgct-hero">
            <div
                class="pgct-hero-orb pgct-hero-orb-one"
                aria-hidden="true"
            ></div>

            <div
                class="pgct-hero-orb pgct-hero-orb-two"
                aria-hidden="true"
            ></div>

            <div class="pgct-shell pgct-hero-grid">
                <div
                    id="psigestor-contact-intro"
                    data-whatsapp-url="https://wa.me/5582991128022?text=Ol%C3%A1%2C%20tenho%20interesse%20no%20PsiGestor%20e%20gostaria%20de%20saber%20mais!"
                ></div>

                <div class="pgct-form-wrap">
                    <div class="pgct-form-card">
                        <div class="pgct-form-head">
                            <span class="pgct-form-kicker">
                                Envie sua mensagem
                            </span>

                            <h2>
                                Conte um pouco
                                <span>do que você precisa.</span>
                            </h2>

                            <p>
                                Escolha o assunto e compartilhe o contexto
                                para que sua mensagem chegue com as
                                informações necessárias.
                            </p>
                        </div>

                        @if (session('success'))
                            <div
                                class="pgct-alert pgct-alert-success"
                                role="status"
                            >
                                <span class="pgct-alert-icon">
                                    ✓
                                </span>

                                <div>
                                    <strong>Mensagem enviada.</strong>
                                    <p>{{ session('success') }}</p>
                                </div>
                            </div>
                        @endif

                        @if (session('error'))
                            <div
                                class="pgct-alert pgct-alert-error"
                                role="alert"
                            >
                                <span class="pgct-alert-icon">
                                    !
                                </span>

                                <div>
                                    <strong>Não foi possível enviar.</strong>
                                    <p>{{ session('error') }}</p>
                                </div>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div
                                class="pgct-alert pgct-alert-error"
                                role="alert"
                            >
                                <span class="pgct-alert-icon">
                                    !
                                </span>

                                <div>
                                    <strong>Revise os campos.</strong>
                                    <p>
                                        Há informações obrigatórias que
                                        precisam ser corrigidas antes do envio.
                                    </p>
                                </div>
                            </div>
                        @endif

                        <form
                            action="{{ route('contato.enviar') }}"
                            method="POST"
                            class="pgct-form"
                        >
                            @csrf

                            <div class="pgct-field">
                                <label for="pgct-nome">
                                    Nome completo
                                    <span>*</span>
                                </label>

                                <input
                                    id="pgct-nome"
                                    type="text"
                                    name="nome"
                                    value="{{ old('nome') }}"
                                    autocomplete="name"
                                    required
                                    class="@error('nome') is-invalid @enderror"
                                    placeholder="Como podemos chamar você?"
                                >

                                @error('nome')
                                    <small class="pgct-field-error">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>

                            <div class="pgct-form-row">
                                <div class="pgct-field">
                                    <label for="pgct-email">
                                        E-mail
                                        <span>*</span>
                                    </label>

                                    <input
                                        id="pgct-email"
                                        type="email"
                                        name="email"
                                        value="{{ old('email') }}"
                                        autocomplete="email"
                                        required
                                        class="@error('email') is-invalid @enderror"
                                        placeholder="voce@email.com"
                                    >

                                    @error('email')
                                        <small class="pgct-field-error">
                                            {{ $message }}
                                        </small>
                                    @enderror
                                </div>

                                <div class="pgct-field">
                                    <label for="pgct-telefone">
                                        Telefone
                                        <small>opcional</small>
                                    </label>

                                    <input
                                        id="pgct-telefone"
                                        type="text"
                                        name="telefone"
                                        value="{{ old('telefone') }}"
                                        autocomplete="tel"
                                        maxlength="20"
                                        class="@error('telefone') is-invalid @enderror"
                                        placeholder="(00) 00000-0000"
                                    >

                                    @error('telefone')
                                        <small class="pgct-field-error">
                                            {{ $message }}
                                        </small>
                                    @enderror
                                </div>
                            </div>

                            <div class="pgct-field">
                                <label for="pgct-assunto">
                                    Assunto
                                    <span>*</span>
                                </label>

                                <div class="pgct-select-wrap">
                                    <select
                                        id="pgct-assunto"
                                        name="assunto"
                                        required
                                        class="@error('assunto') is-invalid @enderror"
                                    >
                                        <option value="">
                                            Selecione o motivo do contato
                                        </option>

                                        <option
                                            value="Agendar demonstração"
                                            @selected(old('assunto') === 'Agendar demonstração')
                                        >
                                            Gostaria de agendar uma demonstração do sistema
                                        </option>

                                        <option
                                            value="Suporte técnico"
                                            @selected(old('assunto') === 'Suporte técnico')
                                        >
                                            Preciso de suporte técnico
                                        </option>

                                        <option
                                            value="Dúvida geral"
                                            @selected(old('assunto') === 'Dúvida geral')
                                        >
                                            Tenho uma dúvida
                                        </option>

                                        <option
                                            value="Sugestão de melhoria"
                                            @selected(old('assunto') === 'Sugestão de melhoria')
                                        >
                                            Gostaria de enviar uma sugestão
                                        </option>

                                        <option
                                            value="Problemas com pagamento"
                                            @selected(old('assunto') === 'Problemas com pagamento')
                                        >
                                            Estou com problemas no pagamento
                                        </option>

                                        <option
                                            value="Ajuda com cadastro"
                                            @selected(old('assunto') === 'Ajuda com cadastro')
                                        >
                                            Preciso de ajuda para me cadastrar
                                        </option>

                                        <option
                                            value="Interesse em parceria"
                                            @selected(old('assunto') === 'Interesse em parceria')
                                        >
                                            Tenho interesse em parcerias
                                        </option>

                                        <option
                                            value="Outro assunto"
                                            @selected(old('assunto') === 'Outro assunto')
                                        >
                                            Outro assunto
                                        </option>
                                    </select>

                                    <span
                                        class="pgct-select-arrow"
                                        aria-hidden="true"
                                    >
                                        ↓
                                    </span>
                                </div>

                                @error('assunto')
                                    <small class="pgct-field-error">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>

                            <div class="pgct-field">
                                <label for="pgct-mensagem">
                                    Mensagem
                                    <span>*</span>
                                </label>

                                <textarea
                                    id="pgct-mensagem"
                                    name="mensagem"
                                    rows="5"
                                    required
                                    class="@error('mensagem') is-invalid @enderror"
                                    placeholder="Conte o contexto, sua dúvida ou o que está acontecendo..."
                                >{{ old('mensagem') }}</textarea>

                                @error('mensagem')
                                    <small class="pgct-field-error">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>

                            <div class="pgct-recaptcha">
                                @if($isLocalContactHost)
                                    <div
                                        class="pgct-captcha-local"
                                        role="note"
                                    >
                                        <span
                                            class="pgct-captcha-local-icon"
                                            aria-hidden="true"
                                        >
                                            ✓
                                        </span>

                                        <div>
                                            <strong>
                                                Proteção reCAPTCHA ativa
                                            </strong>

                                            <span>
                                                O widget real é exibido no domínio autorizado.
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    <div
                                        class="g-recaptcha"
                                        data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"
                                    ></div>
                                @endif

                                @error('g-recaptcha-response')
                                    <small class="pgct-field-error">
                                        Confirme que você não é um robô.
                                    </small>
                                @enderror
                            </div>

                            <button
                                type="submit"
                                class="pgct-submit"
                            >
                                <span>Enviar mensagem</span>

                                <svg
                                    width="18"
                                    height="18"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    aria-hidden="true"
                                >
                                    <path d="M5 12h14"></path>
                                    <path d="m14 7 5 5-5 5"></path>
                                </svg>
                            </button>

                            <p class="pgct-form-note">
                                Os campos com * são obrigatórios.
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <section class="pgct-flow-section">
            <div
                id="psigestor-contact-flow"
                data-whatsapp-url="https://wa.me/5582991128022?text=Ol%C3%A1%2C%20tenho%20interesse%20no%20PsiGestor%20e%20gostaria%20de%20saber%20mais!"
            ></div>
        </section>

        <a
            href="https://wa.me/5582991128022?text=Ol%C3%A1%2C%20tenho%20interesse%20no%20PsiGestor%20e%20gostaria%20de%20saber%20mais!"
            target="_blank"
            rel="noreferrer"
            class="pgct-whatsapp"
            aria-label="Conversar com o PsiGestor pelo WhatsApp"
        >
            <span class="pgct-whatsapp-dot"></span>
            <span>WhatsApp</span>
        </a>
    </main>
@endsection
{{-- RODAPÉ BONITO E MODERNO (com tema Ano Novo sazonal) --}}
<style>
    footer {
        margin-top: 60px;
        background: #f0f8ff;
        color: #333;
        font-size: 0.95rem;
        position: relative;
        overflow: hidden;
        transition: background .35s ease, color .35s ease, border-color .35s ease;
        border-top: 1px solid rgba(0,0,0,0.06);
    }

    .footer-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 20px;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 30px;
        position: relative;
        z-index: 2;
    }

    .footer-column {
        flex: 1 1 200px;
        min-width: 200px;
    }

    .footer-column h4 {
        font-weight: bold;
        margin-bottom: 15px;
        color: #00aaff;
        transition: color .35s ease;
    }

    .footer-column a {
        display: block;
        color: #333;
        text-decoration: none;
        margin-bottom: 8px;
        transition: color 0.2s;
    }

    .footer-column a:hover {
        color: #00aaff;
    }

    .footer-bottom {
        text-align: center;
        padding: 15px;
        background: #e6f6ff;
        font-size: 0.9rem;
        color: #666;
        position: relative;
        z-index: 2;
        transition: background .35s ease, color .35s ease;
    }

    .social-icons {
        display: flex;
        gap: 12px;
        margin-top: 10px;
    }

    .social-icons a {
        color: #00aaff;
        font-size: 1.4rem;
        transition: transform 0.2s, color .2s;
    }

    .social-icons a:hover {
        transform: scale(1.2);
    }

    /* =========================================================
       THEME ANO NOVO (sazonal)
       - aplica se tiver .hero-newyear na página
       - ou se body.theme-newyear-fallback foi setado
    ========================================================= */
    body:has(.hero-newyear) footer,
    body.theme-newyear-fallback footer {
        background:
            radial-gradient(900px 360px at 10% 0%, rgba(255,215,0,0.16), transparent 60%),
            radial-gradient(900px 360px at 90% 30%, rgba(0,255,255,0.10), transparent 55%),
            linear-gradient(180deg, rgba(6,26,58,0.98) 0%, rgba(6,26,58,0.92) 100%);
        color: rgba(255,255,255,0.88);
        border-top: 1px solid rgba(255,255,255,0.14);
    }

    body:has(.hero-newyear) .footer-column h4,
    body.theme-newyear-fallback .footer-column h4 {
        color: rgba(255,215,0,0.95);
    }

    body:has(.hero-newyear) .footer-column a,
    body.theme-newyear-fallback .footer-column a {
        color: rgba(255,255,255,0.85);
    }

    body:has(.hero-newyear) .footer-column a:hover,
    body.theme-newyear-fallback .footer-column a:hover {
        color: rgba(255,255,255,1);
        text-decoration: underline;
        text-underline-offset: 3px;
    }

    body:has(.hero-newyear) .social-icons a,
    body.theme-newyear-fallback .social-icons a {
        color: rgba(255,255,255,0.92);
    }

    body:has(.hero-newyear) .social-icons a:hover,
    body.theme-newyear-fallback .social-icons a:hover {
        color: rgba(255,215,0,0.95);
    }

    body:has(.hero-newyear) .footer-bottom,
    body.theme-newyear-fallback .footer-bottom {
        background: rgba(0,0,0,0.18);
        color: rgba(255,255,255,0.72);
        border-top: 1px solid rgba(255,255,255,0.12);
    }

    /* “Estrelinhas” discretas no fundo */
    body:has(.hero-newyear) footer::before,
    body.theme-newyear-fallback footer::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        opacity: .65;
        background:
            radial-gradient(circle at 8% 15%, rgba(255,255,255,0.10), transparent 14%),
            radial-gradient(circle at 22% 40%, rgba(255,255,255,0.08), transparent 16%),
            radial-gradient(circle at 55% 20%, rgba(255,255,255,0.10), transparent 14%),
            radial-gradient(circle at 78% 55%, rgba(255,255,255,0.08), transparent 16%),
            radial-gradient(circle at 90% 25%, rgba(255,255,255,0.10), transparent 14%);
    }

    /* Uma faixa de brilho dourado */
    body:has(.hero-newyear) footer::after,
    body.theme-newyear-fallback footer::after {
        content: "";
        position: absolute;
        left: -20%;
        top: -40px;
        width: 140%;
        height: 120px;
        transform: rotate(-2deg);
        background: linear-gradient(90deg, transparent, rgba(255,215,0,0.12), transparent);
        filter: blur(2px);
        opacity: .85;
        pointer-events: none;
    }
</style>

<footer>
    <div class="footer-container">
        <div class="footer-column">
            <h4>PsiGestor</h4>
            <p>Simplificando a rotina dos profissionais da área de saúde mental com tecnologia, sigilo e leveza.</p>
        </div>

        <div class="footer-column">
            <h4>Institucional</h4>
            <a href="{{ route('quem-somos') }}">Quem somos</a>
            <a href="{{ route('politica-de-privacidade') }}">Política de Privacidade</a>
            <a href="{{ route('termos-de-uso') }}">Termos de Uso</a>
            <a href="{{ route('cookies') }}">Política de Cookies</a>
        </div>

        <div class="footer-column">
            <h4>Fale Conosco</h4>
            <a href="mailto:psigestor@devtorquato.com.br">psigestor@devtorquato.com.br</a>
            <a href="https://wa.me/5582991128022" target="_blank">(82) 99112-8022</a>

            <div class="social-icons">
                <a href="https://instagram.com/psigestor" target="_blank" aria-label="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://linkedin.com/company/psigestor" target="_blank" aria-label="LinkedIn">
                    <i class="fab fa-linkedin"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        &copy; {{ date('Y') }} PsiGestor. Todos os direitos reservados.
    </div>
</footer>

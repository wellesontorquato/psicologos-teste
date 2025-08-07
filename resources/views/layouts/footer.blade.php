{{-- RODAPÉ BONITO E MODERNO --}}
<style>
    footer {
        margin-top: 60px;
        background: #f0f8ff;
        color: #333;
        font-size: 0.95rem;
    }

    .footer-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 20px;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 30px;
    }

    .footer-column {
        flex: 1 1 200px;
        min-width: 200px;
    }

    .footer-column h4 {
        font-weight: bold;
        margin-bottom: 15px;
        color: #00aaff;
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
    }

    .social-icons {
        display: flex;
        gap: 12px;
        margin-top: 10px;
    }

    .social-icons a {
        color: #00aaff;
        font-size: 1.4rem;
        transition: transform 0.2s;
    }

    .social-icons a:hover {
        transform: scale(1.2);
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

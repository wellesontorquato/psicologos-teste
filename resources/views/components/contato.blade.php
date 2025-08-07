<section style="background: #fff; padding: 10px 20px 0 20px; margin-top: 5px;">
    <h1 style="text-align: center; color: #000; font-size: clamp(1.8rem, 5vw, 2.2rem); margin: 0;">
        Fale Conosco
    </h1>
</section>

<section style="padding: 30px 20px;">
    <div style="max-width: 600px; margin: auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.07);">
        <p style="font-size: 1rem; color: #555; margin-bottom: 25px; text-align: center;">
            Estamos aqui para ajudar você! Preencha o formulário abaixo e nossa equipe entrará em contato o mais breve possível.
        </p>

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

            <div class="form-group">
                <input type="text" name="nome" placeholder="Nome Completo *" required class="input-field">
            </div>
            <div class="form-group">
                <input type="email" name="email" placeholder="E-mail *" required class="input-field">
            </div>
            <div class="form-group">
                <input type="text" name="telefone" placeholder="Telefone (opcional)" class="input-field">
            </div>
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
            <div class="form-group">
                <textarea name="mensagem" placeholder="Mensagem *" rows="4" required class="input-field"></textarea>
            </div>
            <div class="form-group" style="text-align: center;">
                <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
            </div>
            <div style="text-align: center; margin-top: 20px;">
                <button type="submit" class="submit-btn">Enviar Mensagem</button>
            </div>
        </form>
    </div>
</section>
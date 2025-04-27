<div class="section testimonial-section" id="depoimentos" data-aos="fade-up">
    <h2 style="text-align:center; margin-bottom: 40px;">O que dizem os profissionais...</h2>

    @php
        $depoimentos = [
            ['img' => 'psico1.jpg', 'nome' => 'Dra. Camila Ferreira', 'texto' => 'O PsiGestor trouxe leveza e praticidade para meu consultório. Estou encantada!', 'crp' => 'CRP 06/112233'],
            ['img' => 'psico1.jpg', 'nome' => 'Rafael Costa', 'texto' => 'Finalmente uma ferramenta que entende minha rotina de psicólogo. Indico de olhos fechados!', 'crp' => 'CRP 06/445566'],
            ['img' => 'psico1.jpg', 'nome' => 'Luciana Brito', 'texto' => 'Consigo organizar agenda, pagamentos e evoluções com poucos cliques. Ganhou meu coração!', 'crp' => 'CRP 06/778899'],
            ['img' => 'psico1.jpg', 'nome' => 'Bruno Andrade', 'texto' => 'Acompanhar os atendimentos ficou simples e seguro. Plataforma intuitiva!', 'crp' => 'CRP 06/555222'],
            ['img' => 'psico1.jpg', 'nome' => 'Juliana Salles', 'texto' => 'Economizo tempo e reduzi os esquecimentos dos pacientes com os lembretes via WhatsApp!', 'crp' => 'CRP 06/442211'],
            ['img' => 'psico1.jpg', 'nome' => 'Ana Monteiro', 'texto' => 'Gosto do controle financeiro, muito claro e direto. Nunca mais me perdi nas contas.', 'crp' => 'CRP 06/998877'],
            ['img' => 'psico1.jpg', 'nome' => 'Fernanda Dias', 'texto' => 'A agenda com drag & drop me ajuda MUITO no dia a dia!', 'crp' => 'CRP 06/334455'],
            ['img' => 'psico1.jpg', 'nome' => 'Carlos Lima', 'texto' => 'Com o PsiGestor, consegui parar de usar mil planilhas e centralizar tudo em um só lugar.', 'crp' => 'CRP 06/882211'],
            ['img' => 'psico1.jpg', 'nome' => 'Eduarda Martins', 'texto' => 'É como ter um secretário digital: lembretes, recibos e relatórios automáticos!', 'crp' => 'CRP 06/123987'],
            ['img' => 'psico1.jpg', 'nome' => 'Fábio Almeida', 'texto' => 'O suporte é muito bom, sempre me ajudam rápido. Recomendo!', 'crp' => 'CRP 06/554433'],
            ['img' => 'psico1.jpg', 'nome' => 'Joana Silva', 'texto' => 'Minhas anotações de evolução nunca foram tão organizadas. A timeline é sensacional.', 'crp' => 'CRP 06/998844'],
            ['img' => 'psico1.jpg', 'nome' => 'Marcos Torres', 'texto' => 'Gerei um relatório financeiro completo em segundos. Muito eficiente!', 'crp' => 'CRP 06/665544'],
            ['img' => 'psico1.jpg', 'nome' => 'Larissa Moura', 'texto' => 'O layout é leve, bonito e muito fácil de usar.', 'crp' => 'CRP 06/331122'],
            ['img' => 'psico1.jpg', 'nome' => 'Vanessa Rocha', 'texto' => 'Minhas sessões estão mais organizadas. Adoro ver as estatísticas da clínica.', 'crp' => 'CRP 06/778855'],
            ['img' => 'psico1.jpg', 'nome' => 'Gabriel Pires', 'texto' => 'PsiGestor me ajudou a crescer como profissional. Organizado e prático!', 'crp' => 'CRP 06/224466'],
        ];
    @endphp

    <div class="testimonial-carousel">
        <div class="testimonial-container" style="display: flex; width: max-content; animation: scrollDepoimentos 180s linear infinite;">
            @foreach(array_merge($depoimentos, $depoimentos) as $d)
                <div class="testimonial-card" style="min-width: 320px; max-width: 320px; margin: 0 15px; padding: 25px; background: white; border-radius: 12px; box-shadow: 0 3px 10px rgba(0,0,0,0.05); text-align: center; display: flex; flex-direction: column; justify-content: space-between;">
                    <img src="/images/{{ $d['img'] }}" alt="{{ $d['nome'] }}" class="testimonial-img" style="width: 70px; height: 70px; object-fit: cover; border-radius: 50%; margin-bottom: 12px; margin-left: auto; margin-right: auto;">
                    <p style="font-style: italic; margin-bottom: 12px; font-size: 0.95rem; line-height: 1.5;">“{{ $d['texto'] }}”</p>
                    <strong style="font-weight: 600; display: block; margin-bottom: 4px; margin-top: 6px;">{{ $d['nome'] }}</strong>
                    <span>Psicólogo(a) – {{ $d['crp'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <style>
        @keyframes scrollDepoimentos {
            0%   { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
    </style>
</div>

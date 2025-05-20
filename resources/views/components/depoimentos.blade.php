<div class="section testimonial-section" id="depoimentos" data-aos="fade-up" style="padding: 0 20px 10px 20px; margin-top: 10px;">
    <h2 style="text-align: center; margin-bottom: 20px; font-size: 1.8rem;">
        O que dizem os profissionais...
    </h2>


    @php
        $depoimentos = [
            ['img' => '', 'nome' => 'Camila Ferreira', 'texto' => 'O PsiGestor trouxe leveza e praticidade para meu consultório. Estou encantada!', 'registro' => 'CRP 06/2314', 'genero' => 'feminino', 'profissao' => 'psicologo'],
            ['img' => '', 'nome' => 'Rafael Costa', 'texto' => 'Finalmente uma ferramenta que entende minha rotina de psicólogo. Indico de olhos fechados!', 'registro' => 'CRP 06/4821', 'genero' => 'masculino', 'profissao' => 'psicologo'],
            ['img' => '', 'nome' => 'Luciana Brito', 'texto' => 'Consigo organizar agenda, pagamentos e evoluções com poucos cliques. Ganhou meu coração!', 'registro' => 'CRP 06/3158', 'genero' => 'feminino', 'profissao' => 'psicologo'],
            ['img' => '', 'nome' => 'Bruno Andrade', 'texto' => 'Acompanhar os atendimentos ficou simples e seguro. Plataforma intuitiva!', 'registro' => 'CRM 0612378', 'genero' => 'masculino', 'profissao' => 'psiquiatra'],
            ['img' => '', 'nome' => 'Juliana Salles', 'texto' => 'Economizo tempo e reduzi os esquecimentos dos pacientes com os lembretes via WhatsApp!', 'registro' => 'CRP 06/2891', 'genero' => 'feminino', 'profissao' => 'psicologo'],
            ['img' => '', 'nome' => 'Ana Monteiro', 'texto' => 'Gosto do controle financeiro, muito claro e direto. Nunca mais me perdi nas contas.', 'registro' => 'CRP 06/1472', 'genero' => 'feminino', 'profissao' => 'psicanalista'],
            ['img' => '', 'nome' => 'Fernanda Dias', 'texto' => 'A agenda com drag & drop me ajuda MUITO no dia a dia!', 'registro' => 'CRP 06/3229', 'genero' => 'feminino', 'profissao' => 'psicologo'],
            ['img' => '', 'nome' => 'Carlos Lima', 'texto' => 'Com o PsiGestor, consegui parar de usar mil planilhas e centralizar tudo em um só lugar.', 'registro' => 'CRM 0611987', 'genero' => 'masculino', 'profissao' => 'psiquiatra'],
            ['img' => '', 'nome' => 'Eduarda Martins', 'texto' => 'É como ter um secretário digital: lembretes, recibos e relatórios automáticos!', 'registro' => 'CRP 06/2635', 'genero' => 'feminino', 'profissao' => 'psicologo'],
            ['img' => '', 'nome' => 'Fábio Almeida', 'texto' => 'O suporte é muito bom, sempre me ajudam rápido. Recomendo!', 'registro' => 'CRP 06/1738', 'genero' => 'masculino', 'profissao' => 'psicanalista'],
            ['img' => '', 'nome' => 'Joana Silva', 'texto' => 'Minhas anotações de evolução nunca foram tão organizadas. A timeline é sensacional.', 'registro' => 'CRP 06/4510', 'genero' => 'feminino', 'profissao' => 'psicologo'],
            ['img' => '', 'nome' => 'Marcos Torres', 'texto' => 'Gerei um relatório financeiro completo em segundos. Muito eficiente!', 'registro' => 'CRP 06/3017', 'genero' => 'masculino', 'profissao' => 'psicologo'],
            ['img' => '', 'nome' => 'Larissa Moura', 'texto' => 'O layout é leve, bonito e muito fácil de usar.', 'registro' => 'CRP 06/2123', 'genero' => 'feminino', 'profissao' => 'psicanalista'],
            ['img' => '', 'nome' => 'Vanessa Rocha', 'texto' => 'Minhas sessões estão mais organizadas. Adoro ver as estatísticas da clínica.', 'registro' => 'CRP 06/4016', 'genero' => 'feminino', 'profissao' => 'psicologo'],
            ['img' => '', 'nome' => 'Gabriel Pires', 'texto' => 'PsiGestor me ajudou a crescer como profissional. Organizado e prático!', 'registro' => 'CRP 06/3889', 'genero' => 'masculino', 'profissao' => 'psicologo'],
        ];

        function getTituloProfissional($profissao, $genero) {
            return match($profissao) {
                'psicologo'     => $genero === 'feminino' ? 'Psicóloga' : 'Psicólogo',
                'psiquiatra'    => 'Psiquiatra',
                'psicanalista'  => 'Psicanalista',
                default         => 'Profissional'
            };
        }

        function formatarNome($d) {
            if ($d['profissao'] === 'psiquiatra') {
                return ($d['genero'] === 'feminino' ? 'Dra. ' : 'Dr. ') . $d['nome'];
            }
            return $d['nome'];
        }
        @endphp

        <div class="testimonial-carousel">
            <div class="testimonial-container" style="display: flex; width: max-content; animation: scrollDepoimentos 180s linear infinite;">
                @foreach(array_merge($depoimentos, $depoimentos) as $d)
                        <div class="testimonial-card" style="min-width: 320px; max-width: 320px; margin: 0 15px; padding: 25px; background: white; border-radius: 12px; box-shadow: 0 3px 10px rgba(0,0,0,0.05); text-align: center; display: flex; flex-direction: column; justify-content: space-between;">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($d['nome']) }}&background=random&color=ffffff&size=70&rounded=true" alt="{{ $d['nome'] }}" style="width: 70px; height: 70px; object-fit: cover; border-radius: 50%; margin-bottom: 12px; margin-left: auto; margin-right: auto;">    
                        <p style="font-style: italic; margin-bottom: 12px; font-size: 0.95rem; line-height: 1.5;">“{{ $d['texto'] }}”</p>
                        <strong style="font-weight: 600; display: block; margin-bottom: 4px; margin-top: 6px;">{{ formatarNome($d) }}</strong>
                        @if ($d['profissao'] === 'psicanalista')
                            <span style="display: block;">{{ getTituloProfissional($d['profissao'], $d['genero']) }}</span>
                        @else
                            <span>{{ getTituloProfissional($d['profissao'], $d['genero']) }} – {{ $d['registro'] }}</span>
                        @endif
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

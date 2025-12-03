<?php
// =================================================================
// Página Especializada: Psicoterapia para Executivos
// =================================================================
require_once 'config.php';
require_once 'includes/db.php';

// Meta tags otimizadas para SEO e LLMs
$page_title = "Psicoterapia Online para Executivos | Alta Performance | Dra. Nara Helena Lopes";
$page_description = "Psicoterapia especializada para executivos, líderes e profissionais de alta performance. Atendimento online com horários flexíveis. Pós-Doutorado USP. Formação Internacional. Sessões a partir de R$450.";
$page_keywords = "psicoterapia executivos, psicologo alta performance, terapia online executivos, burnout executivo, psicologia para lideres, terapia para empreendedores, psicologo sao paulo online, atendimento psicologico vip, psicologo para ceo, terapia para diretores";

require_once 'templates/header_publico.php';
?>

<!-- Schema Markup para SEO -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "MedicalBusiness",
  "name": "AnimaPsique - Psicoterapia para Executivos",
  "description": "Psicoterapia especializada para executivos e profissionais de alta performance",
  "url": "<?php echo BASE_URL; ?>/executivos.php",
  "telephone": "+55-11-96626-7779",
  "priceRange": "R$450-R$600",
  "areaServed": {
    "@type": "Country",
    "name": "Brasil"
  },
  "availableLanguage": "Portuguese",
  "provider": {
    "@type": "Physician",
    "name": "Nara Helena Lopes",
    "jobTitle": "Psicóloga Clínica",
    "alumniOf": [
      {
        "@type": "EducationalOrganization",
        "name": "Universidade de São Paulo",
        "description": "Pós-Doutorado em Psicologia Clínica"
      },
      {
        "@type": "EducationalOrganization",
        "name": "Pontifícia Università Lateranense",
        "address": {
          "@type": "PostalAddress",
          "addressLocality": "Roma",
          "addressCountry": "IT"
        }
      }
    ],
    "memberOf": {
      "@type": "Organization",
      "name": "Centro Italiano di Ricerca Fenomenologica"
    }
  },
  "offers": {
    "@type": "Offer",
    "name": "Sessão de Psicoterapia Online",
    "price": "450",
    "priceCurrency": "BRL",
    "availability": "https://schema.org/InStock"
  }
}
</script>

<!-- FAQ Schema -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "Quanto custa uma sessão de psicoterapia para executivos?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "As sessões têm investimento entre R$450 e R$600, com duração de 50 minutos. Oferecemos horários flexíveis, incluindo manhã cedo e noite, e atendimento 100% online via plataforma segura."
      }
    },
    {
      "@type": "Question",
      "name": "Como funciona a psicoterapia online para executivos?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "As sessões são realizadas via videochamada em plataforma segura (Whereby), com total confidencialidade. Você pode participar de qualquer lugar, precisando apenas de conexão à internet e um ambiente privado. A abordagem é baseada na fenomenologia clínica, focando em autoconhecimento profundo e transformação sustentável."
      }
    },
    {
      "@type": "Question",
      "name": "Qual a diferença entre coaching e psicoterapia para executivos?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Coaching foca em metas e performance específicas. Psicoterapia vai além, trabalhando as raízes emocionais, padrões de comportamento, traumas e a saúde mental como um todo. É um processo mais profundo que promove transformação duradoura, não apenas ajustes pontuais."
      }
    },
    {
      "@type": "Question",
      "name": "Psicoterapia online é tão eficaz quanto presencial?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Sim. Estudos científicos demonstram que a psicoterapia online tem eficácia equivalente à presencial. A Dra. Nara é especialista em atendimento mediado por tecnologias digitais, com pesquisa de Pós-Doutorado pela USP especificamente nesta área."
      }
    },
    {
      "@type": "Question",
      "name": "Como é garantida a confidencialidade nas sessões online?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Utilizamos plataforma de videochamada com criptografia de ponta a ponta (Whereby), em conformidade com HIPAA. Todos os dados são protegidos conforme LGPD. Orientamos sobre ambiente adequado para sessão e seguimos rigoroso código de ética profissional."
      }
    }
  ]
}
</script>

<!-- Hero Section -->
<section class="relative bg-gradient-to-r from-purple-900 to-indigo-900 text-white py-20">
    <div class="absolute inset-0 bg-black opacity-40"></div>
    <div class="container mx-auto px-6 relative z-10">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">
                Psicoterapia Especializada para Executivos e Profissionais de Alta Performance
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-gray-200">
                Atendimento Online | Horários Flexíveis | Confidencialidade Absoluta
            </p>
            <p class="text-lg mb-8">
                <strong>Dra. Nara Helena Lopes</strong> - CRP 06/73462 | CRP 08/IS-787<br>
                Pós-Doutorado USP | Formação Internacional (Roma, Itália)
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#agendar" style="background-color: var(--cor-botao-bg);" class="inline-block px-8 py-4 text-white rounded-full hover:opacity-90 transition-opacity text-lg font-semibold">
                    Agendar Primeira Sessão
                </a>
                <a href="https://wa.me/5511966267779?text=Olá! Gostaria de agendar uma sessão de psicoterapia." target="_blank" class="inline-block px-8 py-4 bg-green-600 text-white rounded-full hover:bg-green-700 transition-colors text-lg font-semibold">
                    WhatsApp: (11) 96626-7779
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Por que Executivos Buscam Psicoterapia -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-6">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-3xl font-bold text-center mb-12 text-gray-800">
                Por que Executivos e Profissionais de Alta Performance Buscam Psicoterapia?
            </h2>
            
            <div class="grid md:grid-cols-2 gap-8">
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-xl font-bold mb-3 text-purple-900">🔥 Gestão de Estresse e Burnout</h3>
                    <p class="text-gray-700">Ferramentas para lidar com pressão constante sem comprometer a saúde mental e física.</p>
                </div>
                
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-xl font-bold mb-3 text-purple-900">🎯 Tomada de Decisões Complexas</h3>
                    <p class="text-gray-700">Clareza mental e equilíbrio emocional em momentos críticos e estratégicos.</p>
                </div>
                
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-xl font-bold mb-3 text-purple-900">⚖️ Equilíbrio Vida-Trabalho</h3>
                    <p class="text-gray-700">Estratégias para integração saudável entre vida pessoal e profissional.</p>
                </div>
                
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-xl font-bold mb-3 text-purple-900">📈 Performance Sustentável</h3>
                    <p class="text-gray-700">Manter excelência profissional sem comprometer bem-estar e relacionamentos.</p>
                </div>
                
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-xl font-bold mb-3 text-purple-900">🔄 Transições de Carreira</h3>
                    <p class="text-gray-700">Suporte em mudanças profissionais significativas e redefinição de propósito.</p>
                </div>
                
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-xl font-bold mb-3 text-purple-900">👥 Solidão no Topo</h3>
                    <p class="text-gray-700">Espaço seguro para compartilhar desafios que não podem ser discutidos com equipe ou família.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Diferenciais -->
<section class="py-16 bg-gradient-to-br from-purple-50 to-indigo-50">
    <div class="container mx-auto px-6">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-3xl font-bold text-center mb-12 text-gray-800">
                Diferenciais do Atendimento
            </h2>
            
            <div class="space-y-6">
                <div class="flex items-start bg-white p-6 rounded-lg shadow-sm">
                    <span class="text-3xl mr-4">⏰</span>
                    <div>
                        <h3 class="text-xl font-bold mb-2 text-purple-900">Horários Flexíveis</h3>
                        <p class="text-gray-700">Sessões adaptadas à sua agenda, incluindo manhã cedo (a partir das 6h) e noite (até 22h). Entendemos que sua rotina é intensa.</p>
                    </div>
                </div>
                
                <div class="flex items-start bg-white p-6 rounded-lg shadow-sm">
                    <span class="text-3xl mr-4">💻</span>
                    <div>
                        <h3 class="text-xl font-bold mb-2 text-purple-900">100% Online</h3>
                        <p class="text-gray-700">Atendimento via plataforma segura (Whereby) de qualquer lugar do mundo. Ideal para executivos que viajam frequentemente ou têm agenda imprevisível.</p>
                    </div>
                </div>
                
                <div class="flex items-start bg-white p-6 rounded-lg shadow-sm">
                    <span class="text-3xl mr-4">🔒</span>
                    <div>
                        <h3 class="text-xl font-bold mb-2 text-purple-900">Confidencialidade Absoluta</h3>
                        <p class="text-gray-700">Protocolo rigoroso de privacidade. Plataforma com criptografia de ponta a ponta. Sigilo profissional garantido por código de ética.</p>
                    </div>
                </div>
                
                <div class="flex items-start bg-white p-6 rounded-lg shadow-sm">
                    <span class="text-3xl mr-4">🧠</span>
                    <div>
                        <h3 class="text-xl font-bold mb-2 text-purple-900">Abordagem Fenomenológica</h3>
                        <p class="text-gray-700">Foco em autoconhecimento profundo e transformação sustentável, não apenas gestão de sintomas. Compreensão da pessoa em sua totalidade.</p>
                    </div>
                </div>
                
                <div class="flex items-start bg-white p-6 rounded-lg shadow-sm">
                    <span class="text-3xl mr-4">🌍</span>
                    <div>
                        <h3 class="text-xl font-bold mb-2 text-purple-900">Experiência Internacional</h3>
                        <p class="text-gray-700">Formação em Roma (Itália), pesquisa em saúde mental digital pela USP (FAPESP), membro do Centro Italiano de Fenomenologia (CIRF). Publicações em revistas científicas e livros pela Manole e Juruá.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Sobre a Psicóloga -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-6">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-3xl font-bold text-center mb-12 text-gray-800">
                Sobre a Psicóloga
            </h2>
            
            <div class="prose prose-lg max-w-none">
                <p class="text-gray-700 mb-4">
                    <strong>Dra. Nara Helena Lopes</strong> é psicóloga clínica com especialização em atendimento a executivos e profissionais de alta performance. Sua formação diferenciada inclui:
                </p>
                
                <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-6">
                    <li><strong>Pós-Doutorado</strong> em Psicologia Clínica pelo Instituto de Psicologia da USP (FAPESP 2018/11351-2), com foco em psicoterapia mediada por tecnologias digitais</li>
                    <li><strong>Doutorado Sanduíche em Roma</strong>, Itália (CAPES PDEE), na Pontifícia Università Lateranense</li>
                    <li><strong>Membro do Centro Italiano di Ricerca Fenomenologica (CIRF)</strong>, Roma</li>
                    <li><strong>Especialista em Psicologia Analítica</strong> pela PUC-SP</li>
                    <li><strong>Doutorado e Mestrado em Psicologia</strong> pela FFCLRP-USP (FAPESP)</li>
                    <li><strong>Aprimoramento em Psicologia Hospitalar</strong> aplicada à Cardiologia pelo InCor-FMUSP</li>
                </ul>
                
                <p class="text-gray-700 mb-4">
                    Autora de capítulos em livros especializados, incluindo <em>"Psicoterapia on-line: manual para a prática clínica"</em> (Juruá, 2024) e <em>"Consultas Terapêuticas on-line na saúde mental"</em> (Manole, 2021).
                </p>
                
                <p class="text-gray-700">
                    Sua experiência internacional e pesquisa científica em saúde mental digital garantem um atendimento de excelência, aliando rigor técnico à sensibilidade clínica necessária para acompanhar profissionais de alta performance.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Investimento -->
<section class="py-16 bg-gradient-to-br from-purple-50 to-indigo-50">
    <div class="container mx-auto px-6">
        <div class="max-w-3xl mx-auto text-center">
            <h2 class="text-3xl font-bold mb-8 text-gray-800">Investimento</h2>
            
            <div class="bg-white p-8 rounded-lg shadow-lg">
                <div class="mb-6">
                    <p class="text-5xl font-bold text-purple-900 mb-2">R$ 450 - R$ 600</p>
                    <p class="text-gray-600">por sessão individual</p>
                </div>
                
                <div class="border-t border-gray-200 pt-6">
                    <ul class="text-left space-y-3 text-gray-700">
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Duração: 50 minutos
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Frequência: Semanal ou quinzenal
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Plataforma segura inclusa
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Horários flexíveis
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Atendimento de qualquer lugar do Brasil ou exterior
                        </li>
                    </ul>
                </div>
                
                <div class="mt-8 p-4 bg-purple-50 rounded-lg">
                    <p class="text-sm text-gray-600">
                        💡 <strong>Pacotes disponíveis:</strong> Consulte condições especiais para pacotes mensais
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-6">
        <div class="max-w-3xl mx-auto">
            <h2 class="text-3xl font-bold text-center mb-12 text-gray-800">
                Perguntas Frequentes
            </h2>
            
            <div class="space-y-6">
                <details class="bg-gray-50 p-6 rounded-lg">
                    <summary class="font-bold text-lg cursor-pointer text-purple-900">
                        Quanto custa uma sessão de psicoterapia para executivos?
                    </summary>
                    <p class="mt-4 text-gray-700">
                        As sessões têm investimento entre R$450 e R$600, com duração de 50 minutos. Oferecemos horários flexíveis, incluindo manhã cedo e noite, e atendimento 100% online via plataforma segura.
                    </p>
                </details>
                
                <details class="bg-gray-50 p-6 rounded-lg">
                    <summary class="font-bold text-lg cursor-pointer text-purple-900">
                        Como funciona a psicoterapia online para executivos?
                    </summary>
                    <p class="mt-4 text-gray-700">
                        As sessões são realizadas via videochamada em plataforma segura (Whereby), com total confidencialidade. Você pode participar de qualquer lugar, precisando apenas de conexão à internet e um ambiente privado. A abordagem é baseada na fenomenologia clínica, focando em autoconhecimento profundo e transformação sustentável.
                    </p>
                </details>
                
                <details class="bg-gray-50 p-6 rounded-lg">
                    <summary class="font-bold text-lg cursor-pointer text-purple-900">
                        Qual a diferença entre coaching e psicoterapia para executivos?
                    </summary>
                    <p class="mt-4 text-gray-700">
                        Coaching foca em metas e performance específicas. Psicoterapia vai além, trabalhando as raízes emocionais, padrões de comportamento, traumas e a saúde mental como um todo. É um processo mais profundo que promove transformação duradoura, não apenas ajustes pontuais.
                    </p>
                </details>
                
                <details class="bg-gray-50 p-6 rounded-lg">
                    <summary class="font-bold text-lg cursor-pointer text-purple-900">
                        Psicoterapia online é tão eficaz quanto presencial?
                    </summary>
                    <p class="mt-4 text-gray-700">
                        Sim. Estudos científicos demonstram que a psicoterapia online tem eficácia equivalente à presencial. A Dra. Nara é especialista em atendimento mediado por tecnologias digitais, com pesquisa de Pós-Doutorado pela USP especificamente nesta área.
                    </p>
                </details>
                
                <details class="bg-gray-50 p-6 rounded-lg">
                    <summary class="font-bold text-lg cursor-pointer text-purple-900">
                        Como é garantida a confidencialidade nas sessões online?
                    </summary>
                    <p class="mt-4 text-gray-700">
                        Utilizamos plataforma de videochamada com criptografia de ponta a ponta (Whereby), em conformidade com HIPAA. Todos os dados são protegidos conforme LGPD. Orientamos sobre ambiente adequado para sessão e seguimos rigoroso código de ética profissional.
                    </p>
                </details>
                
                <details class="bg-gray-50 p-6 rounded-lg">
                    <summary class="font-bold text-lg cursor-pointer text-purple-900">
                        Qual a frequência ideal das sessões?
                    </summary>
                    <p class="mt-4 text-gray-700">
                        A frequência é definida em conjunto, considerando suas necessidades e disponibilidade. O mais comum é semanal ou quinzenal. Para executivos com agenda muito intensa, podemos adaptar a frequência, mantendo a continuidade do processo terapêutico.
                    </p>
                </details>
                
                <details class="bg-gray-50 p-6 rounded-lg">
                    <summary class="font-bold text-lg cursor-pointer text-purple-900">
                        Quanto tempo dura um processo de psicoterapia?
                    </summary>
                    <p class="mt-4 text-gray-700">
                        Não há um tempo pré-determinado. Cada pessoa tem seu ritmo e objetivos. Alguns buscam suporte pontual para situações específicas (3-6 meses), outros optam por um processo mais longo de autoconhecimento. Avaliamos juntos a evolução e necessidades.
                    </p>
                </details>
            </div>
        </div>
    </div>
</section>

<!-- Formulário de Contato -->
<section id="contato" class="py-16 bg-gray-50">
    <div class="container mx-auto px-6">
        <div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-lg">
            <h2 class="text-3xl font-bold text-center mb-8 text-gray-800">
                Solicite um Contato
            </h2>
            <p class="text-center text-gray-600 mb-8">
                Preencha o formulário abaixo e entrarei em contato para agendar sua primeira sessão.
            </p>
            
            <form action="processa_contato.php" method="POST" class="space-y-6">
                <input type="hidden" name="redirect" value="confirmacao.php">
                
                <div>
                    <label for="nome" class="block text-sm font-medium text-gray-700 mb-1">Nome Completo</label>
                    <input type="text" name="nome" id="nome" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500">
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mail Corporativo ou Pessoal</label>
                    <input type="email" name="email" id="email" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500">
                </div>
                
                <div>
                    <label for="telefone" class="block text-sm font-medium text-gray-700 mb-1">WhatsApp</label>
                    <input type="tel" name="telefone" id="telefone" required placeholder="(11) 99999-9999"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500">
                </div>
                
                <div>
                    <label for="motivo" class="block text-sm font-medium text-gray-700 mb-1">Como posso ajudar? (Opcional)</label>
                    <textarea name="motivo" id="motivo" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500"></textarea>
                </div>
                
                <button type="submit" style="background-color: var(--cor-botao-bg);" 
                        class="w-full text-white font-bold py-3 px-4 rounded-md hover:opacity-90 transition-opacity">
                    Solicitar Agendamento
                </button>
                
                <p class="text-xs text-center text-gray-500 mt-4">
                    Seus dados são protegidos e mantidos em absoluto sigilo.
                </p>
            </form>
        </div>
    </div>
</section>

<!-- CTA Final -->
<section id="agendar" class="py-16 bg-gradient-to-r from-purple-900 to-indigo-900 text-white">
    <div class="container mx-auto px-6">
        <div class="max-w-3xl mx-auto text-center">
            <h2 class="text-3xl font-bold mb-6">
                Pronto para Começar?
            </h2>
            <p class="text-xl mb-8">
                Agende sua primeira sessão e dê o primeiro passo para uma vida mais equilibrada e sustentável.
            </p>
            
            <div class="bg-white p-8 rounded-lg text-gray-800">
                <h3 class="text-2xl font-bold mb-6">Entre em Contato</h3>
                
                <div class="space-y-4 mb-6">
                    <a href="https://wa.me/5511966267779?text=Olá! Gostaria de agendar uma sessão de psicoterapia para executivos." target="_blank" class="block w-full bg-green-600 text-white py-4 px-6 rounded-lg hover:bg-green-700 transition-colors font-semibold text-lg">
                        📱 WhatsApp: (11) 96626-7779
                    </a>
                    
                    <a href="mailto:nara.helena@gmail.com" class="block w-full bg-purple-600 text-white py-4 px-6 rounded-lg hover:bg-purple-700 transition-colors font-semibold text-lg">
                        ✉️ nara.helena@gmail.com
                    </a>
                </div>
                
                <p class="text-sm text-gray-600">
                    Respondo em até 24 horas. Primeira consulta disponível em até 7 dias.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Botão flutuante WhatsApp -->
<a href="https://wa.me/5511966267779?text=Olá! Gostaria de agendar uma sessão de psicoterapia." 
   class="fixed bottom-6 right-6 bg-green-500 text-white p-4 rounded-full shadow-lg hover:bg-green-600 transition-colors z-50"
   target="_blank"
   aria-label="Contato via WhatsApp">
    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
    </svg>
</a>

<?php require_once 'templates/footer_publico.php'; ?>

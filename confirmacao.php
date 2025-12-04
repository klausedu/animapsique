<?php
require_once 'templates/header_publico.php';
?>

<!-- Google Ads Conversion Tracking -->
<script>
  // Ensure gtag is defined (it should be from header_publico.php)
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  
  // Evento de Conversão do Google Ads (Placeholder)
  // Substitua 'AW-CONVERSION_ID/CONVERSION_LABEL' pelos valores reais da sua conta
  gtag('event', 'conversion', {
      'send_to': 'AW-CONVERSION_ID/CONVERSION_LABEL'
  });
</script>


<main class="pt-24 pb-16">
    <div class="container mx-auto px-4 text-center">
        <!-- Ícone de Sucesso -->
        <div class="mb-8 flex justify-center">
            <div class="bg-green-100 p-6 rounded-full">
                <svg class="w-16 h-16 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
        </div>

        <h1 class="text-4xl font-bold text-gray-800 mb-4">Agendamento Recebido!</h1>
        
        <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
            Obrigada pelo seu interesse. Recebi sua solicitação e entrarei em contato 
            em breve (geralmente em até 2 horas) para confirmar os detalhes da sua sessão.
        </p>

        <div class="bg-white p-8 rounded-xl shadow-lg max-w-md mx-auto mb-12">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Próximos Passos:</h3>
            <ul class="text-left space-y-3 text-gray-600">
                <li class="flex items-start">
                    <svg class="w-5 h-5 text-teal-500 mr-2 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Verifique seu WhatsApp/E-mail para minha mensagem.
                </li>
                <li class="flex items-start">
                    <svg class="w-5 h-5 text-teal-500 mr-2 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Se for urgente, você pode me chamar diretamente no botão abaixo.
                </li>
            </ul>
        </div>

        <a href="https://wa.me/5511966267779" 
           class="inline-flex items-center justify-center px-8 py-4 text-lg font-bold text-white transition-all duration-200 bg-green-600 rounded-full hover:bg-green-700 hover:shadow-lg transform hover:-translate-y-1">
            <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
            Falar no WhatsApp Agora
        </a>
    </div>
</main>

<?php require_once 'templates/footer_publico.php'; ?>

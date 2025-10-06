<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Formulário de Debug Final</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-xl mx-auto bg-white p-8 rounded-lg shadow-lg">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Teste Final: Nomes de Campos Reais</h1>
        <p class="text-gray-600 mb-6">Este formulário enviará um grande número de campos com os nomes exatos utilizados em `configuracoes_site.php`. Se este pedido falhar (resultar em "Forbidden"), confirma que o problema está nos nomes dos campos. Se funcionar, o problema é ainda mais complexo.</p>
        
        <form action="debug_receiver.php" method="POST">
            
            <?php
            // Lista de nomes de campos extraídos do seu formulário original
            $field_names = [
                'active_tab', 'conteudo_site_cor_primaria_texto', 'conteudo_site_cor_botao_bg_texto',
                'conteudo_site_cor_header_bg_texto', 'conteudo_site_cor_footer_bg_texto', 'conteudo_banner_inicio_titulo',
                'conteudo_banner_inicio_texto', 'imagem_atual_banner_inicio', 'conteudo_missao_titulo',
                'conteudo_missao_texto', 'conteudo_missao_p2_texto', 'conteudo_filosofia_titulo', 'conteudo_filosofia_texto',
                'conteudo_filosofia_tec_titulo', 'conteudo_filosofia_tec_texto', 'conteudo_filosofia_pra_titulo',
                'conteudo_filosofia_pra_texto', 'conteudo_filosofia_ime_titulo', 'conteudo_filosofia_ime_texto',
                'conteudo_slide1_titulo', 'conteudo_slide1_texto', 'imagem_atual_slide1', 'conteudo_slide2_titulo',
                'conteudo_slide2_texto', 'imagem_atual_slide2', 'conteudo_slide3_titulo', 'conteudo_slide3_texto',
                'imagem_atual_slide3', 'conteudo_atuacao_titulo_titulo', 'conteudo_atuacao_titulo_texto',
                'conteudo_atuacao_card1_exibir_titulo', 'conteudo_atuacao_card1_titulo_titulo', 'imagem_atual_atuacao_card1_titulo',
                'conteudo_atuacao_card1_titulo_texto', 'conteudo_atuacao_p1_titulo_titulo', 'conteudo_atuacao_p1_titulo_texto',
                'conteudo_atuacao_p1_p2_texto', 'conteudo_atuacao_p1_desfecho_texto', 'conteudo_atuacao_card2_exibir_titulo',
                'conteudo_atuacao_card2_titulo_titulo', 'imagem_atual_atuacao_card2_titulo', 'conteudo_atuacao_card2_titulo_texto',
                'conteudo_atuacao_p2_titulo_titulo', 'conteudo_atuacao_p2_titulo_texto', 'conteudo_atuacao_p2_p2_texto',
                'conteudo_atuacao_p2_desfecho_texto', 'conteudo_atuacao_card3_exibir_titulo', 'conteudo_atuacao_card3_titulo_titulo',
                'imagem_atual_atuacao_card3_titulo', 'conteudo_atuacao_card3_titulo_texto', 'conteudo_atuacao_p3_titulo_titulo',
                'conteudo_atuacao_p3_titulo_texto', 'conteudo_atuacao_p3_p2_texto', 'conteudo_atuacao_p3_desfecho_texto',
                'conteudo_atuacao_card4_exibir_titulo', 'conteudo_atuacao_card4_titulo_titulo', 'imagem_atual_atuacao_card4_titulo',
                'conteudo_atuacao_card4_titulo_texto', 'conteudo_atuacao_p4_titulo_titulo', 'conteudo_atuacao_p4_titulo_texto',
                'conteudo_atuacao_p4_p2_texto', 'conteudo_atuacao_p4_desfecho_texto', 'conteudo_atuacao_card5_exibir_titulo',
                'conteudo_atuacao_card5_titulo_titulo', 'imagem_atual_atuacao_card5_titulo', 'conteudo_atuacao_card5_titulo_texto',
                'conteudo_atuacao_p5_titulo_titulo', 'conteudo_atuacao_p5_titulo_texto', 'conteudo_atuacao_p5_p2_texto',
                'conteudo_atuacao_p5_desfecho_texto', 'conteudo_sobre_objetivo_titulo_titulo', 'conteudo_sobre_objetivo_titulo_texto',
                'imagem_atual_sobre_reflexao_imagem', 'imagem_atual_sobre_psicologa_foto', 'conteudo_sobre_mim_texto_texto',
                'conteudo_sobre_quem_sou_titulo_titulo', 'conteudo_sobre_quem_sou_titulo_texto', 'conteudo_sobre_especializacoes_titulo_titulo',
                'conteudo_sobre_especializacoes_titulo_texto', 'imagem_atual_sobre_mod1_imagem', 'conteudo_sobre_mod1_imagem_titulo',
                'conteudo_sobre_mod1_imagem_texto', 'imagem_atual_sobre_mod2_imagem', 'conteudo_sobre_mod2_imagem_titulo',
                'conteudo_sobre_mod2_imagem_texto', 'imagem_atual_sobre_mod3_imagem', 'conteudo_sobre_mod3_imagem_titulo',
                'conteudo_sobre_mod3_imagem_texto', 'conteudo_contato_titulo_titulo', 'conteudo_contato_titulo_texto',
                'conteudo_contato_endereco_sp_texto', 'conteudo_contato_endereco_cwb_texto', 'conteudo_contato_whatsapp_texto'
            ];

            foreach ($field_names as $name) {
                echo "<input type='hidden' name='" . htmlspecialchars($name) . "' value='test_value'>\n";
            }
            ?>

            <button type="submit" class="w-full bg-red-600 text-white font-bold py-3 px-4 rounded-md hover:bg-red-700">
                Executar Teste Final
            </button>
        </form>
    </div>
</body>
</html>

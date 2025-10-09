<?php
require_once '../../config.php';
require_once '../../includes/auth_paciente.php';
require_once '../../includes/db.php';

$page_title = 'Sala de Atendimento';

// --- Lógica do Servidor para obter a URL da sala ---
$roomUrl = '';
$paciente_nome = $_SESSION['user_nome'] ?? 'Convidado';

try {
    $pdo = conectar();
    $stmt = $pdo->prepare("SELECT whereby_room_url FROM pacientes WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $paciente = $stmt->fetch();
    if ($paciente && !empty($paciente['whereby_room_url'])) {
        $roomUrl = $paciente['whereby_room_url'];
    }
} catch (PDOException $e) {
    // Lidar com o erro, se necessário
}
// --- Fim da Lógica do Servidor ---
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    
    <script>
        // Este script é executado imediatamente para garantir que o domínio está correto ANTES de carregar o resto da página.
        const correctHost = '<?php echo parse_url(BASE_URL, PHP_URL_HOST); ?>';
        const currentHost = window.location.hostname;

        if (currentHost !== correctHost) {
            const newUrl = `https://${correctHost}${window.location.pathname}${window.location.search}`;
            // replace() impede que o utilizador possa voltar para a página com o URL errado.
            window.location.replace(newUrl);
        }
    </script>
    <?php
        // Inclui o resto do cabeçalho (CSS, etc.) DEPOIS do script de verificação.
        // Isto assume que o seu ficheiro header.php não tem a tag <html> ou <head>, mas sim o conteúdo que vai dentro delas.
        // Se o header.php tiver a estrutura completa, esta abordagem precisa ser adaptada.
        require_once 'templates/header.php';
    ?>
</head>
<body>

<main class="flex-grow">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white p-6 rounded-lg shadow-md text-center">
            <h1 class="text-3xl font-bold text-gray-800 mb-4">Sala de Atendimento Virtual</h1>

            <?php if ($roomUrl): ?>
                <p class="text-gray-600 mb-6">A sua sessão está pronta. A sala de vídeo irá carregar abaixo.</p>
                
                <div class="aspect-w-16 aspect-h-9 border rounded-lg overflow-hidden">
                    <iframe
                        src="<?php echo htmlspecialchars($roomUrl . '?displayName=' . urlencode($paciente_nome)); ?>"
                        allow="camera; microphone; fullscreen; speaker; display-capture"
                        class="w-full h-full"
                        style="min-height: 500px;"
                    ></iframe>
                </div>
            <?php else: ?>
                <p class="text-red-500 font-semibold">Não foi possível encontrar a sua sala de atendimento. Por favor, entre em contacto com a sua psicóloga.</p>
            <?php endif; ?>

        </div>
    </div>
</main>

<?php require_once 'templates/footer.php'; ?>
</body>
</html>

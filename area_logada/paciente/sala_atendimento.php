<?php
require_once '../../config.php';
require_once '../../includes/auth_paciente.php';
require_once '../../includes/db.php';

$page_title = 'Sala de Atendimento';
require_once 'templates/header.php';

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
?>

<script>
    // Obtém o domínio correto a partir da configuração do PHP
    const correctHost = '<?php echo parse_url(BASE_URL, PHP_URL_HOST); ?>';
    
    // Se o anfitrião atual no navegador for diferente do correto, redireciona.
    if (window.location.hostname !== correctHost) {
        // Monta a nova URL com o anfitrião correto e o resto do caminho
        const newUrl = `https://${correctHost}${window.location.pathname}${window.location.search}`;
        // Redireciona o navegador do utilizador
        window.location.replace(newUrl);
    }
</script>
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

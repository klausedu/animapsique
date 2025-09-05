<?php
require_once '../../config.php';
require_once '../../includes/auth_paciente.php'; // Segurança e dados do paciente
require_once '../../includes/db.php';

$roomUrl = null;

try {
    $pdo = conectar();
    $stmt = $pdo->prepare("SELECT whereby_room_url FROM pacientes WHERE id = ?");
    $stmt->execute([$paciente_id]); // $paciente_id vem de auth_paciente.php
    $paciente_db = $stmt->fetch();
    if ($paciente_db) {
        $roomUrl = $paciente_db['whereby_room_url'];
    }
} catch(Exception $e) {
    die('Erro ao buscar a sala de atendimento.');
}

// Validação para garantir que a URL existe
if (!$roomUrl || !preg_match('/^https:\/\/.*\.whereby\.com\/.*/', $roomUrl)) {
    die('URL da sala inválida ou não encontrada. Por favor, contacte o seu psicólogo.');
}

// Adiciona os parâmetros de forma segura
$finalRoomUrl = htmlspecialchars($roomUrl . '?embed&screenshare=on&chat=on&displayName=' . urlencode($paciente_nome));

$page_title = 'Sala de Atendimento';
require_once 'templates/header.php';
?>

<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold text-gray-800">Sessão Online</h1>
            <button id="fullscreen-btn" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5" />
                </svg>
                Tela Cheia
            </button>
            </div>
        <p class="text-gray-600 mb-6">Sua sessão está prestes a começar. Permita o acesso à sua câmera e microfone quando solicitado pelo navegador.</p>

        <div class="w-full" style="height: 75vh;">
             <iframe id="whereby-iframe" class="w-full h-full border-0 rounded-lg" src="<?php echo $finalRoomUrl; ?>" allow="camera; microphone; fullscreen; speaker; display-capture" allowfullscreen></iframe>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fullscreenButton = document.getElementById('fullscreen-btn');
    const wherebyIframe = document.getElementById('whereby-iframe');

    if (fullscreenButton && wherebyIframe) {
        fullscreenButton.addEventListener('click', function() {
            if (wherebyIframe.requestFullscreen) {
                wherebyIframe.requestFullscreen();
            } else if (wherebyIframe.mozRequestFullScreen) { /* Firefox */
                wherebyIframe.mozRequestFullScreen();
            } else if (wherebyIframe.webkitRequestFullscreen) { /* Chrome, Safari & Opera */
                wherebyIframe.webkitRequestFullscreen();
            } else if (wherebyIframe.msRequestFullscreen) { /* IE/Edge */
                wherebyIframe.msRequestFullscreen();
            }
        });
    }
});
</script>
<?php require_once 'templates/footer.php'; ?>

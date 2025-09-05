<?php
require_once '../../config.php';
require_once '../../includes/auth_paciente.php'; // Segurança

$roomUrl = filter_input(INPUT_GET, 'room', FILTER_VALIDATE_URL);

// Validação para garantir que a URL é do Whereby
if (!$roomUrl || !preg_match('/^https:\/\/.*\.whereby\.com\/.*/', $roomUrl)) {
    die('URL da sala inválida.');
}

$page_title = 'Sala de Atendimento';
require_once 'templates/header.php';
?>

<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Sessão Online</h1>
        <p class="text-gray-600 mb-6">Sua sessão está prestes a começar. Permita o acesso à sua câmera e microfone quando solicitado pelo navegador.</p>

        <div class="w-full" style="height: 75vh;">
             <iframe class="w-full h-full border-0 rounded-lg" src="<?php echo htmlspecialchars($roomUrl); ?>?embed&screenshare=on&chat=on" allow="camera; microphone; fullscreen; speaker; display-capture" allowfullscreen></iframe>
        </div>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>

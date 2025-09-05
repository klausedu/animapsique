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

// **INÍCIO DA CORREÇÃO**
// Adiciona o nome do paciente (disponível em $paciente_nome de auth_paciente.php) ao URL
// O urlencode() garante que nomes com espaços ou acentos funcionem corretamente.
$finalRoomUrl = $roomUrl . '?embed&screenshare=on&chat=on&displayName=' . urlencode($paciente_nome);
// **FIM DA CORREÇÃO**

$page_title = 'Sala de Atendimento';
require_once 'templates/header.php';
?>

<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Sessão Online</h1>
        <p class="text-gray-600 mb-6">Sua sessão está prestes a começar. Permita o acesso à sua câmera e microfone quando solicitado pelo navegador.</p>

        <div class="w-full" style="height: 75vh;">
             <iframe class="w-full h-full border-0 rounded-lg" src="<?php echo htmlspecialchars($finalRoomUrl); ?>" allow="camera; microphone; fullscreen; speaker; display-capture" allowfullscreen></iframe>
        </div>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>

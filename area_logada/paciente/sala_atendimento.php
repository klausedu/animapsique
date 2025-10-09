<?php
// Ficheiro: area_logada/paciente/sala_atendimento.php
require_once '../../config.php'; // Carrega as configurações, incluindo BASE_URL

// **INÍCIO DA CORREÇÃO**
// Este bloco de código garante que a página está a ser acedida através do domínio correto.

// Obtém o domínio correto a partir da sua configuração (ex: 'animapsique.com.br')
$correct_host = parse_url(BASE_URL, PHP_URL_HOST); 

// Obtém o domínio que o utilizador está a usar atualmente para aceder à página
$current_host = $_SERVER['HTTP_HOST'];

// Se o domínio atual for diferente do correto, redireciona para a URL correta.
if ($current_host !== $correct_host) {
    // Monta a URL completa e correta
    $redirect_url = BASE_URL . $_SERVER['REQUEST_URI'];
    // Envia o cabeçalho de redirecionamento permanente
    header('Location: ' . $redirect_url, true, 301);
    exit; // Termina a execução para que o redirecionamento aconteça
}
// **FIM DA CORREÇÃO**


// O resto do seu código original continua aqui
require_once '../../includes/auth_paciente.php'; //
require_once '../../includes/db.php'; //

$page_title = 'Sala de Atendimento'; //
require_once 'templates/header.php'; //

$roomUrl = ''; //
$paciente_nome = $_SESSION['user_nome'] ?? 'Convidado'; //

try {
    $pdo = conectar(); //
    $stmt = $pdo->prepare("SELECT whereby_room_url FROM pacientes WHERE id = ?"); //
    $stmt->execute([$_SESSION['user_id']]); //
    $paciente = $stmt->fetch(); //
    if ($paciente && !empty($paciente['whereby_room_url'])) { //
        $roomUrl = $paciente['whereby_room_url']; //
    }
} catch (PDOException $e) {
    // Lidar com o erro, se necessário
}
?>

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

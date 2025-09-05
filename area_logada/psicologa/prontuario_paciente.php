<?php
require_once '../../config.php';
require_once '../../includes/auth_psicologa.php';
require_once '../../includes/db.php';

// Pega o ID do paciente da URL e valida
$paciente_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$paciente_id) {
    // Se o ID for inválido, redireciona para a lista de pacientes
    header('Location: pacientes.php');
    exit;
}

try {
    $pdo = conectar();

    // Busca os dados do paciente
    $stmt_paciente = $pdo->prepare("SELECT nome, email, telefone FROM pacientes WHERE id = ?");
    $stmt_paciente->execute([$paciente_id]);
    $paciente = $stmt_paciente->fetch();

    // Se o paciente não for encontrado, redireciona
    if (!$paciente) {
        header('Location: pacientes.php');
        exit;
    }

    // Busca todos os prontuários do paciente, do mais recente para o mais antigo
    $stmt_prontuarios = $pdo->prepare("SELECT id, data_sessao, anotacoes FROM prontuarios WHERE paciente_id = ? ORDER BY data_sessao DESC");
    $stmt_prontuarios->execute([$paciente_id]);
    $prontuarios = $stmt_prontuarios->fetchAll();

} catch (PDOException $e) {
    error_log("Erro ao buscar dados do prontuário: " . $e->getMessage());
    // Em caso de erro, redireciona com uma mensagem (opcional)
    $_SESSION['error_message'] = "Não foi possível carregar os dados do paciente.";
    header('Location: pacientes.php');
    exit;
}

$page_title = 'Prontuário de ' . htmlspecialchars($paciente['nome']);
require_once 'templates/header.php';
?>

<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Cartão de Informações do Paciente -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($paciente['nome']); ?></h2>
                <p class="text-sm text-gray-600 mt-1"><?php echo htmlspecialchars($paciente['email']); ?></p>
                <p class="text-sm text-gray-600"><?php echo htmlspecialchars($paciente['telefone'] ?? 'Telefone não cadastrado'); ?></p>
            </div>
            <a href="pacientes.php" class="text-sm text-teal-600 hover:text-teal-800">&larr; Voltar para lista de pacientes</a>
        </div>
    </div>

    <!-- Seção para Adicionar Novo Registro -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Adicionar Anotação de Sessão</h3>
        
        <!-- Exibe mensagens de sucesso ou erro -->
        <?php if (isset($_SESSION['prontuario_success'])): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <p><?php echo $_SESSION['prontuario_success']; unset($_SESSION['prontuario_success']); ?></p>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['prontuario_error'])): ?>
             <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p><?php echo $_SESSION['prontuario_error']; unset($_SESSION['prontuario_error']); ?></p>
            </div>
        <?php endif; ?>

        <form action="processa_prontuario.php" method="POST">
            <input type="hidden" name="paciente_id" value="<?php echo $paciente_id; ?>">
            <div>
                <label for="anotacoes" class="sr-only">Anotações da sessão</label>
                <textarea id="anotacoes" name="anotacoes" rows="6" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm" placeholder="Digite aqui as anotações da sessão..."></textarea>
            </div>
            <div class="mt-4 text-right">
                <button type="submit" class="inline-flex items-center rounded-md border border-transparent bg-teal-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                    Salvar Registro
                </button>
            </div>
        </form>
    </div>

    <!-- Histórico de Prontuários -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Histórico de Sessões</h3>
        <div class="space-y-6">
            <?php if (empty($prontuarios)): ?>
                <p class="text-gray-500">Nenhum registro de prontuário encontrado para este paciente.</p>
            <?php else: ?>
                <?php foreach ($prontuarios as $prontuario): ?>
                    <div class="border-l-4 border-teal-500 pl-4">
                        <p class="text-sm font-semibold text-gray-800">
                            <?php 
                                // Formata a data para o padrão brasileiro
                                $data = new DateTime($prontuario['data_sessao']);
                                echo $data->format('d/m/Y \à\s H:i');
                            ?>
                        </p>
                        <div class="mt-2 text-gray-700 prose max-w-none">
                            <?php 
                                // nl2br converte quebras de linha em <br> para exibição correta no HTML
                                echo nl2br(htmlspecialchars($prontuario['anotacoes'])); 
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>

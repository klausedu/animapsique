<?php
require_once '../../config.php';
require_once '../../includes/auth_psicologa.php';
require_once '../../includes/db.php';

$page_title = 'Salas de Atendimento';
require_once 'templates/header.php';

try {
    $pdo = conectar();
    // A variável $psicologa_nome é definida em auth_psicologa.php a partir de $_SESSION['user_nome']
    $stmt = $pdo->query("SELECT id, nome, whereby_room_url FROM pacientes WHERE ativo = 1 ORDER BY nome ASC");
    $pacientes = $stmt->fetchAll();
} catch (PDOException $e) {
    $pacientes = [];
    $error_message = "Erro ao buscar pacientes: " . $e->getMessage();
}
?>

<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Painel de Controlo de Salas Virtuais</h2>

        <?php if (isset($error_message)): ?>
            <p class="text-red-500"><?php echo htmlspecialchars($error_message); ?></p>
        <?php elseif (empty($pacientes)): ?>
            <p class="text-gray-600">Nenhum paciente ativo encontrado.</p>
        <?php else: ?>
            <ul role="list" class="divide-y divide-gray-200">
                <?php foreach ($pacientes as $paciente): ?>
                    <li class="flex justify-between items-center gap-x-6 py-5" id="paciente-<?php echo $paciente['id']; ?>">
                        <div class="flex min-w-0 gap-x-4">
                            <div class="min-w-0 flex-auto">
                                <p class="text-lg font-semibold leading-6 text-gray-900"><?php echo htmlspecialchars($paciente['nome']); ?></p>
                            </div>
                        </div>
                        <div class="shrink-0 flex flex-wrap items-center gap-x-4 gap-y-2">
                            <?php if (!empty($paciente['whereby_room_url'])): ?>
                                <?php
                                    // Garante que $psicologa_nome existe e está definido
                                    $nome_psicologa_encoded = isset($psicologa_nome) ? urlencode($psicologa_nome) : '';
                                    $sala_url_psicologa = htmlspecialchars($paciente['whereby_room_url'] . '?displayName=' . $nome_psicologa_encoded);
                                ?>
                                <a href="<?php echo $sala_url_psicologa; ?>" target="_blank" class="entrar-sala-link inline-flex items-center rounded-md bg-teal-600 px-3 py-1.5 text-sm font-medium text-white shadow-sm hover:bg-teal-700">
                                    Entrar na Sala
                                </a>
                                <button type="button" class="copiar-link-btn inline-flex items-center rounded-md bg-gray-200 px-3 py-1.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-300" data-url="<?php echo htmlspecialchars($paciente['whereby_room_url']); ?>">
                                    Copiar Link
                                </button>
                                <button type="button" class="remover-sala-btn text-sm font-medium text-red-600 hover:text-red-800" data-paciente-id="<?php echo $paciente['id']; ?>">
                                    Remover
                                </button>
                            <?php else: ?>
                                <button type="button" class="habilitar-sala-btn inline-flex items-center rounded-md bg-blue-600 px-3 py-1.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700" data-paciente-id="<?php echo $paciente['id']; ?>">
                                    Habilitar Sala Whereby
                                </button>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // **INÍCIO DO CÓDIGO CORRIGIDO**

    // Função para enviar os pedidos para o backend
    async function sendRequest(data) {
        const formData = new FormData();
        for (const key in data) {
            formData.append(key, data[key]);
        }

        try {
            const response = await fetch('processa_whereby.php', {
                method: 'POST',
                body: formData
            });
            if (!response.ok) {
                throw new Error(`Erro HTTP: ${response.status}`);
            }
            return await response.json();
        } catch (error) {
            console.error("Erro na comunicação com o servidor:", error);
            alert("Ocorreu um erro de comunicação. Por favor, tente novamente.");
            return null;
        }
    }

    // Função para lidar com o clique no botão "Habilitar"
    async function handleHabilitarClick(button) {
        const pacienteId = button.dataset.pacienteId;
        button.disabled = true;
        button.textContent = 'A habilitar...';

        const result = await sendRequest({ action: 'create_room', paciente_id: pacienteId });

        if (result && result.success) {
            alert('Sala habilitada com sucesso!');
            location.reload(); // Recarrega a página para mostrar o novo estado
        } else {
            alert('Erro: ' + (result ? result.message : 'Não foi possível habilitar a sala.'));
            button.disabled = false;
            button.textContent = 'Habilitar Sala Whereby';
        }
    }

    // Função para lidar com o clique no botão "Remover"
    async function handleRemoverClick(button) {
        if (!confirm('Tem a certeza de que deseja remover a sala deste paciente?')) {
            return;
        }
        
        const pacienteId = button.dataset.pacienteId;
        button.disabled = true;
        button.textContent = 'A remover...';

        const result = await sendRequest({ action: 'remove_room', paciente_id: pacienteId });

        if (result && result.success) {
            alert('Sala removida com sucesso!');
            location.reload(); // Recarrega a página
        } else {
            alert('Erro: ' + (result ? result.message : 'Não foi possível remover a sala.'));
            button.disabled = false;
            button.textContent = 'Remover';
        }
    }

    // Função para lidar com o clique no botão "Copiar"
    function handleCopiarClick(button) {
        const urlParaCopiar = button.dataset.url;
        navigator.clipboard.writeText(urlParaCopiar).then(() => {
            const originalText = button.textContent;
            button.textContent = 'Copiado!';
            setTimeout(() => { button.textContent = originalText; }, 2000);
        }).catch(err => {
            console.error('Erro ao copiar o link: ', err);
            alert('Não foi possível copiar o link.');
        });
    }

    // "Ouvinte" de eventos principal
    const listContainer = document.querySelector('ul[role="list"]');
    if (listContainer) {
        listContainer.addEventListener('click', function(event) {
            const target = event.target;
            if (target.classList.contains('habilitar-sala-btn')) {
                handleHabilitarClick(target);
            } else if (target.classList.contains('remover-sala-btn')) {
                handleRemoverClick(target);
            } else if (target.classList.contains('copiar-link-btn')) {
                handleCopiarClick(target);
            }
        });
    }

    // **FIM DO CÓDIGO CORRIGIDO**
});
</script>

<?php require_once 'templates/footer.php'; ?>

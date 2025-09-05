<?php
require_once '../../config.php';
require_once '../../includes/auth_psicologa.php';
require_once '../../includes/db.php';

$page_title = 'Salas de Atendimento';
require_once 'templates/header.php';

try {
    $pdo = conectar();
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
                        <div class="shrink-0 flex items-center gap-x-4">
                            <?php if (!empty($paciente['whereby_room_url'])): ?>
                                <a href="<?php echo htmlspecialchars($paciente['whereby_room_url']); ?>" target="_blank" class="entrar-sala-link inline-flex items-center rounded-md bg-teal-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-teal-700">
                                    Entrar na Sala
                                </a>
                                <button type="button" class="remover-sala-btn text-sm font-medium text-red-600 hover:text-red-800" data-paciente-id="<?php echo $paciente['id']; ?>">
                                    Remover
                                </button>
                            <?php else: ?>
                                <button type="button" class="habilitar-sala-btn inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700" data-paciente-id="<?php echo $paciente['id']; ?>">
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
    
    function handleHabilitarClick(event) {
        if (!event.target.classList.contains('habilitar-sala-btn')) return;

        const button = event.target;
        const pacienteId = button.dataset.pacienteId;

        button.textContent = 'A criar...';
        button.disabled = true;

        const formData = new FormData();
        formData.append('paciente_id', pacienteId);
        formData.append('action', 'create_room'); // Adiciona a ação

        fetch('processa_whereby.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.roomUrl) {
                const buttonContainer = button.parentElement;
                buttonContainer.innerHTML = `
                    <a href="${data.roomUrl}" target="_blank" class="entrar-sala-link inline-flex items-center rounded-md bg-teal-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-teal-700">
                        Entrar na Sala
                    </a>
                    <button type="button" class="remover-sala-btn text-sm font-medium text-red-600 hover:text-red-800" data-paciente-id="${pacienteId}">
                        Remover
                    </button>
                `;
            } else {
                alert('Erro ao criar a sala: ' + data.message);
                button.textContent = 'Habilitar Sala Whereby';
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            alert('Ocorreu um erro de comunicação com o servidor.');
            button.textContent = 'Habilitar Sala Whereby';
            button.disabled = false;
        });
    }

    function handleRemoverClick(event) {
        if (!event.target.classList.contains('remover-sala-btn')) return;

        if (!confirm('Tem a certeza de que deseja remover esta sala? O link atual deixará de ser válido no sistema e poderá criar um novo.')) {
            return;
        }

        const button = event.target;
        const pacienteId = button.dataset.pacienteId;
        
        button.textContent = 'A remover...';
        button.disabled = true;

        const formData = new FormData();
        formData.append('paciente_id', pacienteId);
        formData.append('action', 'remove_room'); // Adiciona a ação

        fetch('processa_whereby.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const buttonContainer = button.parentElement;
                buttonContainer.innerHTML = `
                    <button type="button" class="habilitar-sala-btn inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700" data-paciente-id="${pacienteId}">
                        Habilitar Sala Whereby
                    </button>
                `;
            } else {
                alert('Erro ao remover a sala: ' + data.message);
                button.textContent = 'Remover';
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            alert('Ocorreu um erro de comunicação com o servidor.');
            button.textContent = 'Remover';
            button.disabled = false;
        });
    }
    
    // Adiciona os "ouvintes" de eventos ao contentor da lista para gerir cliques
    const listContainer = document.querySelector('ul[role="list"]');
    if (listContainer) {
        listContainer.addEventListener('click', handleHabilitarClick);
        listContainer.addEventListener('click', handleRemoverClick);
    }
});
</script>

<?php require_once 'templates/footer.php'; ?>

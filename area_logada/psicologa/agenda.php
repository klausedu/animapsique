<?php
require_once '../../config.php';
require_once '../../includes/auth_psicologa.php';
require_once '../../includes/db.php';

$page_title = 'Agenda de Sessões';
require_once 'templates/header.php';

// Buscar pacientes ativos para preencher o select do modal
try {
    $pdo = conectar();
    $stmt = $pdo->query("SELECT id, nome FROM pacientes WHERE ativo = 1 ORDER BY nome");
    $pacientes = $stmt->fetchAll();
} catch (PDOException $e) { $pacientes = []; }
?>

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>

<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div id='calendar'></div>
    </div>
</div>

<div id="agendaModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="agendaForm">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Novo Agendamento</h3>
                    <div class="mt-4 space-y-4">
                        <input type="hidden" name="action" id="action" value="create">
                        <input type="hidden" name="eventId" id="eventId">
                        
                        <div id="paciente_select_container">
                            <label for="paciente_id" class="block text-sm font-medium text-gray-700">Paciente</label>
                            <select id="paciente_id" name="paciente_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">Selecione um paciente</option>
                                <?php foreach ($pacientes as $paciente): ?>
                                    <option value="<?php echo $paciente['id']; ?>"><?php echo htmlspecialchars($paciente['nome']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="data" class="block text-sm font-medium text-gray-700">Data</label>
                            <input type="date" name="data" id="data" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="hora_inicio" class="block text-sm font-medium text-gray-700">Início</label>
                                <input type="time" name="hora_inicio" id="hora_inicio" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label for="hora_fim" class="block text-sm font-medium text-gray-700">Fim</label>
                                <input type="time" name="hora_fim" id="hora_fim" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                        </div>

                        <div id="sala_reuniao_container" class="hidden">
                            <label class="block text-sm font-medium text-gray-700">Link da Sala</label>
                            <div class="mt-1">
                                <a id="sala_reuniao_link" href="#" target="_blank" class="text-teal-600 hover:text-teal-800 break-all"></a>
                            </div>
                        </div>

                        <div class="border-t pt-4 space-y-4">
                            <div class="relative flex items-start">
                                <div class="flex h-6 items-center"><input id="recorrente" name="recorrente" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-teal-600 focus:ring-teal-600"></div>
                                <div class="ml-3 text-sm leading-6"><label for="recorrente" class="font-medium text-gray-900">Repetir semanalmente</label></div>
                            </div>
                            <div id="recorrencia_fim_container" class="hidden">
                                <label for="data_fim_recorrencia" class="block text-sm font-medium text-gray-700">Repetir até à data de</label>
                                <input type="date" name="data_fim_recorrencia" id="data_fim_recorrencia" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                        </div>

                        <div id="status_container">
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="planejado">Planejado</option>
                                <option value="concluido">Concluído</option>
                                <option value="cancelado">Cancelado</option>
                                <option value="falta">Falta</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse items-center">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border shadow-sm px-4 py-2 bg-teal-600 text-base font-medium text-white hover:bg-teal-700 sm:ml-3 sm:w-auto sm:text-sm">Salvar</button>
                    <button type="button" id="deleteButton" class="hidden mt-3 w-full inline-flex justify-center rounded-md border shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Excluir</button>
                    <button type="button" id="closeModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="deleteRecorrenciaModal" class="fixed z-20 inset-0 overflow-y-auto hidden">
    </div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // ... (declaração de constantes, não precisa de alteração) ...
    const calendarEl = document.getElementById('calendar');
    const modal = document.getElementById('agendaModal');
    const form = document.getElementById('agendaForm');
    const closeModalBtn = document.getElementById('closeModal');
    const deleteButton = document.getElementById('deleteButton');
    const recorrenteCheckbox = document.getElementById('recorrente');
    const recorrenciaFimContainer = document.getElementById('recorrencia_fim_container');
    const dataFimRecorrenciaInput = document.getElementById('data_fim_recorrencia');
    const statusContainer = document.getElementById('status_container');
    const deleteRecorrenciaModal = document.getElementById('deleteRecorrenciaModal');
    const deleteSerieBtn = document.getElementById('deleteSerieBtn');
    const deleteOcorrenciaBtn = document.getElementById('deleteOcorrenciaBtn');
    const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');

    // ... (listener do checkbox, não precisa de alteração) ...
    recorrenteCheckbox.addEventListener('change', function() {
        if (this.checked) {
            recorrenciaFimContainer.classList.remove('hidden');
            dataFimRecorrenciaInput.required = true;
            statusContainer.classList.add('hidden');
        } else {
            recorrenciaFimContainer.classList.add('hidden');
            dataFimRecorrenciaInput.required = false;
            statusContainer.classList.remove('hidden');
        }
    });

    const calendar = new FullCalendar.Calendar(calendarEl, {
        // ... (configurações do calendário, não precisam de alteração) ...
        initialView: 'timeGridWeek',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        locale: 'pt-br',
        buttonText: { today: 'Hoje', month: 'Mês', week: 'Semana', day: 'Dia' },
        
        dateClick: function(info) {
            form.reset();
            document.getElementById('sala_reuniao_container').classList.add('hidden');
            recorrenteCheckbox.checked = false;
            recorrenteCheckbox.dispatchEvent(new Event('change'));
            document.getElementById('modal-title').innerText = 'Novo Agendamento';
            document.getElementById('action').value = 'create';
            deleteButton.classList.add('hidden');
            
            const dataClicada = dayjs(info.dateStr);
            document.getElementById('data').value = dataClicada.format('YYYY-MM-DD');
            document.getElementById('hora_inicio').value = dataClicada.format('HH:mm');
            document.getElementById('hora_fim').value = dataClicada.add(50, 'minute').format('HH:mm');
            
            modal.classList.remove('hidden');
        },
        
        eventClick: function(info) {
            form.reset();
            recorrenteCheckbox.checked = false;
            recorrenteCheckbox.dispatchEvent(new Event('change'));
            document.getElementById('modal-title').innerText = 'Editar Agendamento';
            document.getElementById('action').value = 'update';
            document.getElementById('eventId').value = info.event.id;
            deleteButton.classList.remove('hidden');
            
            document.getElementById('paciente_id').value = info.event.extendedProps.pacienteId;
            document.getElementById('status').value = info.event.extendedProps.status;

            const dataInicio = dayjs(info.event.start);
            document.getElementById('data').value = dataInicio.format('YYYY-MM-DD');
            document.getElementById('hora_inicio').value = dataInicio.format('HH:mm');
            
            if(info.event.end) {
                document.getElementById('hora_fim').value = dayjs(info.event.end).format('HH:mm');
            }

            // INÍCIO: Exibir o link da sala
            const salaUrl = info.event.extendedProps.salaUrl;
            const salaContainer = document.getElementById('sala_reuniao_container');
            const salaLink = document.getElementById('sala_reuniao_link');

            if (salaUrl) {
                salaLink.href = salaUrl;
                salaLink.textContent = "Acessar sala de atendimento";
                salaContainer.classList.remove('hidden');
            } else {
                salaContainer.classList.add('hidden');
            }
            // FIM: Exibir o link da sala
            
            modal.classList.remove('hidden');
        },

        events: 'api_agenda.php'
    });

    calendar.render();

    closeModalBtn.addEventListener('click', () => modal.classList.add('hidden'));
    cancelDeleteBtn.addEventListener('click', () => deleteRecorrenciaModal.classList.add('hidden'));

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(form);
        
        fetch('processa_agenda.php', { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                modal.classList.add('hidden');
                calendar.refetchEvents();
            } else {
                alert('Erro: ' + (data.message || 'Não foi possível guardar o agendamento.'));
            }
        })
        .catch(error => console.error("Erro ao submeter o formulário:", error));
    });

    deleteButton.addEventListener('click', function() {
        // ... (código de exclusão, não precisa de alteração) ...
    });

    deleteSerieBtn.addEventListener('click', function() {
        // ... (código de exclusão, não precisa de alteração) ...
    });

    deleteOcorrenciaBtn.addEventListener('click', function() {
        // ... (código de exclusão, não precisa de alteração) ...
    });
    
    function performDelete(data) {
        // ... (código de exclusão, não precisa de alteração) ...
    }
});
</script>

<?php require_once 'templates/footer.php'; ?>

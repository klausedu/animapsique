<?php
require_once '../../includes/auth_psicologa.php';
require_once '../../includes/db.php';

$pdo = conectar();
$stmt = $pdo->prepare("SELECT id, nome FROM pacientes WHERE ativo = 1 ORDER BY nome");
$stmt->execute();
$pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Agenda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
    <script src="https://kit.fontawesome.com/4b3271b654.js" crossorigin="anonymous"></script>
    <style>
        body { font-family: 'Arial', sans-serif; }
        #calendar { max-width: 1100px; margin: 40px auto; }
        .modal-body label { font-weight: bold; }
    </style>
</head>
<body>
    <?php include 'templates/header.php'; ?>

    <div class="container-fluid">
        <div id="calendar"></div>
    </div>

    <div class="modal fade" id="eventModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Gerir Horário</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="eventId">
                    <input type="hidden" id="eventStart">
                    <input type="hidden" id="eventEnd">
                     <input type="hidden" id="eventRecorrenciaId">

                    <div class="form-group">
                        <label for="pacienteSelect">Paciente</label>
                        <select id="pacienteSelect" class="form-control">
                            <option value="">-- Horário Livre --</option>
                            <?php foreach ($pacientes as $paciente) : ?>
                                <option value="<?= $paciente['id'] ?>"><?= htmlspecialchars($paciente['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                     <div id="infoRecorrencia" class="alert alert-info" style="display: none;">
                        Este é um evento recorrente. As alterações afetarão apenas esta ocorrência.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="saveButton">Salvar</button>
                    <button type="button" class="btn btn-warning" id="cancelButton" style="display: none;">Cancelar Consulta</button>
                    <button type="button" class="btn btn-danger" id="deleteButton" style="display: none;">Excluir Horário</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const modal = new bootstrap.Modal(document.getElementById('eventModal'));
        const modalTitle = document.getElementById('modalTitle');
        const eventIdInput = document.getElementById('eventId');
        const eventStartInput = document.getElementById('eventStart');
        const eventEndInput = document.getElementById('eventEnd');
        const pacienteSelect = document.getElementById('pacienteSelect');
        const saveButton = document.getElementById('saveButton');
        const deleteButton = document.getElementById('deleteButton');
        const cancelButton = document.getElementById('cancelButton');
        const infoRecorrencia = document.getElementById('infoRecorrencia');
        const eventRecorrenciaIdInput = document.getElementById('eventRecorrenciaId');

        // Função para comunicar com o backend
        async function sendRequest(url, data) {
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || `Erro HTTP: ${response.status}`);
                }
                return await response.json();
            } catch (error) {
                alert(`Erro: ${error.message}`);
                return null;
            }
        }

        const calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'pt-br',
            initialView: 'timeGridWeek',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            editable: true,
            selectable: true,
            events: 'api_agenda', // URL para carregar eventos

            // Ao clicar num horário vazio
            select: function(info) {
                modalTitle.textContent = 'Criar Novo Horário';
                eventIdInput.value = '';
                eventStartInput.value = info.startStr;
                eventEndInput.value = info.endStr;
                pacienteSelect.value = '';
                deleteButton.style.display = 'none';
                cancelButton.style.display = 'none';
                infoRecorrencia.style.display = 'none';
                eventRecorrenciaIdInput.value = '';
                modal.show();
            },

            // Ao clicar num evento existente
            eventClick: function(info) {
                const props = info.event.extendedProps;
                modalTitle.textContent = 'Editar Horário';
                eventIdInput.value = info.event.id;
                eventStartInput.value = info.event.startStr;
                pacienteSelect.value = props.pacienteId || '';

                // Lógica para eventos recorrentes vs normais
                if (props.status === 'recorrente') {
                    infoRecorrencia.style.display = 'block';
                    deleteButton.style.display = 'none'; // Não se pode apagar a regra aqui
                    cancelButton.style.display = 'block'; // Pode-se cancelar a ocorrência
                    eventRecorrenciaIdInput.value = props.recorrenciaId;
                } else {
                    infoRecorrencia.style.display = 'none';
                    deleteButton.style.display = 'block';
                    cancelButton.style.display = 'none';
                    eventRecorrenciaIdInput.value = '';
                }
                modal.show();
            },
        });

        calendar.render();

        // Botão Salvar
        saveButton.addEventListener('click', async () => {
            const id = eventIdInput.value;
            const isRecorrente = eventRecorrenciaIdInput.value;

            let data = {
                pacienteId: pacienteSelect.value || null,
                start: eventStartInput.value
            };

            if (isRecorrente) {
                // Se é recorrente, criamos um novo evento para esta data
                 data.action = 'create';
                 data.end = eventEndInput.value;
            } else {
                // Se for um evento normal, ou criamos um novo ou atualizamos
                data.action = id ? 'update' : 'create';
                if(id) data.id = id;
                if(!id) data.end = eventEndInput.value;
            }

            const result = await sendRequest('processa_agenda', data);
            if (result && result.success) {
                calendar.refetchEvents();
                modal.hide();
            }
        });

        // Botão Apagar
        deleteButton.addEventListener('click', async () => {
            if (!confirm("Tem a certeza que quer apagar este horário?")) return;
            
            const data = {
                action: 'delete',
                id: eventIdInput.value
            };

            const result = await sendRequest('processa_agenda', data);
            if (result && result.success) {
                calendar.refetchEvents();
                modal.hide();
            }
        });

        // Botão Cancelar Consulta (para recorrências)
        cancelButton.addEventListener('click', async () => {
            if (!confirm("Tem a certeza que quer cancelar esta consulta específica?")) return;

            const data = {
                action: 'cancel_recorrencia',
                recorrenciaId: eventRecorrenciaIdInput.value,
                start: eventStartInput.value
            };

            const result = await sendRequest('processa_agenda', data);
            if (result && result.success) {
                calendar.refetchEvents();
                modal.hide();
            }
        });
    });
    </script>
</body>
</html>

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
    <style>
        #calendar { max-width: 1100px; margin: 40px auto; }
    </style>
</head>
<body>
    <?php include 'templates/header.php'; ?>

    <div class="container-fluid"><div id="calendar"></div></div>

    <div class="modal fade" id="eventModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modalTitle">Gerir Horário</h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="eventId"><input type="hidden" id="eventStart"><input type="hidden" id="eventEnd"><input type="hidden" id="eventRecorrenciaId">
            <div class="form-group">
                <label for="pacienteSelect">Paciente</label>
                <select id="pacienteSelect" class="form-control">
                    <option value="">-- Horário Livre --</option>
                    <?php foreach ($pacientes as $paciente) : ?>
                        <option value="<?= $paciente['id'] ?>"><?= htmlspecialchars($paciente['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div id="infoRecorrencia" class="alert alert-info" style="display: none;">Este é um evento recorrente. As alterações afetarão apenas esta ocorrência.</div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="saveButton">Salvar</button>
            <button type="button" class="btn btn-warning" id="cancelButton" style="display: none;">Cancelar Consulta</button>
            <button type="button" class="btn btn-danger" id="deleteButton" style="display: none;">Excluir Horário</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
        </div>
    </div></div></div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
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

        async function sendRequest(data) {
            try {
                const response = await fetch('processa_agenda', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                if (!response.ok) {
                    const error = await response.json();
                    throw new Error(error.message || 'Erro no servidor');
                }
                return await response.json();
            } catch (error) {
                alert(`Erro: ${error.message}`);
                return null;
            }
        }

        const calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'pt-br', initialView: 'timeGridWeek',
            headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay' },
            editable: true, selectable: true,
            events: 'api_agenda',

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
                eventModal.show();
            },

            eventClick: function(info) {
                const props = info.event.extendedProps;
                modalTitle.textContent = 'Editar Horário';
                eventIdInput.value = info.event.id;
                eventStartInput.value = info.event.startStr;
                pacienteSelect.value = props.pacienteId || '';

                if (props.status === 'recorrente') {
                    infoRecorrencia.style.display = 'block';
                    deleteButton.style.display = 'none';
                    cancelButton.style.display = 'block';
                    eventRecorrenciaIdInput.value = props.recorrenciaId;
                } else {
                    infoRecorrencia.style.display = 'none';
                    deleteButton.style.display = 'block';
                    cancelButton.style.display = 'none';
                    eventRecorrenciaIdInput.value = '';
                }
                eventModal.show();
            },
        });
        calendar.render();

        saveButton.addEventListener('click', async () => {
            const id = eventIdInput.value;
            const isRecorrente = eventRecorrenciaIdInput.value;
            
            const data = {
                pacienteId: pacienteSelect.value || null,
                start: eventStartInput.value
            };

            if (isRecorrente) {
                data.action = 'create';
                data.end = eventEndInput.value;
            } else {
                data.action = id.startsWith('rec_') || !id ? 'create' : 'update';
                if (data.action === 'update') data.id = id;
                if (data.action === 'create') data.end = eventEndInput.value;
            }
            
            const result = await sendRequest(data);
            if (result?.success) {
                calendar.refetchEvents();
                eventModal.hide();
            }
        });

        deleteButton.addEventListener('click', async () => {
            if (confirm("Tem a certeza?")) {
                const result = await sendRequest({ action: 'delete', id: eventIdInput.value });
                if (result?.success) {
                    calendar.refetchEvents();
                    eventModal.hide();
                }
            }
        });

        cancelButton.addEventListener('click', async () => {
            if (confirm("Cancelar esta consulta específica?")) {
                const result = await sendRequest({ action: 'cancel_recorrencia', recorrenciaId: eventRecorrenciaIdInput.value, start: eventStartInput.value });
                if (result?.success) {
                    calendar.refetchEvents();
                    eventModal.hide();
                }
            }
        });
    });
    </script>
</body>
</html>

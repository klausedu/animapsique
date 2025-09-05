<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda de Horários - Psicóloga Exemplo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <style> 
        body { font-family: 'Inter', sans-serif; } 
        .fc-event { cursor: pointer; }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Incluir o header público aqui -->
    <main class="py-16 md:py-24">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h1 class="text-4xl md:text-5xl font-bold text-teal-800">Consulte os Horários Disponíveis</h1>
                <p class="text-gray-600 mt-4 max-w-2xl mx-auto">Veja abaixo os horários livres para uma primeira conversa. Ao clicar em um horário, você será direcionado para a página de contato para solicitar seu agendamento.</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <div id='calendar'></div>
            </div>
        </div>
    </main>
    <!-- Incluir o footer público aqui -->

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'timeGridWeek',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'timeGridWeek,dayGridMonth'
            },
            locale: 'pt-br',
            allDaySlot: false,
            slotMinTime: '07:00:00',
            events: 'api_agenda_publica.php',
            eventClick: function(info) {
                if (confirm('Você será redirecionado para a página de contato para solicitar este horário. Deseja continuar?')) {
                    window.location.href = 'contato.php';
                }
            }
        });
        calendar.render();
    });
    </script>
</body>
</html>

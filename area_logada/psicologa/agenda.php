<?php

require_once '../../config.php';

require_once '../../includes/auth_paciente.php';

require_once '../../includes/db.php';



$page_title = 'Minha Agenda';

require_once 'templates/header.php';

?>



<!-- Incluindo a biblioteca FullCalendar -->

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>



<div class="container mx-auto px-4 sm:px-6 lg:px-8">

    <div class="bg-white p-6 rounded-lg shadow-md">

        <p class="text-gray-600 mb-6">Aqui estão suas sessões agendadas. Você pode visualizar por mês, semana ou dia.</p>

        <div id='calendar'></div>

    </div>

</div>



<script>

document.addEventListener('DOMContentLoaded', function() {

    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {

        initialView: 'dayGridMonth',

        headerToolbar: {

            left: 'prev,next today',

            center: 'title',

            right: 'dayGridMonth,timeGridWeek,listWeek'

        },

        locale: 'pt-br',

        buttonText: { today: 'Hoje', month: 'Mês', week: 'Semana', list: 'Lista' },

        navLinks: true,

        dayMaxEvents: true,

        events: 'api_agenda_paciente.php' // API específica para o paciente

    });

    calendar.render();

});

</script>



<?php require_once 'templates/footer.php'; ?>


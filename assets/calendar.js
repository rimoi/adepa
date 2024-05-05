
import './styles/calendar.css';
import './admin/vendor/fontawesome-free/css/all.min.css';
import './admin/css/sb-admin-2.min.css';
import './styles/footer.css';


import $ from 'jquery';
import "./admin/vendor/bootstrap/js/bootstrap.bundle";

import "./admin/js/sb-admin-2.js";

import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import allLocales from '@fullcalendar/core/locales-all';


document.addEventListener('DOMContentLoaded', function() {
    // var calendar = new FullCalendar.Calendar(calendarEl, {
    //     initialView: 'dayGridMonth'
    // });

    var missions = $('#calendar').data('info');


        let calendarEl = document.querySelector('#calendar');
        // var calendar = new FullCalendar.Calendar(calendarEl, {
        //     initialView: 'dayGridMonth'
        // });


        let calendar = new Calendar(calendarEl, {
            plugins: [dayGridPlugin, timeGridPlugin, listPlugin],
            initialView: 'listWeek',
            locales: allLocales,
            locale: 'fr',
            timeZone: 'Europe/Paris',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listWeek'
            },
            buttonText:    {
                today:    "Aujourd'hui",
                month:    'Mois',
                week:     "Semaine",
                day:      'Journalière',
                list:     'Liste'
            },
            events: missions
        });
        calendar.render();
});


import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import interactionPlugin from '@fullcalendar/interaction';
import frLocale from '@fullcalendar/core/locales/fr';
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    console.log(calendarEl); // Vérifie que l'élément existe
  
    if (calendarEl) {
      const events = JSON.parse(calendarEl.dataset.events);
      const calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'fr',
        plugins: [FullCalendar.dayGridPlugin, FullCalendar.timeGridPlugin, FullCalendar.listPlugin, FullCalendar.interactionPlugin],
        initialView: 'dayGridMonth',
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        buttonText: {
          today: "Aujourd'hui",
          month: 'Mois',
          week: 'Semaine',
          day: 'Jour',
          list: 'Liste'
        },
        navLinks: true,
        editable: false,
        dayMaxEvents: true,
        events: events,
        eventClick: function (info) {
          info.jsEvent.preventDefault();
          alert('🚗 ' + info.event.title + '\n📅 Date : ' + info.event.start.toLocaleDateString());
        }
      });
      calendar.render();
    } else {
      console.error('Élément avec l\'ID "calendar" non trouvé.');
    }
  });
  
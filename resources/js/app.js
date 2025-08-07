import './bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import * as bootstrap from 'bootstrap'; // JS do Bootstrap

import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import bootstrap5Plugin from '@fullcalendar/bootstrap5';
import ptBr from '@fullcalendar/core/locales/pt-br';

// Expondo globalmente o FullCalendar para uso nos scripts do Blade
window.FullCalendar = {
    Calendar,
    dayGridPlugin,
    timeGridPlugin,
    interactionPlugin,
    bootstrap5Plugin,
    ptBr,
};

// Alpine.js (para interatividade como o sininho de notificações)
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// Confirmação no console para debugging
console.log('App.js carregado com Tailwind, Bootstrap, FullCalendar e Alpine');

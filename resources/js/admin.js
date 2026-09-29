import './bootstrap';
import '../scss/pagination.scss';
import './admin/components';
import Alpine from 'alpinejs';
import * as bootstrap from 'bootstrap';
import { bootAdminEnhancements } from './admin/enhancements';

window.bootstrap = bootstrap;

window.Alpine = Alpine;
Alpine.start();

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootAdminEnhancements);
} else {
    bootAdminEnhancements();
}

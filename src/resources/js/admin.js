import './bootstrap';
import './admin/components';
import Alpine from 'alpinejs';
import { bootAdminEnhancements } from './admin/enhancements';

window.Alpine = Alpine;
Alpine.start();

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootAdminEnhancements);
} else {
    bootAdminEnhancements();
}

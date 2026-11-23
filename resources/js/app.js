import './bootstrap';
import Alpine from 'alpinejs';
import { createIcons, icons } from 'lucide';

window.Alpine = Alpine;
Alpine.start();

window.createIcons = createIcons;
window.lucideIcons = icons;

document.addEventListener('DOMContentLoaded', () => {
    createIcons({ icons });
});

// Re-render lucide icons whenever Livewire or Alpine changes DOM
window.addEventListener('render-icons', () => {
    createIcons({ icons });
});

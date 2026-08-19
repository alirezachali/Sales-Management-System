import './bootstrap';
import '@tabler/core/dist/js/tabler.min.js';

// import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
// import Clipboard from '@ryangjchandler/alpine-clipboard'

// Alpine.plugin(Clipboard)

// Livewire.start()

const toggleButton = document.getElementById('toggleSidebar');
const sidebar = document.getElementById('sidebar');

if (toggleButton && sidebar) {
    toggleButton.addEventListener('click', function () {
        sidebar.classList.toggle('collapsed');
    });
}
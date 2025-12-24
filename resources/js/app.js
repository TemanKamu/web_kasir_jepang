import './bootstrap';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_REVERB_APP_KEY, // Kunci Aplikasi Reverb Anda
    wsHost: import.meta.env.VITE_REVERB_HOST, // Host Reverb Anda (misalnya localhost)
    wsPort: import.meta.env.VITE_REVERB_PORT, // Port Reverb Anda (misalnya 8080)
    wssPort: import.meta.env.VITE_REVERB_PORT, // Port WSS (jika menggunakan SSL)
    forceTLS: false, // Ubah menjadi true jika menggunakan HTTPS/WSS
    enabledTransports: ['ws', 'wss'],
});
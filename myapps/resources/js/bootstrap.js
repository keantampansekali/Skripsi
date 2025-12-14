import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Laravel Echo & Pusher
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// Initialize Echo only if we have the necessary configuration
try {
    const getHostname = () => {
        if (typeof window !== 'undefined' && window.location) {
            return window.location.hostname;
        }
        return 'localhost';
    };

    const getScheme = () => {
        if (typeof window !== 'undefined' && window.location) {
            return window.location.protocol === 'https:' ? 'https' : 'http';
        }
        return 'http';
    };

    const reverbKey = import.meta.env.VITE_REVERB_APP_KEY ?? import.meta.env.VITE_PUSHER_APP_KEY;
    const reverbHost = import.meta.env.VITE_REVERB_HOST ?? import.meta.env.VITE_PUSHER_HOST ?? getHostname();
    const reverbPort = import.meta.env.VITE_REVERB_PORT ?? import.meta.env.VITE_PUSHER_PORT ?? 8080;
    const reverbScheme = import.meta.env.VITE_REVERB_SCHEME ?? import.meta.env.VITE_PUSHER_SCHEME ?? getScheme();

    if (reverbKey) {
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: reverbKey,
            wsHost: reverbHost,
            wsPort: reverbPort,
            wssPort: reverbPort === 8080 ? 443 : reverbPort,
            forceTLS: reverbScheme === 'https',
            enabledTransports: ['ws', 'wss'],
            authEndpoint: '/broadcasting/auth',
            auth: {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
            },
        });
    } else {
        console.warn('Reverb key not configured. Real-time features will be disabled.');
        window.Echo = null;
    }
} catch (error) {
    console.error('Failed to initialize Echo:', error);
    window.Echo = null;
}

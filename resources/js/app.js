import './bootstrap'; // لاستيراد axios وتوابعه

import Echo from 'laravel-echo';
import Pusher from 'pusher-js'; // استيراد Pusher مباشرة

// تعيين Pusher على النافذة (مطلوب لـ Echo)
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY, // لـ Vite
    wsHost: import.meta.env.VITE_PUSHER_HOST ?? window.location.hostname, // لـ Vite
    wsPort: import.meta.env.VITE_PUSHER_PORT ?? 6001, // لـ Vite
    wssPort: import.meta.env.VITE_PUSHER_PORT ?? 6001, // لـ Vite
    forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'http') === 'https', // لـ Vite
    disableStats: true,
    enabledTransports: ['ws', 'wss'],
});

// هذا مهم جداً للمصادقة على القنوات الخاصة (Private Channels)
// يجب أن يكون المستخدم قد سجل الدخول
window.Echo.connector.options.authorizer = (channel, options) => {
    return {
        authorize: (socketId, callback) => {
            axios.post('/api/broadcasting/auth', {
                channel_name: channel.name,
                socket_id: socketId
            })
            .then(response => {
                callback(null, response.data);
            })
            .catch(error => {
                callback(error);
            });
        }
    };
};
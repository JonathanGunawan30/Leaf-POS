import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;
const token = localStorage.getItem('X-API-TOKEN') || '';


Pusher.logToConsole = true;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
    authEndpoint: '/api/broadcasting/auth',
    auth: {
        headers: {
            Authorization: `Bearer ${token}`,
            Accept: 'application/json',
            'Content-Type': 'application/json',
        }
    }
});

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';

axios.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem('X-API-TOKEN');
        if (token) {
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        }

        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

axios.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response) {
            const isLoginRequest = error.config.url.includes('/api/users/login');

            if (error.response.status === 401 && !isLoginRequest) {
                localStorage.removeItem('X-API-TOKEN');
                window.location.href = '/';
            }

            if (error.response.status === 419) {
                window.location.reload();
            }
        }

        return Promise.reject(error);
    }
);

window.axios = axios;

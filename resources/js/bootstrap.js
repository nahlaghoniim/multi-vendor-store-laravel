import _ from 'lodash';
window._ = _;

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true; // Add this

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    wsHost: import.meta.env.VITE_PUSHER_HOST ?? `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
    wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
    wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            'Accept': 'application/json',
        }
    }
});

document.addEventListener('DOMContentLoaded', function() {
    console.log('🔍 DOM loaded, checking for userID...');
    
    if (typeof window.userID === 'undefined') {
        console.log('ℹ️ No userID defined');
        return;
    }

    console.log('🔔 Initializing notifications for admin:', window.userID);

    const badge = document.getElementById('notification-count');
    const dropdownHeader = document.getElementById('dropdown-header');
    const notificationItems = document.getElementById('notification-items');

    Echo.connector.pusher.connection.bind('connected', () => {
        console.log('✅ Pusher connected!');
    });

    Echo.connector.pusher.connection.bind('error', (err) => {
        console.error('❌ Pusher error:', err);
    });

    const channelName = `App.Models.Admin.${window.userID}`;
    console.log('📡 Subscribing to:', channelName);

    Echo.private(channelName)
        .notification((notification) => {
            console.log('✅ Notification received:', notification);

            if (badge) {
                let count = parseInt(badge.innerText) || 0;
                badge.innerText = count + 1;
                badge.style.display = 'inline-block';
            }

            if (dropdownHeader) {
                let headerCount = parseInt(dropdownHeader.innerText.split(' ')[0]) || 0;
                dropdownHeader.innerText = `${headerCount + 1} Notifications`;
            }

            if (notificationItems) {
                const newItem = document.createElement('a');
                newItem.href = `${notification.url}?notification_id=${notification.id}`;
                newItem.className = 'dropdown-item text-wrap text-bold';
                newItem.dataset.id = notification.id;
                newItem.innerHTML = `<i class="${notification.icon} mr-2"></i> ${notification.body} <span class="float-right text-muted text-sm">Just now</span>`;

                const divider = document.createElement('div');
                divider.className = 'dropdown-divider';

                notificationItems.prepend(divider);
                notificationItems.prepend(newItem);
            }
        })
        .subscribed(() => {
            console.log('✅ Successfully subscribed to:', channelName);
        })
        .error((error) => {
            console.error('❌ Subscription error:', error);
        });

    document.addEventListener('click', function(e) {
        const link = e.target.closest('#notification-items a');
        if (link && link.dataset.id) {
            e.preventDefault();
            
            const notificationId = link.dataset.id;

            axios.post(`/admin/dashboard/notifications/${notificationId}/read`)
                .then(response => {
                    link.classList.remove('text-bold');

                    if (badge) {
                        let count = parseInt(badge.innerText) || 0;
                        badge.innerText = Math.max(count - 1, 0);
                        if (badge.innerText == 0) badge.style.display = 'none';
                    }

                    if (dropdownHeader) {
                        let headerCount = parseInt(dropdownHeader.innerText.split(' ')[0]) || 0;
                        dropdownHeader.innerText = `${Math.max(headerCount - 1, 0)} Notifications`;
                    }

                    window.location.href = link.href;
                })
                .catch(error => {
                    console.error('❌ Error:', error);
                    window.location.href = link.href;
                });
        }
    });
});
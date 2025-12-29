import './bootstrap';

const badge = document.getElementById('notification-count');
const dropdownHeader = document.getElementById('dropdown-header');
const notificationItems = document.getElementById('notification-items');

Echo.private(`App.Models.User.${window.userID}`)
    .listen('.Illuminate\\Notifications\\Events\\BroadcastNotificationCreated', (e) => {
        // 1️⃣ Show alert (optional)
        alert(e.body);

        // 2️⃣ Update badge
        if (badge) {
            let count = parseInt(badge.innerText) || 0;
            badge.innerText = count + 1;
            badge.style.display = 'inline-block';
        }

        // 3️⃣ Update dropdown header
        if (dropdownHeader) {
            let headerCount = parseInt(dropdownHeader.innerText.split(' ')[0]) || 0;
            dropdownHeader.innerText = `${headerCount + 1} Notifications`;
        }

        // 4️⃣ Prepend new notification to dropdown
        if (notificationItems) {
            const newItem = document.createElement('a');
            newItem.href = `${e.url}?notification_id=${e.id}`;
            newItem.className = 'dropdown-item text-wrap text-bold';
            newItem.dataset.id = e.id;
            newItem.innerHTML = `<i class="${e.icon} mr-2"></i> ${e.body} <span class="float-right text-muted text-sm">Just now</span>`;

            const divider = document.createElement('div');
            divider.className = 'dropdown-divider';

            notificationItems.prepend(divider);
            notificationItems.prepend(newItem);
        }
    });

// 5️⃣ Mark notification as read on click
document.addEventListener('click', function(e) {
    if (e.target.closest('#notification-items a')) {
        const link = e.target.closest('#notification-items a');
        const notificationId = link.dataset.id;

        // Send AJAX request to mark as read
        axios.post(`/notifications/${notificationId}/read`)
            .then(response => {
                // Remove bold class
                link.classList.remove('text-bold');

                // Decrement badge
                if (badge) {
                    let count = parseInt(badge.innerText) || 0;
                    badge.innerText = Math.max(count - 1, 0);
                    if (badge.innerText == 0) badge.style.display = 'none';
                }

                // Update header
                if (dropdownHeader) {
                    let headerCount = parseInt(dropdownHeader.innerText.split(' ')[0]) || 0;
                    dropdownHeader.innerText = `${Math.max(headerCount - 1, 0)} Notifications`;
                }
            });
    }
});

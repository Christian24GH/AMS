if (window.Worker) {
    const baseUrl = window.location.origin; // Define the base URL here
    const notificationWorker = new Worker('/ams/client/notification/js/notificationWorker.js');

    notificationWorker.postMessage({ baseUrl });

    notificationWorker.onmessage = function(event) {
        const notifications = event.data;
        
        notifications.forEach(notification => {
            // Display or handle the notification in the UI
            console.log('New Notification:', notification);
        });
    };
}
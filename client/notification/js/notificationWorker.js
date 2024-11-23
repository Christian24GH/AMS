let baseUrl;

self.onmessage = function(event) {
    baseUrl = event.data.baseUrl; // Set baseUrl when received from the main script
    checkNotifications(); // Start the notification check immediately
};

async function checkNotifications() {
    try {
        const response = await fetch(`${baseUrl}/ams/client/notification/requestHandler/fetchNotifications.php`);
        if (!response.ok) throw new Error('Network response was not ok');
        
        const notifications = await response.json();
        
        if (notifications.length > 0) {
            self.postMessage(notifications); // Send notifications to the main script
        }
    } catch (error) {
        console.error('Error fetching notifications:', error);
    } finally {
        setTimeout(checkNotifications, 10000);
    }
}
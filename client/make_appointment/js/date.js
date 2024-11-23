function populateDateOptions(){
    const dateSelector = document.getElementById('appt_date');
    const today = new Date();
    
    // Start from tomorrow
    let date = new Date();
    date.setDate(today.getDate() + 1);
    
    // Generate 7 dates, excluding Sundays
    let optionsCount = 0;
    while (optionsCount < 7) {
        // If the date is not Sunday (0 represents Sunday in JavaScript)
        if (date.getDay() !== 0) {
            const option = document.createElement('option');
            option.value = date.toISOString().split('T')[0]; // Format as YYYY-MM-DD
            option.text = date.toDateString(); // Example format: "Mon Nov 20 2024"
            dateSelector.appendChild(option);
            optionsCount++;
        }
        // Move to the next day
        date.setDate(date.getDate() + 1);
    }
}
window.addEventListener("load", populateDateOptions);
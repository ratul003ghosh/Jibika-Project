async function handleSubscribe(event) {
    event.preventDefault();
    const emailInput = document.getElementById('subscribeEmail');
    const messageDiv = document.getElementById('subscribeMessage');
    const email = emailInput.value;
    
    if(!email) return;

    try {
        const formData = new FormData();
        formData.append('email', email);

        // Uses global window.jibikaPathPrefix set in footer.php
        const pathPrefix = window.jibikaPathPrefix || '';

        const response = await fetch(pathPrefix + 'subscribe.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        messageDiv.style.display = 'block';
        if(data.success) {
            messageDiv.className = 'mb-3 small fw-bold text-success';
            messageDiv.innerText = data.message;
            emailInput.value = '';
        } else {
            messageDiv.className = 'mb-3 small fw-bold text-danger';
            messageDiv.innerText = data.message;
        }
        
        setTimeout(() => {
            messageDiv.style.display = 'none';
        }, 5000);
        
    } catch (error) {
        messageDiv.style.display = 'block';
        messageDiv.className = 'mb-3 small fw-bold text-danger';
        messageDiv.innerText = 'Something went wrong. Please try again.';
    }
}

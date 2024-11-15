// common.js

// Function to adjust the iframe height based on the received height
function adjustIframeHeight(event) {
    // Verify that the message is from the expected iframe with the correct type
    if (event.origin === window.location.origin && event.data.type === 'resize' && event.data.id === 'iframe1') {
        const iframe = document.getElementById('iframe1');
        if (iframe) {
            // Set the iframe's height to the updated height
            iframe.style.height = event.data.height + 'px';
        }
    }
}

// Add an event listener to listen for messages from the iframe
window.addEventListener('message', adjustIframeHeight);

// En un nuevo archivo chatbot.js
document.addEventListener('DOMContentLoaded', function() {
    const chatbotToggle = document.getElementById('chatbot-toggle');
    const chatbotContainer = document.getElementById('chatbot-container');
    const chatbotInput = document.getElementById('chatbot-input');
    const chatbotSend = document.getElementById('chatbot-send');
    const chatbotMessages = document.getElementById('chatbot-messages');

    // Preguntas frecuentes y respuestas
    const faqs = {
        "horario": "Nuestro horario es de lunes a sábado de 9:00 am a 8:00 pm.",
        "servicios": "Ofrecemos masajes, tratamientos faciales, manicura, pedicura y más.",
        "precios": "Los precios varían según el servicio. Puedes verlos en nuestra sección de servicios.",
        "membresías": "Ofrecemos membresías con descuentos especiales. Visita la sección de membresías para más información."
    };

    chatbotToggle.addEventListener('click', function() {
        chatbotContainer.classList.toggle('active');
    });

    chatbotSend.addEventListener('click', function() {
        sendMessage();
    });

    chatbotInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    function sendMessage() {
        const message = chatbotInput.value.trim().toLowerCase();
        if (message) {
            addMessage('user', message);
            chatbotInput.value = '';
            
            // Respuesta automática
            setTimeout(() => {
                let response = "Lo siento, no entendí tu pregunta. Prueba con: horario, servicios, precios o membresías.";
                
                for (const [keyword, answer] of Object.entries(faqs)) {
                    if (message.includes(keyword)) {
                        response = answer;
                        break;
                    }
                }
                
                addMessage('bot', response);
            }, 500);
        }
    }

    function addMessage(sender, text) {
        const messageElement = document.createElement('div');
        messageElement.classList.add('message', sender);
        messageElement.textContent = text;
        chatbotMessages.appendChild(messageElement);
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
    }
});
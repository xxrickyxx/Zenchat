        const chatMessages = document.getElementById('chat-messages');
        const userInput = document.getElementById('user-input');

        function appendUserMessage(message) {
            const userMessage = document.createElement('div');
            userMessage.className = 'user-message';
            userMessage.textContent = message;
            chatMessages.appendChild(userMessage);

// Get all elements with the class "user-message"
var userMessages = document.getElementsByClassName('user-message');

// Apply the specified styles to each element
for (var i = 0; i < userMessages.length; i++) {
    userMessages[i].style.margin = '20px !important';
    userMessages[i].style.whiteSpace = 'pre-wrap';
    userMessages[i].style.marginLeft = '38px';
    userMessages[i].style.marginTop = '10px';
    userMessages[i].style.padding = '8px 20px';
    userMessages[i].style.borderRadius = '15px 15px 0 15px';
    userMessages[i].style.fontWeight = '200';
    userMessages[i].style.maxWidth = '80%';
    userMessages[i].style.wordWrap = 'break-word';
    userMessages[i].style.textAlign = 'left';
    userMessages[i].style.background = 'grey';
    userMessages[i].style.color = 'white';
    userMessages[i].style.padding = '15px 20px'; // Esempio: 15px di padding sopra e sotto, 20px di padding a sinistra e a destra

}


 chatMessages.scrollTop = chatMessages.scrollHeight;

        }

        function appendBotMessage(message) {
            const botMessage = document.createElement('div');
            botMessage.className = 'bot-message';
            botMessage.textContent = message;
            chatMessages.appendChild(botMessage);
        }

function sendMessage() {
    const userQuestion = userInput.value;
    appendUserMessage(userQuestion);
chatMessages.scrollTop = chatMessages.scrollHeight;
    // Aggiungi la classe thinking-dots prima di inviare la richiesta AJAX
    appendBotMessageThinking();

// Prova a recuperare user_id dalla div con ID "tokendy"
const tokendyDiv = document.getElementById('tokendy');
let user_id;

if (tokendyDiv) {
  // Se tokendy esiste, recupera user_id da tokendy
  user_id = tokendyDiv.getAttribute('data-user-id');
} else {
  // Se tokendy non esiste, prova a recuperare user_id da tokendy2
  const tokendy2Div = document.getElementById('tokendy2');
  
  if (tokendy2Div) {
    user_id = tokendy2Div.getAttribute('data-user-id');
  } else {
    // Gestisci il caso in cui tokendy2 non esiste o non ha l'attributo data-user-id
    console.error('Nessun elemento trovato con ID "tokendy" o "tokendy2"');
  }
}

const usernameElement = document.getElementById('user_name');
const username = usernameElement.value;

// Verifica se l'elemento con ID "tokendy" è presente
if (tokendyDiv) {
    user_id = tokendyDiv.getAttribute('data-user-id');
} else {
    // Se l'elemento con ID "tokendy" non è presente, prova con "tokendy2"
    const tokendy2Div = document.getElementById('tokendy2');
    
    // Verifica se l'elemento con ID "tokendy2" è presente
    if (tokendy2Div) {
        user_id = tokendy2Div.getAttribute('data-user-id');
    } else {
        // Se nemmeno "tokendy2" è presente, gestisci il caso di fallback come desideri
        console.error('Nessun elemento trovato con ID "tokendy" o "tokendy2"');
    }
}

// Ora user_id contiene il valore desiderato o è undefined se nessun elemento è stato trovato
  //  console.log('User ID:', user_id);

//    console.log('user_name:', username);
    // Crea un oggetto con i dati da inviare
    const requestData = { user_input: userQuestion, user_id: user_id };

    // Visualizza i dati nella console
  //  console.log('Dati inviati:', requestData);
    // Esegui una richiesta AJAX o Fetch per ottenere la risposta del bot
    fetch('https://kloe.zenchat.it/ajax/bot_endpoint.php', {
        method: 'POST',
        body: JSON.stringify({ user_input: userQuestion, user_id: user_id, username: username }), // Passa user_id
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())

    .then(data => {
     //   console.log('Risposta del server:', data);
        // Simula la risposta finale di KLOE
setTimeout(() => {
    // Seleziona il contenitore del testo "Sto pensando" e rimuovi la classe che lo nasconde
    const thinkingTextContainer = document.querySelector('.thinking-text');
    if (thinkingTextContainer) {
        thinkingTextContainer.style.display = 'flex';
    }

    // Rimuovi la classe thinking-dots per far scomparire i puntini di sospensione
    const thinkingDots = document.querySelector('.thinking-dots');
    const botMessages = document.querySelectorAll('.bot-message');

    // Nascondi tutti gli elementi con la classe 'bot-message'
    /*botMessages.forEach(botMessage => {
        botMessage.style.display = 'none';
    });*/

    if (thinkingDots) {
        thinkingDots.remove(); // Rimuovi completamente l'elemento con i puntini di sospensione

    }

    const botResponse = data.bot_response;
  //  console.log('bot rensponse:', botResponse);
    appendBotMessage(botResponse);
}, 2000); // Aggiungi un ritardo di 2 secondi 


    })
    .catch(error => {
        console.error('Errore durante la richiesta al bot:', error);
    });

    userInput.value = '';
}

function appendBotMessageThinking() {
    const botMessage = document.createElement('div');
    botMessage.className = 'message';
    botMessage.innerHTML = `<div class="bot-message" ><div class="thinking-text"></div><span class="thinking-dots" style="animation: pulse 1s infinite;">...........</span></div>`;
    chatMessages.appendChild(botMessage);

    // Seleziona il contenitore del testo "Sto pensando" e nascondilo
    const thinkingTextContainer = botMessage.querySelector('.thinking-text');
    if (thinkingTextContainer) {
        thinkingTextContainer.style.display = 'none';
    }
}


        function appendBotMessage(message) {
            const botMessage = document.createElement('div');
            botMessage.className = 'message';
            const currentTimestamp = new Date();
            const formattedTimestamp = `${currentTimestamp.getFullYear()}-${currentTimestamp.getMonth() + 1}-${currentTimestamp.getDate()} ${currentTimestamp.getHours()}:${currentTimestamp.getMinutes()}:${currentTimestamp.getSeconds()}`;
            botMessage.innerHTML = `<div class="bot-message" style="
  margin:20px !important;
  white-space: pre-wrap !important;
  margin-left: auto !important;
  padding: 8px 20px !important;
  border-radius: 15px 15px 0 15px !important;
  font-weight: 200 !important;
  max-width: 80% !important;
  word-wrap: break-word !important;
  text-align: left !important;
  background: #1e88e5 !important;
  color: white !important;">${message}</div>`;
            botMessage.innerHTML += `<div class="timestamp" style="font-size: 10px;display: block;">${formattedTimestamp}</div>`; // Aggiungi un elemento separato per il timestamp
            chatMessages.appendChild(botMessage);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
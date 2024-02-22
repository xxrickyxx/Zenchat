<?php header('Access-Control-Allow-Origin: *'); 

session_start();
require_once '../conn/index.php';
$username= $_GET['username'];

$sql = "SELECT dominio FROM utenti WHERE username = :username";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':username', $username, PDO::PARAM_STR);
$stmt->execute();
$dominio_arr = $stmt->fetch();
$dominio=$dominio_arr['dominio'];

$stato=2;
$sql = "SELECT * FROM utenti WHERE dominio = :dominio AND stato = :stato";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':dominio', $dominio, PDO::PARAM_STR);
$stmt->bindParam(':stato', $stato, PDO::PARAM_STR);
$stmt->execute();
$operator = $stmt->fetchAll();

$conta_operator=count($operator);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Chat con Utente</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="../css/chat_user_new.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title><?php echo $_GET['username']; ?></title>
      <style>
    #infoDiv {
      display: none;
      border: 1px solid #ccc;
      padding: 10px;
      margin-top: 10px;
    }
  </style>
</head>

<body>

    <div id="wrapper">
        <aside id="chatList" class="closed">
            <!-- Aggiungi qui la tua lista di chat -->
            <div id="szc"></div>
        </aside>

        <main id="chatContainer">
            <header>
<button id="viewPage" class="custom-button" style="height:41px; margin-top: 20px !important; margin-bottom: -10px; eight: 41px;
    /* margin-top: -20px !important; */
    display: block;
    position: relative;"><span class="material-icons">remove_red_eye</span> User Page</button>

<button class="btn btn-primary custom-button" id="toggleChatList">
  <img src="https://kloe.zenchat.it/images/list.png" class="button-icon"> Chat List
</button>

<button id="closechat" class="custom-button" style="margin-right: 120px; display: none;">Close this chat</button>
<button  class="btn btn-primary custom-button" id="operator" style="height: 40px;"><img src="https://kloe.zenchat.it/images/ope.png" style="width:30px; margin-top:-10px;s"> </button>

<button id="toggleSoundButton" class="btn btn-primary custom-button" onclick="toggleSound()">
  <img src="https://kloe.zenchat.it/images/alert.png" class="button-icon"> Toggle Alert
</button>

            </header>
  <div id="infoDiv">
    <!-- Inserisci qui le tue informazioni -->
    <p><?php echo $conta_operator." Operator.";  ?></p>
    <?php foreach ($operator as $key) { if ($key['stato_chat']=="1"){  $stato="Online"; } else {  $stato="Offline"; } ?>

    <p><?php echo $key['username']." ".$stato; ?></p>
	<?php } ?>
  </div>
            <div id="container" style="height: 600px;">
                <!-- La tua chat si troverà qui -->
                <div id="contenuto_all"></div>
            </div>

            <footer>
                <input type="hidden" name="asdada" id="yvcfdgd">
                <textarea type="text" rows="1" spellcheck="false" autocomplete="off" placeholder="Send message..." id="messagechat"></textarea>
                <button id="send" onclick="sendMessagechat()">
                    <span class="material-icons">send</span>
                </button>
            </footer>
        </main>
    </div>
  <script>
    document.getElementById('operator').addEventListener('click', function() {
      var infoDiv = document.getElementById('infoDiv');
      if (infoDiv.style.display === 'none') {
        infoDiv.style.display = 'block';
      } else {
        infoDiv.style.display = 'none';
      }
    });
  </script>
    <script type="text/javascript">
        document.getElementById('toggleChatList').addEventListener('click', function () {
            var chatList = document.getElementById('chatList');
            chatList.classList.toggle('closed');

            var isClosed = chatList.classList.contains('closed');
            var buttonText = isClosed ? 'Open Chat List' : 'Close Chat List';
            document.getElementById('toggleChatList').innerText = buttonText;
        });
    </script>

<script>
    function sendMessagechat() {
        var message_chat = $("#messagechat").val();

        // Ottenere i parametri GET dall'URL
        var urlParams = new URLSearchParams(window.location.search);
        var username_chat = urlParams.get('username');
        var userChats = urlParams.get('userchats');

        // Esegui una richiesta AJAX per inviare il messaggio al server
        $.ajax({
            url: 'https://kloe.zenchat.it/ajax/sendMessage.php',
            method: 'POST',
            data: { message_chat: message_chat, username_chat: username_chat, user_chats: userChats },
            success: function (response) {
                $("#messagechat").val('');
                console.log("username_chat:", username_chat);
                console.log("response:", response);
            },
            error: function () {
                // Mostra un alert se la richiesta non ha successo
                alert("You are offline. Message not sent.");
            }
        });
    }
</script>


    <script>
        document.getElementById('viewPage').addEventListener('click', function () {
            // Recupera il valore di user_chats (sostituisci con il modo effettivo di ottenere questo valore)
            // Ottenere i parametri GET dall'URL
            var urlParams = new URLSearchParams(window.location.search);
            var username_chat = urlParams.get('username');
            var userChats = urlParams.get('userchats');

            // Esegui una richiesta AJAX per ottenere l'URL dal file PHP
            fetch(`https://kloe.zenchat.it/ajax/readPopit.php?user_chats=${userChats}`)
                .then(response => response.text())
                .then(url => {
                    // Apri la nuova finestra del browser con l'URL ottenuto
                    window.open(url, '_blank');

                    // Logga l'URL ottenuto nella console
                    console.log('URL ottenuto dalla risposta:', url);
                })
                .catch(error => {
                    console.error('Errore durante la richiesta del file PHP:', error);
                });
        });
    </script>

    <script>
    var suonoGiàRiprodotto = false; // Dichiarazione della variabile di controllo
    var utenteHaInteragito = false; // Flag per indicare se l'utente ha interagito
    var suonoAbilitato = true; // Flag per indicare se il suono è abilitato o disabilitato

    // Funzione per abilitare/disabilitare il suono
    function toggleSound() {
        suonoAbilitato = !suonoAbilitato; // Inverti lo stato del suono

        // Mostra un alert in base allo stato del suono
        if (suonoAbilitato) {
            alert("Sound and Windows on");
        } else {
            alert("Sound and Windows off");
        }
    }

    // Funzione per verificare se l'utente ha interagito con la pagina
    function userInteracted() {
        utenteHaInteragito = true;
    }

    // Aggiungi un gestore di eventi per rilevare l'interazione dell'utente
    document.addEventListener('click', userInteracted);

    // Esegui la funzione ogni 5 secondi
    setInterval(function () {
        var username_chat = '<?php echo $_GET["username"]; ?>';

        // Esegui una richiesta AJAX per ottenere il contenuto della pagina
        $.ajax({
            url: 'https://kloe.zenchat.it/ajax/read_operator_chat.php',
            method: 'POST',
            data: { username_chat: username_chat },
            success: function (response) {
                // Una volta ottenuto il contenuto della pagina, puoi attaccarlo all'input con ID 'yvcfdgd'
                $('#szc').html(response);

                // Controlla se la funzione suonaMP3 non è stata chiamata, la <div> con ID 'suona' è presente e l'utente ha interagito con la pagina
                if (!suonoGiàRiprodotto && $('#suona').length > 0 && utenteHaInteragito && suonoAbilitato) {
                    // Suona il file mp3.wav e imposta la variabile suonoGiàRiprodotto a true
                    var audio = new Audio('../mp3.wav');
                    audio.play();
                    suonoGiàRiprodotto = true;

                    Swal.fire({
                        title: 'Warning!',
                        text: 'There are no replied messages.',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function () {
                // Gestire eventuali errori di richiesta AJAX qui
            }
        });

    }, 2000); // 2000 millisecondi = 2 secondi
</script>

    <script type="text/javascript">
        // Dichiarazione della variabile globale per userChats

        function sendMessagechatsx(userChats) {
            // Esegui la tua richiesta AJAX

            $.ajax({
                url: 'https://kloe.zenchat.it/ajax/read_chat_operator_all.php',
                method: 'POST',
                data: { username_chat: '<?php echo $_GET["username"]; ?>', user_chats: userChats },
                success: function (response) {
                    // Svuota il contenuto precedente della div
                    $('#contenuto_all').empty();

                    // Aggiungi il nuovo contenuto alla div con ID 'contenuto_all'
                    $('#contenuto_all').html(response);

                    // Aggiorna l'URL con il parametro userchats
                    var newUrl = window.location.href.split('?')[0] + '?username=' + encodeURIComponent('<?php echo $_GET["username"]; ?>') + '&userchats=' + encodeURIComponent(userChats);
                    history.pushState({}, '', newUrl);

                    //console.log("user_chats:", response);
                },
                error: function () {
                    // Gestire eventuali errori di richiesta AJAX qui
                }
            });
        }

        $(document).ready(function () {
            // Esegui la richiesta AJAX ogni 2 secondi
            setInterval(function () {
                // Ottieni il valore di userChats da qualche parte (ad esempio, una variabile globale o un elemento HTML)
                var urlParams = new URLSearchParams(window.location.search);
                var userChats = urlParams.get('userchats');
                //   console.log("user_chats:", userChats);
                // Chiamare la funzione sendMessagechatsx con il valore di userChats
                sendMessagechatsx(userChats);
            }, 2000); // 2000 millisecondi corrispondono a 2 secondi
        });
    </script>


    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
        }

        #wrapper {
            display: flex;
            flex-direction: column; /* Set flex direction to column for mobile */
        }

        #chatList {
            /* Il tuo stile esistente */

            overflow-y: hidden;
            transition: max-height 0.5s ease;
        }

        #chatList.closed {
            max-height: 0;
        }


        button#toggleChatList {
            margin-bottom: 8px;
            height: 41px;
            background: purple;
            border: none;
        }
        button#toggleSoundButton {
            margin-bottom: 8px;
            height: 41px;
            background: #992aa5c7;
            border: none;
            color: white;
            height: 41px;
        }

        textarea#messagechat {
            margin-top: 20px;
            margin-bottom: -12px;
        }

        #chatContainer {
            flex: 1;
            padding: 10px;
            overflow-y: auto;
        }

        header,
        footer {
            background-color: #f0f0f0;
            padding: 10px;
        }

        #container {
            border: 1px solid #ccc;
            height: 300px;
            overflow-y: auto;
        }

        textarea {
            width: calc(100% - 50px);
            padding: 5px;
            margin: 5px;
        }

        button {
            padding: 5px;
            margin: 5px;
        }

        #chatList {
            overflow-y: auto;
        }

        #chatList div {
            border-bottom: 1px solid #ccc;
            padding: 8px;
            cursor: pointer;
        }

        #chatList div:hover {
            background-color: #f0f0f0;
        }

        aside#chatList {
            max-height: 800px;
        }

        .ghghghg {
            background-color: #3498db;
            color: #fff;
            padding: 10px;
            margin: 5px;
            cursor: pointer;
        }

        #audioButton {
            padding: 10px;
            font-size: 16px;
            cursor: pointer;
            padding: 6px 20px;
            border: none;
            border-radius: 4px;
            outline:

}

/* Media query for screens with a maximum width of 600 pixels (typical mobile devices) */
@media only screen and (max-width: 600px) {
    #wrapper {
        flex-direction: column; /* Change flex direction to column for mobile */
    }

    #chatList {
        width: 100%; /* Set the width to 100% for mobile */
    }
}
.custom-button {
  background-color: #3498db !important;
  color: #ffffff !important;
  border: 1px solid #3498db !important;
  padding: 10px 20px !important;
  font-size: 16px !important;
  cursor: pointer !important;
  border-radius: 5px;
  margin: 5px !important;
  transition: background-color 0.3s ease !important;

}

.custom-button:hover {
  background-color: #2980b9 !important;
}

.button-icon {
  width: 20px !important;
  filter: invert(100%) !important;
  margin-right: 5px !important;
}

#closechat {
  /* Aggiungi stili specifici per il bottone closechat se necessario */
}


</style>

</html>
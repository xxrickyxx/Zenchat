<?php if (!$_COOKIE['user_chat']){ $user_chat =  base64_encode('kloe_chat'); setcookie("user_chat", $user_chat, time() + 3600, "/"); } else {   $user_chat=$_COOKIE['user_chat'];  }



require_once '../conn/index.php'; 

include('lang.php');

$username=$_GET['username'];
$sql = "SELECT * FROM utenti WHERE username = :username ";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':username', $username, PDO::PARAM_STR);
$stmt->execute();
$op = $stmt->fetchAll();

$dominio=$op[0]['dominio'];

$sql = "SELECT * FROM utenti WHERE dominio = :dominio";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':dominio', $dominio, PDO::PARAM_STR);
$stmt->execute();
$oppx = $stmt->fetchAll();

foreach ($oppx as $key) {
    $doms = $key['dominio'];

    // Select username where dominio is equal to $doms and stato is equal to 1
    $sql = "SELECT username FROM utenti WHERE dominio = :dominio AND stato = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':dominio', $doms, PDO::PARAM_STR);
    $stmt->execute();
    $opp = $stmt->fetchAll();

    if (!empty($opp)) {
        // If $opp is not empty, assign the username to $dom
        $dom = $opp[0]['username'];
    } else {
        
        $setting=false;
    }
}

        $sqlCheck = "SELECT * FROM setting WHERE id_user = :id_user AND stato = 1";
        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->bindParam(':id_user', $dom, PDO::PARAM_STR);
        $stmtCheck->execute();
        $setting = $stmtCheck->fetch();


if (!empty($setting)) { ?>


<style type="text/css">
  #sofia-chat .chat__container .chat__header {
    background: <?php echo $setting['chatcolor']; ?> !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-around !important;
    padding: 0 0 0 0px !important;
    user-select: none !important;
    color: #f8f8f8 !important;
    font-weight: 600 !important;
} 
</style>


<?php } else { ?>

<style type="text/css">
  #sofia-chat .chat__container .chat__header {
  background: #1e88e5 !important;
  display: flex !important;
  align-items: center !important;
  justify-content: space-around !important;
  padding: 0 0 0 0px !important;
  user-select: none !important;
  color: #f8f8f8 !important;
  font-weight: 600 !important;
}
</style>

<?php }


// Ottieni l'URL corrente
//$current_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

// Rimuovi il prefisso "http://" o "https://"
//$cleaned_url = preg_replace('#^https?://#', '', $current_url);

// Utilizza parse_url per estrarre il dominio
//$parsed_url = parse_url($current_url);
//$domain = $parsed_url['host'];

$dom=$op[0]['dominio'];

$sql = "SELECT * FROM utenti WHERE dominio = :domain AND stato_chat=1";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':domain', $dom, PDO::PARAM_STR);
$stmt->execute();
$domain_x = $stmt->fetchAll();

// Funzione per ottenere la lingua preferita del browser
function getBrowserLanguage() {
    $languages = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'en';
    $languages = explode(',', $languages);
    $preferred_language = $languages[0];
    return substr($preferred_language, 0, 2);
}

// Ottieni la lingua preferita del browser dell'utente
$browser_language = getBrowserLanguage();

// Imposta la lingua predefinita nel caso in cui non sia supportata
$selected_language = isset($translations[$browser_language]) ? $browser_language : 'en';

// Seleziona le traduzioni per la lingua corrente
$current_translations = $translations[$selected_language];


?>

<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" referrerpolicy="no-referrer"></script> -->
<link rel="stylesheet" href="https://kloe.zenchat.it/css/Swiper.css" />
<link href="https://kloe.zenchat.it/css/dash.css" rel="stylesheet">
 <link rel="stylesheet" href="https://kloe.zenchat.it/css/chat_user_new.css" />
<div id="container-sofia">
  <div id="sofia-chat" data-chat style="display:none" class="closed">
    <div id="chat__sidebar">
      <div id="chat__sidetop" style="display: none;">
        <div class="chat__input">
          <form id="chat__searchform" style="display: none;">
            <input type="text" id="chat__search" data-search placeholder="Cerca un prodotto...">
          </form>
          <span class="material-icons" style="display:none;"></span>
          <p>Ticket</p>
        </div>
        <span class="material-icons" id="chat__sideclose" data-close>close</span>
      </div>
      <div id="chat__sidecontent">

  <form id="ticketForm" style="max-width: 400px; margin: 20px auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">

    <label for="email" style="display: block; margin-bottom: 8px;">Email (obbligatoria):</label>
    <input type="email" id="email" name="email" required style="width: 100%; padding: 8px; margin-bottom: 12px; box-sizing: border-box;">

    <label for="phone" style="display: block; margin-bottom: 8px;">Numero di Telefono:</label>
    <input type="tel" id="phone" name="phone" style="width: 100%; padding: 8px; margin-bottom: 12px; box-sizing: border-box;">

    <label for="message" style="display: block; margin-bottom: 8px;">Messaggio:</label>
    <textarea id="message" name="message" rows="4" required style="width: 100%; padding: 8px; margin-bottom: 12px; box-sizing: border-box;"></textarea>

    <button type="button" onclick="inviaTicket()" style="background-color: #1e88e5; color: white; padding: 12px 15px; border: none; border-radius: 4px 4px 4px; cursor: pointer; display: block; margin-top: 10px;">Invia Ticket</button>
  </form>
        <p class="chat__placeholder"></p>
        <div class="swiper slideProdottiSofia">
          <div class="swiper-wrapper"></div>
          <div class="swiper-pagination"></div>
        </div>
        <div data-ticketcontainer>
          <h1>Richiedi assistenza</h1>
          <div class="divinput">
            <label for="ticketemail">Email</label>
            <input type="text" name="ticketemail" id="ticketemail">
          </div>
          <div class="divinput no-col">
            <label for="isassistenza">Voglio assistenza su uno dei miei prodotti</label>
            <input type="checkbox" name="isassistenza" id="isassistenza">
          </div>
          <div class="divinput" data-ordini>
            <label for="ordiniselect">Ordini</label>
            <select name="ordiniselect" id="ordiniselect"></select>
          </div>
          <div class="divinput divinput-textarea">
            <label for="messaggioticket">Messaggio</label>
            <textarea name="messaggioticket" id="messaggioticket"></textarea>
          </div>
          <button data-sendticket>Invia il ticket</button>
        </div>

        <div data-trackingcontainer>
          <h1>Ecco il tracking dei tuoi ultimi ordini</h1>
          <div class="swiper slideTrackingSofia">
            <div class="swiper-wrapper tracking"></div>
            <div class="swiper-pagination pag-tracking"></div>
          </div>
        </div>


      </div>
    </div>
    <div class="chat__container">
      <div class="chat__header">
        <?php if (!$domain_x){ ?>
       <!-- <span id="chat__sideopen"><span class="material-icons" style="transform: rotateY(-180deg)">login</span></span>
        <span id="chat__sideopen__mobile"><span class="material-icons" style="transform: rotateY(0deg)">login</span></span>-->
          <span id="chat__sideopen" style="display:none;">
    <span class="material-icons" style="transform: rotateY(-180deg)" disabled>login</span>
  </span>

  <span id="chat__sideopen__mobile" style="display:none;" disabled>
    <span class="material-icons" style="transform: rotateY(0deg)" disabled>login</span>
  </span>
      <?php } else { ?>
  <span id="chat__sideopen" style="display:none;">
    <span class="material-icons" style="transform: rotateY(-180deg)" disabled>login</span>
  </span>

  <span id="chat__sideopen__mobile" style="display:none;" disabled>
    <span class="material-icons" style="transform: rotateY(0deg)" disabled>login</span>
  </span>

    <?php  } ?>
    <?php if (!empty($setting['img'])) { ?>
        <div class="chat__flex"><img src="https://kloe.zenchat.it/ajax/<?php echo $setting['img']; ?>" alt="kloe">
    <?php } else { ?>
        <div class="chat__flex"><img src="https://kloe.zenchat.it/images/kloe.gif" alt="kloe">
     <?php } ?>
          <?php if (!!$domain_x){ ?>
          <p><?php echo $current_translations['Assistente']; ?></p>
        <?php } else { ?>
          <p><?php echo $current_translations['Assistente']." " .$current_translations['digital'];  ?> <?php if (!!$setting['assistantgpt']) { echo $setting['assistantgpt']; } else { echo "Kloe"; } ?></p>

        <?php } ?>
        </div>
        <span class="material-icons" id="chat__close">close</span>
      </div>
      <div class="chat__body" data-chat-body >
      <input type="hidden" name="sasad" id="user_name"  value="<?php echo  $_GET["username"]; ?>">
      <input type="hidden" name="user_chats" id="user_chats" value="<?php echo  $user_chat; ?>">
      </div>
      <?php if (!!$domain_x){ ?>
        <div class="container">
      <div id="dsddsd" style=" overflow: scroll; max-height: 310px; width:280px; margin:5px;" class="user-msg"></div>
    </div>
      <div class="chat__bottom">
        <input type="hidden" name="asda" id="user_chats" value="<?php echo $user_chat; ?>">
        <textarea id="chat__textarea" placeholder="<?php echo $current_translations['write_message']; ?>" minlength="5" data-textarea class="chat__textarea" rows="1"></textarea>
        <button class="chat__send"  id="chat__send" data-chat-send><span class="material-icons">send</span></button>
      </div>

    <?php } else { ?> 
        <div class="container" style="display:none;">
      <div id="dsddsd" style=" overflow: scroll; max-height: 310px; width:280px; margin:5px;" class="user-msg"></div>
    </div>
      <div class="chat__bottom">
        <input type="hidden" name="asda" id="user_chats" value="<?php echo $user_chat; ?>">
        <textarea style="display:none;" id="chat__textarea" placeholder="<?php echo $current_translations['write_message']; ?>" minlength="5" data-textarea class="chat__textarea" rows="1"></textarea>
        <button class="chat__send" style="display:none;" onclick="inviaRichiestaPost()" id="chat__send" data-chat-send><span class="material-icons">send</span></button>
      </div>
                       <div class="card">
                            <div class="card-body little-profile text-center">

                            <div class="container">
                                <div class="chat-messages" id="chat-messages" style="max-height: 340px; overflow-y: auto;">
                                    <!-- Messaggi di chat verranno visualizzati qui -->
                                </div>
                                <input type="text" id="user-input" style="height:40px !important; font-size: 14px; background-color:#f8f8f8; width:80%;"   placeholder="<?php echo $current_translations['write_message']; ?>">
                                <button class="btn btn-primary" onclick="sendMessage()"><span class="material-icons">send</span></button>
                            </div>

                            </div>
                        </div>



    <?php } ?>
    </div>

  </div>

  <button id="chat__toggle"></button>
<script type="text/javascript">
  setInterval(function() {
    // Codice aggiunto per eseguire l'altro AJAX quando si invia un messaggio
    var user_chats = $("#user_chats").val();
    $.ajax({
        url: "https://kloe.zenchat.it/ajax/read_chat_user.php",
        type: "POST",
        data: { user_chats: user_chats },
        success: function (response) {
            // Inserisci il contenuto nella tua pagina
            $("#dsddsd").html(response);
         // var response = "<?php echo $username.'-'.$op[0]['username']; ?>";
           // console.log("response:", response);
           
        },
      /*  error: function () {
            // Gestisci eventuali errori
            alert("Si è verificato un errore durante la richiesta AJAX.");
        }*/
    });
}, 2000); 
</script>
<script>
    // Questo codice va in una sezione JavaScript globale del tuo documento HTML
    $(document).ready(function() {
        $(".open-chat").click(function() {
            var selectedUsername = $(this).data('username');
            $("#send").data('username', selectedUsername); // Imposta il nome utente selezionato per la chat
        });
    });
</script>
 <script>
    // Trova tutti gli elementi con attributo "disabled"
    var elementsWithDisabled = document.querySelectorAll('[disabled]');

    // Disabilita i link contenenti l'attributo "disabled" e rimuove l'elemento dopo 1 secondo
    elementsWithDisabled.forEach(function(element) {
      element.style.pointerEvents = 'none'; // Imposta il puntatore a "none" per disabilitare il clic
      element.style.opacity = '0.5'; // Opacità ridotta per indicare che il link è disabilitato

      // Rimuovi l'elemento dopo 1 secondo
      setTimeout(function() {
        element.parentNode.removeChild(element); // Rimuovi l'elemento dal DOM
      }, 1000);
    });
  </script>
  <script>
    function inviaTicket() {
      // Recupera i dati del form
      var email = document.getElementById('email').value;
      var phone = document.getElementById('phone').value;
      var message = document.getElementById('message').value;
      var user_chats = $("#user_chats").val();
      // Puoi fare ulteriori controlli sui dati se necessario

      // Invia i dati al server usando AJAX
      var xhr = new XMLHttpRequest();
      xhr.open('POST', 'https://kloe.zenchat.it/ajax/sendTicket.php', true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
          // Gestisci la risposta dal server se necessario
          console.log(xhr.responseText);
        }
      };
      xhr.send('email=' + encodeURIComponent(email) + '&phone=' + encodeURIComponent(phone) + '&message=' + encodeURIComponent(message) + '&user_chats=' + user_chats);
    }
  </script>
<script>
    // Funzione per ottenere automaticamente l'URL dell'utente dalla finestra del browser
    function ottieniURLUtente() {
        return window.location.href;
    }

    // Funzione per inviare la richiesta POST
    function inviaRichiestaPost(url, token) {
        // Dati da inviare
        var data = {
            url: url,
            token: token
        };

        // Effettua la richiesta POST
        $.ajax({
            type: 'POST',
            url: 'https://kloe.zenchat.it/ajax/poPit.php',  // Assicurati che questo sia il percorso corretto al tuo file PHP
            data: data,
            success: function(response) {
                //console.log('Richiesta POST completata con successo', response);
                // Puoi aggiungere ulteriori azioni in base alla risposta del server
            },
            error: function(error) {
                console.error('Errore durante la richiesta POST', error);
            }
        });
    }

    // Esempio di utilizzo
    var urlUtente = ottieniURLUtente();
    var token = $("#user_chats").val();  
  //  console.log('popit', urlUtente + ' - ' + token);
    inviaRichiestaPost(urlUtente, token);
</script>
<style type="text/css">
  .sofia-msg {
    color: black;
}
#sofia-chat .chat__container .chat__body .sofia-msg p.content {

    color: black !important; 

}
.thinking-text{

  color: black !important; 
}
.timestamp{

  color: black !important; 
}

.bot-message {

  color: black !important; 
}
button.btn.btn-primary {
    background: #f8f8f8 !important;
    margin-bottom: -10px;
}
</style>
<script src="https://kloe.zenchat.it/js/dash.js"></script>
<script defer src="https://kloe.zenchat.it/js/newSofia.js"></script>
  <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

</div>
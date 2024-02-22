<?php
session_start();
require_once '../conn/index.php';

$username = $_GET['username'];
$sql = "SELECT password, dominio FROM utenti WHERE username = :username";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':username', $username, PDO::PARAM_STR);
$stmt->execute();
$verify_user = $stmt->fetch();

$pass_verify=$_SESSION['pass']; 

if ($verify_user['password']===hash('sha256', $pass_verify)) {

$sql = "SELECT id, username FROM utenti WHERE dominio = :dominio AND stato=1";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':dominio', $verify_user['dominio'], PDO::PARAM_STR); // :username è il placeholder per il parametro
$stmt->execute();
$bot_response = $stmt->fetch();


    $gfd = "KLOE: Mi dispiace, non ho capito la tua domanda. Puoi formulare in modo diverso?";
    $sql = "SELECT * FROM user_bot_interactions WHERE user_id='" . $bot_response['username'] . "' AND bot_response='" . $gfd . "'";
    $results2 = $conn->query($sql);
    $interactions2 = $results2->fetchAll(PDO::FETCH_ASSOC);
    $conta_interactions2 = count($interactions2);

if (!$interactions2){


  $gfd =  "KLOE: I didn t understand how to formulate the question";
    $sql = "SELECT * FROM user_bot_interactions WHERE user_id='" . $bot_response['username'] . "' AND bot_response='" . $gfd . "'";
    $results2 = $conn->query($sql);
    $interactions2 = $results2->fetchAll(PDO::FETCH_ASSOC);
    $conta_interactions2 = count($interactions2);
}



    // Paginazione
    $items_per_page = 3;
    $total_results2 = count($interactions2);
    $total_pages2 = ceil($total_results2 / $items_per_page);
    $current_page2 = isset($_GET['pagel']) ? $_GET['pagel'] : 1;

    // Calcola l'offset per la query
    $offset2 = ($current_page2 - 1) * $items_per_page;

    // Estrai solo i risultati per la pagina corrente
    $interactions2_page = array_slice($interactions2, $offset2, $items_per_page);

?>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>



<!-- Utilizza Bootstrap per visualizzare i dati come una "tabella" -->
<div class="table-responsive mt-4" id="card-body">
	<!-- Aggiungi il pulsante e il campo di ricerca -->
<input type="text" id="search" class="form-control mb-2" placeholder="Search..."  style="width: 150px; margin-top: 10px;">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Interactions</h5>
        </div>
        <div class="card-body" >
            <?php foreach ($interactions2_page as $key) { $ris=str_replace('TU:', 'Request:', $key['user_input']);  ?>
                <div class="row mb-2">

                    <div class="col-md-6"><p><?php echo $ris; ?></p></div>
                    <textarea name="solve" id="solve"  rows="4" cols="50">Write Response</textarea>
                    <div class="col-md-6">
                    	<input type="hidden" name="idsolve" value="<?php echo $key['id']; ?>">
                        <button class="btn btn-primary btn-sm" id="addsolve" style="background:transparent; border:none;"><img src="https://kloe.zenchat.it/images/add.png" style="width:50px;"> </button>
                        <button class="btn btn-primary btn-sm" id="delsolve" style="background:transparent; border:none;"><img src="https://kloe.zenchat.it/images/del.png" style="width:19px;"> </button>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

<!-- Aggiungi la paginazione -->
<nav aria-label="Page navigation example" style="margin-top: 10px;">
    <ul class="pagination">
        <?php for ($i = 1; $i <= $total_pages2; $i++) { ?>
            <li class="page-item <?php echo ($i == $current_page2) ? 'active' : ''; ?>">
                <a class="page-link" href="https://kloe.zenchat.it/ajax/unsolvedQuestion.php?username=<?php echo $username; ?>&pagel=<?php echo $i; ?>" data-page="<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php } ?>
    </ul>
</nav>

</div>



<script>
$(document).ready(function () {
    // Gestisci il click sui link di paginazione
    $('.pagination a.page-link').on('click', function (e) {
        e.preventDefault(); // Impedisci il comportamento predefinito del link

        var page = $(this).data('page'); // Recupera il numero di pagina dalla data attribute
        loadPage(page); // Chiama la funzione per caricare la pagina dinamicamente
    });

    // Funzione per caricare la pagina in modo dinamico
    function loadPage(page) {
        var username = "<?php echo $_GET['username']; ?>";
        var url = `https://kloe.zenchat.it/ajax/unsolvedQuestion.php?username=${username}&pagel=${page}`;

        $.ajax({
            type: 'GET',
            url: url,
            success: function (response) {
                // Aggiorna la parte HTML con il risultato
                $('#card-body').html(response);
            },
            error: function (xhr, status, error) {
                console.error('Errore durante la richiesta AJAX:', error);
                console.log('Dettagli errore:', xhr.responseText);
                // Puoi aggiungere ulteriori azioni di gestione degli errori se necessario
            }
        });
    }
});

</script>
<script>
    $(document).ready(function() {
        // Aggiungi il listener per la ricerca
        $('#search').on('input', function() {
            var searchText = $(this).val().toLowerCase();

            // Nascondi tutte le righe
            $('.row').hide();

            // Filtra e mostra solo le righe che corrispondono alla ricerca
            $('.row').filter(function() {
                return $(this).text().toLowerCase().includes(searchText);
            }).show();
        });
    });
</script>
<script>
    $(document).ready(function() {
        // Aggiungi il listener per i pulsanti "Add Solve" e "Del Solve"
        $('.btn-primary.btn-sm').on('click', function() {
            var username = "<?php echo $_GET['username']; ?>";
            var row = $(this).closest('.row');
            var textarea = row.find('textarea[name="solve"]');
            var response = textarea.val();
            var id = row.find('input[name="idsolve"]').val();
            var buttonId = $(this).attr('id'); // Get the ID of the clicked button

            // Effettua la richiesta AJAX solo se è stato premuto "Add Solve" o "Del Solve"
            if (buttonId === 'addsolve' || buttonId === 'delsolve') {
                $.ajax({
                    type: 'POST',
                    url: 'https://kloe.zenchat.it/ajax/solve.php',
                    data: {
                        username: username,
                        response: response,
                        id: id,
                        buttonId: buttonId // Pass the ID of the clicked button
                    },
                    success: function(response) {
                        // Gestisci la risposta da solve.php se necessario
                      //  console.log(response);
                        // Mostra un alert con un messaggio
						alert("Success!");
						// Ricarica la pagina corrente forzando il caricamento dalla cache del server
						location.reload(true);

                    },
                    error: function(error) {
                        console.error('Errore nella richiesta AJAX:', error);
                    }
                });
            }
        });

        // Aggiungi il listener per la ricerca
        $('#search').on('input', function() {
            var searchText = $(this).val().toLowerCase();

            // Nascondi tutte le righe
            $('.row').hide();

            // Filtra e mostra solo le righe che corrispondono alla ricerca
            $('.row').filter(function() {
                return $(this).text().toLowerCase().includes(searchText);
            }).show();
        });
    });
</script>




<?php } ?>

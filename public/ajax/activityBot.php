<?php
session_start();
require_once '../conn/index.php';

// Imposta il numero di elementi per pagina
$items_per_page = 10;

$username = $_GET['username'];

$sql = "SELECT password, dominio FROM utenti WHERE username = :username";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':username', $username, PDO::PARAM_STR); // :username è il placeholder per il parametro
$stmt->execute();
$verify_user = $stmt->fetch();

$pass_verify=$_SESSION['pass']; 

if ($verify_user['password'] === hash('sha256', $pass_verify)) {

$sql = "SELECT id, username FROM utenti WHERE dominio = :dominio";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':dominio', $verify_user['dominio'], PDO::PARAM_STR); // :username è il placeholder per il parametro
$stmt->execute();
$bot_response = $stmt->fetch();

    // Conta il numero totale di risultati
    $sql_count = "SELECT COUNT(*) as count FROM bot_responses WHERE user_id='" . $username . "'";
    $result_count = $conn->query($sql_count);

    if ($result_count) {
        $count_result = $result_count->fetch(PDO::FETCH_ASSOC);
        $total_results = $count_result['count'];
    } else {
        // Gestisci l'errore della query di conteggio
        die("Errore nella query di conteggio: " . $conn->error);
    }

    // Calcola il numero totale di pagine
    $total_pages = ceil($total_results / $items_per_page);

    // Ottieni il numero di pagina corrente dalla query strins
    $current_page = isset($_GET['page']) ? $_GET['page'] : 1;

    // Calcola l'offset per la query
    $offset = ($current_page - 1) * $items_per_page;

    // Query con offset e limit


    $sql = "SELECT * FROM bot_responses WHERE user_id='" . $bot_response['username'] . "' LIMIT $items_per_page OFFSET $offset";
    $results = $conn->query($sql);


    if ($results) {
        $bot = $results->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Gestisci l'errore della query principale
        die("Errore nella query principale: " . $conn->error);
    }
?>

<!-- Aggiungi Bootstrap CSS e JavaScript al tuo progetto -->
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>
<!-- Aggiungi i pulsanti e il modulo di ricerca -->
<div class="modal-contents" style="margin: 20px; padding: 20px; border: 1px solid #ccc; color: red; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">Max 1000 Record.<p> For more records contact.</p></div>

<form id="dataForm" style="margin: 20px; padding: 20px; border: 1px solid #ccc; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
    <label for="url" style="font-size: 16px; margin-bottom: 10px; display: block; color: #333;">Enter the URL:</label>
    <input type="text" id="url" name="url" required style="width: 100%; padding: 10px; font-size: 16px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;">
    <button type="button" onclick="extractData()" style="background-color: #4CAF50; color: white; padding: 10px 20px; font-size: 16px; border: none; border-radius: 5px; cursor: pointer;">Extract Data</button>

    <!-- Elemento di caricamento -->
    <div id="loading" style="display: none; margin-top: 10px;">
        Loading... Please wait.
    </div>
</form>

        <div class="modal-contents" style="margin: 20px; padding: 20px; border: 1px solid #ccc; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
            <div class="modal-headers">
                <h5 class="modal-titles" id="addModalLabels">Add New Response</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label for="newResponse">Response:</label>
                        <textarea class="form-control" id="newResponse"></textarea>
                    </div>
                    <button style="background: transparent; border:none;" type="button" id="addtext"><img src="https://kloe.zenchat.it/images/add.png" style="width: 50px;"></button>
                </form>
            </div>
        </div>


<div class="container mt-4">
    <div class="row">
        <?php foreach ($bot as $key) { ?>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $key['keyword']; ?></h5>
                        <p class="card-text"><?php echo $key['response']; ?></p>
                        <button class="btn btn-primary btn-sm edit-btn" data-toggle="modal" data-target="#editModal" data-keyword="<?php echo $key['keyword']; ?>" data-response="<?php echo $key['response']; ?>">Edit</button>
                         <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $key['id']; ?>">Delete</button>

                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <!-- Aggiungi la paginazione -->
<nav aria-label="Page navigation example">
    <ul class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
            <li class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                <!-- Modifica il link di paginazione per utilizzare JavaScript -->
                <a class="page-link" href="https://kloe.zenchat.it/ajax/activityBot.php?username=<?php echo $username; ?>&page=<?php echo $i; ?>" data-page="<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php } ?>
    </ul>
</nav>
</div>
<!-- Finestra modale per l'editor -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Keyword</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label for="editKeyword">Keyword:</label>
                        <input type="text" class="form-control" id="editKeyword" readonly>
                    </div>
                    <div class="form-group">
                        <label for="editResponse">Response:</label>
                        <textarea class="form-control" id="editResponse"></textarea>
                    </div>
                    <button type="button" class="btn btn-primary" id="saveChangesBtn">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Finestra modale per l'aggiunta di un nuovo response -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Add New Response</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label for="newResponse">Response:</label>
                        <textarea class="form-control" id="newResponse"></textarea>
                    </div>
                    <button type="button" class="btn btn-primary" id="addResponseBtn">Add Response</button>
                </form>
            </div>
        </div>
    </div>
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
            $.ajax({
                type: 'GET',
                url: 'https://kloe.zenchat.it/ajax/activityBot.php?username=<?php echo $_GET['username']; ?>&page=' + page,
                success: function (response) {
                    // Aggiorna la parte HTML con il risultato
                    $('#contentContainer').html(response);
                },
                error: function (error) {
                    console.log('Errore durante la richiesta AJAX:', error);
                }
            });
        }
    });
</script>

<script type="text/javascript">
    function extractData() {
        // Ottieni l'URL inserito dall'utente
        var url = $('#url').val();

        // Mostra l'elemento di caricamento
        $('#loading').show();

        // Invia la richiesta AJAX al server
        $.ajax({
            type: 'POST',
            url: 'https://kloe.zenchat.it/ajax/engine.php?username=<?php echo $username; ?>',
            data: { url: url },
            success: function (response) {
                // Aggiorna la parte HTML con il risultato
                $('#result').html(response);

                // Nascondi l'elemento di caricamento dopo il completamento della richiesta
                $('#loading').hide();
            },
            error: function (error) {
                console.log('Errore durante la richiesta AJAX:', error);

                // Nascondi l'elemento di caricamento anche in caso di errore
                $('#loading').hide();
            }
        });
    }
</script>



<script>
$(document).ready(function () {
    var editorInstance;

    // Aggiungi il listener per la ricerca
    $('#search').on('input', function () {
        var searchText = $(this).val().toLowerCase();
        $('.card').parent().hide();

        $('.card').filter(function () {
            return $(this).text().toLowerCase().includes(searchText);
        }).parent().show();
    });

    // Gestisci il click sul pulsante "Edit" per popolare la finestra modale
    $('.edit-btn').on('click', function () {
        var keyword = $(this).data('keyword');
        var response = $(this).data('response');
        $('#editKeyword').val(keyword);

        if (editorInstance) {
            editorInstance.setData(response);
        } else {
            ClassicEditor
                .create(document.querySelector('#editResponse'))
                .then(editor => {
                    editorInstance = editor;
                    editorInstance.setData(response);
                })
                .catch(error => {
                    console.error(error);
                });
        }
    });

// Gestisci il click sul pulsante "Save Changes"
$('#saveChangesBtn').on('click', function () {
    if (editorInstance) {
        var editedResponse = editorInstance.getData();
        var keyword = $('#editKeyword').val().toLowerCase(); // Converto la keyword a minuscolo

        // Trova tutte le card che contengono la keyword
        var cards = $('.card:contains("' + keyword + '")');

        // Itera attraverso ciascuna card e aggiorna il testo
        cards.each(function () {
            var cardBody = $(this).find('.card-body');
            var existingKeyword = cardBody.find('.card-title').text().toLowerCase(); // Converto la keyword esistente a minuscolo

            // Verifica se il testo corrente corrisponde alla keyword
            if (existingKeyword === keyword) {
                // Aggiorna il testo dell'elemento HTML precedente
                cardBody.find('.card-text').text(editedResponse);
            }
        });

        $.ajax({
            type: 'POST',
            url: 'https://kloe.zenchat.it/ajax/writeActivity.php',
            data: {
                keyword: keyword,
                edit: editedResponse,
                username: '<?php echo $username; ?>'
            },
            success: function (responsew) {
                alert('Successfully edited!');
                //  console.log('Risposta AJAX:', responsew);
            },
            error: function () {
                alert('Errore durante la richiesta AJAX.');
            }
        });

        $('#editModal').modal('hide');
    } else {
        console.error('CKEditor non è completamente caricato.');
    }
});


// Gestisci il click sul pulsante "Delete"
$('.delete-btn').on('click', function () {
    var idToDelete = $(this).data('id'); // Ottieni l'ID dalla data attribute

    // Chiedi conferma prima di procedere con la cancellazione
    if (confirm('Are you sure you want to delete this activity?')) {
        $.ajax({
            type: 'POST',
            url: 'https://kloe.zenchat.it/ajax/deleteActivity.php',
            data: {
                id: idToDelete,
                username: '<?php echo $username; ?>'
            },
            success: function (response) {
                alert('Successfully deleted!');
                // Aggiorna la pagina o effettua altre azioni necessarie dopo la cancellazione
                location.reload();
               //console.log('Risposta AJAX:', response);
            },
            error: function () {
                alert('Errore durante la richiesta AJAX.');
            }
        });
    }
});


});
</script>
<script>
    $(document).ready(function() {
        // Gestisci il clic sul pulsante con l'ID addtext
        $('#addtext').on('click', function() {
            // Ottieni il valore dalla textarea con l'ID newResponse
            var responseText = $('#newResponse').val();

            // Assicurati che la variabile $username sia definita o assegnale al valore desiderato
            var username = '<?php echo $username; ?>'; // Sostituisci con il valore effettivo o ottieni dinamicamente

            // Controllo se la lunghezza del testo è almeno 50 caratteri
            if (responseText.length < 50) {
                alert('The text must contain at least 50 characters.');
                return; // Esce dalla funzione in caso di errore
            }

            // Esegui la richiesta AJAX
            $.ajax({
                type: 'POST',
                url: 'https://kloe.zenchat.it/ajax/addActivity.php',
                data: {
                    response: responseText,
                    username: username
                },
                success: function(response) {
                    // Gestisci la risposta dal server
                    // console.log(response);

                    // Aggiungi un alert di successo
                    alert('Add Success!');
                },
                error: function(error) {
                    // Gestisci gli errori
                    console.error('Errore durante la richiesta AJAX:', error);
                }
            });
        });
    });
</script>


<style type="text/css">

@media screen and (max-width: 600px) {
    ul.pagination {
    width: 304px;
    overflow-y: scroll;
}
}
</style>
<?php } ?>

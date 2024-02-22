<?php
ini_set('session.cookie_domain', '.zenchat.it');
session_start();

require_once '../conn/index.php';

$username=base64_decode($_SESSION['token']);

// Utilizziamo un prepared statement con PDO
$sql = "SELECT password, username, dominio, data_scadenza FROM utenti WHERE username = :username and stato=1";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':username', $username, PDO::PARAM_STR); // :username è il placeholder per il parametro
$stmt->execute();
$verify_user = $stmt->fetch();

$pass_verify=$_SESSION['pass']; 

$datanow=date('Y-m-d H:m:s');

if ($verify_user['password']===hash('sha256', $pass_verify) && $verify_user['data_scadenza'] >= $datanow) {

$sql = "SELECT * FROM setting WHERE id_user = :username";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':username', $verify_user['username'], PDO::PARAM_STR); // :username è il placeholder per il parametro
$stmt->execute();
$setting = $stmt->fetch();

$updateQuery = "UPDATE utenti SET data = NOW() WHERE username = :username";
$updateStmt = $conn->prepare($updateQuery);
$updateStmt->bindParam(':username', $username, PDO::PARAM_STR);
$updateStmt->execute();

$sql = "SELECT * FROM user_bot_interactions where user_id='" . $username . "'";
$results = $conn->query($sql);

if ($results) {
    $interactions = $results->fetchAll(PDO::FETCH_ASSOC);
    $conta_interactions=count($interactions);
} else {
    // Gestisci l'errore della query, ad esempio:
    die("Errore nella query: " . $conn->error);
}

$sql = "SELECT * FROM bot_responses where user_id='" . $username . "'";
$results = $conn->query($sql);

if ($results) {
    $bot = $results->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Gestisci l'errore della query, ad esempio:
    die("Errore nella query: " . $conn->error);
}

// Creare un array per il conteggio delle interazioni per ogni mese
$monthlyData = array_fill(0, 12, 0);
$monthNames = [];

foreach ($interactions as $interaction) {
    $date = $interaction['timestamp']; // Data nel formato 'Y-m-d'
    $month = date('n', strtotime($date)) - 1; // -1 per ottenere un indice da 0 a 11
    $monthlyData[$month]++;

    // Aggiungi il nome del mese all'array se non è già presente
    $monthName = date('M', strtotime($date));
    if (!in_array($monthName, $monthNames)) {
        $monthNames[] = $monthName;
    }
}

// Converti l'array dei conteggi in un formato compatibile con JavaScript
$monthlyDataJSON = json_encode($monthlyData);
$monthNamesJSON = json_encode($monthNames);




$gfd="KLOE: Mi dispiace, non ho capito la tua domanda. Puoi formulare in modo diverso?";
$sql = "SELECT * FROM user_bot_interactions WHERE user_id='" . $username . "' AND bot_response='" . $gfd . "'";
$results2 = $conn->query($sql);

if ($results2) {
    $interactions2 = $results2->fetchAll(PDO::FETCH_ASSOC);
    $conta_interactions2=count($interactions2);

} else {
    // Gestisci l'errore della query, ad esempio:
    die("Errore nella query: " . $conn->error);
}

$monthlyData2 = array_fill(0, 12, 0);
$monthNames2 = [];
if ($conta_interactions>0 && $conta_interactions2>0) {
$percent=($conta_interactions2/$conta_interactions)*100;
} else {

$percent=1;
}
foreach ($interactions2 as $interaction) {
    $date2 = $interaction['timestamp']; // Data nel formato 'Y-m-d'
    $month2 = date('n', strtotime($date2)) - 1; // -1 per ottenere un indice da 0 a 11
    $monthlyData2[$month2]++;

    // Aggiungi il nome del mese all'array se non è già presente
    $monthName2 = date('M', strtotime($date2));
    if (!in_array($monthName2, $monthNames2)) {
        $monthNames2[] = $monthName2;
    }
}

// Converti l'array dei conteggi in un formato compatibile con JavaScript
$monthlyDataJSON2 = json_encode($monthlyData2);
$monthNamesJSON2 = json_encode($monthNames2);


$sql = "SELECT stato_chat FROM utenti WHERE username = :user_chats ";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_chats', $username, PDO::PARAM_STR); // :username è il placeholder per il parametro
$stmt->execute();
$stato_chat = $stmt->fetch();

$dominio_op=$verify_user['dominio'];
$stato_op=2;

$sql = "SELECT * FROM utenti WHERE dominio = :dominio AND stato = :stato";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':dominio', $dominio_op, PDO::PARAM_STR);
$stmt->bindParam(':stato', $stato_op, PDO::PARAM_STR); // :username è il placeholder per il parametro
$stmt->execute();
$operatori = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords"
        content="Kloe, AI, Smart Assistant">
    <meta name="description" content="Kloe AI Smart Assistant">
    <meta name="robots" content="noindex,nofollow">
    <title>Dashboard Kloe A.I.</title>
    <link rel="canonical" href="https://kloe.zenchat.it/view/" />
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/favicon.ico">
    <!--This page css - Morris CSS -->
    <link href="../assets/plugins/c3-master/c3.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/style.min.css" rel="stylesheet">
    <link href="../css/dash.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</head>
<style type="text/css">
	a.navbar-brand.ms-4 {
    margin-top: -22px !important;
    margin-left: -10px !important;
}
#main-wrapper[data-layout=vertical] .topbar .top-navbar .navbar-header[data-logobg=skin6] {
     background: none !important; 
}
</style>
    <style>
        /* Aggiungi stili CSS per il popup */
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ccc;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            max-height: 500px;
        }
    </style>
        <style>
        /* Aggiungi stili CSS per la formattazione del codice */
        pre {
            white-space: pre-wrap;
            padding: 10px;
            background-color: #f4f4f4;
            border: 1px solid #ccc;
            border-radius: 5px;
            overflow-x: auto;
            max-height: 400px;
        }
    </style>
<body>
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">
        <!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
        <header class="topbar" data-navbarbg="skin6">
            <nav class="navbar top-navbar navbar-expand-md navbar-dark" style="margin-bottom: 20px;">
                <div class="navbar-header" data-logobg="skin6">
                    <!-- ============================================================== -->
                    <!-- Logo -->
                    <!-- ============================================================== -->
                    <a class="navbar-brand ms-4" href="">
                        <!-- Logo icon -->
                        <b class="logo-icon">
                            <!--You can put here icon as well // <i class="wi wi-sunset"></i> //-->
                            <!-- Dark Logo icon -->
                            <img style="width:70px; border-radius:10px 10px 10px; margin-left: 10px; " src="../images/kloe.gif" alt="homepage" class="dark-logo" />

                        </b>
                        <!--End Logo icon -->
                    </a>
                    <!-- ============================================================== -->
                    <!-- End Logo -->
                    <!-- ============================================================== -->
                    <!-- ============================================================== -->
                    <!-- toggle and nav items -->
                    <!-- ============================================================== -->
                    <a class="nav-toggler waves-effect waves-light text-white d-block d-md-none"
                        href="javascript:void(0)"><i class="ti-menu ti-close"></i></a>
                </div>
                <!-- ============================================================== -->
                <!-- End Logo -->
                <!-- ============================================================== -->
                <div class="navbar-collapse collapse" id="navbarSupportedContent" data-navbarbg="skin5">
                    <ul class="navbar-nav d-lg-none d-md-block ">
                        <li class="nav-item">
                            <a class="nav-toggler nav-link waves-effect waves-light text-white "
                                href="javascript:void(0)"><i class="ti-menu ti-close"></i></a>
                        </li>
                    </ul>
                    <!-- ============================================================== -->
                    <!-- toggle and nav items -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav me-auto mt-md-0 ">
                        <!-- ============================================================== -->
                        <!-- Search -->
                        <!-- ============================================================== -->

                        <!--<li class="nav-item search-box">
                            <a class="nav-link text-muted" href="javascript:void(0)"><i class="ti-search"></i></a>
                            <form class="app-search" style="display: none;">
                                <input type="text" class="form-control" placeholder="Search &amp; enter"> <a
                                    class="srh-btn"><i class="ti-close"></i></a> </form>
                        </li>-->
                    </ul>

                    <!-- ============================================================== -->
                    <!-- Right side toggle and nav items -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav">
                        <!-- ============================================================== -->
                        <!-- User profile and search -->
                        <!-- ============================================================== -->
                        <li class="nav-item dropdown" style="color:white;">
                            <ul  id="tokendy"  data-user-id="<?php echo base64_decode($_SESSION['token']); ?>" >
                              <?php echo base64_decode($_SESSION['token']); ?>
                            </ul>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown"></ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
<!-- Settings Modal -->
<div class="modal" id="settingsModal" tabindex="-1" role="dialog" aria-labelledby="settingsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="settingsModalLabel">Kloe Settings</h5>
                <button type="button" class="close" onclick="closeModal()" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Settings Form -->
                <form id="settingsForm" enctype="multipart/form-data" onsubmit="submitForm(event); return false;">

                    <!-- Email and Password Settings -->
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $verify_user['username']; ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="synt">Synt:</label><br>
                        <textarea name="synt" id="synt"><?php echo $setting['synt']; ?></textarea>
                    </div>
                   <!-- <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>-->

                    <!-- Apt-GPT Setting -->
                    <div class="form-group">
                        <label for="aptGPT">Apt-GPT:</label>
                        <input type="text" class="form-control" id="aptGPT" name="aptGPT" value="<?php echo $setting['apigpt']; ?>">
                    </div>

                                        	<!-- Assistant GPT Setting -->
					<div class="form-group">
   						 <label for="assistantgpt">Assistant Name:</label>
   						 <input type="text" class="form-control" id="assistantgpt" name="assistantgpt" value="<?php echo $setting['assistantgpt']; ?>">
					</div>

                    <!-- ChatGPT Switch -->
                    <div class="form-group">
                        <label>ChatGPT:</label>
                        <div class="custom-control custom-switch">

                            <?php if ($setting['onoffgpt']!=0) { ?>
                            <input type="checkbox" class="custom-control-input" id="chatGPTSwitch" name="chatGPTSwitch" checked="<?php echo $setting['onoffgpt']; ?>">
                            <?php } elseif ($setting['onoffgpt']==0) { ?>
                            <input type="checkbox" class="custom-control-input" id="chatGPTSwitch" name="chatGPTSwitch" >
                            <?php } elseif (!$setting['apigpt']) { ?>
                            <input type="checkbox" class="custom-control-input"  id="chatGPTSwitch" name="chatGPTSwitch" disabled="true" >
                            <?php } ?>
                            <label class="custom-control-label" for="chatGPTSwitch">On/Off</label>
                        </div>
                    </div>


                    <!-- Logo Upload -->
                    <div class="form-group">
                        <label for="logo">Upload Logo:</label>
                        <input type="file" class="form-control-file" id="logoxx" name="logoxx" required="true" >
                    </div>

                    <!-- Color Picker for Header -->
                    <div class="form-group">
                        <label for="headerColor">Chat Color:</label>
                        <input type="color" class="form-control" id="headerColor" name="headerColor" value="<?php echo $setting['chatcolor']; ?>">
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </form>
            </div>
        </div>
    </div>
</div>

        <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <aside class="left-sidebar" data-sidebarbg="skin6">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar">
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        <!-- User Profile-->
                        <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                href="" aria-expanded="false"><i class="mdi me-2 mdi-gauge"></i><span
                                    class="hide-menu">Dashboard</span></a></li>
        <li class="sidebar-item">
          <a class="sidebar-link waves-effect waves-dark sidebar-link" id="account" data-toggle="modal" data-target="#updatePasswordModal">
            <i class="mdi mdi-account-card-details"></i><span class="hide-menu">&nbsp;Account</span>
          </a>
        </li>

    <li class="sidebar-item">
      <a class="sidebar-link waves-effect waves-dark sidebar-link" id="addOperator" data-toggle="modal" data-target="#addOperatorModal">
        <i class="mdi mdi-account-plus"></i><span class="hide-menu">&nbsp;Add Operator</span>
      </a>
    </li>

    <li class="sidebar-item">
      <a class="sidebar-link waves-effect waves-dark sidebar-link" id="deleteOperator" data-toggle="modal" data-target="#deleteOperatorModal">
        <i class="mdi mdi-account-minus"></i><span class="hide-menu">&nbsp;Delete Operator</span>
      </a>
    </li>
    
        <li class="sidebar-item">
      <a class="sidebar-link waves-effect waves-dark sidebar-link" id="deleteAccount" data-toggle="modal" data-target="#deleteAccountModal">
        <i class="mdi mdi-account-multiple-minus"></i><span class="hide-menu">&nbsp;Delete Account</span>
      </a>
    </li>

                        <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                href="https://zenchat.it" target="_blank" aria-expanded="false">
                                <i class="mdi me-2 mdi-home-outline"></i><span class="hide-menu">Home</span></a>
                        </li>
                        <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                href="https://zenchat.it/#section_4" target="_blank" aria-expanded="false"><i class="mdi me-2 mdi-file-document"></i><span
                                    class="hide-menu">Faqs</span></a></li>
                        <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                href="https://zenchat.it/#section_5" target="_blank" aria-expanded="false"><i
                                    class="mdi me-2 mdi-contact-mail"></i><span class="hide-menu">Contacts</span></a></li>
                        <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                href="https://zenchat.it/#section_3" target="_blank" aria-expanded="false"><i class="mdi me-2 mdi-minus-network"></i><span
                                    class="hide-menu">How it works</span></a></li>
                        <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                href="https://zenchat.it/#section_2" target="_blank" aria-expanded="false"><i
                                    class="mdi me-2 mdi-book-open-variant"></i><span class="hide-menu">Browse topics</span></a>
                        </li>
                        <li class="text-center p-20 upgrade-btn">

<label class="switch">
    <input type="checkbox" id="switchButton" <?php echo ($stato_chat['stato_chat'] == '1') ? 'checked' : ''; ?>>
    <span class="slider"></span>
    <br><br>
    <span class="status-text">Chat Online</span>
</label>



                        </li>
                    </ul>

                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
            <div class="sidebar-footer">
                <div class="row">
                     <div class="col-4 link-wrap">
    <button style="border:none; background: transparent;" class="link" onclick="openModal()">
        <i class="ti-settings"></i>
    </button>
                    </div>
<div class="col-4 link-wrap">
    <a href="#" id="openPopup" class="link" data-toggle="tooltip" title="" data-original-title="Email">
        <i class="mdi mdi-code-not-equal-variant"></i>
    </a>
</div>

<div class="col-4 link-wrap">
    <a href="#" id="logoutLink" class="link" data-toggle="tooltip" title="" data-original-title="Logout">
        <i class="mdi mdi-power"></i>
    </a>
</div>

                </div>
            </div>
        </aside>


<div class="modal fade" id="deleteAccountModal" tabindex="-1" role="dialog" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteAccountModalLabel">Delete Account</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Warning: Deleting your account is irreversible and all associated data, including data of connected operators, will be lost.</p>
        <p>Please confirm your password to proceed:</p>
        <input type="password" class="form-control" id="confirmPasswordx" placeholder="Enter your password" required>
        <input type="hidden" name="account" id="confirmAccount" value="<?php echo $verify_user['username']; ?>">
        <input type="hidden" name="dominio" id="confirmDom" value="<?php echo $verify_user['dominio']; ?>">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteAccount">Delete Account</button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="deleteOperatorModal" tabindex="-1" role="dialog" aria-labelledby="deleteOperatorModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteOperatorModalLabel">Delete Operator</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this operator?</p>
        <p>All data cannot be recovered!</p>
        <?php if (!!$operatori) { foreach ($operatori as $key) { ?>
        <button class="operator-btn" style="margin:5px; " id="<?php echo $key['id']; ?>"><?php echo $key['username']; ?></button><br>
        <?php } } ?>
        <input type="hidden" name="admin" value="<?php echo $username; ?>">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<!-- Add Operator Modal -->
  <div class="modal fade" id="addOperatorModal" tabindex="-1" role="dialog" aria-labelledby="addOperatorModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addOperatorModalLabel">Add Operator</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <!-- Add your operator addition form here -->
          <form id="addOperatorForm">
            <div class="form-group">
              <label for="operatorEmail">Operator Email:</label>
              <input type="email" class="form-control" id="operatorEmail" name="operatorEmail" required>
            </div>
            <input type="hidden" name="dominio" id="dominio" value="<?php echo $verify_user['dominio']; ?>">
            <button type="submit" class="btn btn-primary">Add Operator</button>
          </form>
        </div>
      </div>
    </div>
  </div>

<!-- Password Update Modal -->
  <div class="modal fade" id="updatePasswordModal" tabindex="-1" role="dialog" aria-labelledby="updatePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="updatePasswordModalLabel">Update Password</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <!-- Add your password update form here -->
          <form id="passwordUpdateForm">
            <div class="form-group">
              <label for="newPassword">New Password:</label>
              <input type="password" class="form-control" id="newPassword" name="newPassword" required>
            </div>
            <div class="form-group">
              <label for="confirmPassword">Confirm Password:</label>
              <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
            </div>
            <input type="hidden" name="username" id="usernameupdate" value="<?php echo $username; ?>">
            <button type="submit" class="btn btn-primary">Update Password</button>
          </form>
        </div>
      </div>
    </div>
  </div>

        <div id="popup" class="popup">
    <!-- Contenuto del popup -->
    <pre>
<code>
    &lt;script&gt;
$(document).ready(function() {
        var username = "<?php echo $username; ?>";
        $.ajax({
            url: "https://kloe.zenchat.it/ajax/box_user_chat.php",
            type: "GET",
            data: { username: username },
            success: function(response) {
                // Inserisci il contenuto nella tua pagina
                $("#chat-content-user").html(response);
                $(".header-navigation").find("link[rel=stylesheet]").remove();

                // Carica il file di stile specifico
                var styleUrl = "https://kloe.zenchat.it/css/style.css";
                $("<link/>", {
                    rel: "stylesheet",
                    type: "text/css",
                    href: styleUrl
                }).appendTo("head");
            },
        /*    error: function() {
                alert("Error AJAX.");
            }*/
        });

});
&lt;/script&gt;
        &lt;div id="chat-content-user"&gt;&lt;/div&gt;
        &lt;div id="tokendy2" data-user-id="<?php echo $username; ?>"&gt;&lt;/div&gt;


</code></pre>
    <button onclick="closePopup()">X</button>
</div>
        <!-- ============================================================== -->
        <!-- End Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <div class="page-breadcrumb">
                <div class="row align-items-center">
                    <div class="col-md-6 col-8 align-self-center">
                        <h3 class="page-title mb-0 p-0">Dashboard</h3>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <div class="col-md-6 col-4 align-self-center">
                        <div class="text-end upgrade-btn">          
                            <button 
                                class="btn btn-danger" id="noanali" onclick="nascondiDiv()" style="color:white;">Analitycs</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- End Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
                <!-- ============================================================== -->
                <!-- Sales chart -->
                <!-- ============================================================== -->
                <div class="row" id="noap" style="display: none;">
                    <!-- Column -->
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                <div class="row" >

                                    <div class="col-12">
                                        <div class="d-flex flex-wrap align-items-center">
                                            <div>
                                                <h3 class="card-title">Interactions With Users</h3>
                                                <h6 class="card-subtitle">Kloe Bot</h6>
                                            </div>
                                            <div class="ms-lg-auto mx-sm-auto mx-lg-0">
                                              <!--  <ul class="list-inline d-flex">
                                                    <li class="me-4">
                                                        <h6 class="text-success"><i
                                                                class="fa fa-circle font-10 me-2 "></i>Ample</h6>
                                                    </li>
                                                    <li>
                                                        <h6 class="text-info"><i
                                                                class="fa fa-circle font-10 me-2"></i>Pixel</h6>
                                                    </li>
                                                </ul>-->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                    <canvas id="bar-chart" width="800" height="350"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title">Unsolved questions </h3>
                                <div id="visitor">se
                                <canvas id="bar-chart2" width="800" height="350"></canvas>
                                </div>
                            </div>
                            <div>
                                <hr class="mt-0 mb-0">
                            </div>
                                <div class="card-body text-center ">
                                <ul class="list-inline d-flex justify-content-center align-items-center mb-0">
                                    <li class="me-4">
                                        <h6 class="text-info"><i class="fa fa-circle font-10 me-2 "></i>Efficiency<?php echo " ".floor($percent); ?>%</h6>
                                    </li>
                                </ul>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo $percent; ?>%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- Sales chart -->
                <!-- ============================================================== -->
               <!-- <div class="row">
                 
                    <div class="col-lg-4 col-xlg-3">
                    
                       <div class="card">
                            <img src="../images/kloe.gif" style="width:50px;">
                            <div class="card-body little-profile text-center">

                            <div class="chat-container">
                                <div class="chat-messages" id="chat-messages" style="max-height: 300px; overflow-y: auto;">
                                  
                                </div>
                                <input type="text" id="user-input" placeholder="Write your question here...">
                                <button class="btn btn-primary" onclick="sendMessage()">Send</button>
                            </div>

                            </div>
                        </div>
                     
                    </div>-->
<div class="container mt-4">
        <div class="card">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#home" role="tab">
                        <i class="mdi mdi-wechat"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#profile" role="tab">
                       <i class="mdi  mdi-account-alert"></i><i class="mdi mdi-help"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#settings" role="tab">
                        <i class="mdi mdi-message-settings-variant"></i>
                    </a>
                </li>
                
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane active" id="home" role="tabpanel">
                    <div class="card-body">
                        <div id="chat-content"></div>
                    </div>
                </div>
                <!-- Second tab -->
                <div class="tab-pane" id="profile" role="tabpanel">
                    <div class="card-body">
                        <div id="contentContainer2"></div>
                    </div>
                </div>
                <div class="tab-pane" id="settings" role="tabpanel">
                    <div class="card-body">
                        <div id="contentContainer"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

                     <footer class="footer" > © 2023 ALL RIGHTS RESERVED. A.I. TECH INNOVATIONS. P.IVA 06007830877 REA: CT-457230 </footer>
                </div>
                <!-- ============================================================== -->
                <!-- Table -->
                <!-- ============================================================== -->
                                  <div id="chat-content-user"></div>
                
            </div>

        </div>
        <!-- ============================================================== -->
        <!-- End Page wrapper  -->
        <!-- ============================================================== -->
    </div>

    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- All Jquery -->

    <!--<script src="../js/dash.js"></script>-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>

<!-- Include Bootstrap JS and jQuery -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"></script>

<script>
  $(document).ready(function () {
    $("#deleteAccount").click(function () {
      // Show the delete account modal
      $("#deleteAccountModal").modal("show");
    });

    $("#confirmDeleteAccount").click(function () {
      var confirmPassword = $("#confirmPasswordx").val();
      var confirmAccount =  $("#confirmAccount").val();
      var confirmDom = $("#confirmDom").val();

      // Chiamata AJAX per eliminare l'account
      $.ajax({
        type: "POST",
        url: "https://kloe.zenchat.it/ajax/delAcc.php",
        data: {
          confirmPassword: confirmPassword,
          confirmAccount: confirmAccount,
          confirmDom: confirmDom
        },
        success: function (response) {
          console.log(response);

          // Aggiungi il codice per gestire la risposta dal server, ad esempio mostrare un alert
          if (response.trim() === "success") {
            alert("Account deleted successfully!");
            window.location.href = "https://zenchat.it";
            // Redirect or perform additional actions after successful deletion if needed
          } else {
            alert("Error deleting account. Please check your password and try again.");
          }

          // Chiudi il modal dopo l'eliminazione (se necessario)
          $("#deleteAccountModal").modal("hide");
        },
        error: function (error) {
          console.error("Error sending AJAX request:", error);
        }
      });
    });
  });
</script>

<script>
  $(document).ready(function () {
    // Aggiungi un gestore di eventi per il clic su un pulsante operatore
    $(".operator-btn").click(function () {
      var operatorId = $(this).attr("id");
      var adminUsername = $("input[name='admin']").val();

      // Chiamata AJAX per eliminare l'operatore
      $.ajax({
        type: "POST",
        url: "https://kloe.zenchat.it/ajax/delOperator.php",
        data: {
          operatorId: operatorId,
          adminUsername: adminUsername
        },
        success: function (response) {
          console.log(response);

          // Aggiungi il codice per gestire la risposta dal server, ad esempio mostrare un alert
          if (response.trim() === "success") {
            alert("Operator deleted successfully!");
            window.location.href = "https://kloe.zenchat.it/view/";
          } else {
            alert("Error deleting operator. Please try again.");
          }

          // Chiudi il modal dopo l'eliminazione (se necessario)
          $("#deleteOperatorModal").modal("hide");
        },
        error: function (error) {
          console.error("Error sending AJAX request:", error);
        }
      });
    });

    // Aggiungi un gestore di eventi per il clic su "Cancel"
    $("#cancelDeleteOperator").click(function () {
      // Chiudi il modal
      $("#deleteOperatorModal").modal("hide");
    });
  });
</script>

<script>
  $(document).ready(function () {
    $("#addOperatorForm").submit(function (event) {
      event.preventDefault();

      // Proceed with the AJAX request
      var formData = $(this).serialize();

      $.ajax({
        type: "POST",
        url: "https://kloe.zenchat.it/ajax/addOperator.php",
        data: formData,
        success: function (response) {
          console.log(response);

          // Check if the response contains the exact string "success"
          if (response.trim() === "success") {
            // Show success alert
            alert("Operator added successfully! An email with the password has been sent to the operator.");
            
            // Optionally, you can close the modal after successful addition
            $("#addOperatorModal").modal("hide");
          } else {
            // Show error alert
            alert("Error adding operator. Please try again.");
          }
        },
        error: function (error) {
          console.error("Error sending AJAX request:", error);
        }
      });
    });
  });
</script>


<script>
  $(document).ready(function () {
    $("#passwordUpdateForm").submit(function (event) {
      event.preventDefault();

      // Get password values
      var password = $("#newPassword").val();
      var confirmPassword = $("#confirmPassword").val();
      var username = $("#usernameupdate").val(); 

      // Check if passwords match
      if (password !== confirmPassword) {
        alert("Passwords do not match. Please re-enter your passwords.");
        return; // Stop form submission
      }

      // If passwords match, proceed with the AJAX request
      var formData = $(this).serialize();

      $.ajax({
        type: "POST",
        url: "https://kloe.zenchat.it/ajax/updatePassback.php",
        data: formData,
        success: function (response) {
          console.log(response);

          // Check for "success token" and "success user" in the response
          if (response.includes("success user")) {
            alert("Password updated successfully!");

            // Redirect to the specified URL
            window.location.href = "https://kloe.zenchat.it";
          } else {
            alert("Something went wrong. Please try again.");
          }
        },
        error: function (error) {
          console.error("Error sending AJAX request:", error);
        }
      });
    });
  });
</script>

<!-- Script per far apparire il modal -->
<script>
    // Funzione per aprire il popup
    function openPopup() {
        document.getElementById('popup').style.display = 'block';
    }

    // Funzione per chiudere il popup
    function closePopup() {
        document.getElementById('popup').style.display = 'none';
    }

    // Aggiungi un gestore di eventi al clic del link
    document.getElementById('openPopup').addEventListener('click', function(event) {
        event.preventDefault(); // Impedisce il comportamento predefinito del link
        openPopup();
    });
</script>

<script>
    function openModal() {
        var modal = document.getElementById('settingsModal');
        modal.classList.add('show');
        modal.style.display = 'block';
        modal.removeAttribute('aria-hidden');
        document.body.classList.add('modal-open');
    }

    function closeModal() {
        var modal = document.getElementById('settingsModal');
        modal.classList.remove('show');
        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('modal-open');
    }

    function validateImage(fileInput) {
        var img = new Image();
        img.src = URL.createObjectURL(fileInput.files[0]);
        img.onload = function () {
            if (img.width > 800 || img.height > 600) {
                alert('Image dimensions must be a maximum of 800x600 pixels.');
                fileInput.value = '';
            }
        };
    }

    function validateFileSize(fileInput) {
        var fileSize = fileInput.files[0].size;
        if (fileSize > 0.5 * 1024 * 1024) {
            alert('Image size must be a maximum of 0.5 megabytes.');
            fileInput.value = '';
        }
    }

    function submitForm(event) {
        event.preventDefault(); // Evita la ricarica della pagina

        var form = document.getElementById('settingsForm');
        var formData = new FormData(form);
        var logoInput = document.getElementById('logoxx');

        validateImage(logoInput);
        validateFileSize(logoInput);

        fetch('https://kloe.zenchat.it/ajax/setting.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                throw new Error('La risposta non contiene dati JSON validi.');
            }
        })
        .then(data => {
            console.log(data);
            if (data.status === 'success') {
                alert('Settings updated successfully!');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });

        closeModal();
    }
</script>


        <script>
    $(document).ready(function() {
        var username = '<?php echo $username; ?>';
        var url = 'https://kloe.zenchat.it/ajax/unsolvedQuestion.php?username=' + username;

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'html',
            success: function(response) {
                $('#contentContainer2').html(response);
            },
            error: function(error) {
                console.error('Error fetching the page:', error);
            }
        });
    });
</script>
    <script>
    $(document).ready(function() {
        var username = '<?php echo $username; ?>';
        var url = 'https://kloe.zenchat.it/ajax/activityBot.php?username=' + username;

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'html',
            success: function(response) {
                $('#contentContainer').html(response);
            },
            error: function(error) {
                console.error('Error fetching the page:', error);
            }
        });
    });
</script>
<script>
$(document).ready(function() {
    // Inizializza lo stato del pulsante basato sul valore PHP
    updateStatusText();

    // Aggiungi un gestore di eventi al pulsante switch
    $("#switchButton").change(function() {
        var isChecked = $(this).prop('checked'); // Ottieni lo stato del pulsante
        var yourOperatorID = '<?php echo base64_decode($_SESSION['token']); ?>';

        // Esegui una richiesta AJAX per inviare il valore al server
        $.ajax({
            url: 'https://kloe.zenchat.it/ajax/switchop.php',
            method: 'POST',
            data: { idoperatore: yourOperatorID, value: isChecked ? 1 : 0 },
            success: function(response) {
                // Puoi fare qualcosa con la risposta del server, se necessario
                // console.log("Risposta dal server:", response);
            },
            error: function() {
                // Gestisci eventuali errori di richiesta AJAX qui
                console.error("Errore nella richiesta AJAX");
            }
        });

        // Aggiorna dinamicamente il testo in base allo stato
        updateStatusText();

        // Aggiungi o rimuovi la classe CSS in base allo stato del pulsante
      /*  if (isChecked) {
            // Pulsante attivo, mostra gli elementi
            $('#chatContainer').show();
            $('#szc').show();
           // $('.nav-item').show();
        } else {
            // Pulsante disattivo, nascondi gli elementi
            $('#chatContainer').hide();
            $('#szc').hide();
           // $('.nav-item').hide();
        }*/
    });

    function updateStatusText() {
        var isChecked = $("#switchButton").prop('checked');
        var statusText = isChecked ? 'Chat Online' : 'Chat Offline';
        $(".status-text").text(statusText);
    }
});
</script>

<script type="text/javascript">
    // Bar chart
    var ctx = document.getElementById("bar-chart").getContext('2d');

    var chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo $monthNamesJSON; ?>,
            datasets: [
                {
                    label: "Response",
                    backgroundColor: ["#3e95cd", "#8e5ea2", "#3cba9f", "#e8c3b9", "#c45850"],
                    data: <?php echo $monthlyDataJSON; ?>
                }
            ]
        },
        options: {
            legend: { display: false },
            title: {
                display: true,
                text: ''
            }
        }
    });
    // Bar chart 2
    var ctx2 = document.getElementById("bar-chart2").getContext('2d');

    var chart2 = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: <?php echo $monthNamesJSON2; ?>, // Usa gli stessi nomi dei mesi
            datasets: [
                {
                    label: "Response",
                    backgroundColor: ["#3e95cd", "#8e5ea2", "#3cba9f", "#e8c3b9", "#c45850"],
                    data: <?php echo $monthlyDataJSON2; ?> // Usa gli stessi dati
                }
            ]
        },
        options: {
            legend: { display: false },
            title: {
                display: true,
                text: ''
            }
        }
    });
</script>
<script>
function nascondiDiv() {
  var divNoap = document.getElementById("noap");
  if (divNoap.style.display === "none" || divNoap.style.display === "") {
    divNoap.style.display = "flex"; // Mostra la div
  } else {
    divNoap.style.display = "none"; // Nasconde la div
  }
}
</script>
    <script>
        $(document).ready(function() {
            var username = "<?php echo $username; ?>";

            // Effettua la richiesta AJAX
            $.ajax({
                url: "https://kloe.zenchat.it/ajax/box_chat.php",
                type: "GET",
                data: { username: username },
                success: function(response) {
                    // Inserisci il contenuto nella tua pagina
                    $("#chat-content").html(response);
                },
                error: function() {
                    // Gestisci eventuali errori
                    alert("Si è verificato un errore durante la richiesta AJAX.");
                }
            });
        });
    </script>
        <script>
        $(document).ready(function() {
            var username = "sparacinoriccardo@gmail.com";

            // Effettua la richiesta AJAX
            $.ajax({
                url: "https://kloe.zenchat.it/ajax/box_user_chat.php",
                type: "GET",
                data: { username: username },
                success: function(response) {
                    // Inserisci il contenuto nella tua pagina
                    $("#chat-content-user").html(response);
                   // console.log("Risposta dal server:", response);
                },
                error: function() {
                    // Gestisci eventuali errori
                    alert("Si è verificato un errore durante la richiesta AJAX.");
                }
            });
        });
    </script>

        <script>
    document.addEventListener("DOMContentLoaded", function() {
        var logoutLink = document.getElementById("logoutLink");

        logoutLink.addEventListener("click", function(e) {
            e.preventDefault();

            // Create a new XMLHttpRequest object
            var xhr = new XMLHttpRequest();

            // Configure it: GET-request for the URL /logout
            xhr.open('GET', 'https://kloe.zenchat.it/logout.php', true);

            // Send the request over the network
            xhr.send();

            // This will be called after the response is received
            xhr.onload = function() {
                if (xhr.status != 200) {
                    // Handle errors if needed
                    console.error("Logout failed");
                } else {
                    // Redirect to the login page or perform other actions
                    window.location.href = "../";
                }
            };
        });
    });
</script>
    <!-- ============================================================== -->
    <script src="../assets/plugins/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap tether Core JavaScript -->
    <script src="../assets/plugins/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/app-style-switcher.js"></script>
    <!--Wave Effects -->
    <script src="js/waves.js"></script>
    <!--Menu sidebar -->
    <script src="js/sidebarmenu.js"></script>
    <!-- ============================================================== -->
    <!-- This page plugins -->
    <!-- ============================================================== -->
    <!--c3 JavaScript -->
    <script src="../assets/plugins/d3/d3.min.js"></script>
    <script src="../assets/plugins/c3-master/c3.min.js"></script>
    <!--Custom JavaScript -->
    <script src="js/custom.js"></script>
</body>

</html>

<?php } else {

    header('Location: https://kloe.zenchat.it/?errore=login');
    exit();

} ?>
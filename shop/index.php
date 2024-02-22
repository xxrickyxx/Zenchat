<?php
session_start();
// Percorso del file JSON
$jsonFilePath = 'conf/cookie.json';

// Leggi il contenuto del file JSON
$jsonFileContent = file_get_contents($jsonFilePath);

// Decodifica il JSON in un array associativo
$config = json_decode($jsonFileContent, true);

// Verifica se il cookie deve essere impostato

    if (!isset($_COOKIE['user_chat_shop'])) {
        // Genera un UUID univoco
        $uuid = uniqid();

        // Concatena l'UUID al valore base64_encoded
        $user_chat = base64_encode('kloe_chat_shop_' . $uuid);

        // Imposta il cookie con il valore univoco
        setcookie("user_chat_shop", $user_chat, time() + 3600, "/");
    } else {
        // Se il cookie esiste, recupera il valore
        $user_chat = $_COOKIE['user_chat_shop'];
    }

       if (!!isset($_COOKIE['user_chat_shop'])) {
$userChatsFromBrowser = isset($_COOKIE['user_chat_shop']) ? $_COOKIE['user_chat_shop'] : '';

$foundEntry = null;
foreach ($config as $entry) {
    if (isset($entry['userChats']) && $entry['userChats'] === $userChatsFromBrowser) {
        $foundEntry = $entry;
        break;
    }
}

// Se l'entrata è stata trovata, estrai i valori di googleAnalytics e kloeZenchat
if ($foundEntry) {
    $googleAnalytics = isset($foundEntry['googleAnalytics']) ? $foundEntry['googleAnalytics'] : 'false';
    $kloeZenchat = isset($foundEntry['kloeZenchat']) ? $foundEntry['kloeZenchat'] : 'false';
} else {
    // Nessuna corrispondenza trovata
   // echo "Nessuna corrispondenza trovata per userChats: $userChatsFromBrowser";
	$cookie_verify=false;
}


       }

include'conf/lang.php';

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

// Processa il form quando viene inviato
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['language']) && array_key_exists($_POST['language'], $translations)) {
        $selected_language = $_POST['language'];
        $current_translations = $translations[$selected_language];
    }
}
?>
<!doctype html>
<html lang="<?php echo $browser_language; ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta name="description" content="<?php echo $current_translations['zenchat_intro']; ?>">

        <title>Zenchat</title>

        <!-- CSS FILES -->        
        <link rel="preconnect" href="https://fonts.googleapis.com">
        
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700&family=Open+Sans&display=swap" rel="stylesheet">
                        
        <link href="css/bootstrap.min.css" rel="stylesheet">

        <link href="css/bootstrap-icons.css" rel="stylesheet">

        <link href="css/templatemo-topic-listing.css" rel="stylesheet">      

            <!-- Favicon icon -->
		<link rel="apple-touch-icon" sizes="180x180" href="https://zenchat.it/images/favicon/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="https://zenchat.it/images/favicon/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="https://zenchat.it/images/favicon/favicon-16x16.png">


        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

		<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />

		<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

    </head>
    
    <body id="top">

        <main>

            <nav class="navbar navbar-expand-lg">
                <div class="container">
                    <a class="navbar-brand" href="https://zenchat.it">
                        <i class="bi-back"></i>
                        <span>Zenchat 1.0</span>
                    </a>

                    <div class="d-lg-none ms-auto me-4">
                        <a href="https://kloe.zenchat.it" class="navbar-icon bi-person smoothscroll"></a>
                    </div>
    
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
    
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-lg-5 me-lg-auto">
					<li class="nav-item">
					    <a class="nav-link click-scroll" href="#section_1"><?php echo $current_translations['home']; ?></a>
					</li>

					<li class="nav-item">
					    <a class="nav-link click-scroll" href="#section_2"><?php echo $current_translations['browse_topics']; ?></a>
					</li>

					<li class="nav-item">
					    <a class="nav-link click-scroll" href="#section_3"><?php echo $current_translations['how_it_works']; ?></a>
					</li>

					<li class="nav-item">
					    <a class="nav-link click-scroll" href="#section_4"><?php echo $current_translations['faqs']; ?></a>
					</li>

					<li class="nav-item">
					    <a class="nav-link click-scroll" href="#section_5"><?php echo $current_translations['contact']; ?></a>
					</li>
                    <li class="nav-item">
                        <form method="post" action="https://kloe.zenchat.it/login.php">
                        <input type="hidden" name="password" value="demo">
                        <input type="hidden" name="username" value="demo@demo.com">
                        <button class="nav-link" style="background: transparent; border: none;">Demo</button>
                        </form>
                    </li>
					<?php if (!!isset($_COOKIE['user_chat_shop'])) { ?>
					<li class="nav-item"> 
						<button id="apricookie" style="background: transparent; margin-top: 16px;">🍪</button> 
					</li>
					<?php } ?>

                           <!-- <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarLightDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">Pages</a>

                                <ul class="dropdown-menu dropdown-menu-light" aria-labelledby="navbarLightDropdownMenuLink">
                                    <li><a class="dropdown-item" href="topics-listing.html">Topics Listing</a></li>

                                    <li><a class="dropdown-item" href="contact.html">Contact Form</a></li>
                                </ul>
                            </li>-->
                        </ul>


<div class="d-none d-lg-block">
<a href="https://kloe.zenchat.it" class="navbar-icon bi-person smoothscroll"></a>
</div>
                 

                    </div>
                </div>
            </nav>
            

            <section class="hero-section d-flex justify-content-center align-items-center" id="section_1">
                <div class="container">
                    <div class="row">

                        <div class="col-lg-8 col-12 mx-auto">
                            <h1 class="text-white text-center"><?php echo $current_translations['elevate_support']; ?></h1>

                            <h6 class="text-center"><?php echo $current_translations['zenchat_intro']; ?></h6>


                        </div>

                    </div>
                </div>
            </section>


            <section class="featured-section">
                <div class="container">
                    <div class="row justify-content-center">

                        <div class="col-lg-4 col-12 mb-4 mb-lg-0">
                            <div class="custom-block bg-white shadow-lg">
                                <a href="https://kloe.zenchat.it">
                                    <div class="d-flex">
                                        <div>
                                            <h5 class="mb-2"><?php echo $current_translations['chat_title']; ?></h5>

                                            <p class="mb-0"><?php echo $current_translations['chat_description']; ?></p>
                                        </div>
                                    </div>

                                    <img src="images/topics/undraw_Remote_design_team_re_urdx.png" class="custom-block-image img-fluid" alt="">
                                </a>
                            </div>
                        </div>

                        <div class="col-lg-6 col-12">
                            <div class="custom-block custom-block-overlay">
                                <div class="d-flex flex-column h-100">
                                    <img src="images/ana.png" class="custom-block-image img-fluid" alt="config" style="max-height: 400px;">

                                    <div class="custom-block-overlay-text d-flex">
                                        <div>
                                            <h5 class="text-white mb-2"><?php echo $current_translations['kloe_bot_title']; ?></h5>

												<p class="text-white"><?php echo $current_translations['kloe_bot_description_1']; ?></p>

												<p class="text-white"><?php echo $current_translations['kloe_bot_description_2']; ?></p>

												<p class="text-white"><?php echo $current_translations['kloe_bot_description_3']; ?></p>


                                        </div>

                                    </div>



                                    <div class="section-overlay"></div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </section>

  <div id="popup">
    <span id="close-btn" onclick="closePopup()">X</span>
    <div align="center">
    <img src="images/ban1.jpg" style="width: 250px;  margin-top: 10px;" alt="Promozione ZenChat">
	</div>
	<?php echo $current_translations['ban1']; ?><a href="https://kloe.zenchat.it" style="text-decoration: none; float: right;"><img src="images/start.png" alt="start" style="width: 80px; margin-top: -60px;"></a>
  </div>

 <!-- ... (il tuo codice HTML esistente) ... -->

  <div class="overlay">
    <div class="popup-container">
      <div class="swiper-container">
        <div class="swiper-wrapper">
          <div class="swiper-slide">
            <!-- Contenuto del popup -->
            <div class="popup-content">
              <form>
                 <label class="switch">
            <input type="checkbox" id="googleAnalytics" name="googleAnalytics" checked="true">
            <span class="slider"></span>
        </label>
        <label for="googleAnalytics" style="font-size: 20px; font-style: bold;">Google Analytics</label>
        <p style="max-height: 200px; max-width: 400px; overflow-y: scroll;"><?php echo $current_translations['analytics_cookie']; ?></p><br>


        <label class="switch">
            <input type="checkbox" id="kloeChat" name="kloeChat" checked="true">
            <span class="slider"></span>
        </label>
        <label for="kloeChat"  style="font-size: 20px; font-style: bold;">Kloe Chat</label>
        <p style="max-height: 200px; max-width: 400px; overflow-y: none;"><?php echo $current_translations['kloe_cookie']; ?></p>

        	<br><br>
        <button class="btn btn-secondary" type="button" onclick="salvaImpostazioni()">Save</button>
        <button class="btn btn-warning" id="chiudicookie" style="margin-left: 10px;">Close</button>
    </form>
              
            </div>
          </div>
        </div>
        <!-- Aggiungi la paginazione se necessario -->
        <div class="swiper-pagination"></div>
      </div>
    </div>
  </div>

<!-- ... (il tuo codice HTML esistente) ... -->


            <section class="explore-section section-padding" id="section_2">
	             <div class="container">
	    <div class="col-12 text-center">
	        <h2 class="mb-4"><?php echo $current_translations['browse_topics_title']; ?></h2>
	    </div>
	</div>

	<div class="container-fluid">
	    <div class="row">
	        <ul class="nav nav-tabs" id="myTab" role="tablist">
	            <li class="nav-item" role="presentation">
	                <button class="nav-link active" id="design-tab" data-bs-toggle="tab" data-bs-target="#design-tab-pane" type="button" role="tab" aria-controls="design-tab-pane" aria-selected="true"><?php echo $current_translations['subscription_plans']; ?></button>
	            </li>

	            <li class="nav-item" role="presentation">
	                <button class="nav-link" id="marketing-tab" data-bs-toggle="tab" data-bs-target="#marketing-tab-pane" type="button" role="tab" aria-controls="marketing-tab-pane" aria-selected="false"><?php echo $current_translations['multi_operator_chat']; ?></button>
	            </li>

	            <li class="nav-item" role="presentation">
	                <button class="nav-link" id="finance-tab" data-bs-toggle="tab" data-bs-target="#finance-tab-pane" type="button" role="tab" aria-controls="finance-tab-pane" aria-selected="false"><?php echo $current_translations['ai_integration']; ?></button>
	            </li>

	            <li class="nav-item" role="presentation">
	                <button class="nav-link" id="music-tab" data-bs-toggle="tab" data-bs-target="#music-tab-pane" type="button" role="tab" aria-controls="music-tab-pane" aria-selected="false"><?php echo $current_translations['customization_options']; ?></button>
	            </li>

	            <li class="nav-item" role="presentation">
	                <button class="nav-link" id="education-tab" data-bs-toggle="tab" data-bs-target="#education-tab-pane" type="button" role="tab" aria-controls="education-tab-pane" aria-selected="false"><?php echo $current_translations['customer_support']; ?></button>
	            </li>
	        </ul>
	    </div>
	</div>


                <div class="container">
                    <div class="row">

                        <div class="col-12">
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="design-tab-pane" role="tabpanel" aria-labelledby="design-tab" tabindex="0">
                                    <div class="row">
                                        <div class="col-lg-4 col-md-6 col-12 mb-4 mb-lg-0">
                                            <div class="custom-block bg-white shadow-lg">
                                                <a href="https://kloe.zenchat.it">
                                                    <div class="d-flex">
													<div>
													    <h5 class="mb-2"><?php echo $current_translations['free_title']; ?></h5>

													    <p class="mb-0">
													        <?php echo $current_translations['free_description']; ?>
													    </p>
													</div>
                                                        <span class="badge bg-design rounded-pill ms-auto">Free</span>
                                                    </div>

                                                    <img src="images/topics/undraw_Remote_design_team_re_urdx.png" class="custom-block-image img-fluid" alt="">
                                                </a>
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-6 col-12 mb-4 mb-lg-0">
                                            <div class="custom-block bg-white shadow-lg">
                                                <a href="https://kloe.zenchat.it">
                                                    <div class="d-flex">
													<div>
													    <h5 class="mb-2"><?php echo $current_translations['premium_title']; ?></h5>

													    <p class="mb-0">
													        <p style="font-size: 14px;"><?php echo $current_translations['premium_description']; ?></p>

													        <p style="font-size: 14px;"><?php echo $current_translations['premium_key_features']; ?></p>

													        <ul>
													            <li><span class="highlight"><?php echo $current_translations['premium_feature_1']; ?></span> <?php echo $current_translations['premium_feature_1_description']; ?></li>
													            <li><span class="highlight"><?php echo $current_translations['premium_feature_2']; ?></span> <?php echo $current_translations['premium_feature_2_description']; ?></li>
													            <li><span class="highlight"><?php echo $current_translations['premium_feature_3']; ?></span> <?php echo $current_translations['premium_feature_3_description']; ?></li>
													        </ul>
													    </p>
													</div>

                                                        <span class="badge bg-design rounded-pill ms-auto" style="width:60px;">39,99 $</span>
                                                    </div>

                                                    <img src="images/topics/undraw_Redesign_feedback_re_jvm0.png" class="custom-block-image img-fluid" alt="">
                                                </a>
                                            </div>
                                        </div>

										<div class="col-lg-4 col-md-6 col-12">
										    <div class="custom-block bg-white shadow-lg">
										        <a href="https://kloe.zenchat.it">
										            <div class="d-flex">
										                <div>
										                    <h5 class="mb-2"><?php echo $current_translations['super_premium_title']; ?></h5>

										                    <p class="mb-0">
										                        <p style="font-size: 14px;"><?php echo $current_translations['super_premium_description']; ?></p>

										                        <p><?php echo $current_translations['super_premium_key_features']; ?></p>

										                        <ul>
										                            <li><span class="highlight"><?php echo $current_translations['super_premium_feature_1']; ?></span> <?php echo $current_translations['super_premium_feature_1_description']; ?></li>
										                            <li><span class="highlight"><?php echo $current_translations['super_premium_feature_2']; ?></span> <?php echo $current_translations['super_premium_feature_2_description']; ?></li>
										                            <li><span class="highlight"><?php echo $current_translations['super_premium_feature_3']; ?></span> <?php echo $current_translations['super_premium_feature_3_description']; ?></li>
										                            <li><span class="highlight"><?php echo $current_translations['super_premium_contact_us']; ?></span></li>
										                        </ul>
										                    </p>
										                </div>

										                <span class="badge bg-design rounded-pill ms-auto" style="width:100px;"><?php echo $current_translations['super_premium_contact']; ?></span>
										            </div>

										            <img src="images/topics/colleagues-working-cozy-office-medium-shot.png" class="custom-block-image img-fluid" alt="">
										        </a>
										    </div>
										</div>

                                    </div>
                                </div>

								<div class="tab-pane fade" id="marketing-tab-pane" role="tabpanel" aria-labelledby="marketing-tab" tabindex="0">
								    <div class="row">
								        <div class="col-lg-4 col-md-6 col-12 mb-4 mb-lg-3">
								            <div class="custom-block bg-white shadow-lg">
								                <a href="https://kloe.zenchat.it">
								                    <div class="d-flex">
								                        <div>
								                            <h5 class="mb-2"><?php echo $current_translations['marketing_collaborative_support']; ?></h5>
								                            <p class="mb-0"><?php echo $current_translations['marketing_collaborative_support_description']; ?></p>
								                        </div>
								                    </div>

								                    <img src="images/topics/undraw_online_ad_re_ol62.png" class="custom-block-image img-fluid" alt="">
								                </a>
								            </div>
								        </div>

								        <div class="col-lg-4 col-md-6 col-12 mb-4 mb-lg-3">
								            <div class="custom-block bg-white shadow-lg">
								                <a href="https://kloe.zenchat.it">
								                    <div class="d-flex">
								                        <div>
								                            <h5 class="mb-2"><?php echo $current_translations['marketing_real_time_collaboration']; ?></h5>
								                            <p class="mb-0"><?php echo $current_translations['marketing_real_time_collaboration_description']; ?></p>
								                        </div>
								                    </div>

								                    <img src="images/topics/undraw_Group_video_re_btu7.png" class="custom-block-image img-fluid" alt="">
								                </a>
								            </div>
								        </div>

								        <div class="col-lg-4 col-md-6 col-12">
								            <div class="custom-block bg-white shadow-lg">
								                <a href="https://kloe.zenchat.it">
								                    <div class="d-flex">
								                        <div>
								                            <h5 class="mb-2"><?php echo $current_translations['marketing_efficient_team_communication']; ?></h5>
								                            <p class="mb-0"><?php echo $current_translations['marketing_efficient_team_communication_description']; ?></p>
								                        </div>
								                    </div>

								                    <img src="images/topics/undraw_viral_tweet_gndb.png" class="custom-block-image img-fluid" alt="">
								                </a>
								            </div>
								        </div>
								    </div>
								</div>

								
								<div class="tab-pane fade" id="finance-tab-pane" role="tabpanel" aria-labelledby="finance-tab" tabindex="0">
								    <div class="row">
								        <div class="col-lg-6 col-md-6 col-12 mb-4 mb-lg-0">
								            <div class="custom-block bg-white shadow-lg">
								                <a href="https://kloe.zenchat.it">
								                    <div class="d-flex">
								                        <div>
								                            <h5 class="mb-2"><?php echo $current_translations['finance_integrating_ai_solutions']; ?></h5>
								                            <p class="mb-0"><?php echo $current_translations['finance_integrating_ai_solutions_description']; ?></p>
								                        </div>
								                    </div>
								                    <img src="images/topics/undraw_Finance_re_gnv2.png" class="custom-block-image img-fluid" alt="">
								                </a>
								            </div>
								        </div>
								        <div class="col-lg-6 col-md-6 col-12">
								            <div class="custom-block custom-block-overlay">
								                <div class="d-flex flex-column h-100">
								                    <div class="custom-block-overlay-text d-flex">
								                        <div>
								                            <h5 class="text-white mb-2"><?php echo $current_translations['finance_for_business_title']; ?></h5>
								                            <p class="text-white"><?php echo $current_translations['finance_for_business_description']; ?></p>
								                            <p><?php echo $current_translations['finance_demo_plan_access']; ?></p>
								                        </div>
								                    </div>
								                    <div class="section-overlay"></div>
								                </div>
								            </div>
								        </div>
								    </div>
								</div>

								<div class="tab-pane fade" id="music-tab-pane" role="tabpanel" aria-labelledby="music-tab" tabindex="0">
								    <div class="row">
								        <div class="col-lg-4 col-md-6 col-12 mb-4 mb-lg-3">
								            <div class="custom-block bg-white shadow-lg">
								                <a href="https://kloe.zenchat.it">
								                    <div class="d-flex">
								                        <div>
								                            <h5 class="mb-2"><?php echo $current_translations['music_customize_graphics_title']; ?></h5>
								                            <p class="mb-0"><?php echo $current_translations['music_customize_graphics_description']; ?></p>
								                        </div>
								                    </div>
								                    <img src="images/topics/undraw_Compose_music_re_wpiw.png" class="custom-block-image img-fluid" alt="">
								                </a>
								            </div>
								        </div>
								        <div class="col-lg-4 col-md-6 col-12 mb-4 mb-lg-3">
								            <div class="custom-block bg-white shadow-lg">
								                <a href="https://kloe.zenchat.it">
								                    <div class="d-flex">
								                        <div>
								                            <h5 class="mb-2"><?php echo $current_translations['music_choose_colors_title']; ?></h5>
								                            <p class="mb-0"><?php echo $current_translations['music_choose_colors_description']; ?></p>
								                        </div>
								                    </div>
								                    <img src="images/topics/undraw_happy_music_g6wc.png" class="custom-block-image img-fluid" alt="">
								                </a>
								            </div>
								        </div>
								        <div class="col-lg-4 col-md-6 col-12">
								            <div class="custom-block bg-white shadow-lg">
								                <a href="https://kloe.zenchat.it">
								                    <div class="d-flex">
								                        <div>
								                            <h5 class="mb-2"><?php echo $current_translations['music_choose_name_title']; ?></h5>
								                            <p class="mb-0"><?php echo $current_translations['music_choose_name_description']; ?></p>
								                        </div>
								                    </div>
								                    <img src="images/topics/undraw_Podcast_audience_re_4i5q.png" class="custom-block-image img-fluid" alt="">
								                </a>
								            </div>
								        </div>
								    </div>
								</div>

								<div class="tab-pane fade" id="education-tab-pane" role="tabpanel" aria-labelledby="education-tab" tabindex="0">
								    <div class="row">
								        <div class="col-lg-6 col-md-6 col-12 mb-4 mb-lg-3">
								            <div class="custom-block bg-white shadow-lg">
								                <a href="https://kloe.zenchat.it">
								                    <div class="d-flex">
								                        <div>
								                            <h5 class="mb-2"><?php echo $current_translations['education_customization_title']; ?></h5>
								                            <p class="mb-0"><?php echo $current_translations['education_customization_description']; ?></p>
								                        </div>
								                    </div>
								                    <img src="images/topics/undraw_Graduation_re_gthn.png" class="custom-block-image img-fluid" alt="">
								                </a>
								            </div>
								        </div>

								        <div class="col-lg-6 col-md-6 col-12">
								            <div class="custom-block bg-white shadow-lg">
								                <a href="https://kloe.zenchat.it">
								                    <div class="d-flex">
								                        <div>
								                            <h5 class="mb-2"><?php echo $current_translations['education_contact_title']; ?></h5>
								                            <p class="mb-0"><?php echo $current_translations['education_contact_description']; ?></p>
								                        </div>
								                    </div>
								                    <img src="images/topics/undraw_Educator_re_ju47.png" class="custom-block-image img-fluid" alt="">
								                </a>
								            </div>
								        </div>
								    </div>
								</div>

                            </div>

                    </div>
                </div>
            </section>


<section class="timeline-section section-padding" id="section_3">
    <div class="section-overlay"></div>

    <div class="container">
        <div class="row">

            <div class="col-12 text-center">
                <h2 class="text-white mb-4"><?php echo $current_translations['how_it_works_title']; ?></h2>
            </div>

            <div class="col-lg-10 col-12 mx-auto">
                <div class="timeline-container">
                    <ul class="vertical-scrollable-timeline" id="vertical-scrollable-timeline">
                        <div class="list-progress">
                            <div class="inner"></div>
                        </div>

                        <li>
                            <h4 class="text-white mb-3"><?php echo $current_translations['activate_assistant_title']; ?></h4>

                            <p class="text-white"><?php echo $current_translations['activate_assistant_description']; ?></p>

                            <div class="icon-holder">
                                <i class="bi-gear"></i>
                            </div>
                        </li>

                        <li>
                            <h4 class="text-white mb-3"><?php echo $current_translations['customize_brand_title']; ?></h4>

                            <p class="text-white"><?php echo $current_translations['customize_brand_description']; ?></p>

                            <div class="icon-holder">
                                <i class="bi-palette"></i>
                            </div>
                        </li>

                        <li>
                            <h4 class="text-white mb-3"><?php echo $current_translations['ai_powered_interactions_title']; ?></h4>

                            <p class="text-white"><?php echo $current_translations['ai_powered_interactions_description']; ?></p>

                            <div class="icon-holder">
                                <i class="bi-chat"></i>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-12 text-center mt-5">
                <p class="text-white">
                    <?php echo $current_translations['ready_to_revolutionize']; ?>
                    <a href="https://kloe.zenchat.it" class="btn custom-btn custom-border-btn ms-3"><?php echo $current_translations['get_started']; ?></a>
                </p>
            </div>
        </div>
    </div>
</section>


<!-- Sezione FAQ -->
<section class="faq-section section-padding" id="section_4">
    <div class="container">
        <div class="row">

            <div class="col-lg-6 col-12">
                <!-- Titolo FAQ -->
                <h2 class="mb-4">
                    <?php echo $current_translations['faq_title']; ?>
                </h2>
            </div>

            <div class="clearfix"></div>

            <div class="col-lg-5 col-12">
                <!-- Immagine illustrativa FAQ -->
                <img src="images/faq_graphic.jpg" class="img-fluid" alt="FAQs">
            </div>

            <div class="col-lg-6 col-12 m-auto">
                <!-- Accordion FAQ -->
                <div class="accordion" id="accordionExample">
                    <!-- Domanda 1 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                <?php echo $current_translations['question_1']; ?>
                            </button>
                        </h2>

                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <?php echo $current_translations['answer_1']; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Domanda 2 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                <?php echo $current_translations['question_2']; ?>
                            </button>
                        </h2>

                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <?php echo $current_translations['answer_2']; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Domanda 3 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                <?php echo $current_translations['question_3']; ?>
                            </button>
                        </h2>

                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <?php echo $current_translations['answer_3']; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>


        </main>

<footer class="site-footer section-padding">
            <div class="container">
                <div class="row">

                    <div class="col-lg-3 col-12 mb-4 pb-2">
                        <h6 class="site-footer-title mb-3"><?php echo $current_translations['document']; ?></h6>

                        <ul class="site-footer-links">
                            <li class="site-footer-link-item">
                                <a href="https://zenchat.it/magento" class="site-footer-link">Magento 2</a>
                            </li>
                            
                            <li class="site-footer-link-item">
                                <a href="https://zenchat.it/prestashop" class="site-footer-link">Prestashop</a>
                            </li>
                        </ul>
                    </div>

                    <div class="col-lg-3 col-md-4 col-6">
                        <h6 class="site-footer-title mb-3"><?php echo $current_translations['resources_title']; ?></h6>

                        <ul class="site-footer-links">
                            <li class="site-footer-link-item">
                                <a href="https://zenchat.it/#section_1" class="site-footer-link"><?php echo $current_translations['home']; ?></a>
                            </li>

                            <li class="site-footer-link-item">
                                <a href="https://zenchat.it/#section_2" class="site-footer-link"><?php echo $current_translations['browse_topics']; ?></a>
                            </li>

                            <li class="site-footer-link-item">
                                <a href="https://zenchat.it/#section_3" class="site-footer-link"><?php echo $current_translations['how_it_works']; ?></a>
                            </li>

                            <li class="site-footer-link-item">
                                <a href="https://zenchat.it/#section_4" class="site-footer-link"><?php echo $current_translations['faqs']; ?></a>
                            </li>

                            <li class="site-footer-link-item">
                                <a href="https://zenchat.it/#section_5" class="site-footer-link"><?php echo $current_translations['contact']; ?></a>
                            </li>
                        </ul>
                    </div>


                    <div class="col-lg-3 col-md-4 col-6 mb-4 mb-lg-0" id="section_5">
                        <h6 class="site-footer-title mb-3"><?php echo $current_translations['information']; ?></h6>

                        <p class="text-white d-flex mb-1">
                            <a href="mailto:admin@zenchat.it" class="site-footer-link">
                                admin@zenchat.it
                            </a>
                        </p>

                        <p class="text-white d-flex">
                            <a href="mailto:info@zenchat.it" class="site-footer-link">
                                info@zenchat.it
                            </a>
                        </p>
                    </div>

                    <div class="col-lg-3 col-md-4 col-12 mt-4 mt-lg-0 ms-auto">
                        <div class="dropdown">
                            <!--<button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            English</button>-->

                           <!-- <ul class="dropdown-menu">-->
                              <!--  <li><button class="dropdown-item" type="button">Italian</button></li>-->
						<form action="" method="post">
						    <select id="language"  name="language" style="border:none !important; border-bottom: 1px solid grey !important;">
						        <option class="dropdown-item" value="it" <?php echo ($selected_language === 'it') ? 'selected' : ''; ?>>Italiano</option>
						        <option class="dropdown-item" value="en" <?php echo ($selected_language === 'en') ? 'selected' : ''; ?>>English</option>
						        <option class="dropdown-item" value="es" <?php echo ($selected_language === 'es') ? 'selected' : ''; ?>>Español</option>
						        <option class="dropdown-item" value="zh" <?php echo ($selected_language === 'zh') ? 'selected' : ''; ?>>中文</option>
						        <option class="dropdown-item" value="ja" <?php echo ($selected_language === 'ja') ? 'selected' : ''; ?>>日本語</option>
						        <option class="dropdown-item" value="ar" <?php echo ($selected_language === 'ar') ? 'selected' : ''; ?>>العربية</option>
						        <option class="dropdown-item" value="ru" <?php echo ($selected_language === 'ru') ? 'selected' : ''; ?>>Русский</option>
						    </select>
						    <input class="btn btn-secondary dropdown-toggle" type="submit" value="<?php echo $current_translations['change_language']; ?>">
						</form>
						                            <!--</ul>-->
                        </div>

                        <p class="copyright-text mt-lg-5 mt-4">Copyright © 2023 all rights reserved. A.I. Tech innovations. </p>
                        
                    </div>

                </div>
            </div>
        </footer>
<?php if (!isset($_COOKIE['user_chat_shop'])) { ?>
<div id="js-cookie-button" class="cookie-box cookie-box--hide">
 <button id="apricookie">&nbsp;<?php echo $current_translations['usage_cookie']; ?></button>
</div>
<?php } ?>
    <script>
        function salvaImpostazioni() {
            var form = new FormData();
            form.append('googleAnalytics', document.getElementById("googleAnalytics").checked);
            form.append('kloeZenchat', document.getElementById("kloeChat").checked);
            form.append('userChats', '<?php echo $user_chat; ?>');

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "conf/save_preferences.php", true);

            xhr.onload = function () {
                if (xhr.status === 200) {
                    //console.log("Risposta dal server:", xhr.responseText);
                    document.querySelector('.swiper-container').style.display = 'none';
   					document.querySelector('.overlay').style.display = 'none';
   				    document.querySelector('#js-cookie-button').style.display = 'none';
                    mostraPopupConferma();
                    aggiungiElementoAlMenu();
                } else {
                    console.error("Errore nella richiesta al server");
                }
            };

            xhr.send(form);

        //    console.log("Google Analytics attivo:", form.get('googleAnalytics'));
          //  console.log("Kloe Chat attivo:", form.get('kloeZenchat'));
            //console.log("user_chat:", form.get('userChats'));
        }
        function mostraPopupConferma() {
        // Puoi personalizzare il popup qui, ad esempio utilizzando una libreria di popup o creando un elemento HTML personalizzato
        alert("Ok!");
        // Se vuoi utilizzare un popup personalizzato, puoi sostituire alert con il tuo codice di visualizzazione del popup.
        }

        function aggiungiElementoAlMenu() {
    // Crea l'elemento li con il pulsante
    var nuovoElemento = document.createElement("li");
    nuovoElemento.className = "nav-item";
    nuovoElemento.innerHTML = '<button id="apricookie" style="background: transparent; margin-top: 16px;">🍪</button>';

    // Trova l'ul con la classe specifica e aggiungi il nuovo elemento
    var ulMenu = document.querySelector('.navbar-nav.ms-lg-5.me-lg-auto');
    if (ulMenu) {
        ulMenu.appendChild(nuovoElemento);
    }
}
    </script>

<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function () {
  var mySwiper = new Swiper('.swiper-container', {
    // Opzioni Swiper qui...

    // Esempio di opzioni:
    slidesPerView: 1,
    spaceBetween: 10,
    pagination: {
      el: '.swiper-pagination',
      clickable: true,
    },
  });

  // Gestisci l'apertura del popup e della sovrapposizione
  document.getElementById('apricookie').addEventListener('click', function () {
    mySwiper.slideNext();
    document.querySelector('.swiper-container').style.display = 'block';
    document.querySelector('.overlay').style.display = 'flex';
  });

  // Gestisci la chiusura del popup e della sovrapposizione
  document.getElementById('chiudicookie').addEventListener('click', function () {
    mySwiper.slidePrev();
    document.querySelector('.swiper-container').style.display = 'none';
    document.querySelector('.overlay').style.display = 'none';
  });
});

</script>
<?php 

if ($foundEntry['kloeZenchat']=='true' OR !$foundEntry['kloeZenchat']) {

?>
<script>
$(document).ready(function() {
        var username = "sparacinoriccardo@gmail.com";
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
                $("", {
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
</script>
        <div id="chat-content-user"></div>
        <div id="tokendy2" data-user-id="sparacinoriccardo@gmail.com"></div>
<?php } ?>
        <!-- JAVASCRIPT FILES -->
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.bundle.min.js"></script>
        <script src="js/jquery.sticky.js"></script>
        <script src="js/click-scroll.js"></script>
        <script src="js/custom.js"></script>

<?php if ($foundEntry['googleAnalytics']=='true' OR !$foundEntry['googleAnalytics']) { ?>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-DYMYK4JGSV"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-DYMYK4JGSV');
</script>
<?php } ?>

  <script>
    // Inizializza Swiper
    var swiper = new Swiper('.swiper-container', {
      pagination: {
        el: '.swiper-pagination',
        clickable: true,
      },
      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
      },
    });

    // Funzione per mostrare il popup
    function showPopup() {
      document.getElementById('popup').style.display = 'block';
    }

    // Funzione per chiudere il popup
    function closePopup() {
      document.getElementById('popup').style.display = 'none';
    }

    // Mostra il popup al caricamento della pagina
    window.onload = function () {
      showPopup();
    };
  </script>

  <!-- Markup JSON-LD generato da Assistente per il markup dei dati strutturati di Google. -->
<script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "SoftwareApplication",
  "name": "Zenchat",
  "image": "https://kloe.zenchat.it/images/kloe.gif",
  "url": "https://kloe.zenchat.it/",
  "offers": "premium",
  "price": "39.00 $",
  "ratingValue": "5",
  "ratingCount": "500",
  "operatingSystem": "all",
  "author": {
    "@type": "Person",
    "name": "A.I. Tech innovations"
  },
  "applicationCategory": "Chat",
  "softwareVersion": "1.0"
}
</script>
    </body>
</html>

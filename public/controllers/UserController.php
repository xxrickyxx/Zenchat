<?php

// controllers/UserController.php

require_once 'models/User.php';
require_once 'conn/index.php';

class UserController {
    private $user;

    public function __construct(User $user) {
        $this->user = $user;
    }

    public function login($username, $password) {
        $userStatus = $this->user->getUserStatus($username);

        switch ($userStatus) {

            case '1':
                session_start();
            	$_SESSION['token']=base64_encode($username);
                $_SESSION['pass']=$password;
                header('Location: view/');
                exit();

            case '2':
                session_start();
                $_SESSION['token']=base64_encode($username);
                $_SESSION['pass']=$password;
                header('Location: view/op');
                exit();

            case '0':
                // Utente sospeso, reindirizza a una pagina di sospensione
                header('Location: view/sospeso');
                exit();

            default:
                header('Location: errore');
                exit();
        }
    }

}






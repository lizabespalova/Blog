<?php

namespace controllers\auth_controllers;

use models\User;
use PHPMailer;
use services\EmailService;

require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ .'/../../../config/config.php';
require_once __DIR__ . '/../../views/auth/phpmailer/PHPMailerAutoload.php';



class ForgetPasswordController {

    private $userModel;
    private  $emailService;

    public function __construct($dbConnection) {
        $this->userModel = new User($dbConnection);
        $this->emailService = new EmailService();
//        $this->emailService->configure_mailer();
    }
    public function show_forget_form(){
        include __DIR__ . '/../../views/auth/form_forget.php';
    }
    public function handleResetRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset'])) {
            $email = $_POST['email'];
            $user = $this->userModel->get_user_by_email($email);

            if ($user) {
                $this->emailService->sendResetEmail($user, $email);
            } else {
                echo "Email is not registered!";
            }
        }
    }
}

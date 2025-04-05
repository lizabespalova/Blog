<?php

namespace controllers;
use models\Search;

require_once 'app/services/helpers/session_check.php';

class FooterController
{


   public function showAboutPage(){
       require_once 'app/services/helpers/switch_language.php';

       include __DIR__ . '/../views/common_templates/about.php';

   }
   public function showContactPage(){
       require_once 'app/services/helpers/switch_language.php';

       include __DIR__ . '/../views/common_templates/contact_form.php';
   }
   public function showPrivacyPolicy(){
       require_once 'app/services/helpers/switch_language.php';

       include __DIR__ . '/../views/common_templates/privacy_policy_form.php';
   }
}
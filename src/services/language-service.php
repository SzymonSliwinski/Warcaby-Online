<?php
    require_once __DIR__.'/file-service.php';

    class LanguageService{
        public $language;

        public function __construct(){
          $fileService = new FileService();

          if(isset($_COOKIE['language'])){
            $this->language = $fileService->GetLanguage($_COOKIE['language']);
          } else {
            $this->language = $fileService->GetLanguage('polish');
          }
      }  
    }  

?>

<?php 
  
  class News extends CustomMetaBox {

    public $title = 'members';
    public $post_type = 'newsletter';
    public $boxname = 'Versenden';
    public $context = 'side';

    public function init_array(){
      $this->working_dir = preg_replace( '/\/boxes$/', '', dirname( __FILE__));
      $this->fields_array = array(
         array(
          'type'  => 'send'
        )
      );
    }
  }
  
?>

<?php 
  
  class News extends CustomMetaBox {

    public $title = 'newsletter';
    public $post_type = 'newsletter';
    public $boxname = 'Versenden';
    public $context = 'side';

    public function init_array(){
      $this->working_dir = preg_replace( '/\/boxes$/', '', dirname( __FILE__));
      $this->fields_array = array(
         array(
          'type'  => 'send'
        ),        
         array(
          'label' => 'Testemail versenden',
          'id'    => $this->title.'_test',
          'value' => true,
          'type'  => 'checkbox'
        ),     
         array(
          'label' => 'Testadresse',
          'id'    => $this->title.'_testmailer',
          'type'  => 'text',
          'text'  => 'Update bevor versenden'
        )
      );
    }
  }
  
?>

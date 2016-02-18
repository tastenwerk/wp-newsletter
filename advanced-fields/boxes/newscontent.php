<?php 
  
  class Newscontent extends CustomMetaBox {

    public $title = 'newsletter';
    public $post_type = 'newsletter';
    public $boxname = 'Inhalt';

    public function init_array(){
      $this->working_dir = preg_replace( '/\/boxes$/', '', dirname( __FILE__));
      $this->fields_array = array(
         array(
          'label' => 'Betreff',
          'id'    => $this->title.'_subject',
          'type'  => 'text',
          'size'  => 40
        ),
        //  array(
        //   'label' => 'Absender',
        //   'id'    => $this->title.'_from',
        //   'type'  => 'text',
        //   'size'  => 40
        // ),
        array(
          'label' => 'Beiträge',
          'desc'  => 'Hier können Beiträge erstellt und verlinkt werden',
          'id'    => $this->title.'_igft_posts',
          'type'  => 'newsletter'
        ),
      );
    }
  }
  
?>

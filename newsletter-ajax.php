<?php
  


require("lib/sendgrid-php/sendgrid-php.php");

class NewsletterAjax{

  public function __construct() {
    add_action( 'wp_ajax_new_igft_position', array( $this,'new_igft_position') );
    add_action( 'wp_ajax_send_newsletter', array( $this,'send_newsletter') );
    add_action( 'wp_ajax_create_or_update_igft_post', array( $this,'create_or_update_igft_post') );
  }


  function add_to_table( $table, $key, $value ){
    $keys = array_keys($table);
    if( !in_array( $key, $keys) )
      $table[$key] = array();
    array_push( $table[$key], $value );
    return $table;
  }

  function moveElement(&$array, $a, $b) {
    $out = array_splice($array, $a, 1);
    array_splice($array, $b, 0, $out);
  }

  public function new_igft_position(){   
    $newsletter_id = $_POST['post_id'];
    $new_pos = $_POST['new_pos'];
    $old_pos = $_POST['old_pos'];
    $igft_posts = get_post_meta( $newsletter_id, 'newsletter_igft_posts', TRUE );
    $this->moveElement( $igft_posts, $old_pos, $new_pos );
    update_post_meta( $newsletter_id, 'newsletter_igft_posts', $igft_posts );
    die();
  }

  public function send_newsletter() {     
    $newsletter_id = $_POST['post_id'];

    $posts = get_posts( array (
    'post_type' => 'igft_post',
    'meta_query' => array(
        array(
          'value' => $newsletter_id,
          'key' => 'newsletter_id'
        ),
    ),
    'meta_key' => 'newsletter_position',
    'orderby' => 'meta_value_num',
    'order' => 'ASC',
    'posts_per_page' => -1
  ));

    $hr = '<hr height="1" style="height:1px; border:0 none; color: #EF0808; background-color: #EF0808;">';

    $content = "<h1>".get_post( $newsletter_id )->post_title."</h1>";
    $content = $content.$hr;

    // Inhaltsverzeichnis
    $table = array();
    foreach ($posts as $post) {
      $cur_menu = wp_get_post_terms( $post->ID, 'menu', array("fields" => "names") )[0];
      $cur_submenu = wp_get_post_terms( $post->ID, 'submenu', array("fields" => "names") )[0];
      if( $cur_submenu ){
        $table = $this->add_to_table( $table, $cur_submenu, $post->post_title );
      }elseif( $cur_menu ){
        $table = $this->add_to_table( $table, $cur_menu, $post->post_title );
      }else{
        $table = $this->add_to_table( $table, 'Unkategorisiert', $post->post_title );
      }
    }

    $content = $content."<h2>Inhaltsübersicht</h2>";
    $index = 0;
    foreach ($table as $key => $value ) {
      $content = $content."<h3>".$key."</h3>";
      foreach ($value as $item ) {
        $content = $content."<a style='color: #EF0808; font-weight: bold;' href='#".$index."' >".$item."</a><br>";
      }
      // href='#".$index."
      // $content = $content."<a style='color: #EF0808; font-weight: bold;' href='#".$index."' >".$post->post_title."</a><br>";
      $index++;
    }
    $content = $content.$hr;

    // Content
    $index = 0;
    foreach ($posts as $post) {
      $content = $content."<h2 name='#".$index."'>".$post->post_title."</h2>";
      $content = $content.$post->post_content;
      $content = $content.$hr;
    }

    $sendgrid = new SendGrid('igft','IgftNews16');
    $from = get_post_meta( $newsletter_id, 'newsletter_from', TRUE );
    $subject =  get_post_meta( $newsletter_id, 'newsletter_subject', TRUE );

    $email = new SendGrid\Email();
    $email
        ->addTo('david.reinisch@gmx.at')
        //->addTo('bar@foo.com') //One of the most notable changes is how `addTo()` behaves. We are now using our Web API parameters instead of the X-SMTPAPI header. What this means is that if you call `addTo()` multiple times for an email, **ONE** email will be sent with each email address visible to everyone.
        ->setFrom('newsletter@freietheater.at')
        ->setSubject($subject)
        ->setText($content)
        ->setHtml($content)
    ;

    var_dump( $sendgrid->send($email) );
    die();
  }

  public function create_or_update_igft_post() { 
    $post_id = $_POST['igft_post_id'];
    $newsletter_id = $_POST['post_id'];

    if( !$post_id ):
      $post = array(
        'post_name'      => $_POST['title'],
        'post_type'      => "igft_post",
        'post_status'    => 'publish' 
      );
      $post_id = wp_insert_post( $post );
    endif;

    $this->add_or_update( $post_id, 'newsletter_id', $newsletter_id );
    $this->add_or_update( $post_id, 'newsletter_position', $_POST['position'] );

    // print_r( htmlspecialchars( $_POST['content'] ) );
    // echo $_POST['content'];

    $my_post = array(
      'ID'           => $post_id,
      'post_title'   =>  $_POST['title'],
      'post_content' =>  $_POST['content'],
    );
    
    wp_update_post( $my_post );      

    wp_set_object_terms( $post_id, array( $_POST['menu'] ), 'menu', FALSE );
    wp_set_object_terms( $post_id, array( $_POST['submenu'] ), 'submenu', FALSE );

    $result = array();
    $result['id'] = $post_id;
    $result['content'] = get_post( $post_id )->post_content;

    // print_r( get_post_meta($post_id) );
    echo json_encode( $result );

    die();
  }

  function add_or_update( $post_id, $meta_string, $meta_value ){
    if( get_post_meta( $post_id, $meta_string, TRUE )  )
      update_post_meta( $post_id, $meta_string, $meta_value );
    else
      add_post_meta( $post_id, $meta_string, $meta_value );
  }
}


?>
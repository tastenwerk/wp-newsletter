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
    // echo "HERE!";
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


    $head = '<html>
      <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
      </head>
    <body bgcolor="#ef0808" text="#000000"><div id="newsletterWrapper" style="background-color:
        #ef0808;font-family:Arial;font-size:12px; margin:0;padding:0;">
      <style type="text/css">
   #newsletterWrapper a:link, #newsletterWrapper a:active, #newsletterWrapper a:visited
       {
         color: #ef0808;
         text-decoration: none;
       }
   #newsletterWrapper a:hover
       {
         color: #000000;
       }
   #unsubscribe a:link, #unsubscribe a:active, #unsubscribe a:visited
       {
         color: #ffffff;
       }
     </style>
     <div style="width:616px; margin: auto; padding:0;">
      <img src="http://freietheater.sisyphos.at/wp-content/themes/igft/img/newsletter.png" style="width:635px; height: 130px; margin:0;">
     </div>
     <div style="width:616px; margin: auto; background-color:white; padding: 16px; font-family:Arial;">';

     $content = $head;
     

      $content = $content."<div style='font-size:19px;'>".get_post( $newsletter_id )->post_title."</div><br>";$content = $content."Sollte dieser Newsletter nicht richtig angezeigt werden, klicken Sie bitte <a href=".get_permalink( $newsletter_id )."
      >hier</a>";
      // $content = $content.$hr;

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

      $content = $content.'<div style="font-size:19px;">Inhaltsübersicht</div>';
      $index = 0;
      foreach ($table as $key => $value ) {
        $content = $content.'<div style="font-weight:bold; padding-top: 20px">'.$key."</div>";
        foreach ($value as $item ) {
          $content = $content."<a style='color: #EF0808; font-weight: bold;' href=\"#article_".$index."\" >".$item."</a><br>";
        }
      // href='#".$index."
      // $content = $content."<a style='color: #EF0808; font-weight: bold;' href='#".$index."' >".$post->post_title."</a><br>";
        $index++;
      }
      $content = $content.$hr;

    // Content
      $index = 0;
      foreach ($posts as $post) {
        $content = $content."<div style='font-size:19px;' id='article_".$index."'>".$post->post_title."</div>";
        $content = $content.$post->post_content;
        $content = $content."<a href=#>Nach oben</a>";
        $content = $content.$hr;
        $index++;
      }

      $footer = '</body></html>';
      $content = $content.$footer;

      $sendgrid = new SendGrid('igft','IgftNews16');
      $from = get_post_meta( $newsletter_id, 'newsletter_from', TRUE );
      $subject =  get_post_meta( $newsletter_id, 'newsletter_subject', TRUE );

      $testmailer = get_post_meta( $newsletter_id,'newsletter_testmailer', TRUE );
      echo "HERE:".$testmailer;
      $testmailer = $testmailer ? $testmailer : 'david.reinisch@gmx.at';
      if( get_post_meta( $newsletter_id,'newsletter_test', TRUE ) ){
        $email = new SendGrid\Email();
        $email
        ->addTo($testmailer)
          //->addTo('bar@foo.com') //One of the most notable changes is how `addTo()` behaves. We are now using our Web API parameters instead of the X-SMTPAPI header. What this means is that if you call `addTo()` multiple times for an email, **ONE** email will be sent with each email address visible to everyone.
        ->setFrom('newsletter@freietheater.at')
        ->setSubject($subject)
        ->setText( strip_tags(str_replace( "<br>", "\n", $content )))
        ->setHtml($content)
        ;

        var_dump( $sendgrid->send($email) );
      }
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
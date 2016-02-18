<?php
/**
 * @package wp-newsletter
 * @version 2.0
 */
/*
  Plugin Name: wp-newsletter
  Description: Newsletter system for wp
  Author: DR (TASTENWERK)
  Version: 1.0
  Author URI: http://tastenwerk.com
*/

include 'newsletter-ajax.php';

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if ( ! function_exists( 'get_plugins' ) ) {
  require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

  // 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 -->

// require("lib/sendgrid-php/sendgrid-php.php");

class Newsletter{

  public $title = 'newsletter';

  function __construct() {
    add_action('init', array( $this, 'create_post_type') );
    new NewsletterAjax();


    wp_enqueue_script( 'ajax-send-newsletter', plugin_dir_url( __FILE__ ) . 'js/send.js', array( 'jquery' ), '1.0', true );
    wp_localize_script( 'ajax-send-newsletter', 'ajaxpagination', array(
      'ajaxurl' => admin_url( 'admin-ajax.php' )
    ));

  }

   public function create_post_type(){
      register_post_type($this->title, // Register Custom Post Type
        array(
          'labels' => array(
          'name' => __('Newsletter', $this->title), 
          'singular_name' => __('Newsletter', $this->title),
          'add_new' => __('Neuen Eintrag hinzufügen', $this->title),
          'add_new_item' => __('Neues Newsletter', $this->title),
          'edit' => __('Bearbeiten', $this->title),
          'edit_item' => __('Newsletter bearbeiten', $this->title),
          ),
          'public' => true,
        'hierarchical' => true, // Allows your posts to behave like Hierarchy Pages
        'has_archive' => true,
        'supports' => array(
          'title',
          // 'editor',
          // 'excerpt',
          // 'thumbnail'
        ), // Go to Dashboard Custom HTML5 Blank post for supports
        'can_export' => true, // Allows export in Tools > Export
        'taxonomies' => array(
          // 'post_tag',
          'category'
        ), // Add Category and Post Tags support,
        ));

    }

  function send_mail(){
    $sendgrid = new SendGrid('igft','IgftNews16');

    $email = new SendGrid\Email();
    $email
        ->addTo('david.reinisch@gmx.at')
        //->addTo('bar@foo.com') //One of the most notable changes is how `addTo()` behaves. We are now using our Web API parameters instead of the X-SMTPAPI header. What this means is that if you call `addTo()` multiple times for an email, **ONE** email will be sent with each email address visible to everyone.
        ->setFrom('me@bar.com')
        ->setSubject('Subject goes here')
        ->setText('Hello World!')
        ->setHtml('<strong>Hello World!</strong>')
    ;

    var_dump( $sendgrid->send($email) );
  }

}

$plugin = new Newsletter();

?>


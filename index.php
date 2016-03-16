<?php
/**
 * @package TkMailer
 * @version 1.0
 */
/*
Plugin Name: TkMailer
Plugin URI: http://wordpress.org/plugins/advanced-fields/
Description: 
Author: TZ, DR (TASTENWERK)
Version: 1.0
Author URI: http://tastenwerk.com
*/

include 'newsletter-ajax.php';

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if ( ! function_exists( 'get_plugins' ) ) {
  require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

class Tkmailer{

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
          // 'category'
        ), // Add Category and Post Tags support,
        ));

      // Add new taxonomy, NOT hierarchical (like tags)
        $labels = array(
          'name'                       => _x( 'labels', 'taxonomy general name' ),
          'singular_name'              => _x( 'label', 'taxonomy singular name' ),
          'search_items'               => __( 'Search labels' ),
          'popular_items'              => __( 'Popular labels' ),
          'all_items'                  => __( 'All labels' ),
          'parent_item'                => null,
          'parent_item_colon'          => null,
          'edit_item'                  => __( 'Edit label' ),
          'update_item'                => __( 'Update label' ),
          'add_new_item'               => __( 'Add New label' ),
          'new_item_name'              => __( 'New label Name' ),
          'separate_items_with_commas' => __( 'Separate label with commas' ),
          'add_or_remove_items'        => __( 'Add or remove label' ),
          'choose_from_most_used'      => __( 'Choose from the most used label' ),
          'not_found'                  => __( 'No label found.' ),
          'menu_name'                  => __( 'labels' ),
        );

        $args = array(
          'hierarchical'          => false,
          'labels'                => $labels,
          'show_ui'               => true,
          'show_admin_column'     => true,
          'update_count_callback' => '_update_post_term_count',
          'query_var'             => true,
          'rewrite'               => array( 'slug' => 'post_tag' ),
        );

        register_taxonomy( 'post_tag', 'newsletter', $args );

    }
}

$plugin = new Tkmailer();

?>

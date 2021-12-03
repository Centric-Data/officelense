<?php
/**
 * Office-Lense
 *
 * @package     Office-Lense
 * @author      Centric Data
 * @copyright   2021 Centric Data
 * @license     GPL-2.0-or-later
 *
*/
/*
Plugin Name: Office-Lense
Plugin URI:  https://github.com/Centric-Data/officelense
Description: This is a custom offices plugin, to list office branches in the contact page. Its using flex column layout for columns with custom css.
Author: Centric Data
Version: 1.0.0
Author URI: https://github.com/Centric-Data
Text Domain: officelense
*/
/*
Office-Lense is free software: you can redistribute it and/or modify it under the terms of GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or any later version.

Office-Lense is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with Contact-Lense Form.
*/

/* Exit if directly accessed */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define variable for path to this plugin file.
define( 'OFL_LOCATION', dirname( __FILE__ ) );
define( 'OFL_LOCATION_URL' , plugins_url( '', __FILE__ ) );
define( 'OFL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );


/**
 *
 */
class OfficeLense
{

  public function __construct()
  {
    // Enqueue Scripts.
    add_action( 'wp_enqueue_scripts', array( $this, 'ofl_load_assets' ) );

    // Add Shortcode
    add_shortcode( 'office-lense', array( $this, 'ofl_branch_grid_shortcode' ) );

    // Create Custom Post Type 'Offices'
    add_action( 'init', array( $this, 'ofl_offices_post_type' ) );

    add_action( 'init', array( $this, 'ofl_create_branch_taxonomy' ), 0 );

    // Create Meta Boxes in CPT offices
    add_action( 'add_meta_boxes', array( $this, 'ofl_custom_add_metabox' ) );

    // CPT Custom Columns
    add_filter( 'manage_offices_posts_columns', array( $this, 'ofl_slides_columns' ) );

    // Save Meta Box Data
    add_action( 'save_post', array( $this, 'ofl_save_meta_box' ) );

    // Fetch Meta Data
    add_action( 'manage_offices_posts_custom_columns', array( $this, 'ofl_custom_column_data' ), 10, 2 );

    // Register REST Route
    add_filter( 'rest_route_for_post', array( $this, 'ofl_rest_route_cpt' ), 10, 2 );

  }

  // Enqueue Scripts
  public function ofl_load_assets()
  {
    wp_enqueue_style( 'officelense-css', OFL_PLUGIN_URL . 'css/officelense.css', [], time(), 'all' );
    wp_enqueue_script( 'officelense-js', OFL_PLUGIN_URL . 'js/officelense.js', ['jquery'], time(), 1 );
  }

  /**
   * Create Custom Post Type 'Offices'
   */
   public function ofl_offices_post_type(){
     $args = array(
       'labels'           =>  array(
         'name'           =>  __( 'Offices', 'officelense' ),
         'singular_name'  =>  __( 'Office', 'officelense' ),
         'menu_name'      =>  _x( 'Offices', 'Admin Text Menu', 'officelense' ),
         'add_new'        =>  __( 'Add New', 'officelense' ),
         'add_new_item'   =>  __( 'Add New Office', 'officelense' ),
         'new_item'       =>  __( 'New Office', 'officelense' ),
         'edit_item'      =>  __( 'Edit Office', 'officelense' ),
         'view_item'      =>  __( 'View Office', 'officelense' ),
         'all_items'      =>  __( 'All Offices', 'officelense' ),
         'search_items'   =>  __( 'Search Offices', 'officelense' ),
       ),
       'hierarchical'     =>  false,
       'public'           =>  true,
       'rewrite'          =>  array(
         'slug'       =>  'offices/%officescat%/',
         'with_front' => FALSE
       ),
       'capability_type'  =>  'post',
       'has_archive'      =>  'offices',
       'show_in_rest'     =>  true,
       'rest_base'        =>  'offices',
       'rest_controller_class'  =>  'WP_REST_Posts_Controller',
       'supports'         =>  array( 'title', 'editor' ),
       'menu_icon'        =>  'dashicons-location-alt',
     );

     register_post_type( 'offices', $args );
   }

   /**
   * Create branches taxonomy for the post type "centric_enquire"
   *
   */
   public function ofl_create_branch_taxonomy() {
     $labels = array(
       'name'              => _x( 'Branches', 'contactlense' ),
       'singular_name'     => _x( 'Branch', 'contactlense' ),
       'search_items'      => __( 'Search Branches', 'contactlense' ),
       'edit_item'         => __( 'Edit Branch', 'contactlense' ),
       'update_item'       => __( 'Update Branch', 'contactlense' ),
       'add_new_item'      => __( 'Add New Branch', 'contactlense' ),
       'new_item_name'     => __( 'New Branch Name', 'contactlense' ),
       'menu_name'         => __( 'Branch', 'contactlense' ),
     );

     $args = array(
       'hierarchical'      => true,
       'labels'            => $labels,
       'show ui'           => true,
       'show_admin_column' => true,
       'query_var'         => true,
       'rewrite'           => array( 'slug'  =>  'branch' ),
     );

     register_taxonomy( 'branch', array( 'offices' ), $args );
   }

   /**
   * Adds the meta box
   */
   public function ofl_custom_add_metabox() {
     add_meta_box( 'office_fields', __( 'Office Details', 'officelense' ), array( $this, 'ofl_render_officebox' ), 'offices', 'advanced', 'high' );
   }

   // Render Meta-boxes
   public function ofl_render_officebox( $post ){
     // Add nonce for security and authentication.
     include( OFL_LOCATION . '/inc/box_forms.php' );
   }

   // Shortcode Function
   public function ofl_branch_grid_shortcode() {
     include( OFL_LOCATION . '/inc/shortcodehtml.php' );
   }

   // Register a route
   public function ofl_rest_route_cpt( $route, $post ){
     if ( $post->post_type === 'offices' ) {
       $route = '/wp/v2/offices/' . $post->ID;
     }
     return $route;
   }

   // Custom Slides CPT columns
   public function ofl_slides_columns( $columns ){
     $newColumns = array();
     $newColumns['title']   = 'Office Name';
     $newColumns['details'] = 'Office Address';
     $newColumns['phone']   = 'Office Phone';
     $newColumns['email']   = 'Office Email';
     $newColumns['date']    = 'Date';

     return $newColumns;
   }

   // Save data from meta boxes
   public function ofl_save_meta_box( $post_id ){
     if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
     if ( $parent_id = wp_is_post_revision( $post_id ) ) {
       $post_id $parent_id;
     }
     $fields = [
       'office_phone',
       'office_email'
     ];
     foreach ( $fields as $field ) {
       if ( array_key_exists( $field, $_POST ) ) {
         update_post_meta( $post_id, $field, sanitize_text_field( $_POST[$field] ) );
       }
     }
   }

   // Fetch and populate slider data
   public function ofl_custom_column_data( $column, $post_id ){
     switch ( $column ) {
       case 'details':
         echo get_the_excerpt();
         break;
      case 'phone':
        $phone = get_post_meta(get_the_ID(), 'office_phone', true);
        echo $phone;
      break;
      case 'email':
        $email = get_post_meta(get_the_ID(), 'office_email', true);
        echo $email;
      break;

       default:
         // code...
         break;
     }
   }

}

new OfficeLense;

?>

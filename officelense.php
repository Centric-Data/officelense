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
    // Create Custom Post Type 'Offices'
    add_action( 'init', array( $this, 'ofl_offices_post_type' ) );

    add_action( 'init', array( $this, 'ofl_create_branch_taxonomy' ), 0 );

    add_action( 'add_meta_boxes', array( $this, 'ofl_custom_add_metabox' ) );

    add_shortcode( 'office-lense', array( $this, 'ofl_branch_grid_shortcode' ) );

    add_action( 'wp_enqueue_scripts', array( $this, 'ofl_load_assets' ) );

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
       'rewrite'          =>  array( 'slug' =>  'office' ),
       'capability_type'  =>  'post',
       'has_archive'      =>  true,
       'supports'         =>  array( 'title', 'editor' ),
       'exclude_from_search'   =>  true,
       'publicly_queryable'    =>  false,
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
     add_meta_box( 'ofl_phone_meta_box', __( 'Office Phone Number', 'officelense' ), array( $this, 'ofl_render_phone_meta_box' ), 'offices', 'side', 'default' );
     add_meta_box( 'ofl_email_meta_box', __( 'Office Email', 'officelense' ), array( $this, 'ofl_render_email_meta_box' ), 'offices', 'side', 'default' );
   }

   public function ofl_render_phone_meta_box( $post ){
     // Add nonce for security and authentication.
     wp_nonce_field( 'ofl_nonce_phone_action', 'custom_phone_nonce' );
     ?>
     <label for="ofl_phone"></label>
      <input type="text" name="ofl_number" value="">
     <?php
   }

   public function ofl_render_email_meta_box( $post ){
     // Add nonce for security and authentication.
     wp_nonce_field( 'ofl_nonce_email_action', 'custom_email_nonce' );
     ?>
     <label for="ofl_email"></label>
      <input type="text" name="ofl_email" value="">
     <?php
   }

   // Shortcode Function
   public function ofl_branch_grid_shortcode() {
     ?>
     <section>
    	<div class="branch__wrapper lense-row">
    		<div class="branch__offices">
    			<h3>Regional Offices</h3>
    			<ul>
    				<li>
    					<div class="branch__offices--a">
    						<h4>Mashonaland Central Province</h4>
    						<p>Veterinary Complex<br>Bindura<br> info@zlc.co.zw <br> Tel: +263-266-2106003</p>
    					</div>
    				</li>
    				<li>
    					<div class="branch__offices--a">
    						<h4>Manicaland Province</h4>
    						<p>Government Complex, 2nd Floor<br>Mutare<br> info@zlc.co.zw <br> Tel: +263-020-2061307</p>
    					</div>
    				</li>
    				<li>
    					<div class="branch__offices--a">
    						<h4>Masvingo Province</h4>
    						<p>Block No.4, Old Government Complex<br>Masvingo<br> info@zlc.co.zw <br> Tel: +263-239-2260744</p>
    					</div>
    				</li>
    				<li>
    					<div class="branch__offices--a">
    						<h4>Mashonaland East Province</h4>
    						<p>DA`s Office, Cnr 1st & S. Machel Street<br>Marondera<br> info@zlc.co.zw <br> Tel: +263-279-2325537</p>
    					</div>
    				</li>
    				<li>
    					<div class="branch__offices--a">
    						<h4>Mashonaland West Province</h4>
    						<p>Former PA Building, Opposite OK Supermarket<br>Chinhoyi<br> info@zlc.co.zw <br> Tel: +263-0267-2123416</p>
    					</div>
    				</li>
    				<li>
    					<div class="branch__offices--a">
    						<h4>Matebeleland North Province</h4>
    						<p>Government Complex (Social Services Building)<br>Cnr Fort Street & 10th Avenue<br>Bulawayo<br> info@zlc.co.zw <br> Tel: +263-029-63669</p>
    					</div>
    				</li>
    				<li>
    					<div class="branch__offices--a">
    						<h4>Matebeleland South Province</h4>
    						<p>Mtshabezi Building<br>Cnr 4th Avenue & King Street<br>Gwanda<br> info@zlc.co.zw <br> Tel: +263-0284-2821178</p>
    					</div>
    				</li>
    				<li>
    					<div class="branch__offices--a">
    						<h4>Midlands Province</h4>
    						<p>New Government Complex<br>Gweru<br> info@zlc.co.zw <br> Tel: +263-254-2106003</p>
    					</div>
    				</li>
    			</ul>
    		</div>
    	</div>
    </section>
     <?php
   }


}

new OfficeLense;

?>

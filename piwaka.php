<?php
/*
Plugin Name: Piwaka
Plugin URI: http://piwaka.tanyagray.co.nz/
Description: Creates custom posts for Tweeted pictures
Version: 1.0
Author: Tanya Gray
Author URI: http://tanyagray.co.nz
License: GPL2
*/

class Piwaka {

	public  $plugin_url,
			$plugin_dir,
			$plugin_name,
			$plugin_basename;

	/*
	* Construct
	*/
	function __construct() {

		// The Plugin URL Path
	    $this->plugin_url = WP_PLUGIN_URL . '/' . str_replace(basename( __FILE__), "" ,plugin_basename(__FILE__));
	    // The Plugin DIR Path
	    $this->plugin_dir = WP_PLUGIN_DIR . '/' . str_replace(basename( __FILE__), "" ,plugin_basename(__FILE__));
	    // The Plugin Name. Derived from the plugin folder name.
	    $this->plugin_name = basename(dirname(__FILE__));
	    // The Plugin Basename
	    $this->plugin_basename = plugin_basename(__FILE__);

    	// Register the custom post type
    	add_action( 'init', array( $this, 'register_post_type' ) );
    	// Display the icon for the custom post type
    	add_action( 'admin_head', array( $this, 'cpt_icons') );
    	// register the thumbnail size for the slideshow
    	add_action( 'init', array( $this, 'register_thumbnail_size' ) );
    	// Save the event meta data
    	add_action( 'save_post', array( $this, 'save_trip_meta' ) ) ;

    	// Modify the columns shown in the trips list
    	add_filter( 'manage_trip_posts_columns', array( $this, 'change_trip_list_columns') );

	}


	/*
	* Register the 'trips' post type as a menu item
	*/
	function register_post_type() {

		$args = array(
			'labels'	=>	array(
	            'all_items' => 'Piwaka Pics',
					'menu_name'	          =>	'Piwaka Pics',
					'singular_name'       =>	'Post',
				 	'edit_item'           =>	'Edit Post',
				 	'new_item'            =>	'New Post',
				 	'view_item'           =>	'View Post',
				 	'items_archive'       =>	'Post Archive',
				 	'search_items'        =>	'Search Posts',
				 	'not_found'	          =>	'No posts found',
				 	'not_found_in_trash'  =>	'No posts found in trash'	
				),
			'supports'		=>	array( 'title', 'author', 'excerpt', 'thumbnail' ),	
			'show_in_nav_menus'  =>  true,			
			'show_in_menu'  =>  true,
			'menu_position'	=>	5,
			'public'		=>	true,
			'rewrite' 		=> array('slug' => 'photo')
		);


		register_post_type( 'piwaka_pic', $args );
	}


	function cpt_icons() {
	    
	    echo '<style type="text/css" media="screen">'.
	        	'#menu-posts-trip .wp-menu-image {'.
	            	'background: url('.$this->plugin_url.'/images/trip-post-icon.png) no-repeat 6px -17px !important;'.
	        	'}'.
	        	'#menu-posts-trip:hover .wp-menu-image, #menu-posts-trip.wp-has-current-submenu .wp-menu-image {'.
	            	'background-position:6px 7px !important;'.
	        	'}'.
	    	'</style>';
	}


	/* 
	 * Add a custom thumbnail size for the trips slideshow
	 */
	function register_thumbnail_size() {

		$name = "preview";
		$width = 1040;
		$height = 332;
		$crop = true;

		add_image_size( $name, $width, $height, $crop );
	}


}

$piwaka = new Piwaka();


?>
<?php
/*
Plugin Name: Piwaka
Plugin URI: http://piwaka.tanyagray.co.nz/
Description: Provides a url and custom post type for posting pictures from Twitter apps directly to your blog.
Version: 1.0
Author: Tanya Gray
Author URI: http://tanyagray.co.nz
License: GPL2
*/

include_once( WP_PLUGIN_DIR . '/piwaka/functions.php' );

include_once( WP_PLUGIN_DIR . '/piwaka/admin/PiwakaAdmin.php' );
include_once( WP_PLUGIN_DIR . '/piwaka/shortcodes/FeaturedImage.php' );
include_once( WP_PLUGIN_DIR . '/piwaka/shortcodes/ImageGallery.php' );


class Piwaka {

	public  $plugin_url,
			$plugin_dir,
			$plugin_name,
			$plugin_basename;

	/*
	 * Construct
	 */
	function __construct() {

		$this->plugin_url = WP_PLUGIN_URL . '/' . str_replace(basename( __FILE__), "" ,plugin_basename(__FILE__));
		$this->plugin_dir = WP_PLUGIN_DIR . '/' . str_replace(basename( __FILE__), "" ,plugin_basename(__FILE__));
		$this->plugin_name = basename(dirname(__FILE__));
		$this->plugin_basename = plugin_basename(__FILE__);

		// activation and deactivation tasks
		register_activation_hook( __FILE__, array( $this, 'activate_piwaka' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate_piwaka' ) );

		// wordpress action hooks
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'generate_rewrite_rules', array( $this, 'redirect_api_requests' ) );
		add_action( 'admin_head', array( $this, 'display_custom_menubar_icon') );
		

	}

	
	function activate_piwaka() {

		$this->redirect_api_requests();

		flush_rewrite_rules();

	}

	function deactivate_piwaka() {

		// TODO: Remove the rules we added

		flush_rewrite_rules();

	}


	/*
	 * Register the 'piwaka' post type and all its labels.
	 */
	function register_post_type() {

		$args = array(
			'labels'	=>	array(
				'all_items' => 'All Photos',
				'menu_name'	          =>	'Photos',
				'singular_name'       =>	'Post',
				'edit_item'           =>	'Edit Post',
				'new_item'            =>	'New Post',
				'view_item'           =>	'View Post',
				'items_archive'       =>	'Post Archive',
				'search_items'        =>	'Search Posts',
				'not_found'	          =>	'No posts found',
				'not_found_in_trash'  =>	'No posts found in trash'	
			),
			'supports'		=>	array( 'title', 'editor', 'thumbnail' ),	
			'show_in_nav_menus'  =>  true,			
			'show_in_menu'  =>  true,
			'menu_position'	=>	5,
			'public'		=>	true,
			'rewrite' 		=>  array('slug' => 'photo')
		);


		register_post_type( 'piwaka', $args );
	}


	/*
	 * Allow us to use a short URL to access the piwaka API
	 * eg. http://mysite.com/piwaka/ redirects to
	 * http://mysite.com/wp-content/plugins/piwaka/
	 */
	function redirect_api_requests() {
		
		add_rewrite_rule(  
			'piwaka/upload/?$', 
			'wp-content/plugins/piwaka/api/upload.php', 
			'top' 
		); 

	}


	/**
	 * Displays a custom icon next to the Photos admin menu item.
	 */
	function display_custom_menubar_icon() {
		
		echo '<style type="text/css" media="screen">'.
				'#menu-posts-piwaka .wp-menu-image {'.
					'background: url('.$this->plugin_url.'/images/piwaka-post-icon.png) no-repeat 6px -17px !important;'.
				'}'.
				'#menu-posts-piwaka:hover .wp-menu-image, #menu-posts-piwaka.wp-has-current-submenu .wp-menu-image {'.
					'background-position:6px 7px !important;'.
				'}'.
			'</style>';
	}


}

$piwaka = new Piwaka();


?>
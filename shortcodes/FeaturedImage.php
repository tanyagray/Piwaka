<?php

include_once( WP_PLUGIN_DIR . '/piwaka/functions.php' );


class FeaturedImage {
	
	public $url;
	public $alt;
	public $html;


	function __construct() {

		$this->init();

	}


	/*
	 * Set up the featured image shortcode and its
	 * required functionality hooks.
	 */
	function init() {

		add_shortcode('piwaka-featured', array( $this, 'handle_shortcode' ) );

		add_action('init', array($this, 'register_styles'));
		add_action('wp_footer', array($this, 'print_styles'));

	}


	/*
	 * Renders the post or page's featured image as HTML
	 */
	function handle_shortcode() {

		$this->get_post_meta();
		$this->generate_html();

		return $this->html;
	}


	function get_post_meta() {

		global $post;

		$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'large');

		$this->url = $thumbnail[0];
		$this->alt = $post->post_title;
	}


	function generate_html() {

		$html_format = '<img class="piwaka-image paper" src="%s" alt="%s">';
		$html = sprintf($html_format, $this->url, $this->alt);

		$this->html = $html;
	}

	/*
	 * Registers any js and css required for 
	 * this shortcode to function.
	 */
	function register_styles() {

		$css_url = plugins_url('piwaka/css/piwaka.css');
		wp_register_style('piwaka-gallery-css', $css_url);

	}


	/*
	 * Prints any scripts which have been included
	 * for this shortcode.
	 */
	function print_styles() {

		wp_print_styles('piwaka-gallery-css');

	}

}

$featured_shortcode = new FeaturedImage();

?>

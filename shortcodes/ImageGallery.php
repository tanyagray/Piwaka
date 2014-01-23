<?php


class ImageGallery {
	
	private $posts;
	private $html;


	function __construct() {

		$this->init();

	}


	/*
	 * Set up the image gallery shortcode and its
	 * required functionality hooks.
	 */
	function init() {

		add_shortcode('piwaka-gallery', array( $this, 'handle_shortcode' ) );

		add_action('init', array($this, 'register_styles'));
		add_action('wp_footer', array($this, 'print_styles'));

	}


	/*
	 * Returns the gallery HTML for wordpress to 
	 * render to the page.
	 */
	function handle_shortcode() {

		$this->load_posts();
		$this->generate_html();

		return $this->html;
	}


	/*
	 * Loads the set of posts to be displayed in the gallery
	 */
	function load_posts() {

		$post_args = array( 'post_type' => array('piwaka') );
		$posts = get_posts( $post_args );

		$this->posts = $posts;
	}


	/*
	 * Creates the gallery HTML and stores it 
	 * in a local variable.
	 */
	function generate_html() {

		$html_format = '<div class="piwaka-gallery basic">%s</div>';
		$content = '';

		foreach ($this->posts as $piwaka_post) { 

			$thumb_html = $this->get_thumbnail_html($piwaka_post);
			$content .= $thumb_html;

		}

		$this->html = sprintf($html_format, $content);
	}


	/*
	 * Returns the HTML for a single post thumbnail link
	 */
	function get_thumbnail_html($piwaka_post) {

		global $post;

		$post = $piwaka_post;
		setup_postdata($post);

		$html_format = '<a href="%s"><img class="piwaka-image paper" src="%s" alt="%s"></a>';

		$permalink = get_permalink( $post->ID );
		$thumb_url = $this->get_post_thumb_url($post);
		$alt_text = $post->post_title;

		$html = sprintf($html_format, $permalink, $thumb_url, $alt_text);

		// if there is no featured image, leave the post out
		if( empty($thumb_url) ) {
			$html = '';
		}

		return $html;
	}


	/*
	 * Returns the thumbnail URL for a single post
	 */
	function get_post_thumb_url($post) {

		$post_thumbs = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'thumbnail');
		$thumb_url = $post_thumbs[0];

		return $thumb_url;
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

$gallery_shortcode = new ImageGallery();

?>

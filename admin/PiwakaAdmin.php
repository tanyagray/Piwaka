<?php

class PiwakaAdmin {
	
	private $user_settings;


	function __construct() {

		if ( is_admin() ){ // admin actions

		  add_action('admin_menu', array( $this, 'create_menu_page' ) );
		  add_action('admin_init', array( $this, 'register_piwaka_settings' ) );

		} else {
		  // non-admin enqueues, actions, and filters
		}

		

	}


	function create_menu_page() {

		$parent_slug = 'options-general.php';
		$page_title = 'Piwaka Settings';
		$menu_title = 'Piwaka Pics';
		$capability = 'manage_options';
		$menu_slug = 'piwaka-settings';
		$function = array( $this, 'render_page' );

		add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function );

	}


	

	/*
	 * Register all the settings required for the plugin
	 */
	function register_piwaka_settings() {

		register_setting(
            'piwaka_option_group', // Option group
            'piwaka_options', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'required_settings_section', // ID
            'Required Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'piwaka-settings-admin' // Page
        );  

        add_settings_field(
            'twitter_name', // ID
            'Twitter Name', // Title 
            array( $this, 'twitter_name_callback' ), // Callback
            'piwaka-settings-admin', // Page
            'required_settings_section' // Section           
        );      

        /*add_settings_field(
            'title', 
            'Title', 
            array( $this, 'title_callback' ), 
            'piwaka-settings-admin', 
            'required_settings_section'
        ); */    
	}


	function render_page() {
		// Set class property
        $this->options = get_option( 'piwaka_options' );
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2>My Settings</h2>           
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'piwaka_option_group' );   
                do_settings_sections( 'piwaka-settings-admin' );
                submit_button(); 
            ?>
            </form>
        </div>
        <?php
	}


	/**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['twitter_name'] ) )
            $new_input['twitter_name'] = sanitize_text_field( $input['twitter_name'] );

        /*if( isset( $input['title'] ) )
            $new_input['title'] = sanitize_text_field( $input['title'] );*/

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your settings below:';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function twitter_name_callback()
    {
        printf(
            '<input type="text" id="twitter_name" name="piwaka_options[twitter_name]" value="%s" />',
            isset( $this->options['twitter_name'] ) ? esc_attr( $this->options['twitter_name']) : ''
        );
    }

    /** 
     * Get the settings option array and print one of its values
     */
    /*public function title_callback()
    {
        printf(
            '<input type="text" id="title" name="piwaka_options[title]" value="%s" />',
            isset( $this->options['title'] ) ? esc_attr( $this->options['title']) : ''
        );
    }*/

}

$piwaka_admin = new PiwakaAdmin();

?>
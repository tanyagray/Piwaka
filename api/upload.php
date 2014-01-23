<?php

include_once( $_SERVER['DOCUMENT_ROOT'] . "/wp-load.php" );
include_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-admin/includes/file.php' );
include_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-admin/includes/image.php' );
include_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-content/plugins/piwaka/model/PiwakaPost.php' );

class PiwakaUploader {

	private $message;
	private $source;
	private $media;

	private $verified;
	private $verification_endpoint;
	private $verification_credentials;
	private $verification_user_agent;

	private $result;


	/*
	 * Construct
	 */
	function __construct() {

		$this->get_post_vars();
		$this->get_request_headers();
		$this->verify_user();

		if($this->verified) {
			$this->create_post();
		}

		$this->create_result();
		$this->output_result();

	}


	/*
	 * Stores the values POSTed to the page
	 * by the twitter app
	 */
	function get_post_vars() {

		global $_POST;
		global $_FILES;

		$message = $_POST['message'];
		$this->message = $message ? $message : "";

		$source = $_POST['source'];
		$this->source = $source ? $source : "";

		$media = $_FILES['media'];
		$this->media = $media ? $media : "";

	}


	/*
	 * Gets the information required to perform an OAuth Echo verification.
	 * See https://dev.twitter.com/docs/auth/oauth/oauth-echo
	 */
	function get_request_headers() {

		global $_SERVER;

		// TODO: Not supported in some version of PHP. Look into it.
		// $headers = apache_request_headers();

		$headers = array(
			'X-Auth-Service-Provider' => $_SERVER['HTTP_X_AUTH_SERVICE_PROVIDER'],
			'X-Verify-Credentials-Authorization' => $_SERVER['HTTP_X_VERIFY_CREDENTIALS_AUTHORIZATION'],
			'User-Agent' => $_SERVER['HTTP_USER_AGENT']
		);

		$this->verification_endpoint = $headers['X-Auth-Service-Provider'];
		$this->verification_credentials = stripslashes($headers['X-Verify-Credentials-Authorization']);
		$this->verification_user_agent = $headers['User-Agent'];
	}


	function verify_user() {

		$url = $this->verification_endpoint;
		$user_agent = $this->verification_user_agent;
		$ch = curl_init($url);

		// request headers
		$headers = array(
			'Authorization: '.$this->verification_credentials
		);
		
		// Use locally to enable inspecting via a proxy
		//curl_setopt($ch, CURLOPT_PROXY, "127.0.0.1");
		//curl_setopt($ch, CURLOPT_PROXYPORT, 4444);
		//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

		curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		 
		$response = curl_exec($ch);
		
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		$result_json = json_decode($response);
		$twitter_name = $result_json->screen_name;

		curl_close($ch);	


		// check screen name
		$piwaka_settings = get_option('piwaka_options');
		$twitter_name_setting = $piwaka_settings['twitter_name'];

		$this->verified = ($twitter_name === $twitter_name_setting);

	}


	function create_post() {

		$this->piwaka_post = new PiwakaPost( $this->message, $this->media );

	}	


	/*
	 * Do the final checks to see that the post
	 * was created correctly, and create the
	 * corresponding result.
	 */
	function create_result() {

		if($this->verified) {

			$this->result = array(
				'url' => $this->piwaka_post->permalink
			);

		} else {

			$this->result = array(
				'success' => false
			);
		}
		
	}


	/*
	 * Print out the result JSON to be returned to the
	 * twitter app.
	 */
	function output_result() {

		echo json_encode($this->result);

	}

}

// init uploader
$piwaka_uploader = new PiwakaUploader();



?>
<?php

class PiwakaPost {
	
	private $message;
	private $media;

	private $post_id;
	private $post_title;
	private $image_path;
	private $attachment_id;

	public $permalink;
	public $isValid;

	/*
	 * Construct
	 */
	function __construct($message, $media) {

		$this->message = $message;
		$this->media = $media;

		$this->create_draft();
		$this->upload_image();
		$this->add_image_to_library();
		$this->set_post_thumbnail();
		$this->publish();

		$this->verify();

	}

	/*
	 * Creates the post with a status of draft
	 * until we can confirm the upload was successful.
	 */
	function create_draft() {

		$author_id = 1;
		$post_title = $this->message;
		$status = 'draft';

		$post_data = array(
			'post_type'			=> 'piwaka',
			'post_author'		=> $author_id,
			'post_title'		=> $post_title,
			'post_status'		=> $status,
			'post_content'		=> '[piwaka-featured]'
		);

		$post_id = wp_insert_post($post_data);

		$this->post_id = $post_id;
		$this->post_title = $post_title;
		$this->permalink = get_permalink( $post_id );

	}


	/*
	 * We must upload the image to the server before 
	 * we can attach it to our post.
	 */
	function upload_image() {

		$uploaded_image = $this->media;
		$upload_overrides = array( 'test_form' => false );

		$result = wp_handle_upload( $uploaded_image, $upload_overrides );

		if($result) {

			$this->image_path = $result['file'];

		} else {

			error_log('Upload Failed because: '.$result['error']);
		}

	}


	/*
	 * Attach the image to the post we created earlier.
	 */
	function add_image_to_library() {

		$absolute_image_path = $this->image_path;
		$mime_type = $this->media['type'];
		$post_title = $this->post_title.' thumbnail';
		$post_content = '';
		$post_status = 'inherit';
		$parent_post_id = $this->post_id;

		$attachment_data = array(
			'guid'				=> $absolute_image_path,
			'post_mime_type' 	=> $mime_type,
			'post_title' 		=> $post_title,
			'post_content' 		=> $post_content,
			'post_status' 		=> $post_status
		);

		$attachment_id = wp_insert_attachment( 
			$attachment_data, 
			$absolute_image_path, 
			$parent_post_id 
		);

		$attachment_data = wp_generate_attachment_metadata( 
			$attachment_id, 
			$absolute_image_path
		);

		$this->attachment_id = $attachment_id;

		wp_update_attachment_metadata( $attachment_id, $attachment_data );

	}


	function set_post_thumbnail() {

		$post_id = $this->post_id;
		$thumb_meta_name = '_thumbnail_id';
		$thumb_id = $this->attachment_id;

		update_post_meta($post_id, $thumb_meta_name, $thumb_id);

	}


	/*
	 * Change the post's status to published.
	 */
	function publish() {

		$post_data = array(
			'ID'			=> $this->post_id,
			'post_status'	=> 'publish'
		);

		$this->post_id = wp_update_post($post_data);
	}


	function verify() {

		$this->isValid = $this->post_id > 0;

	}
}

?>
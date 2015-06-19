<?php
/*
	Plugin Name: Ezy Front Image Uploader
	Plugin URI:  http://www.wordpress.org/ezy-front-image-uploader
	Description: An easy front image uploader toolkit that helps you upload & attach images to post pages. Beautifully.
	Version: 1.0.0.1
	Author: Neil Gurung
	Author URI: http://www.neil.com.np/
 */


	if ( ! defined( 'ABSPATH' ) ) {
		die('Cannot Accessed  directly this file');
	}

	// Plugin dir path
	!defined('PLUGIN_DIR_PATH') ? define('PLUGIN_DIR_PATH', plugin_dir_path(__FILE__) ) : null;
	// Directory separator
	!defined('DS') ? define('DS', DIRECTORY_SEPARATOR) : NULL;
	// Template path
	!defined('EZY_TEMLATE_DIR_PATH') ? define('EZY_TEMLATE_DIR_PATH', PLUGIN_DIR_PATH .'templates' . DS ) : null;


/**
 * Handles the file handling for image uploader
 *
 * @package     ezy front image uploader
 * @subpackage  lib
 * @copyright   Copyright (c) 2015, Neil
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/


class EzyFrontendImageUploader {
	
	public $attachIds = array();
	public $recentAttachIds = array();
	private $fileNames = array();
	private $postId = '';

	function __construct()
	{
		// include all necessary library classes
		$this->ezyInclude();	

		add_action( 'wp_enqueue_scripts', array( &$this , 'loadScripts' ) );
		add_action('wp_enqueue_scripts', array(&$this,'ezyEnqueueStyles'));

		// shortcode to include form
		add_shortcode('ezy_upload',array(&$this,'ezyFrontEndImageUploaderShortcodes'));
		//add_filter('the_content', array(&$this,'the_content'));
		add_action('wp_ajax_ezy_ajaxToAttachImage', array(__CLASS__,'ajaxToAttachImage'));
		add_action('wp_ajax_nopriv_ezy_ajaxToAttachImage', array(__CLASS__,'ajaxToAttachImage'));

		// shortcode for ezy slider with bxslider [ezy_slider]
		add_shortcode('ezy_slider',array(&$this,'ezySliderShortCodes'));

	}


	/**
	 * 	This method is used to initialize values
	 * 	for ezy-upload
	 * 	@access public
	 *  @since 1.0
	 *  @return void
	 */

	public function init($f,$p) {
		$this->fileNames = $f;
		$this->postId = $p;
	}




	/**
	 * 	This method is used to initialize values
	 * 	for ezy-upload
	 * 	@access public
	 *  @since 1.0
	 *  @return void
	 */

	public function ezyInclude() {
		include_once(PLUGIN_DIR_PATH . 'lib/ezy_file_handler.php');
	}

	

	/**
	 * 	This method is used to attach ids to current post
	 * 	for ezy-upload
	 * 	@access public
	 *  @since 1.0
	 *  @return void
	 */

	public function ezyInitAllAttachIds() {
		global $wpdb;
		$postTable = $wpdb->prefix . "posts";
		$attchIds = $wpdb->get_results($wpdb->prepare("SELECT ID FROM $postTable WHERE post_type='attachment' AND post_parent='%d'",$this->postId));
		foreach ($attchIds as $id) {
			$this->attachIds[] = $id->ID;
		}
		
	}


	/**
	 * 	This method is used to get Guid save in wp_posts 
	 * 	table of attached images for ezy-upload
	 * 	@access public
	 *  @since 1.0
	 *  @return String
	 */
	
	
	public function getGuid($id) {
		global $wpdb;
		$postTable = $wpdb->prefix . "posts";
		$guid = $wpdb->get_var($wpdb->prepare("SELECT guid FROM $postTable WHERE ID ='%d' ",$id));
		
		return $guid;
	}


	

	/**
	 * 	This is static method used to load scripts required
	 * 	for  ezy upload
	 * 	@access public
	 *  @since 1.0
	 *  @return void
	 */

	public function loadScripts() {
		
		global $wp_scripts;

	    // tell WordPress to load jQuery UI tabs
		wp_enqueue_script('jquery-ui-draggable');

	    // get registered script object for jquery-ui
		$ui = $wp_scripts->query('jquery-ui-core');
		wp_enqueue_media();
		wp_enqueue_script('media-upload');
		//wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('ezy-image-upload-js', plugins_url('assets/js/ezy_image_upload.js',__FILE__) , array('jquery'), '1.0.2', true);
		wp_enqueue_script('bx-slider-js', plugins_url('assets/js/bx-slider/jquery.bxslider.min.js',__FILE__) , array('jquery'), '1.0.0', true);
		wp_enqueue_script('ezy-upload-script-js', plugins_url('assets/js/ezy-upload.js',__FILE__) , array('jquery'), '1.0.1', true);
		wp_enqueue_script('bootstrap-file-upload-js', plugins_url('assets/js/bootstrap-fileupload/bootstrap-fileupload.js',__FILE__) , array('jquery'), '1.0.0', true);
		
		
	}

	
	/**
	*
	* This method is used to enqueue all scripts
	*  @access public
	*  @since 1.0
	*  @return void
	*/

	public function ezyEnqueueStyles() {
		// tell WordPress to load the Smoothness theme from Google CDN
		wp_enqueue_style('bootstrap-min-css',  plugins_url('assets/css/bootstrap/bootstrap-min.css',__FILE__) , array(), '1.0.0', false);
		//wp_enqueue_style('ezy-image-upload-css', plugins_url('assets/css/ezy_image_upload.css',__FILE__) , array(), '1.0.0', false);
		wp_enqueue_style('bx-slider-css', plugins_url('assets/css/bx-slider/jquery.bxslider.css',__FILE__) , array(), '1.0.0', false);
		wp_enqueue_style('ezy-upload-css', plugins_url('assets/css/ezy-upload.css',__FILE__) , array(), '1.0.0', false);
		
	}


	/**
	*	This method is used to display all the attached images 
	*  @access public
	*  @since 1.0
	*  @return String
	*/
	

	function the_content( $content ) { 
		$this->postId = get_the_ID();
		$this->ezyInitAllAttachIds();
		
		// This is for bx slider attachments
		$class = count($this->attachIds) > 0 ? 'bxslider' : '';
		$content .= '<ul class="'. $class.'" style="list-style:none;">';
		if(!empty($this->attachIds)){
			foreach ($this->attachIds as $id) {
				$src = $this->getGuid( $id);
					    	//var_dump($id);die;
					    	//$src = $src[0];
				$content .= '<li><img src="' .$src . '" /></li>';
					    	//sprintf("<img src='%s' />",$src,$content);
			}
		}else{
			$content .= '<li>'.__('No images attached to this post').'</li>';
		}
		$content .= '</ul>';

		return $content;
	}


	/**
	*	This method is used to create shortcode for include slider 
	*	at frontend. It can be used by :
	* 	[ezy_slider]
	*   @access public
	*   @since 1.0
	*   @return String
	*/

	public function ezySliderShortCodes($atts)	{
		$this->postId = get_the_ID();
		$this->ezyInitAllAttachIds();
		$class = count($this->attachIds) > 0 ? 'bxslider' : '';	
		$content = '';
		$content .= '<ul class="'. $class.'" style="list-style:none;">';
		if(count($this->attachIds) > 0){
			foreach ($this->attachIds as $id) {
				$src = $this->getGuid($id);
				$content .= '<li><img src="' .$src . '" /></li>';
			}
		}else{
			$content .= '<li>'.__('No images attached to this post').'</li>';
		}
		$content .= '</ul>';

		echo $content;

	}


	/**
	*	This method is used to create shortcode for include form 
	*	for frontend Image uploader. It can be used by :
	*	[ezy_upload]
	*   @access public
	*   @since 1.0
	*   @return void
	*/

	public function ezyFrontEndImageUploaderShortcodes($atts)	{
		$a = shortcode_atts( array(
			'uploadtype' => 'a',
			'multiple_image_upload' => 1,
			), $atts );
		
		extract($a);
		$uploadType = $uploadtype;
		// for multiple image uploader
		$multiple = $multiple_image_upload ? 'multiple' : '';

		// for simple upload add button 
		$addButton = '';
		include_once( EZY_TEMLATE_DIR_PATH . 'upload_form.php' );

	}

	/**
	*	This method is used to save image to
	*	wp_posts table according to current 
	*	post_id
	*   @access public
	*   @since 1.0
	*   @return boolean
	*/

	private function _saveToPost($fileName = ''){

		$filetype = wp_check_filetype( basename( $fileName ), null );

		// Get the path to the upload directory.
		$wp_upload_dir = wp_upload_dir();

		// Prepare an array of post data for the attachment.
		$attachment = array(
			'guid'           => $wp_upload_dir['url'] . '/' . DS .  basename( $fileName ),
			'post_mime_type' => $filetype['type'],
			'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $fileName ) ),
			'post_content'   => '',
			'post_status'    => 'inherit'
			);

		// Insert the attachment.
		$attachId = wp_insert_attachment( $attachment, $fileName, $this->postId );
		array_push($this->attachIds,$attachId);
		array_push($this->recentAttachIds,$attachId);
		// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
		require_once( ABSPATH . 'wp-admin/includes/image.php' );

		// Generate the metadata for the attachment, and update the database record.
		$attach_data = wp_generate_attachment_metadata( $attachId, $fileName );
		wp_update_attachment_metadata( $attachId, $attach_data );
		//update_post_meta($this->postId,'_thumbnail_id',$attachId);
		$_SESSION['msg'] = ($attachId > 0 ) ? __('Uploaded Successfully') : __('Some error occured');
		
		return true;
	}

	/**
	*
	*	This method is used to attach images to post
	*   @access public
	*   @since 1.0
	*   @return void
	*/


	public function AttachImages() {
		ob_clean();
		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}
		
		// The ID of the post this attachment is for.
		$attachIds = array();
		unset($recentAttachIds);

		$files = $_FILES['ezy-upload'];
		/*var_dump($files);die;*/
		$upload_overrides = array( 'test_form' => false );
		if(count($files['name']) == 1){
			$key = 0;
			$file = array(
				'name'     => $files['name'][$key],
				'type'     => $files['type'][$key],
				'tmp_name' => $files['tmp_name'][$key],
				'error'    => $files['error'][$key],
				'size'     => $files['size'][$key]
				);

			$moveFile = wp_handle_upload($file,$upload_overrides);

				if($moveFile){ //file is uploaded successfully.
					$this->_saveToPost($file['name']);
					return true;
				}else{
					$_SESSION['msg'] = __($moveFile['error']);
					return false;
				}

			}
			foreach ($files['name'] as $key => $value) {
				if ($files['name'][$key]) {
					$file = array(
						'name'     => $files['name'][$key],
						'type'     => $files['type'][$key],
						'tmp_name' => $files['tmp_name'][$key],
						'error'    => $files['error'][$key],
						'size'     => $files['size'][$key]
						);

					$moveFile = wp_handle_upload($file,$upload_overrides);

			    if($moveFile){ //file is uploaded successfully.
			    	$this->_saveToPost($file['name']);
			    }else{
			    	$_SESSION['msg'] = __($moveFile['error']);
			    	return false;
			    }
			}
		}
	}

	
	/**
	*	This method is used for ajax Attach images
	*   @access public
	*   @since 1.0
	*   @return void
	*/

	public static function  ajaxToAttachImage()	{
		ob_clean();
		// File name of uploaded image
		$filename = $_POST['filename'];
		// The ID of the post this attachment is for.
		$parent_post_id = $_POST['postid'];
		
		// $filename should be the path to a file in the upload directory.
		//$filename = UPLOAD_IMG_DIR_PATH . $filename;
		
		// Check the type of file. We'll use this as the 'post_mime_type'.
		$filetype = wp_check_filetype( basename( $filename ), null );

		// Get the path to the upload directory.
		$wp_upload_dir = wp_upload_dir();

		// Prepare an array of post data for the attachment.
		$attachment = array(
			'guid'           => $wp_upload_dir['url'] . DS .  basename( $filename ),
			'post_mime_type' => $filetype['type'],
			'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
			'post_content'   => '',
			'post_status'    => 'inherit'
			);

		// Insert the attachment.
		$attach_id = wp_insert_attachment( $attachment, $filename, $parent_post_id );

		// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
		require_once( ABSPATH . 'wp-admin/includes/image.php' );

		// Generate the metadata for the attachment, and update the database record.
		$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
		wp_update_attachment_metadata( $attach_id, $attach_data );

		update_post_meta($parent_post_id,'_thumbnail_id',$attach_id);
		

		wp_die();
	}

}

// global variable for ezy-upload

$GLOBALS['ezyUpload']  = new EzyFrontendImageUploader();


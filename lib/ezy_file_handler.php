<?php 
/**
 * Handles the file upload
 *
 * @package     ezy front image uploader
 * @subpackage  lib
 * @copyright   Copyright (c) 2015, Neil
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	die('Cannot Accessed  directly this file');
}

class EzyFileHandler {

	
	private $extension = '';
	public $files = array();
	public $ezyImageNames = array();
	protected static $uploadDir = '';
	// valid extension to be allowd for image
	protected $validExtn  = array('jpg','jpeg','png','gif');
	// insert true|false after checking each file extension in it
	public $correctExtn = array();
	// insert true|false after file uploaded from temporary in it
	public $everyFileUploaded = array();


	
	function __construct() {
		
		EzyFileHandler::$uploadDir = wp_upload_dir();;
	}

	/**
	*
	* 	This function is used to intializa $_FILES
	*   @access public
	*   @since 1.0
	*   @return void
	*/

	public function init($f) {
		$this->files = $f;
		$this->ezyImageNames = isset($this->files['ezy-upload']['name']) ? $this->files['ezy-upload']['name']: '';
	}

	/**
	*
	* 	This method is used to set other valid extension
	* 	@param Array
	*   @access public
	*   @since 1.0
	*   @return void
	*/

	public function setValidExtension($extn= array()) {
		if(empty($extn) || !array($extn)) {
			return false;
		}
		$validExtn = array_push($extn);
	}

	/**
	*
	* 	This method is used to get extension from name of image
	* 	@param String
	*   @access public
	*   @since 1.0
	*   @return String extension type like .jpg,.png etc
	*/


	private function getExtension($str) {
		$i = strrpos($str, ".");
		if (!$i) {
			return "";
		}
		$l = strlen($str) - $i;
		$ext = substr($str, $i + 1, $l);
		return $ext;
	}

	/**
	*
	* 	This method is used to check image extension wheather it is valid or not
	*   @access public
	*   @since 1.0
	*   @return Array boolean value
	*/

	public function checkEachImageExtension() {
		foreach ($this->ezyImageNames as $name) {
			$extension = strtolower($this->getExtension($name));
			$this->correctExtn[] = in_array($extension,$this->validExtn) ? true : false;
		}
	}


	/**
	*
	* 	This method is used to upload image 
	*   @access public
	*   @since 1.0
	*   @return Array boolean value
	*/

	public function uploadImage(){
		if(in_array(false,$this->correctExtn)){
			return array(false);
		}
		$tmpNames = $this->files['ezy-upload']['tmp_name'];
		foreach ($tmpNames as $key=>$tmp) {
			$filename = basename($this->ezyImageNames[$key]);
			$this->everyFileUploaded[] = move_uploaded_file($tmp, EzyFileHandler::$uploadDir['path'].'/'.$filename) ?
			true : false; 
			if(file_exists(EzyFileHandler::$uploadDir['path'].'/'.$filename)){
				chmod(EzyFileHandler::$uploadDir['path'].'/'.$filename,0777);
			}
		}

		return $this->everyFileUploaded;
		
	}

}

// global variable file handler
$GLOBALS['ezyFileHandle']  = new EzyFileHandler();
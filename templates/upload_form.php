<?php 
/**
*
* s => simple
* a => ajax
*/
global $ezyUpload;
$errors = array();

$ezyNonce = isset($_REQUEST['ezy_nonce']) ? $_REQUEST['ezy_nonce'] : '';
$ezyImageNames = array();
if($uploadType != 's'){
	$ezyImageNames = isset($_REQUEST['ezy_image_name']) ? $_REQUEST['ezy_image_name'] : '';
}



if(wp_verify_nonce( $ezyNonce, 'ezy-nonce' )){ 
	if($uploadType == 's'){
		global $ezyFileHandle;

		$ezyFileHandle->init($_FILES);
		$ezyFileHandle->checkEachImageExtension();
		if(!in_array(false,$ezyFileHandle->correctExtn)){
			$ezyFileHandle->uploadImage();
		}else{
			$errors[] =  'extension wrong';
		}

		if(!in_array(false,$ezyFileHandle->everyFileUploaded)){
			$ezyUpload->init($ezyFileHandle->ezyImageNames,get_the_ID());
			$ezyUpload->AttachImages();
		}else{
			$errors[] = 'cannot upload every files';
		}

	}else{
		$ezyUpload->init($ezyImageNames,get_the_ID());
		$ezyUpload->AttachImages();
	}
}


foreach ($errors as  $error) {
	echo '<div><span class="error">'. $error .'</span></div>';
}
if(isset($_SESSION['msg'])){
	echo '<div><span class="text-success">'. $_SESSION['msg'] .'</span></div>';
}
?>
<button class="ezy-form-hide btn btn-info" data-text="hide">Hide</button>
<?php echo $addButton ?>
<button class="ezy-attach-btn btn btn-warning">Attach Images</button>
<div class="ezy-wrapper" data-adminurl="<?php echo admin_url('admin-ajax.php'); ?>">

	<form id="ezy-wrapper" action="" method="post" name="templateImageUploadFrm" enctype="multipart/form-data" class="upload-template upload-template-image">
		<div class="ezy-upload" style="margin-top:10px">
			<?php $uploadType == 's' ? include_once(PLUGIN_DIR_PATH .'templates/layouts/simple_form.php') : include_once(PLUGIN_DIR_PATH .'templates/layouts/dynamic_uploader_form.php');?>
		</div>
		<input  type="hidden" name="ezy_nonce"  size=""   value="<?php echo wp_create_nonce('ezy-nonce');?>"/>
	</form> 
</div>
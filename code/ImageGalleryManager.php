<?php

class ImageGalleryManager extends ImageDataObjectManager
{
	public $popupClass = "ImageGalleryManager_Popup";
	
	public function __construct($controller, $name = null, $sourceClass = null, $fileFieldName = null, $fieldList = null, $detailFormFields = null, $sourceFilter = "", $sourceSort = "", $sourceJoin = "") 
	{
		$this->setFilter('AlbumID',_t('ImageGalleryManager.CHOOSEALBUM','Choose an album'), $controller->Albums()->toDropdownMap('ID','AlbumName'));
		if($controller->Albums()->Count() > 0)
	
			$this->filter = "AlbumID_".$controller->Albums()->First()->ID;
		parent::__construct($controller, $name, $sourceClass, $fileFieldName, $fieldList, $detailFormFields, $sourceFilter, $sourceSort, $sourceJoin); 
		if(!class_exists("ImageDataObjectManager"))
			die("<strong>"._t('ImageGalleryManager.ERROR','Error')."</strong>: "._t('ImageGalleryManager.DATAOBJECTMANAGER','ImageGallery requires the DataObjectManager module.'));
		
		$this->setAddTitle(_t("ImageGalleryPage.IMAGES","images") . " "  . _t("ImageGalleryPage.TO","to") . " \"" . $this->CurrentAlbumTitle()."\"");
		$this->filter_empty_string = false;
	}
	
	private function getSelectedAlbumID()
	{
		return str_replace("AlbumID_","",$this->filter);
	}
	
	public function CurrentAlbumTitle()
	{
		return DataObject::get_by_id("ImageGalleryAlbum", $this->getSelectedAlbumID())->AlbumName;
	}
		
	protected function getUploadFields()
	{
		if(isset($_REQUEST['album'])) {
			SWFUploadConfig::addPostParam('AlbumID', $_REQUEST['album']);
			$album_name = DataObject::get_by_id("ImageGalleryAlbum", $_REQUEST['album'])->AlbumName;
		}
		else $album_name = "";
		return new FieldSet(
			new HeaderField($title = sprintf(_t('ImageGalleryManager.UPLOADIMAGESTO','Upload Images to "%s"'),$album_name), $headingLevel = 2),
			new HeaderField($title = _t('ImageGalleryManager.UPLOADFROMPC','Upload from my computer'), $headingLevel = 3),
			new SWFUploadField(
				"UploadForm",
				"Upload",
				"",
				array(
					'file_upload_limit' => $this->getUploadLimit(), // how many files can be uploaded
					'file_queue_limit' => $this->getUploadLimit(), // how many files can be in the queue at once
					'browse_button_text' => _t('ImageGalleryManager.UPLOADIMAGES','Upload Images').'...',
					'upload_url' => Director::absoluteURL('ImageGalleryManager_Controller/handleswfupload'),
					'required' => 'true'
				)
			)
		);
	}
	
	protected function getUploadifyFields() {
		$fields = parent::getUploadifyFields();
		if(isset($_REQUEST['album'])) {
			if($album = DataObject::get_by_id("ImageGalleryAlbum", (int) $_REQUEST['album'])) {
				$dest = substr_replace(str_replace(ASSETS_DIR.'/','',$album->Folder()->Filename),"", -1);
				$uploader = $fields->fieldByName('UploadedFiles');
				$fields->fieldByName('UploadedFiles')->removeFolderSelection();
				$fields->fieldByName('UploadedFiles')->uploadFolder = $dest;
				$fields->push(new HiddenField('album', null, $album->ID));
			}
		}
		return $fields;
	}
	
	public function updateDataObject(&$object) {
		if(isset($_REQUEST['album'])) {
			$object->AlbumID = (int) $_REQUEST['album'];
			if(($image = $object->Image()) && ($album = DataObject::get_by_id("ImageGalleryAlbum", (int) $_REQUEST['album']))) {
				$image->setField("ParentID", $album->FolderID);
				$image->write();
			}
		}
	}	
	
	protected function getImportFields()
	{
		$fields = parent::getImportFields();
		if(isset($_REQUEST['album']))
			$fields->push(new HiddenField('AlbumID', '', $_REQUEST['album']));
		return $fields;
	}

	protected function importLinkFor($file)
	{
		return Controller::join_links(parent::importLinkFor($file),"?album=".$_REQUEST['album']);
	}
	
	
	public function saveImportForm($data, $form)
	{
		if(isset($data['imported_files']) && is_array($data['imported_files'])) {
			$_POST['uploaded_files'] = array();
			$album = DataObject::get_by_id("ImageGalleryAlbum", $data['AlbumID']);
			foreach($data['imported_files'] as $file_id) {
				$file = DataObject::get_by_id("File", $file_id);
				if($album->FolderID != $file->ParentID) {
					$new_path = Director::baseFolder().'/'.$album->Folder()->Filename.$file->Name;	
					copy($file->getFullPath(),$new_path);
					$file_class = $this->fileClassName;
					$new_file = new $file_class();
					$new_file->setFilename($album->Folder()->Filename.$file->Name);
					$new_file->setName($file->Name);
					$new_file->setParentID($album->FolderID);
					$new_file->write();
					$file = $new_file;
					$file_id = $new_file->ID;
				}
				$file->ClassName = $this->fileClassName;
				$file->write();
				$do_class = $data['dataObjectClassName'];
				$idxfield = $data['fileFieldName']."ID";
				$owner_id = $data['parentIDName'];
				$obj = new $do_class();
				$obj->$idxfield = $file->ID;
				$obj->$owner_id = $data['controllerID'];
				$obj->AlbumID = $album->ID;
				$obj->write();
				$_POST['uploaded_files'][] = $obj->ID;
			}

			return $this->customise(array(
				'DetailForm' => $this->EditUploadedForm()
			))->renderWith($this->templatePopup);		

		}
	}
	
	
	protected function getChildDataObj()
	{
		$childData = parent::getChildDataObj();
		$childData->ImageGalleryPageID = $this->controllerID;
		return $childData;
	}
	
	public function getPreviewFieldFor($fileObject, $size = 150)
	{
		if($fileObject instanceof Image) {
		echo $fileObject->ID . " " . $fileObject->Filename;
			$URL = $fileObject->SetHeight($size)->URL;
			return new LiteralField("icon",
				"<div class='current-image'>
					<div id='preview-image'>
						<img src='$URL' alt='' class='preview' />
						<div class='ajax-loader'><img src='dataobject_manager/images/ajax-loader.gif' />". _t('ImageGalleryManager.ROTATING','Rotating')."...</div>
					</div>
					<div class='rotate-controls'>
						<a href='".$this->CounterClockwiseLink($fileObject)."' title='"._t("ImageGalleryManager.ROTATECLOCKWISE","Rotate clockwise")."'><img src='image_gallery/images/clockwise.gif' /></a> | 
						<a href='".$this->ClockwiseLink($fileObject)."' title='"._t("ImageGalleryManager.ROTATECOUNTERCLOCKWISE","Rotate counter-clockwise")."'><img src='image_gallery/images/counterclockwise.gif' /></a>
					</div>
					<h3>$fileObject->Filename</h3>
				</div>"
			);
		}
	}
	
	public function RotateLink($imgObj, $dir)
	{
		return "ImageGalleryManager_Controller/rotateimage/{$imgObj->ID}/{$dir}?flush=1";
	}
	
	private function CounterClockwiseLink($fileObject)
	{
		return $this->RotateLink($fileObject, "ccw");
	}
	
	private function ClockwiseLink($fileObject)
	{
		return $this->RotateLink($fileObject, "cw");
	}
	
	
	public function UploadLink()
	{
		return Controller::join_links(parent::UploadLink(),"?album=".$this->getSelectedAlbumID());
	}
	
		
}

class ImageGalleryManager_Controller extends Controller
{
	public function handleswfupload()
	{
		if(isset($_FILES['swfupload_file']) && !empty($_FILES['swfupload_file'])) {
			$do_class = $_POST['dataObjectClassName'];
			$file_class = $_POST['fileClassName'];
			$obj = new $do_class();
			$idxfield = $_POST['fileFieldName']."ID";
			$file = new $file_class();
			$album = DataObject::get_by_id("ImageGalleryAlbum", $_POST['AlbumID']);
			$dest = substr_replace(str_replace(ASSETS_DIR.'/','',$album->Folder()->Filename),"", -1);
			
			if(class_exists("Upload")) {
				$u = new Upload();
				$u->loadIntoFile($_FILES['swfupload_file'], $file, $dest);
			}
			else
				$file->loadUploaded($_FILES['swfupload_file'], $dest);

			$file->setField("ParentID",$album->FolderID);
			$file->write();
			$obj->$idxfield = $file->ID;
			$obj->AlbumID = $album->ID;
			$ownerID = $_POST['parentIDName'];
			$obj->$ownerID = $_POST['controllerID'];
			$obj->write();
			echo $obj->ID;
		}
		else {
			echo ' ';
		}
	
	
	}
	
	public function rotateimage()
	{
		if($image = DataObject::get_by_id("ImageGalleryImage", $this->urlParams['ID'])) {
			$url = $this->urlParams['OtherID'] == 'cw' ? $image->RotateClockwise()->URL : $image->RotateCounterClockwise()->URL;
			$original_file = $image->Filename;
			if(copy(Director::baseFolder().'/'.$url, Director::baseFolder().'/'.$original_file)) {
				$image->flushCache();
				$image->clearResampledImages();
			}
			echo $image->SetHeight(200)->URL . "?t=".time();
		}
	}

}

class ImageGalleryManager_Popup extends FileDataObjectManager_Popup
{
	function __construct($controller, $name, $fields, $validator, $readonly, $dataObject) {
			parent::__construct($controller, $name, $fields, $validator, $readonly, $dataObject);
			Requirements::javascript('image_gallery/javascript/imagegallerymanager_popup.js');
	}
}


?>
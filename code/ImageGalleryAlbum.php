<?php

class ImageGalleryAlbum extends DataObject
{
	static $db = array (
		'AlbumName' => 'Varchar(255)',
		'Description' => 'Text'
	);
	
	static $has_one = array (
		'CoverImage' => 'Image',
		'ImageGalleryPage' => 'ImageGalleryPage',
		'Folder' => 'Folder'
	);
	
	static $has_many = array (
		'GalleryItems' => 'ImageGalleryItem'
	);
	

	
	public function getCMSFields_forPopup()
	{
		return new FieldSet(
			new TextField('AlbumName', _t('ImageGalleryAlbum.ALBUMTITLE','Album Title')),
			new TextareaField('Description', _t('ImageGalleryAlbum.DESCRIPTION','Description')),
			new ImageUploadField('CoverImage',_t('ImageGalleryAlbum.COVERIMAGE','Cover Image'))
		);
	}
	
	public function Link()
	{
		$name = $this->Folder()->Name;
		if(!$name) {
			$name = $this->FolderID;
		}
		return $this->ImageGalleryPage()->Link('album/'.$name);
	}
	
	public function LinkingMode()
	{
		return Controller::curr()->urlParams['ID'] == $this->Folder()->Name ? "current" : "link";
	}
	
	public function ImageCount()
	{
		$images = DataObject::get("ImageGalleryItem","AlbumID = {$this->ID}"); 
		return $images ? $images->Count() : 0;
	}
	
	public function FormattedCoverImage()
	{
		return $this->CoverImage()->CroppedImage($this->ImageGalleryPage()->CoverImageWidth,$this->ImageGalleryPage()->CoverImageHeight);
	}
	
	function onBeforeWrite()
	{
		parent::onBeforeWrite();
		if(isset($_POST['AlbumName'])) {
		  $clean_name = SiteTree::generateURLSegment($_POST['AlbumName']);
			if($this->FolderID) {
				$this->Folder()->setName($clean_name);
				$this->Folder()->Title = $clean_name;

				$this->Folder()->write();
			}
			else {
				$folder = Folder::findOrMake('image-gallery/'.$this->ImageGalleryPage()->RootFolder()->Name.'/'.$clean_name);
				$this->FolderID = $folder->ID;
			}
		}
	}
	
	function onBeforeDelete()
	{
		parent::onBeforeDelete();
		$this->GalleryItems()->removeAll();
		$this->Folder()->delete();
	}
	
	
}


?>
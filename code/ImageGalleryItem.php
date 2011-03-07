<?php

class ImageGalleryItem extends DataObject
{
	protected $ui;
	
	public static $delete_permission = "CMS_ACCESS_CMSMain";
	
	static $db = array (
		'Caption' => 'Text'
	);
	
	static $has_one = array (
		'ImageGalleryPage' => 'ImageGalleryPage',
		'Album' => 'ImageGalleryAlbum',
		'Image' => 'ImageGalleryImage'
	);
	
	public function getCMSFields_forPopup()
	{
		$fields = new FieldSet();
		$fields->push(new TextareaField('Caption', _t('ImageGalleryItem.CAPTION','Caption')));
		if($this->ImageGalleryPageID)
			$fields->push(new DropdownField('AlbumID', _t('ImageGalleryItem.ALBUM','Album'), $this->ImageGalleryPage()->Albums()->toDropdownMap('ID','AlbumName')));
		$class = class_exists('UploadifyField') ? 'ImageUploadField' : 'ImageField';
		$fields->push(new $class('Image'));
		
		return $fields;
	}
	
	public function onBeforeDelete()
	{
		if($this->Image()->exists()) {
			//$this->Image()->delete();
		}
		parent::onBeforeDelete();
	}
	
	public function onBeforeWrite()
	{
		parent::onBeforeWrite();
		
		if($image = $this->Image()) {
			if(isset($_POST['AlbumID']) && $album = DataObject::get_by_id("ImageGalleryAlbum", $_POST['AlbumID'])) {
				$image->setField("ParentID",$album->FolderID);
				$image->write();
			}
		}
	}
	
	public function Thumbnail()
	{
		$i = $this->ImageGalleryPage();
		if($i->Square)
			return $this->Image()->CroppedImage($i->ThumbnailSize, $i->ThumbnailSize);
		return $this->Image()->SetHeight($i->ThumbnailSize);
	}
	
	public function Medium()
	{
		if($this->Image()->Landscape())
			return $this->Image()->SetWidth($this->ImageGalleryPage()->MediumSize);
		else
			return $this->Image()->SetHeight($this->ImageGalleryPage()->MediumSize);
	}
	
	public function Large()
	{
		if($this->Image()->Landscape())
			return $this->Image()->SetWidth($this->ImageGalleryPage()->NormalSize);
		else {
			$height = $this->ImageGalleryPage()->NormalHeight > 0 ? $this->ImageGalleryPage()->NormalHeight : $this->ImageGalleryPage()->NormalSize;
			return $this->Image()->SetHeight($height);
		}
	}
	
	public function setUI(ImageGalleryUI $ui)
	{
		$this->UI = $ui;
	}
	
	public function GalleryItem()
	{
		if($this->UI)
			return $this->renderWith(array($this->UI->item_template));
		return false;
	}
	
	public function canDelete() 
	{ 
		return Permission::check(self::$delete_permission); 
	}
}
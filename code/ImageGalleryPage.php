<?php
class ImageGalleryPage extends Page
{
	static $db = array (
		'SortBy' => "Enum( array( 'Title', 'UploadDateASC', 'UploadDateDESC','SortASC' ), 'SortASC' )",
		'GalleryUI' => "Varchar(50)",
		'CoverImageWidth' => 'Int',
		'CoverImageHeight' => 'Int',
		'ThumbnailSize' => 'Int',
		'MediumSize' => 'Int',
		'Square' => 'Boolean',
		'NormalSize' => 'Int',
		'NormalHeight' => 'Int',
		'MediaPerPage' => 'Int',
		'UploadLimit' => 'Int'
	);
	
	static $has_one = array (
		'RootFolder' => 'Folder'
	);
	
	static $defaults = array (
		'CoverImageWidth' => '128',
		'CoverImageHeight' => '128',
		'ThumbnailSize' => '128',
		'Square' => '1',
		'MediumSize' => '400',
		'NormalSize' => '600',
		'MediaPerPage' => '30',
		'MediaPerLine' => '6',
		'UploadLimit' => '20',
		'GalleryUI' => 'Lightbox'
	);
	
	static $has_many = array (
		'Albums' => 'ImageGalleryAlbum',
		'GalleryItems' => 'ImageGalleryItem'
	);
		
	protected $itemClass = "ImageGalleryItem";
	protected $albumClass = "ImageGalleryAlbum";
	public $UI;
  

	public function getItemClass()
	{
		return $this->itemClass;
	}
	
	public function getAlbumClass()
	{
		return $this->albumClass;
	}
	
	function onBeforeWrite() { 
    parent::onBeforeWrite(); 
    if( $this->ID ) 
    	$this->RootFolder()->Title = $this->Title; 
   }	
	
	function onAfterWrite() 
	{
		if( $this->ID ) $this->checkFolder();
		parent::onAfterWrite();
	}
	/*
	function onBeforeDelete()
	{
		parent::onBeforeDelete();
		$this->RootFolder()->delete();
	}
	*/
	function checkFolder() {
		if( ! $this->RootFolderID ) {
			$galleries = Folder::findOrMake('image-gallery');
			$galleries->Title = 'Image Gallery';
			$galleries->write();
			$folder = Folder::findOrMake('image-gallery/' . $this->URLSegment);
			$folder->Title = $this->Title;
			$folder->setName($this->Title);
			$folder->write();
			
			$this->RootFolderID = $folder->ID;
			$this->write();
			
			$this->requireDefaultAlbum();
			FormResponse::add( "\$( 'Form_EditForm' ).getPageFromServer( $this->ID );" );
		}
		else {
			$this->RootFolder()->setName($this->Title);
			$this->RootFolder()->write();
		}
			
	}

		
	private function requireDefaultAlbum()
	{
		$class = $this->albumClass;
		$album = new $class();
		$album->AlbumName = "Default Album";
		$album->ImageGalleryPageID = $this->ID;
		$album->ParentID = $this->RootFolderID;
		$folder = Folder::findOrMake('image-gallery/'.$this->RootFolder()->Name.'/'.$album->AlbumName);
		$folder->write();
		$album->FolderID = $folder->ID;
		$album->write();
	}
	
	public function getCMSFields($cms) {
		
		$configuration = _t('ImageGalleryPage.CONFIGURATION','Configuration');
		$albums = _t('ImageGalleryPage.ALBUMS','Albums');
		$photos = _t('ImageGalleryPage.PHOTOS','Photos');
		
		$f = parent::getCMSFields($cms);
		$f->addFieldToTab("Root.Content.$configuration", new HeaderField($title = _t('ImageGalleryPage.ALBUMCOVERIMAGES','Album cover images'), $headingLevel = "6"));
		$f->addFieldToTab("Root.Content.$configuration", new FieldGroup(
				new NumericField('CoverImageWidth','Width'),
				new NumericField('CoverImageHeight','Height')
			)
		);
		$f->addFieldToTab("Root.Content.$configuration", new NumericField('ThumbnailSize',_t('ImageGalleryPage.THUMBNAILHEIGHT','Thumbnail height (pixels)')));
		$f->addFieldToTab("Root.Content.$configuration", new CheckboxField('Square',_t('ImageGalleryPage.CROPTOSQUARE','Crop thumbnails to square')));
		$f->addFieldToTab("Root.Content.$configuration", new NumericField('MediumSize',_t('ImageGalleryPage.MEDIUMSIZE','Medium size (pixels)')));
		$f->addFieldToTab("Root.Content.$configuration", new NumericField('NormalSize',_t('ImageGalleryPage.NORMALSIZE','Normal width (pixels)')));
		$f->addFieldToTab("Root.Content.$configuration", new NumericField('NormalHeight',_t('ImageGalleryPage.NORMALHEIGHT','Normal height (pixels)')));
		$f->addFieldToTab("Root.Content.$configuration", new NumericField('MediaPerPage',_t('ImageGalleryPage.IMAGESPERPAGE','Number of images per page')));
		$popup_map = array();
		if($ui_list = ClassInfo::subclassesFor("ImageGalleryUI")) {
			foreach($ui_list as $ui) {
				if($ui != "ImageGalleryUI") {
					$ui_label = eval("return $ui::\$label;");
					$demo_url = eval("return $ui::\$link_to_demo;");
					$demo_link = !empty($demo_url) ? sprintf('<a href="%s" target="_blank">%s</a>',$demo_url, _t('ImageGalleryPage.VIEWDEMO','view demo')) : "";
					$popup_map[$ui] = $ui_label . " " . $demo_link;
				}
			}
		}		
		$f->addFieldToTab("Root.Content.$configuration",new OptionsetField('GalleryUI',_t('ImageGalleryPage.POPUPSTYLE','Popup style'), $popup_map));
		$f->addFieldToTab("Root.Content.$configuration", new NumericField('UploadLimit',_t('ImageGalleryPage.MAXFILES','Max files allowed in upload queue')));				
		
		if($this->RootFolderID) {

			$manager = new DataObjectManager(
				$this,
				'Albums',
				$this->albumClass,
				array('AlbumName' => _t('ImageGalleryAlbum.ALBUMNAME','Album Name'), 'Description' => _t('ImageGalleryAlbum.DESCRIPTION','Description')),
				'getCMSFields_forPopup',
				"ImageGalleryPageID = {$this->ID}"
			);
			
			$manager->setAddTitle(_t('ImageGalleryPage.ANALBUM','an Album'));
			$manager->setSingleTitle(_t('ImageGalleryPage.ALBUM','Album'));
			$manager->setParentClass('ImageGalleryPage');

			$f->addFieldToTab("Root.Content.$albums", $manager);
		}
		else 
			$f->addFieldToTab("Root.Content.$albums", new HeaderField($title = _t("ImageGalleryPage.ALBUMSNOTSAVED","You may add albums to your gallery once you have saved the page for the first time."), $headingLevel = "3"));
		
		if($this->RootFolderID && $this->Albums()->Count() > 0) {
			$manager = new ImageGalleryManager(
				$this,
				'GalleryItems',
				$this->itemClass,
				'Image',
				array('Caption' => _t('ImageGalleryItem.CAPTION','Caption')),
				'getCMSFields_forPopup'
			);
			
			$manager->setPluralTitle(_t('ImageGalleryPage.IMAGES','Images'));
			$manager->setParentClass("ImageGalleryPage");
			if($this->UploadLimit)
				$manager->setUploadLimit($this->UploadLimit);
				
			$f->addFieldToTab("Root.Content.$photos", $manager);
		}
		elseif(!$this->RootFolderID)
			$f->addFieldToTab("Root.Content.$photos", new HeaderField($title = _t("ImageGalleryPage.PHOTOSNOTSAVED","You may add photos to your gallery once you have saved the page for the first time."), $headingLevel = "3"));
		elseif($this->Albums()->Count() == 0)
			$f->addFieldToTab("Root.Content.$photos", new HeaderField($title = _t("ImageGalleryPage.NOALBUMS","You have no albums. Click on the Albums tab to create at least one album before adding photos."), $headingLevel = "3"));
		return $f;
	}
	
	public function CurrentAlbum()
	{
		if($this->current_album)
			return $this->current_album;
		$c = Controller::curr();
		if(isset($c->urlParams['ID'])) {
			$url_segment = Convert::raw2sql($c->urlParams['ID']);
			$field = is_numeric($url_segment) ? "ID" : "Name";
			$albums = DataObject::get($this->albumClass,"ImageGalleryPageID = {$this->ID} AND File.{$field} = '$url_segment'", "", "LEFT JOIN File ON File.ID = FolderID");
			return $albums ? $albums->First() : false; 
		}
		return false;
	}
	
	public function AlbumTitle()
	{
		return $this->CurrentAlbum()->AlbumName;
	}

	public function AlbumDescription()
	{
		return $this->CurrentAlbum()->Description;
	}

	public function SingleAlbumView()
	{
		if($this->Albums()->Count() == 1) {
			$this->current_album = $this->Albums()->First();
			return true;
		}
		return false;
	}
	
	private static function get_default_ui()
	{
    $classes = ClassInfo::subclassesFor("ImageGalleryUI");
    foreach($classes as $class) {
      if($class != "ImageGalleryUI") return $class; 
    }
    return false;
	}
	
	public function GalleryUI()
	{
	   return $this->GalleryUI ? $this->GalleryUI : self::get_default_ui();
	}
	
	public function includeUI()
	{
		if($this->GalleryUI() && ClassInfo::exists($this->GalleryUI())) {
			Requirements::javascript("image_gallery/javascript/imagegallery_init.js");
			$ui = $this->GalleryUI();
			$this->UI = new $ui();
			$this->UI->setImageGalleryPage($this);
			$this->UI->initialize();
		}
	}
	
	protected function Items($limit = null) {
		if($limit === null && $this->MediaPerPage ) {
			if( !isset($_REQUEST['start']) || ! is_numeric( $_REQUEST['start'] ) )
				$_REQUEST['start'] = 0;
			
			$limit = $_REQUEST['start'] . "," . $this->MediaPerPage;
		}
		
		$filter = ($current_album = $this->CurrentAlbum()) ? "AlbumID = {$current_album->ID} AND" : "";
		$files = DataObject::get(
			$this->getItemClass(), 
			"$filter ImageGalleryPageID = {$this->ID}",
			null,
			"",
			$limit
		);
		return $files;	
	}


	public function GalleryItems($limit = null, $items = null) {
		
		if($items === null) 
			$items = $this->Items($limit);
	  $this->includeUI();
		if( $items ) {
			foreach( $items as $item ) {
				if($this->Square)
					$thumbImg = $item->Image()->CroppedImage($this->ThumbnailSize,$this->ThumbnailSize);
				else
					$thumbImg = $item->Image()->SetHeight($this->ThumbnailSize);					
				if($thumbImg) {
					$item->ThumbnailURL = $thumbImg->URL;
					$item->ThumbnailWidth = $this->Square ? $this->ThumbnailSize : $thumbImg->getWidth();
					$item->ThumbnailHeight = $this->ThumbnailSize;
					
					if($item->Image()->Landscape())
						$normalImg = $item->Image()->SetWidth($this->NormalSize);
					else
						$normalImg = $item->Image()->SetHeight($this->NormalSize);
	
					$item->ViewLink = $normalImg->URL;
					$item->setUI($this->UI);
				}
			}
	  	return $this->UI->updateItems($items);
		}
		return false;
	}
	
	public function PreviousGalleryItems()
	{
		if(isset($_REQUEST['start']) && is_numeric($_REQUEST['start']) && $this->MediaPerPage) {
			return $this->GalleryItems("0, " . $_REQUEST['start']);
		}
		return false;
	}
	
	public function NextGalleryItems()
	{
		if($_REQUEST['start'] > 0 && $this->MediaPerPage)
			return $this->GalleryItems($_REQUEST['start']+$this->MediaPerPage . ",999");

		return $this->GalleryItems($this->MediaPerPage.",999");

	}
	
	public function AllGalleryItems()
	{
		return $this->GalleryItems("0,999");
	}
	
	public function GalleryLayout()
	{
		return $this->customise(array(
			'GalleryItems' => $this->GalleryItems(),
			'PreviousGalleryItems' => $this->PreviousGalleryItems(),
			'NextGalleryItems' => $this->NextGalleryItems()
		))->renderWith(array($this->UI->layout_template));
	}
	
	
	
	
}

class ImageGalleryPage_Controller extends Page_Controller
{
	
	public function init() 
	{

		parent::init();
		Requirements::themedCSS('ImageGallery');
	}
	
	public function index()
	{
			if($this->SingleAlbumView())
				return $this->renderWith(array($this->getModelClass().'_album','Page'));
			return $this->renderWith(array('ImageGalleryPage','Page'));
	}
				
	private function getModelClass()
	{
	  return str_replace("_Controller","",$this->class);
	}
	
	private function getModel()
	{
    return DataObject::get_by_id($this->getModelClass(),$this->ID);
	}
	
	
	protected function adjacentAlbum($dir) 
  { 
      $t = $dir == "next" ? ">" : "<"; 
      $sort = $dir == "next" ? "ASC" : "DESC"; 
      return DataObject::get_one( 
         $this->albumClass, 
         "ImageGalleryPageID = {$this->ID} AND SortOrder $t {$this->CurrentAlbum()->SortOrder}", 
         false, 
         "SortOrder $sort" 
      );      
  }	
  
  public function NextAlbum()
	{
		return $this->adjacentAlbum("next");
	}

	public function PrevAlbum()
	{
		return $this->adjacentAlbum("prev");
	}
	
	public function album() {
		if(!$this->CurrentAlbum()) {
			return $this->httpError(404);
		}
		return array();
	}
		
	
	
	

}

?>
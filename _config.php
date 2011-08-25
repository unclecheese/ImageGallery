<?php
	$dir = basename(rtrim(dirname(__FILE__),'/'));
	// Check directory
	if($dir != "image_gallery") {
		user_error(sprintf(
			_t('Messages.WRONGDIRECTORY','The ImageGallery module must be in a directory named "image_gallery" (currently "%s")'),
			$dir
		), E_USER_ERROR);
	}


	// Check dependencies
	if(!class_exists("DataObjectManager")) {
		user_error(_t('Messages.DATAOBJECTMANAGER','The ImageGallery module requires DataObjectManager'),E_USER_ERROR);
	}

	SortableDataObject::add_sortable_classes(array(
		"ImageGalleryItem",
		"ImageGalleryAlbum"
	)); 
	
	DataObject::add_extension("SiteTree","ImageGallerySiteTree");
?>

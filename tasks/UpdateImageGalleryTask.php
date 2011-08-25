<?php
/**
 * This task searches the folders belonging to image galleries and adds any missing images.
 * This means you can upload images using sftp or rsync etc, then automatically add them in
 * without going through the web interface which is a hassle with 100s of images.
 */
class UpdateImageGalleryTask extends BuildTask {
	protected $title = 'Update Image Gallery Task';
	
	protected $description = "Updates the image gallery with all the extra images that have 
		been manually uploaded to the gallery's folder";
	
	public function run($request) {
		
		$galleries = DataObject::get('ImageGalleryPage');
		$count = 0;
		if ($galleries && $galleries->count()) {
			Debug::message("Importing, please wait....");
			foreach ($galleries as $g) {
				if ($g->Albums()) {
					foreach ($g->Albums() as $a) {
						$folder = $a->Folder();
						$existing = $a->GalleryItems()->column('ImageID');
						foreach($folder->Children() as $image) {
							if (!in_array($image->ID, $existing)) {
								
								//Make the image a Image Gallery Image
								$image = $image->newClassInstance('ImageGalleryImage');
								$image->write();
								
								//Add to the album
								$item = new ImageGalleryItem();
								$item->ImageGalleryPageID = $g->ID;
								$item->AlbumID = $a->ID;
								$item->ImageID = $image->ID;
								$item->write();
								$count++;
							}
						}
					}
				} else {
					Debug::message("Warning: no album found in gallery '{$g->Title}'");
				}
			}
			Debug::message("Imported $count images into galleries");
		} else {
			user_error('No image gallery pages found', E_USER_ERROR);
		}
	}
}
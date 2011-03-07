<?php

class PrettyPhoto extends ImageGalleryUI
{
	static $link_to_demo = "http://www.no-margin-for-errors.com/projects/prettyPhoto-jquery-lightbox-clone/#image-gallery-demo";
	static $label = "Pretty Photo";
	public $item_template = "PrettyPhoto_item";
	
	public function initialize()
	{
		Requirements::javascript(THIRDPARTY_DIR.'/jquery/jquery.js'); 	
		Requirements::javascript('image_gallery/gallery_ui/prettyphoto/javascript/jquery.prettyPhoto.js');
		Requirements::javascript('image_gallery/gallery_ui/prettyphoto/javascript/prettyphoto_init.js');
		Requirements::css('image_gallery/gallery_ui/prettyphoto/css/prettyPhoto.css');
		
	}

}
<?php

class NyroModal extends ImageGalleryUI
{
	static $link_to_demo = "http://nyromodal.nyrodev.com/";
	static $label = "NyroModal";
	public $item_template = "NyroModal_item";
	
	public function initialize()
	{
		Requirements::javascript(THIRDPARTY_DIR.'/jquery/jquery.js'); 	
		Requirements::javascript('image_gallery/gallery_ui/nyro_modal/javascript/jquery.nyroModal.js');
		Requirements::javascript('image_gallery/gallery_ui/nyro_modal/javascript/nyro_modal_init.js');
		Requirements::css('image_gallery/gallery_ui/nyro_modal/css/nyroModal.css');
		
	}

}
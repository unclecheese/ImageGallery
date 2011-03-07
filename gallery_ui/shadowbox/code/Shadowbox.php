<?php

class Shadowbox extends ImageGalleryUI
{
	static $label = "Shadowbox";
	static $link_to_demo = "http://www.shadowbox-js.com/";
	public $item_template = "Shadowbox_item";
	
	
	public function initialize()
	{
		Requirements::javascript(THIRDPARTY_DIR.'/jquery/jquery.js'); 	
		Requirements::javascript('image_gallery/gallery_ui/shadowbox/javascript/shadowbox.js');
		Requirements::javascript('image_gallery/gallery_ui/shadowbox/javascript/shadowbox_init.js');
		Requirements::css('image_gallery/gallery_ui/shadowbox/css/shadowbox.css');
		
	}

}
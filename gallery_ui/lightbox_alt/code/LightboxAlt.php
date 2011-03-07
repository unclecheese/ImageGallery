<?php

class LightboxAlt extends ImageGalleryUI
{
	static $link_to_demo = "http://www.balupton.com/sandbox/jquery_lightbox_bal/demo/";
	static $label = "LightBox (Balupton edition)";
	public $item_template = "LightboxAlt_item";
	
	public function initialize()
	{
		Requirements::javascript(THIRDPARTY_DIR.'/jquery/jquery.js'); 
		Requirements::javascript('image_gallery/gallery_ui/lightbox_alt/javascript/jquery.color.js');
		Requirements::javascript('image_gallery/gallery_ui/lightbox_alt/javascript/jquery.lightbox.js');
		Requirements::css('image_gallery/gallery_ui/lightbox_alt/css/jquery.lightbox.css');
		
	}
	
}
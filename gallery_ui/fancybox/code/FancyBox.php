<?php

class FancyBox extends ImageGalleryUI
{
	static $link_to_demo = "http://fancybox.net/example";
	static $label = "Fancy Box";
	public $item_template = "FancyBox_item";
	
	public function initialize()
	{
		Requirements::javascript(THIRDPARTY_DIR.'/jquery/jquery.js'); 
		Requirements::javascript('image_gallery/gallery_ui/fancybox/javascript/jquery.fancybox.js');
		Requirements::javascript('image_gallery/gallery_ui/fancybox/javascript/jquery.pngFix.pack.js');
		Requirements::javascript('image_gallery/gallery_ui/fancybox/javascript/fancybox_init.js');

		Requirements::css('image_gallery/gallery_ui/fancybox/css/fancy.css');
		
	}
	
}
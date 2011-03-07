<?php

abstract class ImageGalleryUI 
{
	static $link_to_demo;
	public $layout_template = "GalleryUI_layout";
	public $item_template = "GalleryUI_item";

	protected $ImageGalleryPage;
	
	abstract public function initialize();
		
	public function setImageGalleryPage(ImageGalleryPage $page)
	{
		$this->ImageGalleryPage = $page;
	}
	
	public function updateItems(DataObjectSet $items) { return $items; }
}

<?php

class ImageGallerySiteTree extends DataObjectDecorator
{
	
	function getGalleryFor($url_segment)
	{
		return ($url_segment === null) ? DataObject::get_one("ImageGalleryPage") : DataObject::get_one("ImageGalleryPage","URLSegment='$url_segment'");
	}
	
	function RecentImages($count = 5, $url_segment = null)
	{
		$gallery = $this->getGalleryFor($url_segment);
		if($gallery) {
			$items = DataObject::get("ImageGalleryItem","ImageGalleryPageID = {$gallery->ID}","Created DESC",null,$count);
			return $gallery->GalleryItems(null,$items);
		}
		return false;		
	}
	
	function RecentImagesGallery($count = 5, $url_segment = null)
	{
		$gallery = $this->getGalleryFor($url_segment);
		if($gallery) {
  		Requirements::themedCSS('ImageGallery');
  		return $this->owner->customise(array(
  			'GalleryItems' => $this->RecentImages($count, $url_segment),
  			'PreviousGalleryItems' => new DataObjectSet(),
  			'NextGalleryItems' => new DataObjectSet()
  		))->renderWith(array($gallery->UI->layout_template));
	  }
	  return false;
	}
}
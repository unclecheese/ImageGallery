<?php

class ImageGalleryImage extends Image 
{
	public function generateRotateClockwise(GD $gd) 
	{
		return $gd->rotate(90);
	}
	
	public function generateRotateCounterClockwise(GD $gd)
	{
		return $gd->rotate(270);
	}
	
	public function clearResampledImages()
	{
		$files = glob(Director::baseFolder().'/'.$this->Parent()->Filename."_resampled/*-$this->Name");
	 	foreach($files as $file) {unlink($file);}
	}
	
	public function Landscape()
	{
		return $this->getWidth() > $this->getHeight();
	}
	
	public function Portrait()
	{
		return $this->getWidth() < $this->getHeight();
	}
	
	function BackLinkTracking() {return false;}
	
}

?>

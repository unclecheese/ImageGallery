<div class="gallery-layout-wrapper">
	<% if GalleryItems %>
	<ul class="gallery-layout" id="gallery-list">
		<% if GalleryItems.NotFirstPage %>
			<% control PreviousGalleryItems %>
						<li style="display:none;">$GalleryItem</li>
			<% end_control %>
		<% end_if %>
		<% control GalleryItems %>
			<li>
					$GalleryItem
			</li>
		<% end_control %>
		<% if GalleryItems.NotLastPage %>
			<% control NextGalleryItems %>
				<li style="display:none;">$GalleryItem</li>
			<% end_control %>
		<% end_if %>
	</ul>
	<% end_if %>
</div>
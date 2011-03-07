
<% if CurrentGalleryItems %>
	<div id="ImageGallery">
		<div style="display:none">
			<% control PrevGalleryItems %>
				<a id="ViewLink-$ID" class="$JSLightWindow"<% if JSMedia %> rel="$JSMedia"<% end_if %> href="$ViewLink" title="$Title" caption="<% if Content %>$Content - <% end_if %><% if HasDimensions %>$Width x $Height - <% end_if %>$Size"<% if PopupParams %> params="$PopupParams"<% end_if %>/>
			<% end_control %>
		</div>
		<h3>$AlbumTitle</h3>
		<% if AdminGallery %>
			<% include AdminGallery %>
		<% else %>
			<% include UserGallery %>
		<% end_if %>
		
		<div style="display:none">
			<% control NextGalleryItems %>
				<a id="ViewLink-$ID" class="$JSLightWindow"<% if JSMedia %> rel="$JSMedia"<% end_if %> href="$ViewLink" title="$Title" caption="<% if Content %>$Content - <% end_if %><% if HasDimensions %>$Width x $Height - <% end_if %>$Size"<% if PopupParams %> params="$PopupParams"<% end_if %>/>
			<% end_control %>
		</div>
		<% if MediaPerPageLimit %>
			<% if CurrentGalleryItems.MoreThanOnePage %>
				<div id="NavigationBar">
					<div id="Previous">
						<% if CurrentGalleryItems.NotFirstPage %>
							<a rel="$JSPrevPage" href="$CurrentGalleryItems.PrevLink" title="View the previous page">&lt; Previous</a>
						<% end_if %>
					</div>
					<div id="PageNumbers">
						<% control CurrentGalleryItems.Pages %>
							<% if CurrentBool %>
								<span class="currentPage">$PageNum</span>
							<% else %>
								<a href="$Link" title="View page number $PageNum">$PageNum</a>
							<% end_if %>
						<% end_control %>
					</div>
					<div id="Next">
						<% if CurrentGalleryItems.NotLastPage %>
							<a rel="$JSNextPage" href="$CurrentGalleryItems.NextLink" title="View the next page">Next &gt;</a>
						<% end_if %>
					</div>
					<div class="clear"><!-- --></div>
				</div>
			<% end_if %>
		<% end_if %>
	</div>
<% end_if %>
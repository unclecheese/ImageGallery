<div id="primaryContent">
<div class="innerpad">
<div class="typography">
	<% if Albums %>
		<div id="Sidebar" class="typography">
			<div class="sidebarBox">
				<h3>$Title</h3>
				<ul id="Menu2">
				<% control Albums %>
					<li class="$LinkingMode"><a class="$LinkingMode" href="$Link" title="$AlbumName">$AlbumName</a></li>
				<% end_control %>
				</ul>
				<div class="clear"></div>
			</div>
			<div class="sidebarBottom"></div>
		</div>
	<div id="Content">
	<% end_if %>
		<h2>$AlbumTitle</h2>
		$GalleryLayout
		<div class="album-nav">
			<ul>
			<% if PrevAlbum %>
				<% control PrevAlbum %>
					<li class="prev">
						<div class="album-nav-img"><a href="$Link" title="<% sprintf(_t('GOTOALBUM','Go to the %s album'),$AlbumName) %>">$CoverImage.SetWidth(50)</a></div>
						<div class="album-nav-desc">
							<h4><% _t('PREVIOUSALBUM','Previous Album') %>:</h4>							
							<h5><a href="$Link">$AlbumName</a></h5>
						</div>
					</li>
				<% end_control %>
			<% end_if %>
			<% if NextAlbum %>
				<% control NextAlbum %>
					<li class="next">
						<div class="album-nav-img"><a href="$Link" title="<% sprintf(_t('GOTOALBUM','Go to the %s album'),$AlbumName) %>">$CoverImage.SetWidth(50)</a></div>
						<div class="album-nav-desc">
							<h4><% _t('NEXTALBUM','Next Album') %>:</h4>
							<h5><a href="$Link">$AlbumName</a></h5>
						</div>
					</li>
				<% end_control %>
			<% end_if %>
			</ul>
		</div>
		<% if GalleryItems.MoreThanOnePage %>
			<ul id="pagination-imagegallery">		
				<% if GalleryItems.NotFirstPage %>
					<li class="previous"><a title="<% _t('VIEWPREVIOUSPAGE','View the previous page') %>" href="$GalleryItems.PrevLink">&laquo;<% _t('PREVIOUS','Previous') %></a></li>				
				<% else %>	
					<li class="previous-off">&laquo;<% _t('PREVIOUS','Previous') %></li>
				<% end_if %>
				<% control GalleryItems.Pages %>
					<% if CurrentBool %>
						<li class="active">$PageNum</li>
					<% else %>
						<li><a href="$Link" title="<% sprintf(_t('VIEWPAGENUMBER','View page number %s'),$PageNum) %>">$PageNum</a></li>				
					<% end_if %>
				<% end_control %>
				<% if GalleryItems.NotLastPage %>
					<li class="next"><a title="<% _t('VIEWNEXTPAGE', 'View the next page') %>" href="$GalleryItems.NextLink"><% _t('NEXT','Next') %> &raquo;</a></li>
				<% else %>
					<li class="next-off"><% _t('NEXT','Next') %> &raquo;</li>				
				<% end_if %>
			</ul> 		
		<% end_if %>
	<% if Albums %>
		</div>
	<% end_if %>
</div>
</div>
</div>

	
	
	
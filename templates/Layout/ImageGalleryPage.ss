<div id="primaryContent">
<div class="innerpad">
<div class="typography">
	<% if Albums %>
		<div id="Sidebar" class="typography">
			<div class="sidebarBox">
				<h3><% sprintf(_t('ALBUMSINGALLERY','Albums in %s'),$Title) %></h3>
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
		<h2>$Title</h2>
			$Content
			<% include AlbumList %>
	<% if Albums %>
		</div>
	<% end_if %>
</div>
</div>
</div>

	
	
	
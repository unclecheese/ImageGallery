<div id="album-list">
	<ul>
	<% control Albums %>
			<li>
			<div class="defaultImage">
				<a href="$Link" title="$Title">
				<% if CoverImage %>
					<% control FormattedCoverImage %>
						<img src="$URL" alt="" />
					<% end_control %>
				<% else %>
					<span class="no-image"></span>
				<% end_if %>
				</a>
			</div>
			<div class="galleryDetails">
				<h4><a href="$Link" title="$Title">$AlbumName</a> ($ImageCount photos)</h4>
				<div class="galleryDescription">$Description.LimitWordCount(60)</div>	
			</div>
			</li>
	<% end_control %>
	</ul>
</div>

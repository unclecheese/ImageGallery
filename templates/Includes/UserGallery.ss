		<table>
			<% control CurrentGalleryItems %>
				<% if First %>
					<tr>
				<% else_if FirstItemLine %>
					<tr>
				<% end_if %>
				<td>
					<a id="ViewLink-$ID" rel="$RelAttr" class="$ClassAttr" title="$Caption" href="$ViewLink"><img src="$ThumbnailURL" alt="$Title"/></a>
				</td>
				<% if Last %>
					</tr>
				<% else_if LastItemLine %>
					</tr>
				<% end_if %>
			<% end_control %>
		</table>

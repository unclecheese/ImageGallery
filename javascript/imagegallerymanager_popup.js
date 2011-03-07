(function($) {
$(function() {
	$('.rotate-controls a').click(function() {
		link = $(this).attr('href');
		 $.ajax({
		   url: link,
		   success: function(html){
		   	$('#preview-image img.preview').attr('src', html);
		   }
		
		});
		return false;
	});
});

$().ajaxSend(function(r,s){  
 $(".ajax-loader").slideDown();  
});  
   
$().ajaxStop(function(r,s){  
  $(".ajax-loader").slideUp();  
}); 
})(jQuery); 

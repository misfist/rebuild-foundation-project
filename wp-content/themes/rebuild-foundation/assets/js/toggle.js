$(document).ready(function () {

	$('article').click(function() {
		$(this).addClass('expanded');
		$(this).siblings().removeClass('expanded');
	});

});
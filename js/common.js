(function($) {
$(function(){

	// スクロールボタン
	var $page_top = $('#page_top');
	$(window).on('scroll', function(){
		if ($(this).scrollTop() > 100) {
            $page_top.fadeIn();
		} else {
			$page_top.stop(true, true).fadeOut();
		}
	});
	$page_top.click(function () {
		$('body,html').animate({
			scrollTop: 0
		}, 800);
		return false;
	});

});
})(jQuery);

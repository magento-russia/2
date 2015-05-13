;(function($) { $(function() {
	if ('ru' === $('html').attr('lang')) {
		$(window).bind(rm.tweaks.ThemeDetector.initialized, function() {
			/** @type {jQuery} HTMLBodyElement */
			var $root = $('body.df-theme-cattheme-se105');
			if (0 < $root.length) {
				rm.dom
					.replaceText(
						$('#blocklogin .top-links > em', $root)
						,'or'
						,'или'
					)
					.replaceText(
						$('#sidenav-title', $root)
						,'All Categories'
						,'Наши товары'
					)
					.replaceText(
						$('#search_mini_form .form-search option[value=""]', $root)
						,'All Departments'
						,'все разделы'
					)
					.replaceText(
						$('.livechat a', $root)
						,'Live Chat'
						,'спросить'
					)
					.replaceText(
						$('.featured-cat-title h2', $root)
						,'Featured Categories'
						,'Обратите внимание'
					)
					.replaceText(
						$('.product-other-info span', $root)
						,'In '
						,''
					)
				;
			}
		});
	}
}); })(jQuery);
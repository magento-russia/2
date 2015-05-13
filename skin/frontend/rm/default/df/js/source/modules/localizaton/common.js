;(function($) { $(function() {
	if ('ru' === $('html').attr('lang')) {
		(function() {
			/** @type {jQuery} HTMLDivElement */
			var $block = $('#remember-me-popup');
			if (0 < $block.length)  {
				$block
					.html(
						$block.html()
							.replace(
								'What\'s this?'
								,'Что это?'
							)
							.replace(
								'Checking "Remember Me" will let you access your shopping cart on this computer when you are logged out'
								,'Вам не придется вводить логин и пароль каждый раз'
							)
					)
				;
			}
		})();
	}
}); })(jQuery);
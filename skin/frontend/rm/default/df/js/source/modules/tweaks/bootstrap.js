;(function($) { $(function() {
	(function() {
		/** @type {jQuery} HTMLAnchorElement */
		var $reviewLinks = $('.product-view .ratings .rating-links a');
		$reviewLinks.first().addClass('.first-child');
		$reviewLinks.last().addClass('.last-child');
	})();
	rm.tweaks.ThemeDetector.construct({});
	(function() {
		/** @type {jQuery} HTMLUListElement */
		var $links = $(".header-container .quick-access .links");
		$("a[href*='persistent/index/unsetCookie']", $links)
			.addClass('rm-preserve-case')
		;
		/**
		 * Если администратор включил опцию
		 * «Заменить «личный кабинет» («my account») на имя авторизованного клиента?»,
		 * то надо сохранить регистр букв у данной ссылки,
		 * потому что там будет написано имя клиента
		 */
		/** @type {jQuery} HTMLAnchorElement */
		var $accountLink = $("a[href$='customer/account/']", $links);
		/** @type string[] */
		var standardAccountTitles = ['личный кабинет', 'my account'];
		/**
		 *  Используем jQuery.inArray вместо Array.indexOf,
		 *  потому что Array.indexOf отсутствует в IE 8
		 *  @link http://www.w3schools.com/jsref/jsref_indexof_array.asp
		 */
		if (-1 === $.inArray($accountLink.text().toLowerCase(), standardAccountTitles)) {
			$accountLink.addClass('rm-preserve-case');
		}
	})();
}); })(jQuery);
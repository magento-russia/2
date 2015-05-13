;(function($) {$(function() {
	/**
	 * Magento Community Edition
	 * отводит на многие административные заголовки слишком много места — половину экрана,
	 * и поэтому расположенным справа от таких заголовков кнопкам и другим элементам управления
	 * не хватает места при нераспахнутом на весь монитор окне.
	 */
	/** @type {jQuery} HTMLTableCellElement */
	var $headerCell = $('.content-header table tr:first td:first');
	if ('width:50%;' === $headerCell.attr('style')) {
		$headerCell.removeAttr('style');
	}
});})(jQuery);
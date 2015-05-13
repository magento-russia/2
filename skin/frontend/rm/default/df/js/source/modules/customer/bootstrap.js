/**
 * Программный код,
 * который надо выполнить сразу после загрузки страницы
 */
rm.namespace('rm.checkout');
(function($) { $(function() {
	rm.namespace('rm.tweaks');
	// rm.tweaks.options отсутствует на страницах формы ПД-4
	if (!rm.tweaks.options) {
		rm.tweaks.options = {};
	}
	/** @type {jQuery} HTMLFormElement */
	var $loginForm = $('#login-form');
	if (0 < $loginForm.length) {
		/** @type {jQuery} HTMLInputElement */
		var $formKeyField = $('input[name="form_key"]', $loginForm);
		if (0 === $formKeyField.length) {
			/** @type string */
			var formKey = rm.tweaks.options.formKey;
			if (formKey) {
				$loginForm.append(
					$('<input/>').attr({type: 'hidden', name: 'form_key', 'value': formKey})
				);
			}
		}
	}
}); })(jQuery);
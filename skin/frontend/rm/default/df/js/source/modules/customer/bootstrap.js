rm.namespace('rm.checkout');
(function($) { $(function() {
	rm.namespace('rm.tweaks');
	// rm.tweaks.options отсутствует на страницах формы ПД-4
	if (!rm.tweaks.options) {
		rm.tweaks.options = {};
	}
	/**
	 * 2015-12-05
	 * Удивительно, как я не додумался до такого решения раньше, ведь оно совсем простое.
	 * Раньше тут стоял код var $loginForms = $('#login-form');
	 * Конечно, новый код намного универсальнее.
	 */
	/** @type {jQuery} HTMLFormElement[] */
	var $loginForms = $("form[action*='customer/account/loginPost']");
	$loginForms.each(function() {
		/** @type {jQuery} HTMLFormElement */
		var $form = $(this);
		/** @type {jQuery} HTMLInputElement */
		var $keyField = $('input[name="form_key"]', $form);
		if (0 === $keyField.length) {
			/** @type string */
			var formKey = rm.tweaks.options.formKey;
			if (formKey) {
				$form.append(
					$('<input/>').attr({type: 'hidden', name: 'form_key', 'value': formKey})
				);
			}
		}
	});
}); })(jQuery);
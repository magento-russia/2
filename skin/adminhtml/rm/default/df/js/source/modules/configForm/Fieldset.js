;(function($) {
	rm.namespace('rm.admin.configForm');
	/**
	 * @param {jQuery} HTMLElement
	 */
	rm.admin.configForm.Fieldset = {
		construct: function(_config) { var _this = {
			init: function() {
				$(':input', this.getElement())
					.each(
						function() {
							rm.admin.configForm.Field
								.construct(
									{
										element: $(this)
									}
								)
							;
						}
					)
				;
			}
			,
			/**
			 * @private
			 * @returns {jQuery} HTMLElement
			 */
			getElement: function() {
				return _config.element;
			}
		}; _this.init(); return _this; }
	};





})(jQuery);
;(function($) {
	rm.namespace('rm.checkout.ergonomic');
	//noinspection JSValidateTypes
	rm.checkout.ergonomic.Helper = {
		/**
		 * @function
		 * @returns {rm.checkout.ergonomic.Helper}
		 */
		construct: function(_config) { var _this = {
			init: function() {}
			,/**
			 * @public
			 * @param {String} inputId
			 * @returns {rm.checkout.ergonomic.Helper}
			 */
			addFakeInputIfNeeded: function(inputId) {
				if (!document.getElementById(inputId)) {
					$('<input/>')
						.attr({id: inputId, type: 'text'})
						.hide()
						.appendTo(this.getFakeForm())
					;
				}
				return this;
			}
			,/**
			 * @public
			 * @returns {jQuery} HTMLFormElement
			 */
			getFakeForm: function() {
				if (rm.undefined(this._fakeForm)) {
					var fakeFormClass = 'df-fake-form';
					/**  @type {jQuery} HTMLFormElement */
					this._fakeForm = $('form.' + fakeFormClass);
					if (1 > this._fakeForm.length) {
						this._fakeForm = $('<form/>').addClass(fakeFormClass).appendTo('body');
					}
				}
				return this._fakeForm;
			}
			,
			/**
			 * @public
			 * @used-by rm.checkout.ergonomic.address.Billing.listenForSelection()
			 * @used-by rm.checkout.ergonomic.address.Shipping.listenForSelection()
			 * @param {rm.checkout.ergonomic.address.Billing}|{rm.checkout.ergonomic.address.Shipping} address
			 * @returns {void}
			 */
			listenForSelection: function(address) {
				/** @type {jQuery} HTMLSelectElement */
				var addressSwitcher = $('.address-select', address.getElement());
				address.getAddress().getFields().add(addressSwitcher).change(function() {
					address.handleSelection();
				});
				if (addressSwitcher.length) {
					address.handleSelection();
				}
			}
			,/**
			 * @public
			 * @param {Object} response
			 * @returns {rm.checkout.ergonomic.Helper}
			 */
			updateSections: function(response) {
				if (response.df_update_sections) {
					$.each(response.df_update_sections, function() {
						/** @type {String} */
						var containerId = '#checkout-'+this.name+'-load';
						/** @type {jQuery} HTMLElement[] */
						var $newContent = $(this.html);
						/** @type {jQuery} HTMLElement */
						var $oldContainer = $(containerId);
						/** @type {jQuery} HTMLElement */
						var $newContainer = $(containerId, $newContent);
						if (0 === $newContainer.length) {
							$oldContainer.html(this.html);
						}
						else {
							$oldContainer.replaceWith(this.html);
						}
						$(window).trigger({
							/** @type {String} */
							type: rm.checkout.Ergonomic.sectionUpdated
							,/** @type {String} */
							section: this.name
						});
					});
				}
				if (response.update_section) {
					$(window).trigger({
						/** @type {String} */
						type: rm.checkout.Ergonomic.sectionUpdated
						,/** @type {String} */
						section: response.update_section.name
					});
				}
				return this;
			}
		}; _this.init(); return _this; }
	};
	/**
	 * @type {rm.checkout.ergonomic.Helper}
	 */
	rm.checkout.ergonomic.helperSingleton = rm.checkout.ergonomic.Helper.construct({});

})(jQuery);
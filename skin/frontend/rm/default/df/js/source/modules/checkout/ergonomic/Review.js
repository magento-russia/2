;(function($) {
	rm.namespace('rm.checkout.ergonomic');
	//noinspection JSValidateTypes
	rm.checkout.ergonomic.Review = {
		construct: function(_config) { var _this = {
			init: function() {
				/**
				 * При нажатии кнопки "Оформить заказ"
				 * система должна провести валидацию всех форм.
				 */
				/** @type {jQuery} HTMLButtonElement */
				var $submitOrderButton = $('#review-buttons-container .btn-checkout');
				$submitOrderButton
					.removeAttr('onclick')
					.click(
						/**
						 * @param {jQuery.Event} event
						 */
						function(event) {
							event.preventDefault();
							/** @type {Object}[] */
							var blocks =
								[
									rm.checkout.ergonomic.billingAddressSingleton
									,rm.checkout.ergonomic.shippingAddressSingleton
									,rm.checkout.ergonomic.shippingMethodSingleton
									,rm.checkout.ergonomic.paymentMethodSingleton
									/**
									 * Обратите внимание, что у Review нет формы и валидатора!
									 */
								]
							;
							/** @type {Boolean} */
							var valid = true;
							$.each(blocks, function(index, block) {
								if (!block.getValidator().validate()) {
									valid = false;
								}
							});
							if (valid) {
								if (false === _this.getCheckout().loadWaiting) {
									_this.save();
								}
								else {
									/**
									 * Вызывать save() пока бесполезно, потому что система занята.
									 * Поэтому вместо прямого вызова save планируем этот вызов на будущее.
									 */
									_this.needSave(true);
								}
							}
						}
					)
				;
				$(window)
					.bind(
						rm.checkout.Ergonomic.interfaceUpdated
						,/**
						 * @param {jQuery.Event} event
						 */
						function(event) {
							if (_this.needSave()) {
								_this.save();
							}
						}
					)
				;
			}
			,/**
			 * @public
			 * @returns {rm.checkout.ergonomic.Review}
			 */
			save: function() {
				_this.needSave(false);
				_this.getReview().save();
				return this;
			}
			,/**
			 * @private
			 * @returns {Review}
			 */
			getReview: function() {
				return review;
			}
			,/**
			 * @private
			 * @returns {Checkout}
			 */
			getCheckout: function() {
				return checkout;
			}
			,/**
			 * @public
			 * @param {Boolean}
			 * @returns {rm.checkout.ergonomic.method.Shipping}
			 */
			needSave: function(value) {
				if (rm.defined(value)) {
					this._needSave = value;
				}
				return this._needSave;
			}
			,/** @type {Boolean} */
			_needSave: false
			,/**
			 * @private
			 * @returns {jQuery} HTMLElement
			 */
			getElement: function() {
				return _config.element;
			}
		}; _this.init(); return _this; }
	};

})(jQuery);
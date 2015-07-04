;(function($) {
rm.namespace('rm.checkout');
//noinspection JSValidateTypes
/**
 * @param {String} elementSelector
 */
rm.checkout.Ergonomic = {
	interfaceUpdated: 'rm.checkout.Ergonomic.interfaceUpdated'
	,loadWaiting: 'rm.checkout.Ergonomic.loadWaiting'
	,sectionUpdated: 'rm.checkout.Ergonomic.sectionUpdated'
	,construct: function(_config) {var _this = {
		init: function() {
			$(function() {
				if (0 < _this.getElement().length) {
					/**
					 * Иногда нам требуется задать стили блоков вне текущего блока
					 *(в частности, блока .col-mail в теме Blanco).
					 */
					$('div.page').addClass('df-checkout-ergonomic-page');
					checkout.reloadProgressBlock = function() {};
					_this.loadWaiting_adjust();
					$('a.df-login', _this.getElement()).fancybox({
						titlePosition : 'inside'
						,transitionIn : 'none'
						,transitionOut : 'none'
						/**
						 * 2015-07-04
						 * Чтобы стили наших всплывающих окон
						 * не ломали внешний вид всплывающих окон других модулей и оформительских тем,
						 * которые тоже используют библиотеку Fancybox,
						 * добавляем свой уникальный класс CSS для корневого контейнера.
						 * Просто удивительно, что я не сделал этого раньше.
						 */
						, wrapCss: 'df-fancybox'
					});
					/** @type {rm.checkout.ergonomic.address.Billing} */
					rm.checkout.ergonomic.billingAddressSingleton =
						rm.checkout.ergonomic.address.Billing.construct({
							element: $('.df-block-address-billing', _this.getElement())
						})
					;
					/** @type {rm.checkout.ergonomic.address.Shipping} */
					rm.checkout.ergonomic.shippingAddressSingleton =
						rm.checkout.ergonomic.address.Shipping.construct({
							element: $('.df-block-address-shipping', _this.getElement())
						})
					;
					/** @type {rm.checkout.ergonomic.method.Shipping} */
					rm.checkout.ergonomic.shippingMethodSingleton =
						rm.checkout.ergonomic.method.Shipping.construct({
							element: $('.df-block-method-shipping', _this.getElement())
						})
					;
					/** @type {rm.checkout.ergonomic.method.Payment} */
					rm.checkout.ergonomic.paymentMethodSingleton =
						rm.checkout.ergonomic.method.Payment.construct({
							element: $('.df-block-method-payment', _this.getElement())
						})
					;
					/** @type {rm.checkout.ergonomic.Review} */
					rm.checkout.ergonomic.reviewSingleton =
						rm.checkout.ergonomic.Review.construct({
							element: $('.order-review', _this.getElement())
						})
					;
				}
			});
		}
		,/**
		 * @private
		 * @returns {rm.checkout.Ergonomic}
		 */
		loadWaiting_adjust: function() {
			/** @function */
			var originalFunction = this.getCheckout().setLoadWaiting;
			this.getCheckout().setLoadWaiting = function(step, keepDisabled) {
				originalFunction.call(_this.getCheckout(), step, keepDisabled);
				if (false !== step) {
					_this.loadWaiting_enable();
				}
				else {
					_this.loadWaiting_disable();
				}
			};
			return this;
		}
		,/**
		 * @private
		 * @returns {rm.checkout.Ergonomic}
		 */
		loadWaiting_disable: function() {
			$.unblockUI();
			return this;
		}
		,/**
		 * @private
		 * @returns {rm.checkout.Ergonomic}
		 */
		loadWaiting_enable: function() {
			$.blockUI({
				message: $('#df-loading-mask').clone()
				,css: {border: 0}
				,overlayCSS: {opacity: 0}
			});
			return this;
		}
		,/**
		 * @private
		 * @returns {Checkout}
		 */
		getCheckout: function() {return checkout;}
		,/**
		 * @private
		 * @returns {jQuery} HTMLElement
		 */
		getElement: function() {
			if (rm.undefined(this._element)) {
				this._element = $(this.getElementSelector());
			}
			return this._element;
		}
		/**
		 * @private
		 * @returns {String}
		 */
		,getElementSelector: function() {
			return _config.elementSelector;
		}
	}; _this.init(); return _this; }
};
})(jQuery);
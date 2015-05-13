;(function($) {
	rm.namespace('rm.checkout.ergonomic.method');
	//noinspection JSValidateTypes
	rm.checkout.ergonomic.method.Payment = {
		construct: function(_config) { var _this = {
			init: function() {
				this.getPaymentMethod().onSave = function(transport) {
					try {
						_this.getPaymentMethod().nextStep(transport);
					}
					catch(e) {
						console.log(e);
					}
					_this.onComplete();
				};
				this.handleAutoCommit();
				this.handleSelection();
				this.subscribeToSectionUpdate();
				this.handleNoMethods();
			}
			,/**
			 * @public
			 * @returns {rm.checkout.ergonomic.method.Payment}
			 */
			handleAutoCommit: function() {
				/**
				 * @function
				 */
				var commitSelectionToTheServerIfNeeded = function() {
					/** @type {jQuery} HTMLInputElement[] */
					var $paymentMethods = $('input[name=payment\\[method\\]]', _this.getElement());
					if (
							(1 === $paymentMethods.length)
						||
						/**
						 * Один из способов оплаты уже автоматически выбран (браузером?).
						 */
							(0 < $paymentMethods.filter(':checked').length)
					) {
						_this.save();
					}
				};
				$(window)
					.bind(
						rm.checkout.Ergonomic.interfaceUpdated
						,/**
						 * @param {jQuery.Event} event
						 */
						function(event) {
							if (
								/**
								 *  Данное ограничение — не просто прихоть ради ускорения.
								 *  Без этого ограничения система зависнет,
								 *  потому что система постоянно будет выполнять метод save/
								 *  Причём возможны два вида бесконечных циклов:
								 *  1) прямой(shipping.save(),  shipping.save(), shipping.save())
								 *  2) косвенный(shipping.save(),  billing.save(), shipping.save())
								 *
								 *  Используем jQuery.inArray вместо Array.indexOf,
								 *  потому что Array.indexOf отсутствует в IE 8
								 *  @link http://www.w3schools.com/jsref/jsref_indexof_array.asp
								 */
									-1
								===
									$.inArray(event.updateType, ['shippingMethod', 'paymentMethod'])
							) {
								commitSelectionToTheServerIfNeeded();
							}
							else if (_this.needSave()) {
								_this.save();
							}
						}
					)
				;
				commitSelectionToTheServerIfNeeded();
				return this;
			}
			,/**
			 * @public
			 * @returns {rm.checkout.ergonomic.method.Payment}
			 */
			handleNoMethods: function() {
				/** @type {jQuery} HTMLDListElement */
				var $methodsContainer = $('#checkout-payment-method-load');
				if (0 === $('dt', $methodsContainer).length) {
					var $parent = $methodsContainer.parent();
					/**
					 * Отсутствуют способы оплаты
					 */
					$methodsContainer
						.replaceWith(
							$('<div id="checkout-payment-method-load" />')
								.html(
"<p><span class='p'>Чтобы узнать доступные для Вашего заказа варианты оплаты — подробно заполните предыдущие блоки анкеты(платёжные реквизиты, адрес доставки, способ доставки).</span>"
+
"<span class='p'>Если Вы уже заполнили подробно предыдущие блоки анкеты, но варианты доставки так и не появились — пожалуйста, позвоните нам по телефону, и мы подберём индивидуальный вариант оплаты Вашего заказа.</span></p>"
								)
						)
					;
				}
				return this;
			}
			,/**
			 * @public
			 * @returns {rm.checkout.ergonomic.method.Payment}
			 */
			subscribeToSectionUpdate: function() {
				$(window)
					.bind(
						rm.checkout.Ergonomic.sectionUpdated
						,/**
						 * @param {jQuery.Event} event
						 */
						function(event) {
							if ('payment-method' === event.section) {
								_this.handleNoMethods();
							}
						}
					)
				;
				return this;
			}
			,/**
			 * @public
			 * @returns {rm.checkout.ergonomic.method.Payment}
			 */
			handleSelection: function() {
				$(document.getElementById(_this.getPaymentMethod().form))
					.change(
						function() {
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
					)
				;
				return this;
			}
			,/**
			 * @public
			 * @returns {rm.checkout.ergonomic.method.Payment}
			 */
			save: function() {
				this.getValidator().dfValidateFilledFieldsOnly();
				if (this.getValidator().dfValidateSilent()) {
					this.needSave(false);
					this.getPaymentMethod().save();
				}
				return this;
			}
			,/**
			 * @public
			 * @param {Object} transport
			 * @returns {rm.checkout.ergonomic.method.Payment}
			 */
			onComplete: function(transport) {
				this.getPaymentMethod().resetLoadWaiting(transport);
				rm.checkout.ergonomic.helperSingleton.updateSections(response);
				$(window)
					.trigger(
						{
							/** @type {String} */
							type: rm.checkout.Ergonomic.interfaceUpdated
							,/** @type {String} */
							updateType: 'paymentMethod'
						}
					)
				;
				return this;
			}
			,/** @function */
			_standardOnSaveHandler: payment.onSave
			,/**
			 * @private
			 * @returns {Payment}
			 */
			getPaymentMethod: function() {
				return payment;
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
			 * @returns {rm.checkout.ergonomic.method.Payment}
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
			,/**
			 * @private
			 * @returns {Validation}
			 */
			getValidator: function() {
				if (rm.undefined(this._validator)) {
					this._validator = new Validation(_this.getPaymentMethod().form);
				}
				return this._validator;
			}
		}; _this.init(); return _this; }
	};

})(jQuery);
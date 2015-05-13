;(function($) {
	rm.namespace('rm.checkout.ergonomic.method');
	//noinspection JSValidateTypes
	rm.checkout.ergonomic.method.Shipping = {
		construct: function(_config) {
			var _this = {
			init: function() {
				this.getShippingMethod().onSave = function(transport) {
					try {
						_this.getShippingMethod().nextStep(transport);
					}
					catch(e) {
						console.log(e);
					}
					_this.onComplete();
				};
				this.handleAutoCommit();
				this.handleSelection();
				this.handleNoMethods();
			}
			,/**
			 * @public
			 * @returns {rm.checkout.ergonomic.method.Shipping}
			 */
			handleAutoCommit: function() {
				/**
				 * @function
				 */
				var commitSelectionToTheServerIfNeeded = function() {
					/** @type {jQuery} HTMLInputElement[] */
					var $shippingMethods = $('input[name=shipping_method]', _this.getElement());
					/** @type {Boolean} */
					var needCommit = false;
					if (1 === $shippingMethods.length) {
						needCommit = true;
						/**
						 * Обратите внимание,
						 * что даже при единиственном предложенном покупателю способе доставки ,
						 * возможна ситуация, когда он не будет выбран системой по умолчанию:
						 * так будет, если на странице имеется диагностическое сообщение
						 * от другого способа доставки, например, от Почты России:
						 * «Способ доставки недоступен для выбранной страны получения».
						 * В таком случае единственный доступный способ доставки надо выбрать вручную.
						 */
						if (!$shippingMethods.prop('checked')) {
							$shippingMethods.prop('checked', true);
						}
					}
					else if (0 < $shippingMethods.filter(':checked').length) {
						/**
						 * Один из способов доставки уже автоматически выбран (браузером?).
						 * Таким образом, надо загрузить информацию следующего шага:
						 * перечень спссобов оплаты.
						 */
						needCommit = true;
					}
					if (needCommit) {
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
			 * @returns {rm.checkout.ergonomic.method.Shipping}
			 */
			handleNoMethods: function() {
				/** @type {jQuery} HTMLDListElement */
				var $methodsContainer = $('#checkout-shipping-method-load');
				if (0 === $('dt', $methodsContainer).length) {
					var $parent = $methodsContainer.parent();
					/**
					 * Отсутствуют способы оплаты
					 */
					$methodsContainer
						.replaceWith(
							$('<div id="checkout-shipping-method-load" />')
								.html(
"<p><span class='p'>Чтобы узнать доступные для Вашего заказа варианты доставки — укажите подробный адрес доставки.</span>"
+ "<span class='p'>Если Вы уже указали адрес доставки, но варианты доставки так и не появились — пожалуйста, позвоните нам по телефону, и мы подберём индивидуальный вариант доставки Вашего заказа.</span></p>"
								)
						)
					;
				}
				return this;
			}
			,/**
			 * @public
			 * @returns {void}
			 */
			handleSelection: function() {
				$(document.getElementById(_this.getShippingMethod().form)).change(
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
				);
			}
			,/**
			 * @public
			 * @returns {rm.checkout.ergonomic.method.Shipping}
			 */
			save: function() {
				this.getValidator().dfValidateFilledFieldsOnly();
				if (this.getValidator().dfValidateSilent()) {
					this.needSave(false);
					this.getShippingMethod().save();
				}
				return this;
			}
			,/**
			 * @public
			 * @param {Object} transport
			 * @returns {rm.checkout.ergonomic.method.Shipping}
			 */
			onComplete: function(transport) {
				this.getShippingMethod().resetLoadWaiting(transport);
				rm.checkout.ergonomic.helperSingleton.updateSections(response);
				$(window)
					.trigger(
						{
							/** @type {String} */
							type: rm.checkout.Ergonomic.interfaceUpdated
							,/** @type {String} */
							updateType: 'shippingMethod'
						}
					)
				;
				return this;
			}
			,/** @function */
			_standardOnSaveHandler: shippingMethod.onSave
			,/**
			 * @private
			 * @returns {ShippingMethod}
			 */
			getShippingMethod: function() {return shippingMethod;}
			,/**
			 * @private
			 * @returns {Checkout}
			 */
			getCheckout: function() {return checkout;}
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
			,/**
			 * @private
			 * @returns {Validation}
			 */
			getValidator: function() {
				if (rm.undefined(this._validator)) {
					this._validator = new Validation(_this.getShippingMethod().form);
				}
				return this._validator;
			}
		}; _this.init(); return _this; }
	};
})(jQuery);
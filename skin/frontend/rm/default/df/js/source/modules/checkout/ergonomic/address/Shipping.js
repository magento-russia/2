;(function($) {
	rm.namespace('rm.checkout.ergonomic.address');
	//noinspection JSValidateTypes
	rm.checkout.ergonomic.address.Shipping = {
		hasNoFields: 'rm.checkout.ergonomic.address.Shipping.hasNoFields'
		,construct: function(_config) { var _this = {
			init: function() {
				this.getShipping().onSave = function(transport) {
					try {
						_this.getShipping().nextStep(transport);
					}
					catch(e) {
						console.log(e);
					}
					_this.onComplete();
				};
				// Важно вызывать этот метод ранее других
				this.addFakeRegionFieldsIfNeeded();
				this.listenForShippingAddressTheSameAsBilling();
				this.disableShippingAddressTheSameSwitcherIfNeeded();
				this.handleShippingAddressHasNoFields();
				this.listenForSelection();
				$(document.getElementById('shipping:country_id')).select2({
					width: 150
					, minimumResultsForSearch: 0
					,dropdownCss: {width: 200}
				})
					.on('change', function(e) {
						if (window.shippingRegionUpdater) {
							window.shippingRegionUpdater.update();
          				}
					});
				$(window).bind(rm.checkout.Ergonomic.interfaceUpdated
					, /** @param {jQuery.Event} event */
					function(event) {
						if (
								_this.needSave()
							||
								(
										('billingAddress' === event.updateType)
									&&
										_this.hasNoFields()
								)
						) {
							_this.save();
						}
					}
				);
			}
			,/**
			 * @public
			 * @returns {rm.checkout.ergonomic.address.Shipping}
			 */
			save: function() {
				this.needSave(false);
				var $regionAsText = this.getAddress().getFieldRegionText().getElement();
				var $regionAsSelect = this.getAddress().getFieldRegionSelect().getElement();
				var regionAsText = $regionAsText.get(0);
				var regionAsSelect = $regionAsSelect.get(0);
				if (regionAsText && regionAsSelect) {
					if ('none' === regionAsText.style.display) {
						regionAsText.value = $('option:selected', $regionAsSelect).text();
					}
				}
				this.getShipping().save();
				return this;
			}
			,/**
			 * @public
			 * @returns {rm.checkout.ergonomic.address.Shipping}
			 */
			addFakeRegionFieldsIfNeeded: function() {
				rm.checkout.ergonomic.helperSingleton.addFakeInputIfNeeded('shipping:region');
				rm.checkout.ergonomic.helperSingleton.addFakeInputIfNeeded('shipping:region_id');
				return this;
			}
			,/**
			 * @public
			 * @returns {rm.checkout.ergonomic.address.Shipping}
			 */
			disableShippingAddressTheSameSwitcherIfNeeded: function() {
				/**
				 * Если форма адреса доставки содержит обязательное для заполнения поле,
				 * которое в то же время не является обязательным для заполнения в адресе плательщика,
				 * то переключатель "Доставить на этот адрес" / "Доставить по другому адресу"
				 * надо скрыть и сразу выбрать значение "Доставить по другому адресу".
				 */
				/** @type {Boolean} */
				var needDisableSwitcher = false;
				this.getAddress().getFields().each(function() {
					/** @type {rm.customer.address.Field} */
					var shippingField = rm.customer.address.Field.construct({element: $(this)});
					if (shippingField.isRequired()) {
						/** @type {rm.customer.address.Field} */
						var billingField =
							rm.checkout.ergonomic.billingAddressSingleton.getAddress().getField(
								shippingField.getShortName()
							)
						;
						if (!billingField.isExist() || !billingField.isRequired()) {
							needDisableSwitcher = true;
							return false;
						}
					}
				});
				if (needDisableSwitcher) {
					_this.handleShippingAddressTheSameAsBilling(false);
					$(
						rm.checkout.ergonomic.billingAddressSingleton.getAddress()
							.getField('use_for_shipping').getElement()
					)
						.closest('li.control')
							.hide()
					;
					_this._useForShippingYes.removeAttr('checked');
					_this._useForShippingNo.attr('checked', 'checked');
					_this._sameAsBilling.val(0);
					_this._sameAsBilling.get(0).checked = false;
				}
				return this;
			}
			,/**
			 * @public
			 * @returns {rm.checkout.ergonomic.address.Shipping}
			 */
			listenForSelection: function() {
				rm.checkout.ergonomic.helperSingleton.listenForSelection(this);
				return this;
			}
			,/**
			 * @public
			 * @returns {void}
			 */
			handleSelection: function() {
				if (!_this._useForShippingYes.get(0).checked) {
					_this.validateAndSave();
				}
			}
			,/** @returns {void} */
			validateAndSave: function() {
				this.getValidator().dfValidateFilledFieldsOnly();
				if (this.getValidator().dfValidateSilent()) {
					if (false === this.getCheckout().loadWaiting) {
						this.save();
					}
					else {
						/**
						 * Вызывать save() пока бесполезно, потому что система занята.
						 * Поэтому вместо прямого вызова save планируем этот вызов на будущее.
						 */
						this.needSave(true);
					}
				}
			}
			,/**
			 * @public
			 * @returns {rm.checkout.ergonomic.method.Shipping}
			 */
			handleShippingAddressHasNoFields: function() {
				// один невидимый элемента у нас всегда есть: shipping:address_id
				if (this.hasNoFields()) {
					this.getElement().hide();
					$(window).trigger({
						/** @type {String} */
						type: rm.checkout.ergonomic.address.Shipping.hasNoFields
					});
				}
				return this;
			}
			,/** @returns {void} */
			listenForShippingAddressTheSameAsBilling: function() {
				/** @type {jQuery} HTMLSelectElement */
				var $shippingAddressSelect = $('#shipping-address-select');
				/** @type {jQuery} HTMLSelectElement */
				var $billingAddressSelect = $('#billing-address-select');
				_this._useForShipping.change(function() {
					_this.handleShippingAddressTheSameAsBilling();
					// 2015-03-04
					// Пересчитываем тарифы на доставку
					// только при выборе варианта «доставить по этому адресу».
					// Если же выбран вариант «доставить по другому адресу»,
					// то персчитывать тарифы на доставку не нужно,
					// потому что адресом доставки по умолчанию
					// всё равно является текущий платёжный адрес
					// (вот когда покупатель его сменит — тогда и пересчитаем тарифы).
					if (_this._useForShippingYes.get(0).checked) {
						// Если покупатель выбрал пункт «доставить по этому адресу»,
						// и так уж получилось, что текущий адрес доставки в пыпадающем списке
						// совпал с текущим платёжным адресом в пыпадающем списке,
						// то пересчитывать тарифы не нужно
						/** @type {Integer} */
						var currentShippingAddressId = parseInt($shippingAddressSelect.val());
						/** @type {Integer} */
						var currentBillingAddressId = parseInt($billingAddressSelect.val());
						if (
								!currentShippingAddressId
							||
								!currentBillingAddressId
							||
								(currentShippingAddressId !== currentBillingAddressId)
						) {
							_this.validateAndSave();
						}
					}
				});
				/**
				 * Явно вызываем метод handleShippingAddressTheSameAsBilling в первый раз,
				 * потому что {rm.checkout.ergonomic.address.Billing} инициализируется до
				 * {rm.checkout.ergonomic.address.Shipping}, и первое оповещение от
				 * {rm.checkout.ergonomic.address.Billing} не доходит до
				 * {rm.checkout.ergonomic.address.Shipping}.
				 */
				_this.handleShippingAddressTheSameAsBilling();
			}
			,/**
			 * @public
			 * @param {?Boolean} value
			 * @returns {void}
			 */
			handleShippingAddressTheSameAsBilling: function(value) {
				if (!rm.defined(value)) {
					value = _this._useForShippingYes.get(0).checked;
				}
				_this.getShipping().setSameAsBilling(value);
				_this.getElement().toggle(!value);
			}
			,/**
			 * @public
			 * @param {Object} transport
			 * @returns {rm.checkout.ergonomic.address.Shipping}
			 */
			onComplete: function(transport) {
				this.getShipping().resetLoadWaiting(transport);
				rm.checkout.ergonomic.helperSingleton.updateSections(response);
				$(window)
					.trigger(
						{
							/** @type {String} */
							type: rm.checkout.Ergonomic.interfaceUpdated
							,/** @type {String} */
							updateType: 'shippingAddress'
						}
					)
				;
				return this;
			}
			,/**
			 * @public
			 * @returns {Boolean}
			 */
			hasNoFields: function() {
				if (rm.undefined(this._hasNoFields)) {
					/** @type {jQuery} HTMLInputElement */
					var $fields = $('#shipping-new-address-form fieldset :input', _this.getElement());
					/** @type {Boolean} */
					this._hasNoFields = (2 > $fields.length);
				}
				return this._hasNoFields;
			}
			,/**
			 * @public
			 * @returns {rm.customer.Address}
			 */
			getAddress: function() {
				if (rm.undefined(this._address)) {
					/**
					 * @type {rm.customer.Address}
					 */
					this._address =
						rm.customer.Address
							.construct(
								{
									element: $('#co-shipping-form', _this.getElement())
									,type: 'shipping'
								}
							)
					;
				}
				return this._address;
			}
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
			 * @returns {Shipping}
			 */
			getShipping: function() {return shipping;}
			,/**
			 * @private
			 * @returns {jQuery} HTMLElement
			 */
			getElement: function() {return _config.element;}
			,/**
			 * @private
			 * @returns {Validation}
			 */
			getValidator: function() {
				if (rm.undefined(this._validator)) {
					this._validator = new Validation(_this.getShipping().form);
				}
				return this._validator;
			}
			/** @type {jQuery} HTMLInputElement */
			,_sameAsBilling: $(document.getElementById('shipping:same_as_billing'))
			/** @type {jQuery} HTMLInputElement */
			,_useForShipping: rm.checkout.ergonomic.billingAddressSingleton
				.getAddress().getField('use_for_shipping').getElement()
			/** @type {jQuery} HTMLInputElement */
			,_useForShippingYes: $(document.getElementById('billing:use_for_shipping_yes'))
			/** @type {Boolean} */
			,_useForShippingPreviousState: null
			/** @type {jQuery} HTMLInputElement */
			,_useForShippingNo: $(document.getElementById('billing:use_for_shipping_no'))
		}; _this.init(); return _this; }
	};

})(jQuery);
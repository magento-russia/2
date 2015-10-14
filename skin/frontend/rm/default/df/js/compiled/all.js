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
						 * http://stackoverflow.com/a/9526314
						 */
						, wrapCSS: 'df-fancybox'
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
})(jQuery);;(function($) {
	rm.namespace('rm.checkout');
	//noinspection JSValidateTypes
	rm.checkout.OrderComments = {
		construct: function(_config) { var _this = {
			init: function() {
				if (
					/**
					 * Отсутствие блока комментариев
					 * говорит об отключенности данной функциональности
					 */
					(0 < this.getElement().length)
				) {
					if (this.isItMultiShippingCheckout()) {
						if ('below' === rm.checkout.orderComments.position) {
							this.getTarget().after(this.getElement());
						}
						else {
							this.getTarget().before(this.getElement());
						}
					}
					else {
						if (0 === this.getAgreements().length) {
							/*
								В Magento CE 1.9 шаблон
								base/default/template/checkout/onepage/agreements.phtml
								немного дефектен:
								там в начале стоит условие
								<?php if (!$this->getAgreements()) return; ?>
								Однако это условие никогда не будет выполняться,
								потому что $this->getAgreements() — коллекция, а не массив.
								Поэтому при отсутствии условий продажи
								шаблон всё равно добавить на страницу мусорную разметку
								<form action="" id="checkout-agreements" onsubmit="return false;">
								<ol class="checkout-agreements">
								</ol>
								</form>
								Вот её надо удалить.
							 */
							$('#checkout-agreements').remove();
							$('#checkout-review-submit')
								.prepend(
									$('<form/>')
										.attr({
											id: 'checkout-agreements'
											,action: ''
											,onsubmit: 'return false;'
										})
										.append(
											this.getElement()
										)
								)
							;
						}
						else {
							this.getElement().removeClass('buttons-set');
							if ('below' === rm.checkout.orderComments.position) {
								this.getTarget().append(this.getElement());
							}
							else {
								this.getTarget().prepend(this.getElement());
							}
						}
					}
				}
			}
			,/**
			 * @private
			 * @returns {jQuery} HTMLElement[]
			 */
			getAgreements: function() {
				if (rm.undefined(this._agreements)) {
					this._agreements = $('.agree');
				}
				return this._agreements;
			}
			,/**
			 * @private
			 * @returns {jQuery} HTMLElement
			 */
			getElement: function() {
				if (rm.undefined(this._element)) {
					/** @type {jQuery} HTMLElement */
					this._element =
						$('#df_checkout_review_orderComments')
							.clone()
							.removeAttr('id')
							.removeClass('df-hidden')
					;
					/**
					 * Стандартный браузерный программный код оформления заказа
					 * перезаписывает блок review
					 * после практически любых шагов покупателя при оформлении заказа.
					 * При этом перезаписывается и блок комментариев, и комментарии теряются.
					 *
					 * Чтобы сохранить комментарии,
					 * надо на событие потери фокуса блоком комментариев
					 * сохранять комментарий в какой-нибудь браузерной переменной
					 * (но не динамической переменной внутри данного класса,
					 * потому что объект данного класса создается заново
					 * после перезаписи блока review).
					 */
					/** @type {jQuery} HTMLTextAreaElement */
					var $textarea = $('textarea', this._element);
					$textarea
						.blur(
							function() {
								rm.checkout.ergonomic.helperSingleton.orderComment =
									$textarea.val()
								;
							}
						)
						.val(
							rm.checkout.ergonomic.helperSingleton.orderComment
						)
					;
				}
				return this._element;
			}
			,/**
			 * @private
			 * @returns {jQuery} HTMLElement
			 */
			getTarget: function() {
				if (rm.undefined(this._target)) {
					this._target = $('#checkout-agreements');
				}
				return this._target;
			}
			,/**
			 * @private
			 * @returns {Boolean}
			 */
			isItMultiShippingCheckout: function() {
				if (rm.undefined(this._itIsMultiShippingCheckout)) {
					this._itIsMultiShippingCheckout = 0 < $('.multiple-checkout').length;
				}
				return this._itIsMultiShippingCheckout;
			}
		}; _this.init(); return _this; }
	};

})(jQuery);// программный код, который надо выполнить сразу после загрузки страницы
rm.namespace('rm.checkout');
(function($) {$(function() {
	rm.checkout.Ergonomic.construct({elementSelector: '.df .df-checkout-ergonomic'});
	rm.checkout.OrderComments.construct({});
	$(window).bind(
		rm.checkout.Ergonomic.sectionUpdated
		,/** @param {jQuery.Event} event */
		function(event) {
			if ('review' === event.section) {
				rm.checkout.OrderComments.construct({});
			}
		}
	);
});})(jQuery);;(function($) {
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

})(jQuery);;(function($) {
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

})(jQuery);;(function($) {
	rm.namespace('rm.checkout.ergonomic.address');
	//noinspection JSValidateTypes
	/** @param {jQuery} element */
	rm.checkout.ergonomic.address.Billing = {
		shippingAddressIsTheSame: 'rm.checkout.ergonomic.address.Billing.shippingAddressIsTheSame'
		,construct: function(_config) { var _this = {
			init: function() {
				this.getBilling().onSave = function(transport) {
					try {
						_this.getBilling().nextStep(transport);
					}
					catch(e) {
						console.log(e);
					}
					_this.onComplete();
				};
				this.handleShippingAddressHasNoFields();
				this.listenForSelection();
				this.addFakeRegionFieldsIfNeeded();
			}
			,/** @returns {void} */
			save: function() {
				_this.needSave(false);
				var $regionAsText = this.getAddress().getFieldRegionText().getElement();
				var $regionAsSelect = this.getAddress().getFieldRegionSelect().getElement();
				var regionAsText = $regionAsText.get(0);
				var regionAsSelect = $regionAsSelect.get(0);
				if (regionAsText && regionAsSelect) {
					if ('none' === regionAsText.style.display) {
						regionAsText.value = $('option:selected', $regionAsSelect).text();
					}
				}
				_this.getBilling().save();
			}
			,/** @returns {void} */
			addFakeRegionFieldsIfNeeded: function() {
				rm.checkout.ergonomic.helperSingleton.addFakeInputIfNeeded('billing:region');
				rm.checkout.ergonomic.helperSingleton.addFakeInputIfNeeded('billing:region_id');
			}
			,/** @returns {void} */
			listenForSelection: function() {
				rm.checkout.ergonomic.helperSingleton.listenForSelection(this);
			}
			,
			/**
			 * @public
			 * @used-by rm.checkout.ergonomic.Helper.listenForSelection()
			 * @returns {void}
			 */
			handleSelection: function() {
				this.getValidator().dfValidateFilledFieldsOnly();
				if (this.getValidator().dfValidateSilent()) {
					false === this.getCheckout().loadWaiting
					? this.save()
					/**
					 * Вызывать @see save() пока бесполезно, потому что система занята.
					 * Поэтому вместо прямого вызова @see save() планируем этот вызов на будущее.
					 */
					: this.needSave(true);
				}
			}
			,/**
			 * @public
			 * @returns {rm.checkout.ergonomic.method.Billing}
			 */
			handleShippingAddressHasNoFields: function() {
				$(window).bind(
					rm.checkout.ergonomic.address.Shipping.hasNoFields
					/** @param {jQuery.Event} event */
					,function(event) {
						$(_this.getAddress().getField('use_for_shipping').getElement())
							.closest('li.control').hide()
						;
					}
				);
				return this;
			}
			,/**
			 * @public
			 * @param {Object} transport
			 * @returns {rm.checkout.ergonomic.address.Billing}
			 */
			onComplete: function(transport) {
				this.getBilling().resetLoadWaiting(transport);
				rm.checkout.ergonomic.helperSingleton.updateSections(response);
				$(window).trigger({
					/** @type {String} */
					type: rm.checkout.Ergonomic.interfaceUpdated
					,/** @type {String} */
					updateType: 'billingAddress'
				});
				return this;
			}
			,/**
			 * @public
			 * @returns {rm.customer.Address}
			 */
			getAddress: function() {
				if (rm.undefined(this._address)) {
					/** @type {rm.customer.Address} */
					this._address = rm.customer.Address.construct({
						element: $('#co-billing-form', _this.getElement())
						,type: 'billing'
					});
				}
				return this._address;
			}
			,/**
			 * @private
			 * @returns {Billing}
			 */
			getBilling: function() {return billing;}
			,/**
			 * @private
			 * @returns {Checkout}
			 */
			getCheckout: function() {return checkout;}
			,/**
			 * @public
			 * @param {Boolean}
			 * @returns {rm.checkout.ergonomic.address.Billing}
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
			getElement: function() {return _config.element;}
			,/**
			 * @private
			 * @returns {Validation}
			 */
			getValidator: function() {
				if (rm.undefined(this._validator)) {
					/** @type {Validation} */
					this._validator = new Validation(_this.getBilling().form);
				}
				return this._validator;
			}
		}; _this.init(); return _this; }
	};
})(jQuery);;(function($) {
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
				$(document.getElementById('shipping:country_id'))
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

})(jQuery);;(function($) {
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
								 *  http://www.w3schools.com/jsref/jsref_indexof_array.asp
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

})(jQuery);;(function($) {
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
								 *  http://www.w3schools.com/jsref/jsref_indexof_array.asp
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
})(jQuery);;(function($) {
	rm.namespace('rm.customer');
	//noinspection JSValidateTypes
	/**
	 * @param {String} type		тип адреса
	 * @param {jQuery} element
	 */
	rm.customer.Address = {construct: function(_config) {
		var _this = {
		init: function() {$(function() {});}
		,/**
		 * @public
		 * @returns {jQuery} HTMLElement[]
		 */
		getFields: function() {
			if (rm.undefined(this._fields)) {
				/**
				 * 2015-02-15
				 * Класс «rm-checkout-input» добавляет метод
				 * @see Df_Checkout_Block_Frontend_Ergonomic_Address_Field::getCssClasses().
				 * Обратите внимание, что псевдоселектор «:input» добавлять всё равно нужно,
				 * потому что плагин JQuery Select2 копирует все классы настоящего поля
				 * в свой автогенерируемый div.
				 */
				this._fields = $(':input.rm-checkout-input', this.getElement()).not('[type="hidden"]');
			}
			return this._fields;
		}
		,/**
		 * @public
		 * @returns {rm.customer.address.Field}
		 */
		getFieldCity: function() {return this.getField('city');}
		,/**
		 * @public
		 * @returns {rm.customer.address.Field}
		 */
		getFieldCountry: function() {return this.getField('country');}
		,/**
		 * @public
		 * @returns {rm.customer.address.Field}
		 */
		getFieldNameFirst: function() {return this.getField('firstname');}
		,/**
		 * @public
		 * @returns {rm.customer.address.Field}
		 */
		getFieldNameLast: function() {return this.getField('lastname');}
		,/**
		 * @public
		 * @returns {jQuery} HTMLElement
		 */
		getFieldNameMiddle: function() {return this.getField('middlename');}
		,/**
		 * @public
		 * @returns {rm.customer.address.Field}
		 */
		getFieldPostalCode: function() {return this.getField('postcode');}
		,/**
		 * @public
		 * @returns {rm.customer.address.Field}
		 */
		getFieldRegionSelect: function() {return this.getField('region_id');}
		,/**
		 * @public
		 * @returns {rm.customer.address.Field}
		 */
		getFieldRegionText: function() {return this.getField('region');}
		,/**
		 * @public
		 * @param {String} nameSuffix
		 * @returns {rm.customer.address.Field}
		 */
		getField: function(nameSuffix) {
			if (rm.undefined(this._field[nameSuffix])) {
				this._field[nameSuffix] =
					rm.customer.address.Field
						.construct({
							element:
								$(
									'[name="%fieldName%"]'
										.replace('%fieldName%', this.getFieldName(nameSuffix))
									,this.getElement()
								)
						})
				;
			}
			return this._field [nameSuffix];
		}
		,/**
		 * @type {rm.customer.address.Field[]}
		 */
		_field: []
		,/**
		 * @private
		 * @returns {jQuery} HTMLElement
		 */
		getElement: function() {
			return _config.element;
		}
		,/**
		 * @private
		 * @param {String} nameSuffix
		 * @returns {String}
		 */
		getFieldName: function(nameSuffix) {
			/** @type {String} */
			var result = null;
			if (0 === this.getType().length) {
				result = nameSuffix;
			}
			else {
				/** @type ?Array */
				var matches = nameSuffix.match(/(\w+)\[\]/);
				if (null === matches) {
					result =
						'%prefix%[%suffix%]'
							.replace('%prefix%', this.getType())
							.replace('%suffix%', nameSuffix)
					;
				}
				else {
					result =
						'%prefix%[%suffix%][]'
							.replace('%prefix%', this.getType())
							.replace('%suffix%', matches[1])
					;
				}
			}
			return result;
		}
		,/**
		 * @private
		 * @returns {String}
		 */
		getType: function() {
			return _config.type;
		}
	}; _this.init(); return _this; } };

})(jQuery);;(function($) {
	rm.namespace('rm.customer.address');
	//noinspection JSValidateTypes
	/**
	 * @param {jQuery} element
	 */
	rm.customer.address.Field = {construct: function(_config) {
		var _this = {
		init: function() {
			$(function() {
			});
		}
		,/**
		 * @public
		 * @returns {jQuery} HTMLElement
		 */
		getElement: function() {return _config.element;}
		,/**
		 * @public
		 * @returns {Boolean}
		 */
		isExist: function() {
			if (rm.undefined(this._exist)) {
				this._exist =(0 < this.getElement().length);
			}
			return this._exist;
		}
		,/**
		 * @public
		 * @returns {Boolean}
		 */
		isRequired: function() {
			if (rm.undefined(this._required)) {
				this._required = 0 < $('label.required', this.getElement().closest('.field')).length;
			}
			return this._required;
		}
		,/**
		 * @public
		 * @returns {String}
		 */
		getShortName: function() {
			if (rm.undefined(this._shortName)) {
				/** @type {String} */
				var name = this.getElement().attr('name');
				if (!name) {
					alert('Ошибка: поле без имени: ' + this.getElement().html());
				}
				this._shortName = name.replace(/\w+\[(\w+)\]/, '$1');
			}
			return this._shortName;
		}
	}; _this.init(); return _this; } };

})(jQuery);/**
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
}); })(jQuery);function show_hide_checkbox_fields(objName,condition)
{
	document.getElementById(objName).disabled = condition==1?"disabled":"";
}
;(function($) { $(function() {
	$(window).bind(rm.tweaks.ThemeDetector.initialized, function() {
		/** @type {jQuery} HTMLBodyElement */
		var $root = $('body.df-theme-8theme-mercado');
		if (0 < $root.length) {
			$('#nav ul.level0', $root).each(function(){
				/** @type {jQuery} HTMLUListElement */
				var $this = $(this);
				$this
					// Обратите внимание, что в названии класса нет опечатки,
					// просто так его назвал безграмотный разработчик темы Mercado
					.addClass('chield')
					.addClass('chield' + $this.children('li').length)
				;
			});
		}
	});
}); })(jQuery);;(function($) { $(function() {
	$(window).bind(rm.tweaks.ThemeDetector.initialized, function() {
		/** @type {jQuery} HTMLBodyElement */
		var $root = $('body.df-theme-argento');
		if (0 < $root.length) {
			rm.dom
				.replaceText(
					$('.price-label', $root)
					,'Special Price'
					,'Со скидкой:'
				)
				.replaceText(
					$('.highlight-popular .bottom-links a', $root)
					,'See all popular products »'
					,'другие ходовые товары »'
				)
				.replaceText(
					$('.bottom-links a', $root)
					,'See all new products »'
					,'все новинки »'
				)
				.replaceText(
					$('.brands-home .block-title span', $root)
					,'Featured Brands'
					,'Ведущие бренды'
				)
				.replaceText(
					$('.footer-social .label', $root)
					,'Join our community'
					,'мы в социальных сетях'
				)
				.replaceText(
					$('#product_tabs_review_tabbed a', $root)
					,"Product's Review"
					,'Отзывы'
				)
				.replaceText(
					$('#product_tabs_tags_tabbed a', $root)
					,"Product Tags"
					,'Метки'
				)
				.replaceText(
					$('#product_tabs_description_tabbed a', $root)
					,"Product Description"
					,'Описание'
				)
			;
		}
	});
}); })(jQuery);;(function($) { $(function() {
	if ('ru' === $('html').attr('lang')) {
		$(window).bind(rm.tweaks.ThemeDetector.initialized, function() {
			/** @type {jQuery} HTMLBodyElement */
			var $root = $('body.df-theme-cattheme-se105');
			if (0 < $root.length) {
				rm.dom
					.replaceText(
						$('#blocklogin .top-links > em', $root)
						,'or'
						,'или'
					)
					.replaceText(
						$('#sidenav-title', $root)
						,'All Categories'
						,'Наши товары'
					)
					.replaceText(
						$('#search_mini_form .form-search option[value=""]', $root)
						,'All Departments'
						,'все разделы'
					)
					.replaceText(
						$('.livechat a', $root)
						,'Live Chat'
						,'спросить'
					)
					.replaceText(
						$('.featured-cat-title h2', $root)
						,'Featured Categories'
						,'Обратите внимание'
					)
					.replaceText(
						$('.product-other-info span', $root)
						,'In '
						,''
					)
				;
			}
		});
	}
}); })(jQuery);;(function($) { $(function() {
	if ('ru' === $('html').attr('lang')) {
		(function() {
			/** @type {jQuery} HTMLDivElement */
			var $block = $('#remember-me-popup');
			if (0 < $block.length)  {
				$block
					.html(
						$block.html()
							.replace(
								'What\'s this?'
								,'Что это?'
							)
							.replace(
								'Checking "Remember Me" will let you access your shopping cart on this computer when you are logged out'
								,'Вам не придется вводить логин и пароль каждый раз'
							)
					)
				;
			}
		})();
	}
}); })(jQuery);;(function($) { $(function() {
	if ('ru' === $('html').attr('lang')) {
		$(window).bind(rm.tweaks.ThemeDetector.initialized, function() {
			/** @type {jQuery} HTMLBodyElement */
			var $root = $('body.df-theme-koolthememaster-caramel');
			if (0 < $root.length) {
				rm.dom
					.replaceText(
						$('li.brands a', $root)
						,'Brands'
						,'производители'
					)
					.replaceText(
						$('.quickllook', $root)
						,'Quick look'
						,'открыть'
					)
					.replaceText(
						$('.quick_view', $root)
						,'quick view'
						,'открыть'
					)
					.replaceText(
						$('a.prod-prev', $root)
						,'PREV'
						,'предыдущий'
					)
					.replaceText(
						$('a.prod-next', $root)
						,'NEXT'
						,'следующий'
					)
				;
			}
		});
	}
}); })(jQuery);;(function($) { $(function() {
	$(window).bind(rm.tweaks.ThemeDetector.initialized, function() {
		/** @type {jQuery} HTMLBodyElement */
		var $root = $('body.df-theme-magento-rwd');
		if (0 < $root.length) {
			if ($root.hasClass('checkout-cart-index')) {
				rm.dom
					.replaceText(
						$('.or', $root)
						,'-or-'
						,'-или-'
					)
				;
			}
			if ($root.hasClass('review-product-list')) {
				rm.dom
					.replaceHtmlPartial(
						$('.review-heading > h2 > span', $root)
						,'item(s)'
						,''
					)
				;
			}
		}
	});
}); })(jQuery);;(function($) { $(function() {
	$(window).bind(rm.tweaks.ThemeDetector.initialized, function() {
		/** @type {jQuery} HTMLBodyElement */
		var $root = $('body.df-theme-templatemonster-41220');
		if (0 < $root.length) {
			rm.dom
				.replaceText(
					$('.header .cart-title', $root)
					,'Cart:'
					,'товаров в корзине:'
				)
				.replaceText(
					$('.header .block-cart-header p.empty', $root)
					,'0 item(s)'
					,'0'
				)
				.replaceText(
					$('.header .block-cart-header .amount-2 strong', $root)
					,'1 item'
					,'1'
				)
				.replaceText(
					$('.cart-price strong', $root)
					,'Unit Price: '
					,'цена за штуку: '
				)
				.replaceText(
					$('.cart-price strong', $root)
					,'Subtotal: '
					,'стоимость: '
				)
				.replaceText(
					$('.cart-qty span', $root)
					,'Qty:'
					,'штук: '
				)
				.replaceText(
					$('.footer-col h4', $root)
					,'Newsletter'
					,'Рассылка'
				)
				.replaceHtmlPartial(
					$('.header .block-cart-header .amount-2 strong', $root)
					,' items'
					,''
				)
				.replaceHtmlPartial(
					$('.footer-col-content', $root)
					,'Follow us on:'
					,'Мы в социальных сетях:'
				)
			;
		}
	});
}); })(jQuery);;(function($) { $(function() {
	/**
	 * Наша задача: выделить в корзине товары-подарки.
	 */
	if (
			window.df
		&&
			window.rm.promo_gift
		&&
			window.rm.promo_gift.giftingQuoteItems
	) {
		var giftingQuoteItems = window.rm.promo_gift.giftingQuoteItems;
		if (giftingQuoteItems instanceof Array) {
			/**
			 * Итак, надо найти в корзине строки заказа giftingQuoteItems и выделить их.
			 */
			/** @type {jQuery} HTMLAnchorElement[] */
			var $quoteItems = $('#shopping-cart-table a.btn-remove');
			if (1 > $quoteItems.length) {
				$quoteItems = $('#shopping-cart-table a.btn-remove2');
			}
			$quoteItems.each(function(item) {
				var url = item.href;
				if ('string' === typeof(url)) {
					var quoteItemIdExp = /id\/(\d+)\//;
					var matches = url.match(quoteItemIdExp);
					if (matches instanceof Array) {
						if (1 < matches.length) {
							var quoteItemId = parseInt(matches [1]);
							if (!isNaN(quoteItemId)) {
								/**
								 * Нашли идентификатор текущего товара в корзине
								 */
								/**
								 *  Используем jQuery.inArray вместо Array.indexOf,
								 *  потому что Array.indexOf отсутствует в IE 8
								 *  http://www.w3schools.com/jsref/jsref_indexof_array.asp
								 */
								if (-1 < $.inArray(quoteItemId, giftingQuoteItems)) {
									/**
									 * Эта строка заказа — подарок. Выделяем её
									 */
									/** @type {jQuery} HTMLTableRowElement */
									var $tr = $(item).closest('tr');
									$tr.addClass('df-free-quote-item');
									/**
									 * Подписываем подарок
									 */
									/** @type {jQuery} HTMLElement[] */
									var $elements = $('.product-name', $tr);
									if (0 < elements.length) {
										/** @type {jQuery} HTMLDivElement */
										var $giftLabel = $('<div/>');
										$giftLabel.addClass('df-gift-label');
										$giftLabel
											.html(
												window.rm.promo_gift.giftingQuoteItemTitle
											)
										;
										$elements.first()
											.after($giftLabel)
										;
									}
								}
							}
						}
					}
				}
			});
		}
	}
}); })(jQuery);;(function($) { $(function() {
	/**
	 * Наша задача: выделить в корзине товары-подарки.
	 */
	if (
			window.df
		&&
			window.rm.promo_gift
		&&
			window.rm.promo_gift.giftingQuoteItems
	) {
		var eligibleProductIds = window.rm.promo_gift.eligibleProductIds;
		if (eligibleProductIds instanceof Array) {
			/**
			 * Итак, если покупатель смотрит карточку товара,
			 * * и данный товар он вправе получить в подарок(выполнил условия акции),
			 * * то надо внешне отразить сей факт на карточке товара
			 */
			/** @type {jQuery} HTMLElement */
			var $addToCartForm = $('#product_addtocart_form');
			if (0 < $addToCartForm.length) {
				/** @type {jQuery} HTMLInputElement[] */
				var $productIdInputs = $("input[name='product']", $addToCartForm);
				if (0 < $productIdInputs.length) {
					/** @type {Number} */
					var productId = parseInt($productIdInputs.first().val());
					/**
					 *  Используем jQuery.inArray вместо Array.indexOf,
					 *  потому что Array.indexOf отсутствует в IE 8
					 *  http://www.w3schools.com/jsref/jsref_indexof_array.asp
					 */
					if (-1 < $.inArray(productId, eligibleProductIds)) {
						$addToCartForm.closest('.product-view').addClass('df-gift-product');
						/** @type {String} */
						var labelText = window.rm.promo_gift.eligibleProductLabel;
						if ('string' === typeof(labelText)) {
							var $giftLabel = $('<div/>');
							$giftLabel.addClass('df-gift-label');
							$giftLabel
								.html(
									labelText
								)
							;
							/** @type {jQuery} HTMLElement[] */
							var $priceBoxes = $('.price-box', $addToCartForm);
							if (0 < $priceBoxes.length) {
								var $priceBox = $priceBoxes.first();
								$priceBox
									.after($giftLabel)
								;
							}
						}
					}
				}
			}
		}
	}
}); })(jQuery);;(function($) { $(function() {
	/**
	 * Наша задача:
	 * 		[*]	назначить чётным подаркам класс df-even
	 * 		[*] назначить нечётным подаркам класс df-odd
	 * 		[*] назначить первому подарку класс df-first
	 * 		[*] назначить последнему подарку класс df-last
	 */
	/** @type {Boolean} */
	var odd = true;
	/** @type {jQuery} HTMLLIElement[] */
	var $products = $('.df-promo-gift .df-gift-chooser .df-side li.df-product');
	$products.first().addClass('df-first');
	$products.last().addClass('df-last');
	$products.filter(':odd').addClass('df-odd');
	$products.filter(':even').addClass('df-even');
}); })(jQuery);(function($){$(function() {
	rm.namespace('rm.topMenu');
	if (rm.topMenu.activeNodePath) {
		/** @type {jQuery} HTMLElement */
		var $container = $('#nav');
		$.each(rm.topMenu.activeNodePath, function(index, value) {
			/** @type string */
			var selector = '.id-' + value;
			$(selector, $container).addClass('active');
		});
	}
});})(jQuery);;(function($) {
	rm.namespace('rm.tweaks');
	// rm.tweaks.options отсутствует на страницах формы ПД-4
	if (!rm.tweaks.options) {
		rm.tweaks.options = {};
	}
	//noinspection JSValidateTypes
	rm.tweaks.ThemeDetector = {
		initialized: 'rm.tweaks.ThemeDetector.initialized'
		,construct: function(_config) { var _this = {
			init: function() {
				$.each(rm.tweaks.dictionary, function(themeCssId, themeConditions) {
					/** @type {boolean} */
					var applicable;
					/**
					 * Ключ «package» должен всегда присутствовать как в правилах, так и в rm.tweaks.options.
					 * Значение ключа «package» применимого правило
					 * должно всегда совпадать со знчением этого ключа в rm.tweaks.options
					 */
					applicable = (themeConditions.package === rm.tweaks.options.package);
					if (applicable) {
						/**
						 * Ключ «skin» может и присутствовать, и отсутствовать
						 * как в правилах, так и в rm.tweaks.options.
						 */
						if (!rm.defined(themeConditions.skin)) {
							/**
							 * Если значение ключа «skin» отсутствует в правиле,
							 * то значение этого ключа в rm.tweaks.options должно либо отсутствовать,
							 * либо быть равно «default».
							 *
							 * Это условие нужно для того, чтобы, например,
							 * правило {package: 'default', 'theme': 'default'}
							 * не применялось к состоянию rm.tweaks.options
							 * {'package':'default', 'theme':'default', 'skin':'theme454'}
							 */
							applicable =
									!rm.defined(rm.tweaks.options.skin)
								||
									('default' === rm.tweaks.options.skin)
							;
						}
						else {
							/**
							 * Если значение ключа «skin» присутствует в правиле,
							 * то оно должно либо совпадать со значением этого ключа в rm.tweaks.options,
							 * либо быть массивом, содержащим в себе значение этого ключа в rm.tweaks.options.
							 */
							applicable =
									(themeConditions.skin === rm.tweaks.options.skin)
								||
									(
											$.isArray(themeConditions.skin)
										&&
											(-1 !== $.inArray(rm.tweaks.options.skin, themeConditions.skin))
									)
							;
						}
					}
					if (applicable) {
						if (rm.defined(themeConditions.theme)) {
							applicable =
									(themeConditions.theme === rm.tweaks.options.theme)
								||
									(
											$.isArray(themeConditions.theme)
										&&
											(-1 !== $.inArray(rm.tweaks.options.theme, themeConditions.theme))
									)
							;
						}
					}
					if (applicable) {
						$('body').addClass(themeCssId);
						return false;
					}
				});
				$(window).trigger({
					/** @type {String} */
					type: rm.tweaks.ThemeDetector.initialized
				});
			}
		}; _this.init(); return _this; }
	};
})(jQuery);;(function($) { $(function() {
	(function() {
		/** @type {jQuery} HTMLAnchorElement */
		var $reviewLinks = $('.product-view .ratings .rating-links a');
		$reviewLinks.first().addClass('.first-child');
		$reviewLinks.last().addClass('.last-child');
	})();
	rm.tweaks.ThemeDetector.construct({});
	(function() {
		/** @type {jQuery} HTMLUListElement */
		var $links = $(".header-container .quick-access .links");
		$("a[href*='persistent/index/unsetCookie']", $links)
			.addClass('rm-preserve-case')
		;
		/**
		 * Если администратор включил опцию
		 * «Заменить «личный кабинет» («my account») на имя авторизованного клиента?»,
		 * то надо сохранить регистр букв у данной ссылки,
		 * потому что там будет написано имя клиента
		 */
		/** @type {jQuery} HTMLAnchorElement */
		var $accountLink = $("a[href$='customer/account/']", $links);
		/** @type string[] */
		var standardAccountTitles = ['личный кабинет', 'my account'];
		/**
		 *  Используем jQuery.inArray вместо Array.indexOf,
		 *  потому что Array.indexOf отсутствует в IE 8
		 *  http://www.w3schools.com/jsref/jsref_indexof_array.asp
		 */
		if (-1 === $.inArray($accountLink.text().toLowerCase(), standardAccountTitles)) {
			$accountLink.addClass('rm-preserve-case');
		}
	})();
}); })(jQuery);;(function($) {rm.namespace('rm.tweaks'); rm.tweaks.dictionary = {
	'df-theme-8theme-blanco': {'package': 'default', 'skin': 'blanco'}
	,'df-theme-8theme-gadget': {'package': 'default', 'skin': 'gadget'}
	,'df-theme-8theme-mercado': {'package': 'mercado'}
	,'df-theme-argento': {'package': 'argento'}
	,'df-theme-cattheme-se105': {'package': 'default', 'skin': 'se105'}
	/**
	 * EM Marketplace
	 * http://www.emthemes.com/premium-magento-themes/em-marketplace.html
	 * http://magento-forum.ru/forum/312/
	 */
	,'df-theme-em-marketplace': {'package': 'default', 'skin': 'em0067'}
	/**
	 * ThemeForest Gala TitanShop
	 * http://themeforest.net/item/responsive-magento-theme-gala-titanshop/8202636
	 * http://magento-forum.ru/forum/352/
	 */
	,'df-theme-gala-titanshop': {'package': 'default', 'theme': 'galatitanshop'}
	/**
	 * ThemeForest Infortis Fortis
	 * http://themeforest.net/item/fortis-responsive-magento-theme/1744309
	 * http://magento-forum.ru/forum/350/
	 */
	,'df-theme-infortis-fortis': {'package': 'fortis'}
	,'df-theme-infortis-ultimo': {'package': 'ultimo'}
	,'df-theme-koolthememaster-caramel': {'package': 'default', 'skin': 'caramel'}
	,'df-theme-magento-default': {'package': 'default', 'theme': 'default'}
	,'df-theme-magento-enterprise': {'package': 'enterprise'}
	,'df-theme-magento-modern': {'package': 'default', 'theme': 'modern'}
	,'df-theme-magento-rwd': {'package': 'rwd'}
	,'df-theme-smartwave-porto': {'package': 'smartwave', 'theme': 'porto', skin: 'porto'}
	,'df-theme-sns-xsport': {'package': 'default', 'theme': 'sns_xsport'}
	,'df-theme-templatemela-beauty': {'package': 'default', 'skin': 'MAG080119'}
	/**
	 * TemplateMela Classy Shop (MAG090171)
	 * http://www.templatemela.com/classyshop-magento-theme.html
	 * http://themeforest.net/item/classy-shop-magento-responsive-template/519426
	 * http://magento-forum.ru/forum/342/
	 */
	,'df-theme-templatemela-classy-shop': {'package': 'default', 'skin': 'MAG090171'}
	/**
	 * TemplateMela (ThemeForest) Fancy Shop
	 * http://themeforest.net/item/fancy-shop-magento-template/3087093
	 * http://magento-forum.ru/forum/316/
	 */
	,'df-theme-templatemela-fancyshop':
		{'package': 'default', 'skin': ['fancyshop_brown', 'fancyshop_blue', 'forest_fancyshop']}
	/**
	 * TemplateMela Mega Shop (MAG090172)
	 * http://www.templatemela.com/mega-shop-magento-template.html
	 * http://themeforest.net/item/mega-shop-magento-responsive-template/6608610
	 * http://magento-forum.ru/forum/363/
	 */
	,'df-theme-templatemela-mega-shop': {'package': 'default', 'skin': 'MAG090172'}
	/**
	 * TemplateMela Minimal Multi Purpose (MAG090180)
	 * http://www.templatemela.com/minimal-multi-purpose-magento-theme.html
	 * http://magento-forum.ru/forum/341/
	 */
	,'df-theme-templatemela-minimal-multi-purpose': {'package': 'default', 'skin': 'MAG090180'}
	,'df-theme-templatemonster-34402': {'package': 'default', 'skin': 'theme043k'}
	,'df-theme-templatemonster-37419': {'package': 'default', 'skin': 'theme264'}
	,'df-theme-templatemonster-41220': {'package': 'default', 'skin': 'theme411'}
	/**
	 * TemplateMonster #43373
	 * http://www.templatemela.com/minimal-multi-purpose-magento-theme.html
	 * http://magento-forum.ru/forum/364/
	 * http://www.templatemonster.com/ru/magento-themes-type/43373.html
	 */
	,'df-theme-templatemonster-43373': {'package': 'default', 'skin': 'theme454'}
	,'df-theme-templatemonster-43442': {'package': 'default', 'skin': 'theme464'}
	,'df-theme-templatemonster-45035': {'package': 'default', 'skin': 'theme500'}
	/**
	 * TemplateMonster #49198 («Men's Underwear»)
	 * http://www.templatemonster.com/magento-themes/49198.html
	 * http://magento-forum.ru/forum/340/
	 */
	,'df-theme-templatemonster-49198': {'package': 'default', 'skin': 'theme611'}
	/**
	 * TemplateMonster #53174 («Kids Fashion»)
	 * http://www.templatemonster.com/magento-themes/53174.html
	 * http://magento-forum.ru/forum/372/
	 */
	,'df-theme-templatemonster-53174': {'package': 'default', 'skin': 'theme690'}
	,'df-theme-tt-theme069': {'package': 'tt', 'skin': 'theme069'}
	/**
	 * Ves Super Store (ThemeForest 8002349)
	 * http://themeforest.net/item/ves-super-store-responsive-magento-theme-/8002349?ref=dfediuk
	 * http://demoleotheme.com/superstore/
	 * http://magento-forum.ru/forum/370/
	 */
	,'df-theme-ves-super-store': {'package': 'default', 'skin': 'ves_superstore'}
};})(jQuery);;(function($) {
	rm.namespace('rm.vk');
	//noinspection JSValidateTypes
	rm.vk.Widget = {
		construct: function(_config) { var _this = {
			init: function() {
				/** @type {jQuery} HTMLElement */
				var $parent = $(this.getParentSelector());
				if (0 < $parent.length) {
					$parent.append($('<div></div>').attr('id', this.getContainerId()));
					if (rm.defined(window.VK)) {
						_this.createWidget();
					}
					else {
						$.getScript('http://userapi.com/js/api/openapi.js', function() {
							VK.init({
								apiId: _this.getApplicationId()
								,onlyWidgets: true
							});
							_this.createWidget();
						});
					}
				}
			}
			,/**
			 * @private
			 * @returns {rm.vk.Widget }
			 */
			createWidget: function() {
				/**
				 *  Надо вызвать конструктор типа VK.Widgets.Comments
				 *  по его текстовой записи: "VK.Widgets.Comments"
				 */
				var dotParser = function(object, index) {
					var result = object[index];
					if (rm.undefined(result)) {
						console.log('Index %index is undefined'.replace('%index', index));
					}
					return result;
				};
				var constructor =
					/**
					 *  Не используем Array.prototype.reduce из JavaScript 1.8,
					 *  потому что в Magento 1.4.1.0 этот метод конфликтует
					 *  с одноимённым методом библиотеки Prototype.
					 */
					rm.reduce(
						this.getObjectName().split('.')
						,dotParser
						,window
					)
				;
				constructor.call(window, this.getContainerId(), this.getWidgetSettings());
				return this;
			}
			,/**
			 * @private
			 * @returns {Number}
			 */
			getApplicationId: function() {return _config.applicationId;}
			,/**
			 * @private
			 * @returns {String}
			 */
			getContainerId: function() {return _config.containerId;}
			,/**
			 * @private
			 * @returns {String}
			 */
			getObjectName: function() {return _config.objectName;}
			,/**
			 * @private
			 * @returns {String}
			 */
			getParentSelector: function() {return _config.parentSelector;}
			,/**
			 * @private
			 * @returns {Object}
			 */
			getWidgetSettings: function() {return _config.widgetSettings;}
		}; _this.init(); return _this; }
	};
})(jQuery);;(function($) { $(function() {
	rm.namespace('rm.vk.comments');
	if (rm.vk.comments.enabled) {
		rm.vk.Widget.construct({
			applicationId: rm.vk.comments.applicationId
			,containerId: 'vk_comments'
			,objectName: 'VK.Widgets.Comments'
			,parentSelector: '.product-view'
			,widgetSettings: rm.vk.comments.settings
		});
	}
	rm.namespace('rm.vk.like');
	if (rm.vk.like.enabled) {
		rm.vk.Widget.construct({
			applicationId: rm.vk.like.applicationId
			,containerId: 'vk_like'
			,objectName: 'VK.Widgets.Like'
			,parentSelector: '.product-shop'
			,widgetSettings: rm.vk.like.settings
		});
	}
	rm.namespace('rm.vk.groups');
	if (rm.vk.groups.enabled) {
		rm.vk.widget.Groups.construct({
			applicationId: rm.vk.groups.applicationId
			,containerId: 'vk_groups'
			,objectName: 'VK.Widgets.Group'
			,widgetSettings: rm.vk.groups.settings
		});
	}
}); })(jQuery);;(function($) {
	rm.namespace('rm.vk.widget');
	//noinspection JSValidateTypes
	rm.vk.widget.Groups = {
		construct: function(_config) { var _this = {
			init: function() {
				if (0 < this.getParent().length) {
					/** @type {jQuery} HTMLElement[] */
					var $blocks = $('.block', this.getParent());
					/** @type {Number} */
					var childrenCount = $blocks.length;
					/** @type {Number} */
					var insertionIndex =
						Math.max(
							0
							,Math.min(
								childrenCount - 1
								,/**
								 * Вычитает единицу, * потому что в административном интерфейсе
								 * нумерация начинается с 1
								 */
								rm.vk.groups.verticalOrdering - 1
							)
						)
					;
					/** @type {jQuery} HTMLElement */
					var $widget =
						$('<div></div>')
							.attr('id', this.getContainerId())
							.addClass('block')
					;
					if (0 === insertionIndex) {
						this.getParent().prepend($widget);
					}
					else {
						$($blocks.get(insertionIndex)).before($widget);
					}
					if (rm.defined(window.VK)) {
						_this.createWidget();
					}
					else {
						$
							.getScript(
								'http://userapi.com/js/api/openapi.js'
								,function() {
									_this.createWidget();
								}
							)
						;
					}
				}
			}
			,/**
			 * @private
			 * @returns {rm.vk.Widget }
			 */
			createWidget: function() {
				/**
				 *  Надо вызвать конструктор типа VK.Widgets.Comments
				 *  по его текстовой записи: "VK.Widgets.Comments"
				 */
				var dotParser = function(object, index) {
					var result = object[index];
					if (rm.undefined(result)) {
						console.log('Index %index is undefined'.replace('%index', index));
					}
					return result;
				};
				var constructor =
					/**
					 *  Не используем Array.prototype.reduce из JavaScript 1.8, *  потому что в Magento 1.4.1.0 этот метод конфликтует
					 *  с одноимённым методом библиотеки Prototype.
					 */
					rm.reduce(
						this.getObjectName().split('.')
						,dotParser
						,window
					)
				;
				constructor
					.call(
						window
						,this.getContainerId()
						,this.getWidgetSettings()
						,_this.getApplicationId()
					)
				;
				return this;
			}
			,/**
			 * @private
			 * @returns {Number}
			 */
			getApplicationId: function() {
				return _config.applicationId;
			}
			,/**
			 * @private
			 * @returns {String}
			 */
			getContainerId: function() {
				return _config.containerId;
			}
			,/**
			 * @private
			 * @returns {String}
			 */
			getObjectName: function() {
				return _config.objectName;
			}
			,/**
			 * @private
			 * @returns {jQuery} HTMLElement
			 */
			getParent: function() {
				if (rm.undefined(this._parent)) {
					/** @type {String} */
					var selector =
							('left' === rm.vk.groups.position)
						?
							'.col-left'
						:
							'.col-right'
					;
					/**
					 * @type {jQuery} HTMLElement
					 */
					this._parent = $(selector);
					if (0 === this._parent.length) {
						if (
								0
							<
								(
										$('.col2-right-layout').length
									+
										$('.col2-left-layout').length
								)
						) {
							this._parent = $('.col-main');
						}
					}
				}
				return this._parent;
			}
			,/**
			 * @private
			 * @returns {Object}
			 */
			getWidgetSettings: function() {
				return _config.widgetSettings;
			}
		}; _this.init(); return _this; }
	};

})(jQuery);rm.namespace('rm.checkout');
(function($) { $(function() {
	(function() {
		/** @type {Object} */
		var cookies = {
			'#opc-billing .df-yandex-market-address': 'rm.yandex_market.address.billing'
			,'#opc-shipping .df-yandex-market-address': 'rm.yandex_market.address.shipping'
		};
		/** @type {Object} */
		var options = {expires: 1, path: '/'};
		$.each(cookies, function(selector, cookieToSet) {
			$(selector).click(function() {
				$.cookie(cookieToSet, 1, options);
				$.each(cookies, function(selector, cookie) {
					if (cookie !== cookieToSet) {
						$.removeCookie(cookie, options);
					}
				});
			});
		});
	})();
}); })(jQuery);/**
 * Обратите внимание, что нужно писать именно rm.defined(window.RegionUpdater),
 * а не rm.defined(RegionUpdater),
 * потому что второй вариант приводит к сбою в Firefox:
 * «ReferenceError: RegionUpdater is not defined».
 */
if (rm.defined(window.RegionUpdater)) {
	RegionUpdater.prototype.update =
		function() {
			if (this.regions[this.countryEl.value]) {
				var i, option, region, def;
				var defaultRegionId = this.regionSelectEl.getAttribute('defaultValue');
				if (this.regionTextEl) {
					def = this.regionTextEl.value.toLowerCase();
					this.regionTextEl.value = '';
				}
				if (!def) {
					def = defaultRegionId;
				}
				this.regionSelectEl.options.length = 1;
				for (regionId in this.regions[this.countryEl.value]) {
					region = this.regions[this.countryEl.value][regionId];
					// НАЧАЛО ЗАПЛАТКИ
					/**
					 * 2014-10-23
					 * Скрипт RegionUpdater.js был перекрыт 3 года назад, 2011-11-05,
					 * причём к заплатке в системе контроля версий был дан такой комментарий:
					 * «Исправление упорядочивания субъектов РФ для Webkit».
					 *
					 * Я уже сейчас не помню, в чём там проблема была с упорядочиванием регионов,
					 * но заплатка оставалась все 3 года и останется сейчас.
					 *
					 * Обратите внимание, что идентификаторы добавлены в массив регионов
					 * другой заплаткой, в методах
					 * @see Df_Directory_Helper_Data::getRegionJson()
					 * @see Df_Directory_Helper_Data::_getRegions()
					 */
					regionId = region.id;
					if (rm.undefined(regionId)) {
						continue;
					}
					// КОНЕЦ ЗАПЛАТКИ
					option = document.createElement('OPTION');
					option.value = regionId;
					option.text = region.name;
					if (this.regionSelectEl.options.add) {
						this.regionSelectEl.options.add(option);
					} else {
						this.regionSelectEl.appendChild(option);
					}
					if (
							(regionId == defaultRegionId)
						||
							(region.name.toLowerCase()==def)
						||
							(region.code.toLowerCase()==def)
					) {
						this.regionSelectEl.value = regionId;
					}
				}
				if (this.disableAction=='hide') {
					if (this.regionTextEl) {
						this.regionTextEl.style.display = 'none';
					}
					this.regionSelectEl.style.display = '';
				} else if (this.disableAction=='disable') {
					if (this.regionTextEl) {
						this.regionTextEl.disabled = true;
					}
					this.regionSelectEl.disabled = false;
				}
				this.setMarkDisplay(this.regionSelectEl, true);
			} else {
				if (this.disableAction=='hide') {
					if (this.regionTextEl) {
						this.regionTextEl.style.display = '';
					}
					this.regionSelectEl.style.display = 'none';
					Validation.reset(this.regionSelectEl);
				} else if (this.disableAction=='disable') {
					if (this.regionTextEl) {
						this.regionTextEl.disabled = false;
					}
					this.regionSelectEl.disabled = true;
				} else if (this.disableAction=='nullify') {
					this.regionSelectEl.options.length = 1;
					this.regionSelectEl.value = '';
					this.regionSelectEl.selectedIndex = 0;
					this.lastCountryId = '';
				}
				this.setMarkDisplay(this.regionSelectEl, false);
			}
			// Make Zip and its label required/optional
			var zipUpdater = new ZipUpdater(this.countryEl.value, this.zipEl);
			zipUpdater.update();
		}
	;
}
;(function($) {
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
								шаблон всё равно добавляет на страницу мусорную разметку
								<form action="" id="checkout-agreements" onsubmit="return false;">
								<ol class="checkout-agreements">
								</ol>
								</form>
								Вот её надо удалить.

								2016-06-28
								Предыдущий комментарий не совсем верен.
								Смотрите реализацию метода Mage_Checkout_Block_Agreements::getAgreements()
								Она идентична в самой свежей версии 1.9.2.4 и в старой версии 1.6.0.0.
								Так вот, этот метод вчсё-таки возвращает массив: в том случае,
								когда «Правила покупки» отклчены администратором магазина:
									if (!Mage::getStoreConfigFlag('checkout/options/enable_agreements')) {
										$agreements = array();
									}
								https://github.com/OpenMage/magento-mirror/blob/1.9.2.4/app/code/core/Mage/Checkout/Block/Agreements.php#L31-L33
							 */
							$('#checkout-agreements').remove();
							/** @type jQuery {HTMLFormElement} */
							var $agreementsForm =
								$('<form/>')
									.attr({
										id: 'checkout-agreements'
										,action: ''
										,onsubmit: 'return false;'
									})
									.append(this.getElement())
							;
							$('#checkout-review-submit').prepend($agreementsForm);
							/**
							 * 2016-06-30
							 * Очень важно!
							 * Странно, как раньше без этого работала
							 * функциональность коментария при размещении заказа.
							 * Когда покупатель нажимает кнопку «Подтвердить заказ»,
							 * то метод JavaScript Review.save() проверяет наличие формы
							 * с идентификатором «checkout-agreements», и если эта форма присутствует на странице,
							 * то отправляет её поля на сервер наравне с полями платёжной формы:
								 var params = Form.serialize(payment.form);
								 if (this.agreementsForm) {
									params += '&'+Form.serialize(this.agreementsForm);
								 }
							 * https://github.com/OpenMage/magento-mirror/blob/1.9.2.4/skin/frontend/base/default/js/opcheckout.js#L922-L924
							 * Если же форма «checkout-agreements» отсутствует на странице,
							 * то у нас пропадает возможность отправки на сервер комментария к заказу.
							 */
							review.agreementsForm = $agreementsForm.get(0);
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

})(jQuery);
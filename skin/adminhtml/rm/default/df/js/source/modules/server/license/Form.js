;(function($) {
	'use strict';
	rm.namespace('rm.server.license');
	//noinspection JSValidateTypes
	rm.server.license.Form = {
		construct: function(_config) { var _this = {
			init: function() {
				if (0 < this.getElement().length) {
					this
						.subscribeChange(
							this.getInputLifeLong()
							,
							this.onChangeLifeLong
						)
					;
					this
						.subscribeChange(
							this.getInputPromo()
							,
							this.onChangePromo
						)
					;
				}
			}
			,
			/**
			 * @private
			 * @returns {jQuery} HTMLElement
			 */
			getElement: function() {
				return _config.element;
			}
			,
			/**
			 * @private
			 * @returns {jQuery} HTMLInputElement
			 */
			getInputLifeLong:
				function() {
					if (rm.undefined(this._inputLifeLong)) {
						this._inputLifeLong = $('#lifelong', this.getElement());
					}
					return this._inputLifeLong;
				}
			,
			/**
			 * @private
			 * @returns {jQuery} HTMLInputElement
			 */
			getInputPromo:
				function() {
					if (rm.undefined(this._inputPromo)) {
						this._inputPromo = $('#promo', this.getElement());
					}
					return this._inputPromo;
				}
			,
			/**
			 * @private
			 * @returns {jQuery} HTMLInputElement
			 */
			getRowDateExpiration:
				function() {
					if (rm.undefined(this._rowDateExpiration)) {
						this._rowDateExpiration = $('#date_expiration', this.getElement()).closest('tr');
					}
					return this._rowDateExpiration;
				}
			,
			/**
			 * @private
			 * @returns {jQuery} HTMLInputElement
			 */
			getRowPaymentAmount:
				function() {
					if (rm.undefined(this._rowPaymenAmount)) {
						this._rowPaymenAmount = $('#payment_amount', this.getElement()).closest('tr');
					}
					return this._rowPaymenAmount;
				}
			,
			/**
			 * @private
			 * @returns {jQuery} HTMLInputElement
			 */
			getRowPaymentCurrency:
				function() {
					if (rm.undefined(this._rowPaymentCurrency)) {
						this._rowPaymentCurrency = $('#payment_currency', this.getElement()).closest('tr');
					}
					return this._rowPaymentCurrency;
				}
			,
			/**
			 * @private
			 * @returns {jQuery} HTMLInputElement
			 */
			getRowPaymentDate:
				function() {
					if (rm.undefined(this._rowPaymentDate)) {
						this._rowPaymentDate = $('#payment_date', this.getElement()).closest('tr');
					}
					return this._rowPaymentDate;
				}
			,
			/**
			 * @private
			 * @returns {jQuery} HTMLInputElement
			 */
			getRowPaymentMethod:
				function() {
					if (rm.undefined(this._rowPaymenMethod)) {
						this._rowPaymenMethod = $('#payment_method', this.getElement()).closest('tr');
					}
					return this._rowPaymenMethod;
				}
			,
			/**
			 * @private
			 * @returns {jQuery} HTMLInputElement
			 */
			getRowPromo:
				function() {
					if (rm.undefined(this._rowPromo)) {
						this._rowPromo = this.getInputPromo().closest('tr');
					}
					return this._rowPromo;
				}
			,
			/**
			 * @private
			 * @returns {rm.server.license.Form}
			 */
			onChangeLifeLong: function() {
				/** @type {Boolean} */
				var isLifeLong =(1 === parseInt(this.getInputLifeLong().val()));
				/** @type {Boolean} */
				var isPromo =(1 === parseInt(this.getInputPromo().val()));
				this.getRowDateExpiration()
					.toggle(
						!isLifeLong
					)
				;
				this.getRowPaymentAmount()
					.toggle(
						isLifeLong && !isPromo
					)
				;
				this.getRowPaymentCurrency()
					.toggle(
						isLifeLong && !isPromo
					)
				;
				this.getRowPaymentDate()
					.toggle(
						isLifeLong && !isPromo
					)
				;
				this.getRowPaymentMethod()
					.toggle(
						isLifeLong && !isPromo
					)
				;
				/**
				 * Этот код должен выполняться до аналогичного кода полей оплаты,
				 * потому что этот код влияет на видимость полей оплаты
				 */
				this.getRowPromo()
					.toggle(
						isLifeLong
					)
				;
				return this;
			}
			,
			/**
			 * @private
			 * @returns {rm.server.license.Form}
			 */
			onChangePromo: function() {
				/** @type {Boolean} */
				var isLifeLong =(1 === parseInt(this.getInputLifeLong().val()));
				/** @type {Boolean} */
				var isPromo =(1 === parseInt(this.getInputPromo().val()));
				this.getRowPaymentAmount()
					.toggle(
						isLifeLong && !isPromo
					)
				;
				this.getRowPaymentCurrency()
					.toggle(
						isLifeLong && !isPromo
					)
				;
				this.getRowPaymentDate()
					.toggle(
						isLifeLong && !isPromo
					)
				;
				this.getRowPaymentMethod()
					.toggle(
						isLifeLong && !isPromo
					)
				;
				return this;
			}
			,
			/**
			 * @private
			 * @param {jQuery} HTMLInputElement
			 * @param {function}
			 * @returns {rm.server.license.Form}
			 */
			subscribeChange: function($input, $function) {
				$input.change(function() {
					$function.call(_this)
				});
				$function.call(this);
				return this;
			}
		}; _this.init(); return _this; }
	};





})(jQuery);
;(function($) {
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

})(jQuery);
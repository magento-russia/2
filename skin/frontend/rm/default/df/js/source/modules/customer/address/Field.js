;(function($) {
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

})(jQuery);
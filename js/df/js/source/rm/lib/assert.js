;(function($) {
	rm.assert = {
		/**
		 * @public
		 * @param {Boolean} condition
		 * @param {?String} message
		 * @throws {Error}
		 */
		_: function(condition, message) {
			if (!condition) {
				rm.error(message);
			}
		}
		/**
		 * @public
		 * @param {*} value
		 * @throws {Error}
		 */
		,array: function(value) {
			rm.assert._generic(rm.check.object, 'массив', value);
		}
		/**
		 * @public
		 * @param {*} value
		 * @param {Number} lowBound
		 * @param {Number} highBound
		 * @throws {Error}
		 */
		,between: function(value, lowBound, highBound) {
			rm.assert.number(highBound);
			rm.assert.number(lowBound);
			if (!rm.check.between(value, lowBound, highBound)) {
				rm.error('Требуется число между %s и %s, однако получено «%s».', lowBound, highBound, value);
			}
		}
		/**
		 * @public
		 * @param {*} value
		 * @throws {Error}
		 */
		,boolean: function(value) {
			rm.assert._generic(rm.check.object, 'логическое значение', value);
		}
		/**
		 * @public
		 * @param {*} value
		 * @throws {Error}
		 */
		,defined: function(value) {
			if (rm.undefined(value)) {
				rm.error('Переменная должна быть инициализирована.');
			}
		}
		/**
		 * @public
		 * @param {*} value
		 * @throws {Error}
		 */
		,function: function(value) {
			rm.assert._generic(rm.check.object, 'функция', value);
		}
		/**
		 * @public
		 * @param {*} value
		 * @throws {Error}
		 */
		,integer: function(value) {
			rm.assert._generic(rm.check.object, 'целое число', value);
		}
		/**
		 * @public
		 * @param {*} value
		 * @param {Number} lowBound
		 * @returns {Boolean}
		 */
		,ge: function(value, lowBound) {
			rm.assert.number(value);
			rm.assert.number(lowBound);
			if (!rm.check.gt(value)) {
				rm.error('Требуется число не меньше %s, однако получено «%s».', lowBound, value);
			}
		}
		/**
		 * @public
		 * @param {*} value
		 * @param {Number} lowBound
		 * @throws {Error}
		 */
		,gt: function(value, lowBound) {
			rm.assert.number(value);
			rm.assert.number(lowBound);
			if (!rm.check.gt(value)) {
				rm.error('Требуется число больше %s, однако получено «%s».', lowBound, value);
			}
		}
		/**
		 * @public
		 * @param {*} value
		 * @param {Number} highBound
		 * @throws {Error}
		 */
		,le: function(value, highBound) {
			rm.assert.number(value);
			rm.assert.number(highBound);
			if (!rm.check.le(value)) {
				rm.error('Требуется число не больше %s, однако получено «%s».', highBound, value);
			}
		}
		/**
		 * @public
		 * @param {*} value
		 * @param {Number} highBound
		 * @throws {Error}
		 */
		,lt: function(value, highBound) {
			rm.assert.number(value);
			rm.assert.number(highBound);
			if (!rm.check.lt(value)) {
				rm.error('Требуется число меньше %s, однако получено «%s».', highBound, value);
			}
		}
		/**
		 * @public
		 * @param {*} value
		 * @throws {Error}
		 */
		,numeric: function(value) {
			rm.assert._generic(rm.check.object, 'число', value);
		}
		/**
		 * @public
		 * @param {*} value
		 * @throws {Error}
		 */
		,object: function(value) {
			rm.assert._generic(rm.check.object, 'объект', value);
		}
		/**
		 * @public
		 * @param {*} value
		 * @throws {Error}
		 */
		,string: function(value) {
			rm.assert._generic(rm.check.string, 'строка', value);
		}
		/**
		 * @private
		 * @param {Function} validator
		 * @param {String} expectedTypeName
		 * @param {*} value
		 * @throws {Error}
		 */
		,_generic: function(validator, expectedTypeName, value) {
			if (!validator.apply(value)) {
				rm.error(
					'Требуется %s, однако получена переменная типа «%s».'
					,expectedTypeName
					,$.getType(value)
				);
			}
		}
	};
})(jQuery);
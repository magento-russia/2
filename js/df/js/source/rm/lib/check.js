;(function($) {
	rm.check = {
		/**
		 * @function
		 * @param {*} value
		 * @returns {Boolean}
		 */
		array: function(value) {
			return $.isArray(value);
		}
		/**
		 * @function
		 * @param {*} value
		 * @param {Number} lowBound
		 * @param {Number} highBound
		 * @returns {Boolean}
		 */
		,between: function(value, lowBound, highBound) {
			return rm.check.numeric(value) && (value >= lowBound) && (value <= highBound);
		}
		/**
		 * @function
		 * @param {*} value
		 * @returns {Boolean}
		 */
		,boolean: function(value) {
			return (true === value) || (false === value);
		}
		/**
		 * @function
		 * @param {*} value
		 * @returns {Boolean}
		 */
		,function: function(value) {
			return $.isFunction(value);
		}
		/**
		 * @function
		 * @param {*} value
		 * @returns {Boolean}
		 */
		,integer: function(value) {
			return rm.check.numeric(value) && (value === Math.floor(value));
		}
		/**
		 * @function
		 * @param {*} value
		 * @param {Number} lowBound
		 * @returns {Boolean}
		 */
		,ge: function(value, lowBound) {
			return rm.check.numeric(value) && (value >= lowBound);
		}
		/**
		 * @function
		 * @param {*} value
		 * @param {Number} lowBound
		 * @returns {Boolean}
		 */
		,gt: function(value, lowBound) {
			return rm.check.numeric(value) && (value > lowBound);
		}
		/**
		 * @function
		 * @param {*} value
		 * @param {Number} lowBound
		 * @returns {Boolean}
		 */
		,le: function(value, lowBound) {
			return rm.check.numeric(value) && (value <= lowBound);
		}
		/**
		 * @function
		 * @param {*} value
		 * @param {Number} lowBound
		 * @returns {Boolean}
		 */
		,lt: function(value, lowBound) {
			return rm.check.numeric(value) && (value < lowBound);
		}
		/**
		 * @function
		 * @param {*} value
		 * @returns {Boolean}
		 */
		,numeric: function(value) {
			return $.isNumeric(value);
		}
		/**
		 * @function
		 * @param {*} value
		 * @returns {Boolean}
		 */
		,object: function(value) {
			return $.isPlainObject(value);
		}
		/**
		 * @function
		 * @param {*} value
		 * @returns {Boolean}
		 */
		,string: function(value) {
			return('string' === typeof(value));
		}
	};
})(jQuery);
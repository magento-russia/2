;(function($) {
	rm.format = {
		date: {
			russianLong:
				/**
				 * Преобразовывает объект-дату
				 * в строку-дату в российском формате (дд.мм.ГГГГ)
				 * @param {Date} date
				 * @returns {String}
				 */
				function(date) {
					/** @type {String} */
					var result =
						[
							rm.format.pad(date.getDate(), 2)
							,rm.format.pad(1 + date.getMonth(), 2)
							,date.getFullYear()

						].join('.')
					;
					return result;
				}
		}
		/**
		 * Форматирует число number как строку из length цифр,
		 * добавляя, при необходимости, нули в начале строки
		 * @param {Number} number
		 * @param {Number} length
		 * @returns {String}
		 */
		,pad: function(number, length) {
			var result = number.toString();
			if (result.length < length) {
				result =
					('0000000000' + result)
						.slice(-length)
				;
			}

			return result;
		}
	};
})(jQuery);
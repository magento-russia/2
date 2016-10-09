;(function($) {rm.string = {
	/**
	 * 2016-08-07
	 * Замещает параметры аналогично моей функции PHP df_var()
	 * https://github.com/mage2pro/core/blob/1.5.23/Core/lib/text.php?ts=4#L913-L929
	 *
	 * 2016-08-08
	 * Lodash содержит функцию template: https://lodash.com/docs#template
	 * Я не использую её, потому что она слишком навороченная для моего случая.
	 *
	 * JSFiddle: https://jsfiddle.net/dfediuk/uxusbhes/1/
	 *
	 * @param {String} result
	 * @param {Object|String|Array=} params [optional]
	 * @returns {String}
	 */
	t: function(result, params) {
		params = rm.arg(params, {});
		/**
		 * 2016-08-08
		 * Simple — не массив и не объект.
		 * @type {Boolean}
		 */
		var paramsIsSimple = !rm.isObject(params);
		// 2016-08-07
		// Поддерживаем сценарий df.t('One-off Payment: %s.');
		if (paramsIsSimple && 2 === arguments.length) {
			result = result.replace('%s', params).replace('{0}', params);
		}
		else {
			if (paramsIsSimple) {
				/**
				 * 2016-08-08
				 * Почему-то прямой вызов arguments.slice(1) приводит к сбою:
				 * «arguments.slice is not a function».
				 * Решение взял отсюда: http://stackoverflow.com/a/960870
				 */
				params = Array.prototype.slice.call(arguments, 1);
			}
			/**
			 * 2016-08-08
			 * params теперь может быть как объектом, так и строкой: алгоритм един.
			 * http://api.jquery.com/jquery.each/
			 */
			$.each(params, function(name, value) {
				result = result.replace('{' + name + '}', value);
			});
		}
		return result;
	}
};})(jQuery);
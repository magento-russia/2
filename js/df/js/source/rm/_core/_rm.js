/**
 * Обратите внимание,
 * что имя файла намеренно начинается с символа подчёркивания.
 * Благодаря этому, сборщик (компилятор) помещает функции этого файла до других
 * (он размещает их в алфавитном порядке).
 */
/**
 * Обратите внимание, что без начального «;»
 * стандартное слияние файлов JavaScript в Magento создаёт сбойный файл
 */
;(function($) {
	$.extend(true, window,{
		rm: {
			/**
			 * @param value
			 * @returns {Boolean}
			 */
			defined: function(value) {
				return ('undefined' !== typeof value);
			}
			/**
			 * @param {*} value
			 * @returns {Boolean}
			 */
			,empty: function(value) {
				/**
				 * @link http://stackoverflow.com/a/154068/254475
				 */
				return !value;
			}
			/**
			 * @function
			 * @throws {Error}
			 */
			,error: function() {
				/** @type {String} */
				var message = '';
				if (0 < arguments.length) {
					message =
						(1 === arguments.length)
						? arguments[0]
						: sprintf.apply(arguments)
					;
				}
				console.trace();
				throw new Error(message);
			}
			,namespace:
				/**
				 * Создаёт иерархическое объектное пространство имён.
				 * Пример применения:
				 * rm.namespace('rm.catalog.showcase');
				 * rm.catalog.showcase.product = {
				 * 		<...>
				 * };
				 *
				 */
				function() {
					var a=arguments, o=null, i, j, d;
					for(i=0; i<a.length; i+=1) {
						d=a[i].split(".");
						o=window;
						for(j=0; j<d.length; j+=1) {
							o[d[j]]=o[d[j]] || {};
							o=o[d[j]];
						}
					}
					return o;
				}
			,reduce: function(array, fnReduce, valueInitial) {
				$.each(array, function(index, value) {
					valueInitial = fnReduce.call(value, valueInitial, value, index, array);
				});
				return valueInitial;
			}
			/**
			 * @param value
			 * @returns {Boolean}
			 */
			,undefined: function(value) {
				return !rm.defined(value);
			}
		}
	});
})(jQuery);

(function($) {$(function() {
	/**
	 * Мы начали разрабатывать прикладные решения,
	 * которые не включают библиотеку Prototype и стандартные скрипты Magento.
	 * Поэтому учитываем ситуацию, когда класс Validation остутствует
	 * Обратите внимание, что нужно писать именно rm.defined(window.Validation),
	 * а не rm.defined(Validation),
	 * потому что второй вариант приводит к сбою в Firefox:
	 * «ReferenceError: Validation is not defined».
	 */
	if (rm.defined(window.Validation)) {
		rm.namespace('rm.checkout');
		/** @type {RegExp} */
		var alphabetRu = /^[a-zA-Zа-яА-ЯёЁ]*$/;
		/** @type {RegExp} */
		var alphabetRuExtended = /^[a-zA-Zа-яА-ЯёЁ\-\s]*$/;
		// Обратите внимание, что украинские буквы «Іі» внешне неотличимы от латинских,
		// однако имеют другой код в Unicode,
		// поэтому не покрываются регулярным выражением [a-zA-Z]
		/** @type {RegExp} */
		var alphabetRuUa = /^[a-zA-Zа-яА-ЯёЁҐґЄєЇїІі]*$/;
		/** @type {RegExp} */
		var alphabetRuUaExtended = /^[a-zA-Zа-яА-ЯёЁҐґЄєЇїІі\-\s]*$/;
		// Обратите внимание, что казахские буквы «Іі» внешне неотличимы от латинских,
		// однако имеют другой код в Unicode,
		// поэтому не покрываются регулярным выражением [a-zA-Z]
		/** @type {RegExp} */
		var alphabetRuKz = /^[a-zA-Zа-яА-ЯёӘәҒғҚқҢңӨөҰұҺһІі]*$/;
		/** @type {RegExp} */
		var alphabetRuKzExtended = /^[a-zA-Zа-яА-ЯёӘәҒғҚқҢңӨөҰұҺһІі\-\s]*$/;
		/** @type {RegExp} */
		var alphabet = alphabetRu;
		/** @type {RegExp} */
		var alphabetExtended = alphabetRuExtended;
		switch(rm.checkout.alphabet) {
			case 'ua':
				alphabet = alphabetRuUa;
				alphabetExtended = alphabetRuUaExtended;
				break;
			case 'kz':
				alphabet = alphabetRuKz;
				alphabetExtended = alphabetRuKzExtended;
				break;
			default:
				break;
		}
		Validation
			.addAllThese(
				[
					[
						'rm.validate.firstName'
						,'Пожалуйста, исправьте написание Вашего имени.'
						,/**
						 * @param {String} value
						 * @returns {Boolean}
						 */
						function(value) {
							return alphabet.test(value);
						}
					]
					,[
						'rm.validate.lastName'
						,'Фамилия должна состоять только из букв, дефиса(«-») и пробелов'
						,/**
						 * @param {String} value
						 * @returns {Boolean}
						 */
						function(value) {
							return alphabetExtended.test(value);
						}
					]
					,[
						'rm.validate.patronymic'
						,'Отчество должно состоять только из букв, дефиса(«-») и пробелов'
						,/**
						 * @param {String} value
						 * @returns {Boolean}
						 */
						function(value) {
							return alphabetExtended.test(value);
						}
					]
					,[
						'rm.validate.postalCode'
						,'Данное поле должно содержать 6 цифр.'
						,/**
						 * @param {String} value
						 * @returns {Boolean}
						 */
						function(value) {
							/** @type {Boolean} */
							var result =
									Validation.get('IsEmpty').test(value)
								||
									/^[\d]{6}$/.test(value)
							;
							return result;
						}
					]
					,[
						'rm.validate.phone'
						,'Укажите действующий телефонный номер'
						,/**
						 * @param {String} value
						 * @returns {Boolean}
						 */
						function(value) {
							/** @type {Boolean} */
							var result =
									Validation.get('IsEmpty').test(value)
								||
									/^[\d\-\(\)\+\s]{5,20}$/.test(value)
							;
							return result;
						}
					]
					,[
						'rm.validate.city'
						,'Название города должно состоять только из букв, дефиса(«-») и пробелов'
						,/**
						 * @param {String} value
						 * @returns {Boolean}
						 */
						function(value) {
							/** @type {Boolean} */
							var result =
									Validation.get('IsEmpty').test(value)
								||
									alphabetExtended.test(value)
							;
							return result;
						}
					]
					,[
						'rm.validate.region.text'
						,'Название области должно состоять только из букв, дефиса(«-») и пробелов'
						,/**
						 * @param {String} value
						 * @returns {Boolean}
						 */
						function(value) {
							/** @type {Boolean} */
							var result =
									Validation.get('IsEmpty').test(value)
								||
									alphabetExtended.test(value)
							;
							return result;
						}
					]
					,[
						'rm.validate.urlKey'
						,'Уникальная часть веб-адреса должна начинаться с буквы или цифры, '
						+ 'затем допустимы буквы, цифры, символ пути(«/»), дефис(«-»), символ подчёркивания(«_»). '
						+ 'В конце допустимо расширение, как у имён файлов: '
						+ 'точка(«.») и после неё: буквы, цифры, дефис(«-») и символ подчёркивания(«_») .'
						,/**
						 * @param {String} value
						 * @returns {Boolean}
						 */
						function(value) {
							/** @type {Boolean} */
							var result =
									Validation.get('IsEmpty').test(value)
								||
									/^[a-zа-яА-ЯёЁ0-9][a-zа-яА-ЯёЁ0-9_\/-]+(\.[a-zа-яА-ЯёЁ0-9_-]+)?$/.test(value)
							;
							return result;
						}
					]
				]
			)
		;
	}
});})(jQuery);
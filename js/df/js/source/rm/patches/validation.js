(function() {
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
		Object.extend(Validation, {
			/**
			 * @used-by Validation.prototype.dfValidateFilledFieldsOnly()
			 * @param {Element} elm
			 * @return {Boolean}
			 */
			dfIsVisibleAndNotEmpty : function(elm) {
				/** @type {Boolean} */
				var result =
						Validation.rm.parent.isVisible(elm)
					&&
						/**
						 * Временно считаем пустые поля "невидимыми",
						 * чтобы стандарный класс не считал их неправильно заполненными
						 */
						('' !== $F(elm))
				;
				return result;
			}

			,/**
			 * Данный метод проверяет корректность заполнения формы
			 * так же, как и стандартный метод test(),
			 * Это используется при Быстром оформлении заказа
			 * @param {String} name
			 * @param {Element} elm
			 * @param {Boolean} useTitle
			 * @return {Boolean}
			 */
			dfTestSilent: function(name, elm, useTitle) {
				/** @type {Boolean} */
				var result = false;
				/** @type {Validator} */
				var validator = Validation.get(name);
				try {
					result = (!Validation.isVisible(elm) || validator.test($F(elm), elm));
				}
				catch(e) {
					alert("exception: " + e.message);
					alert(e.stack.toString());
					console.log(e.message);
					console.log(e.stack.toString());
					throw(e);
				}
				return result;
			}
		});
		Object.extend(Validation.prototype, {
			/**
			 * Это используется при Быстром оформлении заказа
			 * @return {Boolean}
			 */
			dfValidateFilledFieldsOnly: function() {
				/** @type {Boolean} */
				var result = false;
				rm.namespace('Validation.rm.parent');
				Validation.rm.parent.isVisible = Validation.isVisible;
				try {
					Validation.isVisible = Validation.dfIsVisibleAndNotEmpty;
					result = this.validate();
				}
				finally {
					Validation.isVisible = Validation.rm.parent.isVisible;
				}
				return result;
			}


			,/**
			 * Данный метод проверяет корректность заполнения формы
			 * так же, как и стандартный метод validate(),
			 * но не выводит диагностических сообщений.
			 * Это используется при Быстром оформлении заказа
			 * @function
			 * @return {Boolean}
			 */
			dfValidateSilent: function() {
				/** @type {Boolean} */
				var result = false;
				/** @function */
				var standardMethod = Validation.test;
				try {
					Validation.test = Validation.dfTestSilent;
					result = this.validate();
				}
				finally {
					Validation.test = standardMethod;
				}
				return result;
			}
		});
	}
})();
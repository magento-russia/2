;(function($) { $(function() {
	rm.namespace('rm.localization.verification');
	//noinspection JSValidateTypes
	rm.localization.verification.FileWorkspace = {
		construct: function(_config) { var _this = {
			init:
				function() {
					$(window)
						.bind(
							rm.localization.verification.FileList.itemSelected
							,
							/**
							 * Отображаем подробную информацию о текущем файле
							 *
							 * @param {jQuery.Event} event
							 */
							function(event) {
								_this.getElement().removeClass('df-hidden');
								_this.showFileDetails(event.file.name);
							}
						)
					;
				}
			,
			showFileDetails:
				/**
				 * Отображаем подробную информацию о текущем файле
				 *
				 * @param {String} fileName
				 */
				function(fileName) {
					this.getTitleElement().text(fileName);
					this.getUntranslatedListElement().empty();
					this.getAbsentListElement().empty();
					/**
					 * @type {?Object}
					 */
					var details =
						rm.localization.verification.details [fileName]
					;
					if (details) {
						this
							.fillList(
								this.getUntranslatedListElement()
								,
								details.untranslatedEntries
								,
								'df-untranslatedItem'
							)
							.fillList(
								this.getAbsentListElement()
								,
								details.absentEntries
								,
								'df-absentItem'
							)
						;
					}
					return this;
				}
			,
			fillList:
				/**
				 * @param {jQuery(HTMLOListElement)} list
				 * @param {Array} items
				 * @param {String} itemClass
				 */
				function(list, items, itemClass) {
					if ($.isArray(items)) {
						if (0 === items.length) {
							list.parent().addClass('df-hidden');
						}
						else {
							list.parent().removeClass('df-hidden');
						}
						$
							.each(
								items
								,
								function(index, item) {
									list
										.append(
											$('<li/>')
												.addClass(itemClass)
												.text(item)
										)
									;
								}
							)
						;
					}
					return this;
				}
			,
			getUntranslatedListElement:
				/**
				 * @returns {jQuery} HTMLElement
				 */
				function() {
					if (rm.undefined(this._untranslatedListElement)) {
						this._untranslatedListElement = $('.df-untranslatedItems', this.getElement());
					}
					return this._untranslatedListElement;
				}
			,
			getAbsentListElement:
				/**
				 * @returns {jQuery} HTMLElement
				 */
				function() {
					if (rm.undefined(this._absentListElement)) {
						this._absentListElement = $('.df-absentItems', this.getElement());
					}
					return this._absentListElement;
				}
			,
			getTitleElement:
				/**
				 * @returns {jQuery} HTMLElement
				 */
				function() {
					if (rm.undefined(this._titleElement)) {
						this._titleElement = $('h2', this.getElement());
					}
					return this._titleElement;
				}
			,
			getElementSelector:
				/**
				 * @returns {String}
				 */
				function() {
					return _config.elementSelector;
				}
			,
			getElement:
				/**
				 * @returns {jQuery} HTMLElement
				 */
				function() {
					if (rm.undefined(this._element)) {
						this._element = $(this.getElementSelector());
					}
					return this._element;
				}
		}; _this.init(); return _this; }
	};





}); })(jQuery);
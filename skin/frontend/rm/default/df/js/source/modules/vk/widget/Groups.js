;(function($) {
	rm.namespace('rm.vk.widget');
	//noinspection JSValidateTypes
	rm.vk.widget.Groups = {
		construct: function(_config) { var _this = {
			init: function() {
				if (0 < this.getParent().length) {
					/** @type {jQuery} HTMLElement[] */
					var $blocks = $('.block', this.getParent());
					/** @type {Number} */
					var childrenCount = $blocks.length;
					/** @type {Number} */
					var insertionIndex =
						Math.max(
							0
							,Math.min(
								childrenCount - 1
								,/**
								 * Вычитает единицу, * потому что в административном интерфейсе
								 * нумерация начинается с 1
								 */
								rm.vk.groups.verticalOrdering - 1
							)
						)
					;
					/** @type {jQuery} HTMLElement */
					var $widget =
						$('<div></div>')
							.attr('id', this.getContainerId())
							.addClass('block')
					;
					if (0 === insertionIndex) {
						this.getParent().prepend($widget);
					}
					else {
						$($blocks.get(insertionIndex)).before($widget);
					}
					if (rm.defined(window.VK)) {
						_this.createWidget();
					}
					else {
						$
							.getScript(
								'http://userapi.com/js/api/openapi.js'
								,function() {
									_this.createWidget();
								}
							)
						;
					}
				}
			}
			,/**
			 * @private
			 * @returns {rm.vk.Widget }
			 */
			createWidget: function() {
				/**
				 *  Надо вызвать конструктор типа VK.Widgets.Comments
				 *  по его текстовой записи: "VK.Widgets.Comments"
				 */
				var dotParser = function(object, index) {
					var result = object[index];
					if (rm.undefined(result)) {
						console.log('Index %index is undefined'.replace('%index', index));
					}
					return result;
				};
				var constructor =
					/**
					 *  Не используем Array.prototype.reduce из JavaScript 1.8, *  потому что в Magento 1.4.1.0 этот метод конфликтует
					 *  с одноимённым методом библиотеки Prototype.
					 */
					rm.reduce(
						this.getObjectName().split('.')
						,dotParser
						,window
					)
				;
				constructor
					.call(
						window
						,this.getContainerId()
						,this.getWidgetSettings()
						,_this.getApplicationId()
					)
				;
				return this;
			}
			,/**
			 * @private
			 * @returns {Number}
			 */
			getApplicationId: function() {
				return _config.applicationId;
			}
			,/**
			 * @private
			 * @returns {String}
			 */
			getContainerId: function() {
				return _config.containerId;
			}
			,/**
			 * @private
			 * @returns {String}
			 */
			getObjectName: function() {
				return _config.objectName;
			}
			,/**
			 * @private
			 * @returns {jQuery} HTMLElement
			 */
			getParent: function() {
				if (rm.undefined(this._parent)) {
					/** @type {String} */
					var selector =
							('left' === rm.vk.groups.position)
						?
							'.col-left'
						:
							'.col-right'
					;
					/**
					 * @type {jQuery} HTMLElement
					 */
					this._parent = $(selector);
					if (0 === this._parent.length) {
						if (
								0
							<
								(
										$('.col2-right-layout').length
									+
										$('.col2-left-layout').length
								)
						) {
							this._parent = $('.col-main');
						}
					}
				}
				return this._parent;
			}
			,/**
			 * @private
			 * @returns {Object}
			 */
			getWidgetSettings: function() {
				return _config.widgetSettings;
			}
		}; _this.init(); return _this; }
	};

})(jQuery);
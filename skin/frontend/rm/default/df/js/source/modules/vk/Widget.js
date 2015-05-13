;(function($) {
	rm.namespace('rm.vk');
	//noinspection JSValidateTypes
	rm.vk.Widget = {
		construct: function(_config) { var _this = {
			init: function() {
				/** @type {jQuery} HTMLElement */
				var $parent = $(this.getParentSelector());
				if (0 < $parent.length) {
					$parent.append($('<div></div>').attr('id', this.getContainerId()));
					if (rm.defined(window.VK)) {
						_this.createWidget();
					}
					else {
						$.getScript('http://userapi.com/js/api/openapi.js', function() {
							VK.init({
								apiId: _this.getApplicationId()
								,onlyWidgets: true
							});
							_this.createWidget();
						});
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
					 *  Не используем Array.prototype.reduce из JavaScript 1.8,
					 *  потому что в Magento 1.4.1.0 этот метод конфликтует
					 *  с одноимённым методом библиотеки Prototype.
					 */
					rm.reduce(
						this.getObjectName().split('.')
						,dotParser
						,window
					)
				;
				constructor.call(window, this.getContainerId(), this.getWidgetSettings());
				return this;
			}
			,/**
			 * @private
			 * @returns {Number}
			 */
			getApplicationId: function() {return _config.applicationId;}
			,/**
			 * @private
			 * @returns {String}
			 */
			getContainerId: function() {return _config.containerId;}
			,/**
			 * @private
			 * @returns {String}
			 */
			getObjectName: function() {return _config.objectName;}
			,/**
			 * @private
			 * @returns {String}
			 */
			getParentSelector: function() {return _config.parentSelector;}
			,/**
			 * @private
			 * @returns {Object}
			 */
			getWidgetSettings: function() {return _config.widgetSettings;}
		}; _this.init(); return _this; }
	};
})(jQuery);
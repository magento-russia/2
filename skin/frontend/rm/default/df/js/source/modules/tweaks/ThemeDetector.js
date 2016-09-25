;(function($) {
	rm.namespace('rm.tweaks');
	// rm.tweaks.options отсутствует на страницах формы ПД-4
	if (!rm.tweaks.options) {
		rm.tweaks.options = {};
	}
	//noinspection JSValidateTypes
	rm.tweaks.ThemeDetector = {
		initialized: 'rm.tweaks.ThemeDetector.initialized'
		,construct: function(_config) { var _this = {
			init: function() {
				$.each(rm.tweaks.dictionary, function(themeCssId, themeConditions) {
					/** @type {boolean} */
					var applicable;
					/**
					 * Ключ «package» должен всегда присутствовать как в правилах, так и в rm.tweaks.options.
					 * Значение ключа «package» применимого правило
					 * должно всегда совпадать со знчением этого ключа в rm.tweaks.options
					 */
					applicable = (themeConditions.package === rm.tweaks.options.package);
					if (applicable) {
						/**
						 * Ключ «skin» может и присутствовать, и отсутствовать
						 * как в правилах, так и в rm.tweaks.options.
						 */
						if (!rm.defined(themeConditions.skin)) {
							/**
							 * Если значение ключа «skin» отсутствует в правиле,
							 * то значение этого ключа в rm.tweaks.options должно либо отсутствовать,
							 * либо быть равно «default».
							 *
							 * Это условие нужно для того, чтобы, например,
							 * правило {package: 'default', 'theme': 'default'}
							 * не применялось к состоянию rm.tweaks.options
							 * {'package':'default', 'theme':'default', 'skin':'theme454'}
							 */
							applicable =
								!rm.defined(rm.tweaks.options.skin)
								|| ('default' === rm.tweaks.options.skin)
								/**
								 * 2016-09-30
								 * Добавил звёздочку, чтобы условие
								 * {package: 'ultimo', theme: '*'} пропускало модификации тем,
								 * которые Infortis рекомендует называть «child».
								 */
								|| ('*' === themeConditions.theme)
								/**
								 * 2015-12-04
								 * Добавил это условие для ситуации:
								 * rm.tweaks.options:
										{
											"package":"default"
											,"theme":"galasoftwaremarket"
											,"skin":"galasoftwaremarket"
										}
								 * themeConditions.theme:
										{
											'package': 'default'
											, 'theme': 'galasoftwaremarket'
										}
								 */
								|| (
									('default' !== rm.tweaks.options.theme)
									&& rm.defined(themeConditions.theme)
									&& themeConditions.theme === rm.tweaks.options.theme
								)
							;
						}
						else {
							/**
							 * Если значение ключа «skin» присутствует в правиле,
							 * то оно должно либо совпадать со значением этого ключа в rm.tweaks.options,
							 * либо быть массивом, содержащим в себе значение этого ключа в rm.tweaks.options.
							 */
							applicable =
									(themeConditions.skin === rm.tweaks.options.skin)
								||
									(
											$.isArray(themeConditions.skin)
										&&
											(-1 !== $.inArray(rm.tweaks.options.skin, themeConditions.skin))
									)
							;
						}
					}
					if (applicable) {
						if (rm.defined(themeConditions.theme)) {
							applicable =
									/**
									 * 2016-09-30
									 * Добавил звёздочку, чтобы условие
									 * {package: 'ultimo', theme: '*'}
									 * пропускало модификации тем,
									 * которые Infortis рекомендует называть «child».
									 */
									('*' === themeConditions.theme)
								||
									(themeConditions.theme === rm.tweaks.options.theme)
								||
									(
											$.isArray(themeConditions.theme)
										&&
											(-1 !== $.inArray(rm.tweaks.options.theme, themeConditions.theme))
									)
							;
						}
					}
					if (applicable) {
						$('body').addClass(themeCssId);
						return false;
					}
				});
				$(window).trigger({
					/** @type {String} */
					type: rm.tweaks.ThemeDetector.initialized
				});
			}
		}; _this.init(); return _this; }
	};
})(jQuery);
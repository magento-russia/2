;(function($) { $(function() {
	rm.namespace('rm.localization.verification');
	//noinspection JSValidateTypes
	rm.localization.verification.FileList = {
		itemSelected: 'rm.localization.verification.fileList.itemSelected'
		,
		construct: function(_config) { var _this = {
			init:
				function() {
					this.getFileElements().filter(':odd').addClass('df-file-odd');
					this.getFileElements().filter(':even').addClass('df-file-even');
					this.getElement()
						.css(
							'max-height'
							,
							Math.round(0.5 * screen.height) + "px"
						)
					;
					this.getFileElements()
						.hover(
							function() {
								$(this).addClass('df-file-hovered');
							}
							,
							function() {
								$(this).removeClass('df-file-hovered');
							}
						)
						.click(
							function() {
								var $this = $(this);
								$this.addClass('df-file-selected');
								$this.siblings().removeClass('df-file-selected');
								/** @type {String} */
								var fileName =
									$.trim(
										$('.df-name', $this).text()
									)
								;
								$(window)
									.trigger(
										{
											/** @type {String} */
											type: rm.localization.verification.FileList.itemSelected
											,
											/** @type {Object} */
											file: {
												/** @type {String} */
												name: fileName
											}
										}
									)
								;
							}
						)
					;
				}
			,
			getFileElements:
				/**
				 * @type {jQuery} HTMLLIElement[]
				 */
				function() {
					if (rm.undefined(this._fileElements)) {
						/** @type {jQuery} HTMLLIElement[] */
						var result =
							$('.df-file', this.getElement())
						;
						this._fileElements = result;
					}
					return this._fileElements;
				}
			,
			getElementSelector:
				function() {
					return _config.elementSelector;
				}
			,
			getElement:
				function() {
					if (rm.undefined(this._element)) {
						this._element = $(this.getElementSelector());
					}
					return this._element;
				}
		}; _this.init(); return _this; }
	};





}); })(jQuery);
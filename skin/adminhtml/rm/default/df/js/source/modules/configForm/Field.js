;(function($) {
	rm.namespace('rm.admin.configForm');
	//noinspection JSValidateTypes
	/**
	 * @param {jQuery} HTMLElement
	 */
	rm.admin.configForm.Field = {
		construct: function(_config) { var _this = {
			init: function() {
				if (this.getMasterField()) {
					this.getMasterField().getElement()
						.change(
							function() {
								_this.updateVisibilityByMasterField();
							}
						)
					;
					this.updateVisibilityByMasterField();
					$(window)
						.bind(
							'rm.admin.cms.hierarchy.node.formUpdated'
							,
							function() {
								_this.updateVisibilityByMasterField();
							}
						)
					;
				}
			}
			,
			/**
			 * @private
			 * @returns {String[]}
			 */
			getCssClasses: function() {
				if (rm.undefined(this._cssClasses)) {
					/** @type {?String} */
					var cssClassesAsString = this.getElement().attr('class');
					this._cssClasses =
						rm.undefined(cssClassesAsString)
						? []
						: cssClassesAsString.split(/\s+/)
					;
				}
				return this._cssClasses;
			}
			,
			/**
			 * @private
			 * @returns {jQuery} HTMLTableRowElement
			 */
			getContainer: function() {
				if (rm.undefined(this._container)) {
					this._container = this.getElement().closest('tr');
				}
				return this._container;
			}
			,
			/**
			 * @private
			 * @returns {?rm.admin.configForm.Field}
			 */
			getMasterField: function() {
				if (rm.undefined(this._masterField)) {
					/**
					 * @type {?rm.admin.configForm.Field}
					 */
					this._masterField = null;
					$.each(this.getCssClasses(), function() {
						/** @type {String} */
						var _class = this;
						/** @type {RegExp} */
						var pattern = /df\-depends\-\-(.*)/;
						/** @type {?String[]} */
						var matches = _class.match(pattern);
						if (null !== matches) {
							/** @type {String} */
							var masterElementId = matches[1];
							/** @type {HTMLElement} */
							var masterElement = document.getElementById(masterElementId);
							if (masterElement) {
								/**
								 * @type {?rm.admin.configForm.Field}
								 */
								_this._masterField =
									rm.admin.configForm.Field
										.construct(
											{
												element: $(masterElement)
											}
										)
								;
								return false;
							}
						}
					});
				}
				return this._masterField;
			}
			,
			/**
			 * @private
			 * @returns {rm.admin.configForm.Field}
			 */
			updateVisibilityByMasterField: function() {
				if (this.getMasterField()) {
					if ('0' === this.getMasterField().getElement().val().toString()) {
						this.getContainer().hide();
					}
					else {
						this.getContainer().show();
					}
				}
				return this;
			}
			,
			/**
			 * @private
			 * @returns {jQuery} HTMLElement
			 */
			getElement: function() {
				return _config.element;
			}
		}; _this.init(); return _this; }
	};
})(jQuery);
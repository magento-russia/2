;(function($) {$(function() {
	rm.namespace('rm.editor');
	if (true === rm.editor.useRm) {
		if (rm.defined(window.tinyMceWysiwygSetup)) {
			/** @function */
			var parentGetSettings = tinyMceWysiwygSetup.prototype.getSettings;
			tinyMceWysiwygSetup.prototype.getSettings = function(mode) {
				/** @type {Object} */
				var parentSettings = parentGetSettings.call(this, mode);
				/** @type {Object} */
				var customSettings = {
					language: 'ru'
					,skin : 'o2k7'
					,plugins:
							parentSettings.plugins
						+	',inlinepopups,codemagic,simpleupload'
					,theme_advanced_buttons3:
							parentSettings.theme_advanced_buttons3
						+	',codemagic,simpleupload'
					,simpleupload_connector_path: '/var/media/connector/php/upload.php'
					,simpleupload_images_path: '/var/media/media'
					,entity_encoding: 'raw'
				};
				/** @type {Object} */
				var result =
					$.extend(
						parentSettings
						,
						customSettings
					)
				;
				return result;
			};
		}
		if (rm.defined(window.MediabrowserUtility)) {
			MediabrowserUtility.openDialog = function(url, width, height, title) {
				if ((0 < $('#browser_window').length) && rm.defined(Windows)) {
					Windows.focus('browser_window');
					return;
				}
				this.dialogWindow = Dialog.info(null, {
					closable:     true,
					resizable:    false,
					draggable:    true,
					className:    'magento',
					windowClassName:    'popup-window',
					title:        title || 'Insert File...',
					top:          50,
					width:        width || 950,
					height:       height || 600,
					zIndex:       4000,
					recenterAuto: false,
					hideEffect:   Element.hide,
					showEffect:   Element.show,
					id:           'browser_window',
					onClose: this.closeDialog.bind(this)
				});
				new Ajax.Updater('modal_dialog_message', url, {evalScripts: true});
			};
			MediabrowserUtility.closeDialog = function(window) {
				if (!window) {
					window = this.dialogWindow;
				}
				if (window) {
					// IE fix - hidden form select fields after closing dialog
					WindowUtilities._showSelect();
					window.close();
				}
				if (rm.defined(window.Windows)) {
					Windows.maxZIndex = 1000;
				}
			}
		}
	}
});})(jQuery);
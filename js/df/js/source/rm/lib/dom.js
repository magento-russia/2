;(function($) {
	rm.dom = {
		/**
		 * @public
		 * @param {HTMLElement} node
		 * @param {Boolean} includeWhitespaceNodes
		 * @returns {HTMLElement}[]
		 */
		getChildrenTextNodes: function(node, includeWhitespaceNodes) {
			var textNodes = [], whitespace = /^\s*$/;
			function getTextNodes(node) {
				if (3 == node.nodeType) {
					if (includeWhitespaceNodes || !whitespace.test(node.nodeValue)) {
						textNodes.push(node);
					}
				}
				else {
					for(var i = 0, len = node.childNodes.length; i < len; ++i) {
						getTextNodes(node.childNodes[i]);
					}
				}
			}
			getTextNodes(node);
			return textNodes;
		}

		/**
		 * @public
		 * @returns {rm.text}
		 */
		,replaceHtmlPartial: function($element, originalText, translatedText) {
			/**
			 * Этот цикл обязателен, потому что .text()
			 * правильно работает только с единичным элементом.
			 */
			$element.each(function() {
				/** @type {jQuery} HTMLElement */
				var $element = $(this);
				$element.html($element.html().replace(originalText, translatedText));
			});
			return this;
		}

		/**
		 * @public
		 * @returns {rm.text}
		 */
		,replaceText: function($element, originalText, translatedText) {
			/**
			 * Этот цикл обязателен, потому что .text()
			 * правильно работает только с единичным элементом.
			 */
			$element.each(function() {
				/** @type {jQuery} HTMLElement */
				var $element = $(this);
				if (originalText === $element.text()) {
					$element.text(translatedText);
				}
			});
			return this;
		}
	};
})(jQuery);
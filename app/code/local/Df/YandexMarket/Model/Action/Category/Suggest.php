<?php
class Df_YandexMarket_Model_Action_Category_Suggest extends Df_Core_Model_Controller_Action {
	/**
	 * @override
	 * @return string
	 */
	protected function generateResponseBody() {
		return $this->getSuggestionsAsJson();
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getContentType() {
		return 'application/json';
	}

	/** @return string */
	private function getQuery() {
		return $this->getRmRequest()->getParam('query');
	}
	
	/** @return array(string => string|string[]) */
	private function getSuggestions() {
		return
			array(
				'query' => $this->getQuery()
				,'suggestions' =>
					Df_YandexMarket_Model_Category_Adviser::s()->getSuggestions(
						$this->getQuery()
					)
			)
		;
	}

	/** @return string */
	private function getSuggestionsAsJson() {
		/**
		 * Zend_Json::encode использует json_encode при наличии расширения PHP JSON
		 * и свой внутренний кодировщик при отсутствии расширения PHP JSON.
		 * @see Zend_Json::encode
		 * @link http://stackoverflow.com/questions/4402426/json-encode-json-decode-vs-zend-jsonencode-zend-jsondecode
		 * Обратите внимание,
		 * что расширение PHP JSON не входит в системные требования Magento.
		 * @link http://www.magentocommerce.com/system-requirements
		 * Поэтому использование Zend_Json::encode выглядит более правильным, чем json_encode.
		 */
		return Zend_Json::encode($this->getSuggestions());
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param Df_YandexMarket_CategoryController $controller
	 * @return Df_YandexMarket_Model_Action_Category_Suggest
	 */
	public static function i(Df_YandexMarket_CategoryController $controller) {
		return new self(array(self::P__CONTROLLER => $controller));
	}
}
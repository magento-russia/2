<?php
class Df_YandexMarket_Model_Action_Category_Suggest extends Df_Core_Model_Action {
	/**
	 * @override
	 * @see Df_Core_Model_Action::generateResponseBody()
	 * @used-by Df_Core_Model_Action::getResponseBody()
	 * @return string
	 */
	protected function generateResponseBody() {return $this->getSuggestionsAsJson();}

	/**
	 * @override
	 * @see Df_Core_Model_Action::getContentType()
	 * @used-by Df_Core_Model_Action::getResponseLogFileExtension()
	 * @used-by Df_Core_Model_Action::processPrepare()
	 * @return string
	 */
	protected function getContentType() {return 'application/json';}

	/** @return string */
	private function getQuery() {return $this->getRmRequest()->getParam('query');}
	
	/** @return array(string => string|string[]) */
	private function getSuggestions() {
		return array(
			'query' => $this->getQuery()
			,'suggestions' => Df_YandexMarket_Model_Category_Adviser::s()->getSuggestions($this->getQuery())
		);
	}

	/** @return string */
	private function getSuggestionsAsJson() {
		/**
		 * @see Zend_Json::encode() использует
		 * @see json_encode() при наличии расширения PHP JSON
		 * и свой внутренний кодировщик при отсутствии расширения PHP JSON.
		 * http://stackoverflow.com/questions/4402426/json-encode-json-decode-vs-zend-jsonencode-zend-jsondecode
		 * Обратите внимание,
		 * что расширение PHP JSON не входит в системные требования Magento.
		 * http://www.magentocommerce.com/system-requirements
		 * Поэтому использование @see Zend_Json::encode()
		 * выглядит более правильным, чем @see json_encode().
		 */
		return Zend_Json::encode($this->getSuggestions());
	}
}
<?php
namespace Df\YandexMarket\Action\Category;
use Df\YandexMarket\Category\Advisor as Advisor;
class Suggest extends \Df_Core_Model_Action {
	/**
	 * @override
	 * @see Df_Core_Model_Action::generateResponseBody()
	 * @used-by Df_Core_Model_Action::responseBody()
	 * @return string
	 */
	protected function generateResponseBody() {return $this->getSuggestionsAsJson();}

	/**
	 * @override
	 * @see Df_Core_Model_Action::contentType()
	 * @used-by Df_Core_Model_Action::getResponseLogFileExtension()
	 * @used-by Df_Core_Model_Action::processPrepare()
	 * @return string
	 */
	protected function contentType() {return 'application/json';}

	/** @return string */
	private function getQuery() {return $this->rmRequest()->getParam('query');}
	
	/** @return array(string => string|string[]) */
	private function getSuggestions() {return [
		'query' => $this->getQuery(), 'suggestions' => Advisor::s()->getSuggestions($this->getQuery())
	];}

	/** @return string */
	private function getSuggestionsAsJson() {return df_json_encode($this->getSuggestions());}
}
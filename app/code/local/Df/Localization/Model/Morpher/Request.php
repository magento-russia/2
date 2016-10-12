<?php
class Df_Localization_Model_Morpher_Request extends Df_Core_Model {
	/** @return string */
	public function getResponse() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				str_replace(
					' xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://morpher.ru/"'
					,''
					,$this->getHttpResponse()->getBody()
				)
			;
		}
		return $this->{__METHOD__};
	}
	
	/** @return Zend_Http_Client */
	private function getHttpClient() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = new Zend_Http_Client();
			$this->{__METHOD__}->setUri($this->getUri());
		}
		return $this->{__METHOD__};
	}

	/** @return Zend_Http_Response */
	private function getHttpResponse() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getHttpClient()->request(Zend_Http_Client::GET);
		}
		return $this->{__METHOD__};
	}
	
	/** @return string */
	private function getInCaseNominative() {return $this->cfg(self::P__CASE_NOMINATIVE);}
	
	/** @return Zend_Uri_Http */
	private function getUri() {
		if (!isset($this->{__METHOD__})) {
			/** @var Zend_Uri_Http $result */
			$result = Zend_Uri::factory('http');
			$result->setHost('morpher.ru');
			$result->setPath('/WebService.asmx/GetXml');
			$result->setQuery(array(
				's' => $this->getInCaseNominative()
				,'username' => 'Дмитрий Федюк'
				,'password' => 'duN9frug'
			));
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}
	
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__CASE_NOMINATIVE, self::V_STRING_NE);
	}
	const _CLASS = __CLASS__;
	const P__CASE_NOMINATIVE = 'case_nominative';
	/**
	 * @static
	 * @param string $word
	 * @return Df_Localization_Model_Morpher_Request
	 */
	public static function i($word) {
		return new self(array(self::P__CASE_NOMINATIVE => $word));
	}
}
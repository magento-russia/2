<?php
class Df_PonyExpress_Model_Request_Rate extends Df_Shipping_Model_Request {
	/**
	 * @return array(array(string => string))
	 * @throws Exception
	 */
	public function getVariants() {
		if (!isset($this->{__METHOD__})) {
			try {
				$this->{__METHOD__} = $this->response()->json('tariffall');
				// Сервер может возвращать либо один тариф, либо массив тарифов.
				if (isset($this->{__METHOD__}['tariffvat'])) {
					$this->{__METHOD__} = array($this->{__METHOD__});
				}
				df_result_array($this->{__METHOD__});
			}
			catch (Exception $e) {
				$this->logResponseAsJson();
				throw $e;
			}
		}
		return $this->{__METHOD__};
	}
	/** @var array(array(string => string))*/
	private $_variants;

	/**
	 * @override
	 * @param string $responseAsText
	 * @return string
	 */
	public function preprocessJson($responseAsText) {return str_replace('﻿', '' , $responseAsText);}

	/**
	 * @override
	 * @return Df_Shipping_Model_Carrier
	 */
	protected function getCarrier() {return $this->cfg(self::P__CARRIER);}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getHeaders() {
		return array_merge(parent::getHeaders(), array(
			'Accept' => '	application/json, text/javascript, */*; q=0.01'
			,'Accept-Encoding' => 'gzip, deflate'
			,'Cache-Control'	=> 'no-cache'
			,'Connection' => 'keep-alive'
			,'Host' => 'www.ponyexpress.ru'
			,'Pragma' => 'no-cache'
			,'Referer' => 'http://www.ponyexpress.ru/tariff.php'
			,'User-Agent' => Df_Core_Const::FAKE_USER_AGENT
			,'X-Requested-With'	=> 'XMLHttpRequest'
		));
	}

	/** @return array(string => mixed) */
	protected function getPostParameters() {
		return array(
			'excel' => 0
			,'data' =>
				array(
					array(
						'from' => $this->getRateRequest()->getLocatorOrigin()->getResult()
						,'to' => $this->getRateRequest()->getLocatorDestination()->getResult()
						,'weight' => $this->getRateRequest()->getWeightInKilogrammes()
						,'go' => 0
						,'og' => 0
						,'service' => array(array(1,2,3,5,6,7))
						,'service_count' => 6
					)
				)
		);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getPostRawData() {
		return
			strtr(
				preg_replace(
					'#%5B(?:[0-9]|[1-9][0-9]+)%5D=#u'
					,'='
					,http_build_query($this->getPostParameters())
				)
				,array(urlencode('[service][0]') => urlencode('[service][]'))
			)
		;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getQueryHost() {return 'www.ponyexpress.ru';}

	/**
	 * @override
	 * @return string
	 */
	protected function getQueryPath() {return '/tracking/rate';}

	/**
	 * @override
	 * @return string
	 */
	protected function getRequestMethod() {return Zend_Http_Client::POST;}

	/** @return Df_Shipping_Model_Rate_Request */
	private function getRateRequest() {return $this->cfg(self::P__RATE_REQUEST);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__CARRIER, Df_Shipping_Model_Carrier::_CLASS)
			->_prop(self::P__RATE_REQUEST, Df_Shipping_Model_Rate_Request::_CLASS)
		;
	}
	const _CLASS = __CLASS__;
	const P__CARRIER = 'carrier';
	const P__RATE_REQUEST = 'rate_request';
	/**
	 * @static
	 * @param Df_Shipping_Model_Rate_Request $rateRequest
	 * @param Df_Shipping_Model_Carrier $carrier
	 * @return Df_PonyExpress_Model_Request_Rate
	 */
	public static function i(Df_Shipping_Model_Rate_Request $rateRequest, Df_Shipping_Model_Carrier $carrier) {
		return new self(array(self::P__CARRIER => $carrier, self::P__RATE_REQUEST => $rateRequest));
	}
}
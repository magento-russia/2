<?php
namespace Df\MoySklad;
// 2016-10-13
class Request extends \Df_Core_Model {
	/**
	 * 2016-10-13
	 * @param string $method
	 * @param string $suffix [optional]
	 * @param array[string => mixed] $data [optional]
	 * @return \Zend_Http_Response
	 */
	private function response($method, $suffix = '', array $data = array()) {
		/** @var \Df\MoySklad\Settings\General $s */
		$s = \Df\MoySklad\Settings\General::s();
		/** @var \Zend_Http_Client $c */
		$c = new \Zend_Http_Client;
		$c->setUri(df_cc_path('https://online.moysklad.ru/api/remap/1.1/entity/product', $suffix));
		$c->setAuth($s->login(), $s->password());
		$c->setHeaders('content-type', 'application/json');
		if ($data) {
			$c->setRawData(json_encode($data));
		}
		return $c->request($method);
	}

	/** @return string */
	private function method() {return $this[self::$P__METHOD];}

	/** @return array */
	private function params() {return $this[self::$P__PARAMS];}

	/** @return string */
	private function path() {return $this[self::$P__PATH];}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__METHOD, DF_V_STRING_NE)
			->_prop(self::$P__PARAMS, DF_V_ARRAY)
			->_prop(self::$P__PATH, DF_V_STRING)
		;
	}

	/** @var string */
	private static $P__METHOD = 'method';
	/** @var string */
	private static $P__PARAMS = 'params';
	/** @var string */
	private static $P__PATH = 'path';
}
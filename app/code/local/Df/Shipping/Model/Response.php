<?php
class Df_Shipping_Model_Response extends Df_Core_Model_Abstract {
	/**
	 * @param string $needle
	 * @return bool
	 */
	public function contains($needle) {return rm_contains($this->text(), $needle);}

	/**
	 * @param string|string[]|null $path [optional]
	 * @param string|null $defaultValue [optional]
	 * @return mixed[]|mixed
	 */
	public function json($path = null, $defaultValue = null) {
		if (!isset($this->_jsonDecoded)) {
			$this->_jsonDecoded = Zend_Json::decode($this->getRequest()->preprocessJson($this->text()));
			df_result_array($this->_jsonDecoded);
		}
		/** @var mixed[]|mixed $result */
		$result = null;
		if (is_null($path)) {
			$result = $this->_jsonDecoded;
		}
		else {
			if (is_array($path)) {
				$path = df_concat_xpath($path);
			}
			df_param_string_not_empty($path, 0);
			if (!isset($this->_jsonCache[$path])) {
				$this->_jsonCache[$path] = df_array_query($this->_jsonDecoded, $path, $defaultValue);
			}
			$result = $this->_jsonCache[$path];
		}
		return $result;
	}
	/** @var mixed[] */
	private $_jsonDecoded;
	/** @var array(string => mixed) */
	private $_jsonCache = array();

	/**
	 * @param string $pattern
	 * @param bool $needThrow [optional]
	 * @return string|null
	 */
	public function match($pattern, $needThrow = true) {
		return rm_preg_match($pattern, $this->text(), $needThrow);
	}

	/**
	 * @param string $selector
	 * @param bool $idIsString [optional]
	 * @return array(string => int|string)
	 */
	public function options($selector, $idIsString = false) {
		df_param_string_not_empty($selector, 0);
		if (!isset($this->{__METHOD__}[$selector])) {
			/** @var array(string => int) $result */
			$result = df_h()->phpquery()->parseOptions($this->pq($selector));
			foreach ($result as $locationName => $locationId) {
				$locationName = mb_strtoupper($locationName);
				if ($idIsString) {
					$result[$locationName] = $idIsString;
				}
				else {
					if (df_check_integer($locationId)) {
						$result[$locationName] = rm_int($locationId);
					}
				}
			}
			$this->{__METHOD__}[$selector] = $result;
		}
		return $this->{__METHOD__}[$selector];
	}

	/**
	 * @param string|null $selector [optional]
	 * @return phpQueryObject
	 */
	public function pq($selector = null) {
		if (!isset($this->_pq)) {
			$this->_pq = df_pq($this->text());
		}
		/** @var phpQueryObject $result */
		$result = null;
		if (is_null($selector)) {
			$result = $this->_pq;
		}
		else {
			df_param_string_not_empty($selector, 0);
			if (!isset($this->_pqCache[$selector])) {
				$this->_pqCache[$selector] = df_pq($selector, $this->_pq);
			}
			$result = $this->_pqCache[$selector];
		}
		return $result;
	}
	/** @var phpQueryObject */
	private $_pq;
	/** @var array(string => phpQueryObject) */
	private $_pqCache = array();

	/** @return string */
	public function text() {return $this->cfg(self::P__TEXT);}
	
	/**
	 * @param string|null $path [optional]
	 * @param bool $all [optional]
	 * @return Df_Varien_Simplexml_Element|Df_Varien_Simplexml_Element[]|null
	 */
	public function xml($path = null, $all = false) {
		if (!isset($this->_xml)) {
			$this->_xml = rm_xml($this->text());
		}
		/** @var Df_Varien_Simplexml_Element|Df_Varien_Simplexml_Element[] $result */
		$result = $this->_xml;
		if (!is_null($path)) {
			$result = $this->_xml->xpath($path);
			if (false === $result) {
				$result = null;
			}
			if (!is_null($result)) {
				df_assert_array($result);
				if (!$all) {
					$result = rm_first($result);
					if (!is_null($result)) {
						df_assert($result instanceof Df_Varien_Simplexml_Element);
					}
				}
			}
		}
		return $result;
	}
	/** @var Df_Varien_Simplexml_Element */
	private $_xml;	
		
	/** @return Df_Shipping_Model_Request */
	protected function getRequest() {return $this->cfg(self::P__REQUEST);}
	
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__REQUEST, Df_Shipping_Model_Request::_CLASS)
			->_prop(self::P__TEXT, self::V_STRING_NE)
		;
	}
	const _CLASS = __CLASS__;
	const P__REQUEST = 'request';
	const P__TEXT = 'text';
	/**
	 * @static
	 * @param Df_Shipping_Model_Request $request
	 * @param string $text
	 * @return Df_Shipping_Model_Response
	 */
	public static function i(Df_Shipping_Model_Request $request, $text) {
		return new self(array(self::P__REQUEST => $request, self::P__TEXT => $text));
	}
}
<?php
use Df\Xml\X;
class Df_Shipping_Model_Response extends Df_Core_Model {
	/**
	 * @param string $needle
	 * @return bool
	 */
	public function contains($needle) {return df_contains($this->text(), $needle);}

	/**
	 * @param string|string[]|null $path [optional]
	 * @param string|null $defaultValue [optional]
	 * @return mixed[]|mixed
	 */
	public function json($path = null, $defaultValue = null) {
		$this->_type = self::$TYPE__JSON;
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
				$path = df_cc_path($path);
			}
			df_param_string_not_empty($path, 0);
			if (!isset($this->_jsonCache[$path])) {
				$this->_jsonCache[$path] = dfa_deep($this->_jsonDecoded, $path, $defaultValue);
			}
			$result = $this->_jsonCache[$path];
		}
		return $result;
	}

	/**
	 * @return void
	 */
	public function log() {df_report($this->getLogFileNameTemplate(), $this->report());}

	/**
	 * @param string $pattern
	 * @param bool $needThrow [optional]
	 * @return string|null
	 */
	public function match($pattern, $needThrow = true) {
		return df_preg_match($pattern, $this->text(), $needThrow);
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
			$result = df_pq_options($this->pq($selector));
			foreach ($result as $locationName => $locationId) {
				$locationName = mb_strtoupper($locationName);
				if ($idIsString) {
					$result[$locationName] = $idIsString;
				}
				else {
					if (df_check_integer($locationId)) {
						$result[$locationName] = (int)$locationId;
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
		$this->_type = self::$TYPE__HTML;
		if (!isset($this->_pq)) {
			$this->_pq = df_pq($this->text());
		}
		/** @var phpQueryObject $result */
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

	/**
	 * @return string
	 */
	public function report() {
		return
				(self::$TYPE__JSON === $this->_type)
			&&
				isset($this->_jsonDecoded) && is_array($this->_jsonDecoded)
			? df_print_params($this->_jsonDecoded)
			: $this->text()
		;
	}

	/** @return string */
	public function text() {return $this->cfg(self::P__TEXT);}
	
	/**
	 * @param string|null $path [optional]
	 * @param bool $all [optional]
	 * @return X|X[]|null
	 */
	public function xml($path = null, $all = false) {
		$this->_type = self::$TYPE__XML;
		if (!isset($this->_xml)) {
			$this->_xml = df_xml_parse($this->text());
		}
		/** @var X|X[] $result */
		$result = $this->_xml;
		if (!is_null($path)) {
			$result = $this->_xml->xpath($path);
			if (false === $result) {
				$result = null;
			}
			if (!is_null($result)) {
				df_assert_array($result);
				if (!$all) {
					$result = df_first($result);
					if (!is_null($result)) {
						df_assert($result instanceof X);
					}
				}
			}
		}
		return $result;
	}
		
	/** @return Df_Shipping_Model_Request */
	protected function getRequest() {return $this->cfg(self::P__REQUEST);}

	/** @return string */
	private function getLogFileNameTemplate() {
		return strtr('{module}-{ordering}.{extension}', array(
			'{extension}' => $this->_type, '{module}' => df_module_id($this->getRequest(), '.')
		));
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__REQUEST, Df_Shipping_Model_Request::class)
			->_prop(self::P__TEXT, DF_V_STRING_NE)
		;
	}
	/** @var array(string => mixed) */
	private $_jsonCache = array();
	/** @var mixed[] */
	private $_jsonDecoded;
	/** @var string */
	private $_type = 'txt';
	/** @var phpQueryObject */
	private $_pq;
	/** @var array(string => phpQueryObject) */
	private $_pqCache = array();
	/** @var X */
	private $_xml;

	/** @var string */
	private static $TYPE__HTML = 'html';
	/** @var string */
	private static $TYPE__JSON = 'json';
	/** @var string */
	private static $TYPE__XML = 'xml';

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
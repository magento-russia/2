<?php
class Df_Core_Model_InputRequest extends Df_Core_Model {
	/**
	 * @param string $paramName
	 * @param string $defaultValue [optional]
	 * @return string|null
	 */
	public function getParam($paramName, $defaultValue = null) {
		/** @var string|null $result */
		$result = $this->getRequest()->getParam($paramName, $defaultValue);
		if (!is_null($result)) {
			df_result_string($result);
		}
		return $result;
	}

	/**
	 * @param string $paramName
	 * @param string[] $allowedValues
	 * @return string|null
	 */
	public function getParamFromRange($paramName, array $allowedValues) {
		/** @var string|null $result */
		$result = $this->getParamRequired($paramName);
		if (!in_array($result, $allowedValues)) {
			df_error($this->getErrorMessage_paramIsOutOfRange($paramName, $result, $allowedValues));
		}
		return $result;
	}

	/**
	 * @param string $paramName
	 * @return string
	 */
	public function getParamRequired($paramName) {
		/** @var string|null $result */
		$result = $this->getParam($paramName);
		/**
		 * Не используем !$result,
		 * потому что «0» является допустимым значением,
		 * а выражение !'0' в PHP истинно.
		 */
		if (df_empty_string(df_nts($result))) {
			df_error($this->getErrorMessage_paramIsAbsent($paramName));
		}
		return $result;
	}

	/**
	 * @param string $paramName
	 * @return string
	 */
	protected function getErrorMessage_paramIsAbsent($paramName) {
		return strtr(
			'{prefix}В запросе отсутствует значение обязательного параметра «{paramName}».'
			."\nЗапрос: «{url}»"
			,array(
				'{paramName}' => $paramName
				,'{prefix}' => $this->getErrorMessagePrefix()
				,'{url}' => $this->getRequest()->getRequestUri()
			)
		);
	}

	/**
	 * @param string $paramName
	 * @param string $paramValue
	 * @param string[] $allowedValues
	 * @return string
	 */
	protected function getErrorMessage_paramIsOutOfRange($paramName, $paramValue, array $allowedValues) {
		return strtr(
			'{prefix}Значение «{paramValue}» параметра «{paramName}» недопустимо для данного запроса.'
			."\nЗапрос: «{url}»"
			."\nДопустимые значения параметра «{paramName}»: {allowedValues}."
			,array(
				'{allowedValues}' => df_csv_pretty_quote($allowedValues)
				,'{paramName}' => $paramName
				,'{paramValue}' => $paramValue
				,'{prefix}' => $this->getErrorMessagePrefix()
				,'{url}' => $this->getRequest()->getRequestUri()
			)
		);
	}

	/** @return string */
	protected function getErrorMessagePrefix() {return '';}

	/** @return Mage_Core_Controller_Request_Http */
	protected function getRequest() {return $this->cfg(self::$P__REQUEST);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__REQUEST, 'Mage_Core_Controller_Request_Http');
	}
	/** @var string */
	private static $P__REQUEST = 'request';

	/**
	 * @used-by Df_Core_Model_Action::getRmRequest()
	 * @param string $class
	 * @param Mage_Core_Controller_Request_Http $request
	 * @return Df_Core_Model_InputRequest
	 */
	public static function ic($class, Mage_Core_Controller_Request_Http $request) {
		return rm_ic($class, __CLASS__, array(self::$P__REQUEST => $request));
	}
}
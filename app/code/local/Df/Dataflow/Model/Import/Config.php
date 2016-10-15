<?php
class Df_Dataflow_Model_Import_Config extends Df_Core_Model {
	/** @return string */
	public function getDecimalSeparator() {return $this->getParam('decimal_separator', '.');}

	/**
	 * @param string $paramName
	 * @param string|null $defaultValue [optional]
	 * @return string|null
	 */
	public function getParam($paramName, $defaultValue = null) {
		df_param_string($paramName, 0);
		if (!is_null($defaultValue)) {
			df_param_string($defaultValue, 1);
		}


		/** @var string $result */
		$result =
			dfa($this->getParams(), $paramName, $defaultValue)
		;
		if (!is_null($result)) {
			df_result_string($result);
		}
		return $result;
	}

	/** @return array */
	public function getParams() {
		/** @var array $result */
		$result =
				is_null(df_mage()->dataflow()->batch()->getId())
			?
				array()
			:
				df_mage()->dataflow()->batch()->getParams()
		;
		df_result_array($result);
		return $result;
	}


	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Dataflow_Model_Import_Config
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}

	const DATAFLOW_PARAM__STORE = 'store';
	const DATAFLOW_PARAM__PRODUCT_TYPE = 'type';
}
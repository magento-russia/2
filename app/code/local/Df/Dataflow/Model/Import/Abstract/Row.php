<?php
class Df_Dataflow_Model_Import_Abstract_Row extends Df_Core_Model {
	/** @return array */
	public function getAsArray() {return $this->cfg(self::P__ROW_AS_ARRAY);}

	/**
	 * @param string $fieldName
	 * @param bool $isRequired [optional]
	 * @param string|null $default [optional]
	 * @return string|null
	 */
	public function getFieldValue($fieldName, $isRequired = false, $default = null) {
		df_param_string_not_empty($fieldName, 0);
		df_param_boolean($isRequired, 1);
		if (!is_null($default)) {
			df_param_string($default, 2);
		}
		/** @var string|null $result */
		$result = dfa($this->getAsArray(), $fieldName, $default);
		if ($isRequired && is_null($result)) {
			$this->error(new Df_Dataflow_Exception_Import_RequiredValueIsAbsent(
				$fieldName, $this->getOrdering()
			));
		}
		if (!is_null($result)) {
			if (is_bool($result)) {
				// true => '1'
				// false => '0'
				$result = strval((int)($result));
			}
			else if (is_int($result)) {
				$result = strval($result);
			}
			else if (is_float($result)) {
				$result =
					number_format(
						$result
						,$decimals = 4
						,$dec_point = $this->getConfig()->getDecimalSeparator()
						,$thousands_sep = ''
					)
				;
			}
			else if (!df_check_string($result)) {
				$this->error(
					'Значение поля «%s» должно быть строкой, числом или логического типа,'
					.' однако получено значение типа «%s».'
					,$fieldName
					,gettype($result)
				);
			}
		}
		return $result;
	}

	/** @return int */
	public function getOrdering() {return $this->cfg(self::P__ORDERING);}

	/**
	 * @param string|int|float $value
	 * @return float
	 */
	public function parseAsNumber($value) {
		/** @var float $result */
		$result = null;
		if (!is_string($value)) {
			$result = rm_float($value);
		}
		else {
			/** @var array $allowedSymbols */
			$allowedSymbols  =
				array(
					'0',1,2,3,4,5,6,7,8,9
					,'-'
					,$this->getConfig()->getDecimalSeparator()
				)
			;
			df_assert_array($allowedSymbols);
			/** @var string $resultAsString */
			$resultAsString = '';
			for ($i = 0; $i < strlen($value); $i ++) {
				if (in_array($value[$i], $allowedSymbols)) {
					$resultAsString .= $value[$i];
				}
			}
			if ('.' !== $this->getConfig()->getDecimalSeparator()) {
				$resultAsString =
					str_replace(
						$this->getConfig()->getDecimalSeparator()
						,'.'
						,$resultAsString
					)
				;
			}
			/** @var float $result */
			$result = rm_float($resultAsString);
		}
		return $result;
	}

	/** @return Df_Dataflow_Model_Import_Config */
	protected function getConfig() {return df_h()->dataflow()->import()->getConfig();}

	/**
	 * @param string|string[]|mixed[]|Df_Dataflow_Exception_Import $arguments
	 * @return void
	 * @throws Df_Core_Exception
	 */
	protected function error($arguments) {
		/** @var Df_Dataflow_Exception_Import $exception */
		if ($arguments instanceof Df_Dataflow_Exception_Import) {
			$exception = $arguments;
		}
		else {
			/** @uses func_get_args() не может быть параметром другой функции */
			$arguments = is_array($arguments) ? $arguments : func_get_args();
			$exception = new Df_Dataflow_Exception_Import(rm_format($arguments));
		}
		$exception->setRow($this);
		df_error($exception);
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__ORDERING, RM_V_INT)
			->_prop(self::P__ROW_AS_ARRAY, RM_V_ARRAY)
		;
	}
	/** @used-by Df_Dataflow_Model_Importer_Row::_construct() */
	const _C = __CLASS__;
	const P__ORDERING = 'ordering';
	const P__ROW_AS_ARRAY = 'rowAsArray';
}
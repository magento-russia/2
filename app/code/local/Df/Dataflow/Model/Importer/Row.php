<?php
abstract class Df_Dataflow_Model_Importer_Row extends Df_Core_Model_Abstract {
	/**
	 * @abctract
	 * @return Df_Dataflow_Model_Importer_Row
	 */
	abstract public function import();

	/**
	 * Возвращает значения параметров импорта, общих для всех строк импортируемых данных.
	 * Как правило, общие параметры используются в качестве параметров по умолчанию.
	 * @return Df_Dataflow_Model_Import_Config
	 */
	protected function getConfig() {
		return df_h()->dataflow()->import()->getConfig();
	}

	/**
	 * Возвращает значение конкретного параметра импорта,
	 * общего для всех строк импортируемых данных.
	 * Как правило, общие параметры используются в качестве параметров по умолчанию.
	 * @param string $paramName
	 * @param string|null $defaultValue[optional]
	 * @return string|null
	 */
	protected function getConfigParam($paramName, $defaultValue = null) {
		df_param_string($paramName, 0);
		if (!is_null($defaultValue)) {
			df_param_string($defaultValue, 1);
		}
		/** @var string|null $result */
		$result = $this->getConfig()->getParam($paramName, $defaultValue);
		if (!is_null($result)) {
			df_result_string($result);
		}
		return $result;
	}

	/** @return Df_Dataflow_Model_Import_Abstract_Row */
	protected function getRow() {return $this->cfg(self::P__ROW);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__ROW, Df_Dataflow_Model_Import_Abstract_Row::_CLASS);
	}
	const _CLASS = __CLASS__;
	const P__ROW = 'row';
}
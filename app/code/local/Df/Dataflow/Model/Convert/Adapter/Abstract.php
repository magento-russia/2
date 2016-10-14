<?php
abstract class Df_Dataflow_Model_Convert_Adapter_Abstract
	extends Mage_Dataflow_Model_Convert_Adapter_Abstract {
	/**
	 * @override
	 * @return Df_Dataflow_Model_Convert_Adapter_Abstract
	 */
	public function load() {return $this;}

	/** @return Df_Dataflow_Model_Convert_Adapter_Abstract */
	public function parse() {
		try {
			/**
			 * Ведение счётчика строк упрощает диагностику сбоев,
			 * потому что мы можем указать администратору номер сбойной строки.
			 * @var int $rowOrdering
			 */
			$rowOrdering = 0;
			foreach ($this->getImportIds() as $importId) {
				/** @var int $importId */
				df_assert_integer($importId);
				$rowOrdering++;
				$this->getBatchImportModel()->load($importId);
				df_assert_eq(
					df_nat0($importId)
					, df_nat0($this->getBatchImportModel()->getId())
					, sprintf('Отсутствует пакет данных №%d.', $importId)
				)
				;
				/** @var array $rowAsArray */
				$rowAsArray = $this->getBatchImportModel()->getBatchData();
				df_assert_array($rowAsArray);
				$this->saveRowInternal($rowAsArray, $rowOrdering);
			}
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e, true);
		}
		return $this;
	}

	/**
	 * @override
	 * @return Df_Dataflow_Model_Convert_Adapter_Abstract
	 */
	public function save() {return $this;}

	/**
	 * Данный метод публичен,
	 * потому что его вызывает Df_Adminhtml_System_Convert_ProfileController на строке 73.
	 * Сам класс данный метод не использует.
	 * @see Df_Adminhtml_System_Convert_ProfileController
	 * @param mixed[] $rowAsArray
	 * @return Df_Dataflow_Model_Convert_Adapter_Abstract
	 */
	public function saveRow(array $rowAsArray) {
		try {
			$this->saveRowInternal($rowAsArray, rm_session()->getData(Df_Dataflow_Const::P__COUNTER));
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e, true);
		}
		return $this;
	}

	/**
	 * @param mixed[] $rowAsArray
	 * @param int $rowOrdering
	 * @return Df_Dataflow_Model_Convert_Adapter_Abstract
	 */
	abstract protected function saveRowInternal(array $rowAsArray, $rowOrdering);
	/**
	 * Возвращает значения параметров импорта, общих для всех строк импортируемых данных.
	 * Как правило, общие параметры используются в качестве параметров по умолчанию.
	 * @return Df_Dataflow_Model_Import_Config
	 */
	protected function getConfig() {return df_h()->dataflow()->import()->getConfig();}
	/**
	 * Возвращает значение конкретного параметра импорта,
	 * общего для всех строк импортируемых данных.
	 * Как правило, общие параметры используются в качестве параметров по умолчанию.
	 * @param string $paramName
	 * @param string|null $defaultValue [optional]
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

	/** @return Mage_Dataflow_Model_Batch_Import */
	private function getBatchImportModel() {return df_mage()->dataflow()->batch()->getBatchImportModel();}

	/** @return int[] */
	private function getImportIds() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getBatchImportModel()->getIdCollection();
		}
		return $this->{__METHOD__};
	}
}
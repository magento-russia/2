<?php
abstract class Df_Dataflow_Model_Convert_Mapper_Abstract
	extends Mage_Dataflow_Model_Convert_Mapper_Abstract {
	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getFeatureCode();

	/**
	 * @abstract
	 * @param array $row
	 * @return void
	 */
	abstract protected function processRow(array $row);

	/** @return Mage_Dataflow_Model_Batch */
	public function getBatchModel() {
		if (is_null($this->_batch)) {
			/** @var Mage_Dataflow_Model_Batch $result */
			$result = df_mage()->dataflow()->batch();
			if (!$result->getData(Df_Dataflow_Model_Batch::P__ADAPTER)) {
				/** @var string|null $adapterClassMf */
				$adapterClassMf = $this->getVar(self::VAR_ADAPTER);
				$result->setParams($this->getVars());
				if (!is_null($adapterClassMf)) {
					df_assert_string($adapterClassMf);
					$result->setData(Df_Dataflow_Model_Batch::P__ADAPTER, $adapterClassMf);
				}
				$result->save();
			}
			$this->_batch = $result;
		}
		return $this->_batch;
	}
	/** @var Mage_Dataflow_Model_Batch */
	protected $_batch;

	/** @return Df_Dataflow_Model_Convert_Mapper_Abstract */
	public function map() {
		if (
				df_module_enabled(Df_Core_Module::LICENSOR)
			&&
				!$this->getFeature()->isEnabled()
		) {
			$this->addException(
				rm_sprintf(
					self::T_LICENSE_NEEDED, $this->getFeature()->getTitle())
					,Mage_Dataflow_Model_Convert_Exception::ERROR
				)
			;
		}
		else {
			foreach ($this->getSourceRowIds() as $sourceRowId) {
				/** @var int $sourceRowModel */
				/** @var Mage_Dataflow_Model_Batch_Abstract $sourceRowModel */
				$sourceRowModel = $this->createSourceRowModel()->load($sourceRowId);
				df_assert($sourceRowModel instanceof Mage_Dataflow_Model_Batch_Abstract);
				/** @var array $sourceRow */
				$sourceRow = $sourceRowModel->getBatchData();
				df_assert_array($sourceRow);
				/** @var array $destinationRow */
				$destinationRow = $this->processRow($sourceRow);
				df_assert_array($destinationRow);
				/** @var Mage_Dataflow_Model_Batch_Abstract $destinationRowModel */
				$destinationRowModel =
					$this->isSourceAndDestinationAreSame()
					? $sourceRowModel
					: $this->createDestinationRowModel()
				;
				df_assert($destinationRowModel instanceof Mage_Dataflow_Model_Batch_Abstract);
				$destinationRowModel
					->setBatchData($destinationRow)
					->setData('status', 2)
				;
				$destinationRowModel->save();
				// update column list (list of field names)
				$this
					->getBatchModel()
					->parseFieldList($destinationRowModel->getBatchData())
				;
			}
		}
		return $this;
	}

	/**
	 * @param array $row
	 * @param string $fieldName
	 * @param bool $isRequired[optional]
	 * @param string|null $defaultValue[optional]

	 * @return string
	 */
	protected function getFieldValue(array $row, $fieldName, $isRequired = false, $defaultValue = null) {
		df_param_array($row, 0);
		df_param_string($fieldName, 1);
		df_param_boolean($isRequired, 2);
		if (!is_null($defaultValue)) {
			df_param_string($defaultValue, 3);
		}
		/** @var string|null $result */
		$result = df_a($row, $fieldName);
		if (!is_null($result)) {
			df_result_string($result);
		}
		if (is_null($result)) {
			df_assert(
				!$isRequired
				,rm_sprintf(
					'В строке импортируемых данных необходимо заполнить поле «%s»'
					,$fieldName
				)
			);
			$result = $defaultValue;
		}
		if (!is_null($result)) {
			df_result_string($result);
		}
		return $result;
	}

	/** @return bool */
	protected function isSourceAndDestinationAreSame() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->getBatchTableAlias(self::DESTINATION) === $this->getBatchTableAlias(self::SOURCE)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return array */
	protected function getSourceRowIds() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getSourceRowModelSingleton()->getIdCollection();
		}
		return $this->{__METHOD__};
	}

	/** @return array */
	protected function getMap() {
		if (!isset($this->{__METHOD__})) {
			/** @var array $result */
			$result = $this->getVar(self::VAR_MAP);
			if (is_null($result)) {
				$result = array();
			}
			df_result_array($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Mage_Dataflow_Model_Batch_Abstract */
	protected function getSourceRowModelSingleton() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->createSourceRowModel();
		}
		return $this->{__METHOD__};
	}

	/** @return Mage_Dataflow_Model_Batch_Abstract */
	protected function createSourceRowModel() {
		return $this->createRowModel($this->getBatchTableAlias(self::SOURCE));
	}

	/** @return Mage_Dataflow_Model_Batch_Abstract */
	protected function createDestinationRowModel() {
		return $this->createRowModel($this->getBatchTableAlias(self::DESTINATION));
	}

	/**
	 * @param string $importOrExport  ("import" or "export")
	 * @return Mage_Dataflow_Model_Batch_Abstract
	 */
	protected function createRowModel($importOrExport) {
		df_param_string($importOrExport, 0);
		/** @var Mage_Dataflow_Model_Batch_Abstract $result */
		$result = df_model(rm_sprintf("dataflow/batch_%s", $importOrExport));
		df_assert($result instanceof Mage_Dataflow_Model_Batch_Abstract);
		$result->setData('batch_id', $this->getBatchModel()->getId());
		return $result;
	}

	/**
	 * @param string $souceOrDestination  ("source" or "destination")
	 * @return string
	 */
	private function getBatchTableAlias($souceOrDestination) {
		df_param_string($souceOrDestination, 0);
		/** @var array $validResults */
		$validResults = array(self::IMPORT, self::EXPORT);
		df_assert_array($validResults);
		/** @var string $paramName */
		$paramName = $this->convertBatchTableTypeToParamName($souceOrDestination);
		df_assert_string($paramName);
		/** @var string $result */
		$result = (string)$this->getVar($paramName);
		if (!$result) {
			df_error('You must specify «%s» parameter in the profile XML', $paramName);
		}
		if (!in_array($result, $validResults)) {
			df_error(
				"Invalid value «%s» for parameter «%s» in the profile XML"
				."\nValid values are: «import» and «export»"
				,$result
				,$paramName
			);
		}
		df_result_string($result);
		return $result;
	}

	/**
	 * @param string $souceOrDestination
	 * @return string
	 */
	private function convertBatchTableTypeToParamName($souceOrDestination) {
		return rm_sprintf(self::DF_TABLE_TYPE_TEMPLATE, $souceOrDestination);
	}

	/** @return Df_Licensor_Model_Feature */
	private function getFeature() {return df_feature($this->getFeatureCode());}

	const DF_TABLE_TYPE_TEMPLATE = 'df-table-%s';
	const VAR_MAP = 'map';
	const VAR_ADAPTER = 'adapter';
	const SOURCE = 'source';
	const DESTINATION = 'destination';
	const IMPORT = 'import';
	const EXPORT = 'export';
	const T_LICENSE_NEEDED = 'Для использования данного профиля импорта/экспорта Вы должны приобрести лицензию на модуль «%s»';
}
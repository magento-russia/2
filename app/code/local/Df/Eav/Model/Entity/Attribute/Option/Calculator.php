<?php
class Df_Eav_Model_Entity_Attribute_Option_Calculator extends Df_Core_Model {
	/** @return array */
	public function calculate() {
		/** @var Mage_Eav_Model_Entity_Attribute_Source_Table $source */
		$source = $this->getAttribute()->getSource();
		df_assert($source instanceof Mage_Eav_Model_Entity_Attribute_Source_Table);
		/**
		 * Похоже, надо сохранять те старые опции, у которых нет идентификаторов из 1С,
		 * потому что они были введены администратором вручную.
		 * ---
		 * Дополнение от 2013-06-19:
		 * Похоже, в некоторых ситуациях надо сохранять вообще ВСЕ СТАРЫЕ ОПЦИИ,
		 * потому что в конкретном сеансе обмена данными
		 * 1С передает интернет-магазину не всё содержимое конкретного справочника,
		 * а лишь те справочные значения, которые участвуют в текущем сеансе обмена данными.
		 * А в следущем сеансе обмена данными (с другими настройками)
		 * могут быть переданы другие значения из того же самого справочника,
		 * но не передаваться переданные ранее.
		 * http://magento-forum.ru/topic/3750/
		 */
		/** @var array $oldValues */
		$oldValues = array();
		/** @var array $oldValuesToPreserve */
		$oldValuesToPreserve = array();
		/** @var array $oldLabels */
		$oldLabels = array();
		/** @var string $updateMode */
		$updateMode = rm_1c_cfg()->referenceLists()->updateMode();
		foreach ($this->getOptionsOld() as $oldOption) {
			/** @var Df_Eav_Model_Entity_Attribute_Option $oldOption */
			/** @var int $value */
			$value = df_nat0($oldOption->getData('option_id'));
			df_assert_integer($value);
			if (!Df_1C_Config_Source_ReferenceListUpdateMode::isNone($updateMode)) {
				// Сохраняем те старые опции, у которых нет идентификаторов из 1С,
				// потому что они были введены администратором вручную.
				if (
						!$oldOption->get1CId()
					||
						Df_1C_Config_Source_ReferenceListUpdateMode::isAll($updateMode)
				) {
					$oldValuesToPreserve[]= $value;
				}
			}
			/** @var string $label */
			$label = $oldOption->getData('default_value');
			df_assert_string($label);
			$oldLabels[]= $label;
			$oldValues[$value] = array($label);
		}
		/** @var int[] $oldValueIds */
		$oldValueIds = array_keys($oldValues);
		df_assert_array($oldValueIds);
		/** @var array $oldMapFromLabelsToValueIds */
		$oldMapFromLabelsToValueIds = array_combine($this->labelsNormalize($oldLabels), $oldValueIds);
		df_assert_array($oldMapFromLabelsToValueIds);
		/** @var array $newLabels */
		$newLabels = $this->extractLabelsFromValues($this->getOptionsValuesNew());
		df_assert_array($newLabels);
		/**
		 * @var array $actualValues
		 */
		$actualValues = $oldValues;
		/** @var array $labelsToAdd */
		$labelsToAdd = $this->labelsDiff($newLabels, $oldLabels);
		df_assert_array($labelsToAdd);
		/** @var int $optionIndex */
		$optionIndex = 0;
		foreach ($labelsToAdd as $labelToAdd) {
			/** @var string $labelToAdd */
			df_assert_string($labelToAdd);
			/** @var string $valueId */
			$valueId = implode('_', array('option', $optionIndex));
			$actualValues[$valueId] = array($labelToAdd);
			$optionIndex++;
		}
		/** @var array $labelsToDelete */
		$labelsToDelete = $this->labelsDiff($oldLabels, $newLabels);
		df_assert_array($labelsToDelete);
		/**
		 * Сначала все старые значения помечаем нулём
		 * @var array(int => 0|1) $actualDelete
		 */
		$actualDelete = array_fill_keys($oldValueIds, 0);
		// ... и лишь затем то, что надо удалить, помечаем единицей
		foreach ($labelsToDelete as $labelToDelete) {
			/** @var string $labelToDelete */
			df_assert_string($labelToDelete);
			/** @var int $valueIdToDelete */
			$valueIdToDelete = dfa($oldMapFromLabelsToValueIds, $this->labelNormalize($labelToDelete));
			df_assert_integer($valueIdToDelete);
			if (
				!(
						/**
						 * Сохраняем те старые опции, которые положено сохранять
						 * в соответствии с указанными администратором настройками.
						 */
						in_array($valueIdToDelete, $oldValuesToPreserve)
					||
						/**
						 * В режим вставки программист указывает параметром
						 * @see Df_Eav_Model_Entity_Attribute_Option_Calculator::P__OPTIONS_VALUES_NEW
						 * не все опции свойства, а лишь новые — те,
						 * которые надо добавить к свойству
						 */
						$this->isModeInsert()
				)
			) {
				$actualDelete[$valueIdToDelete] = 1;
			}
		}
		/** @var array $actualOrders */
		$actualOrders = array();
		/** @var string $actualLabels */
		$actualLabels = $this->extractLabelsFromValues($actualValues);
		df_assert_array($actualLabels);
		/** @var array $actualMapFromLabelsToValueIds */
		$actualMapFromLabelsToValueIds =
			array_combine($this->labelsNormalize($actualLabels), array_keys($actualValues))
		;
		df_assert_array($actualMapFromLabelsToValueIds);
		/** @var array $actualLabelsToSort */
		$actualLabelsToSort = $actualLabels;
		sort($actualLabelsToSort);
		$order = 0;
		foreach ($actualLabelsToSort as $sortedLabel) {
			/** @var string $sortedLabel */
			df_assert_string($sortedLabel);
			/** @var string|int $valueId */
			$valueId = dfa($actualMapFromLabelsToValueIds, $this->labelNormalize($sortedLabel));
			$actualOrders[$valueId] = $order;
			$order++;
		}
		return array('value' => $actualValues, 'order' => $actualOrders, 'delete' => $actualDelete);
	}

	/**
	 * @param array $values
	 * @return array
	 */
	private function extractLabelsFromValues(array $values) {
		/** @var array $result */
		$result = array();
		foreach ($values as $label) {
			/** @var string|array $label */
			if (is_array($label)) {
				$label = dfa($label, 0);
			}
			df_assert_string($label);
			$result[]= $label;
		}
		return $result;
	}

	/** @return Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection|Mage_Eav_Model_Mysql4_Entity_Attribute_Option_Collection */
	private function getOptionsOld() {
		if (!isset($this->{__METHOD__})) {
			/** @var int $attributeId */
			$attributeId = df_nat0($this->getAttribute()->getId());
			/** @var int $storeId */
			$storeId = df_nat0($this->getAttribute()->getDataUsingMethod('store_id'));
			/** @var Df_Eav_Model_Resource_Entity_Attribute_Option_Collection $result */
			$result = Df_Eav_Model_Entity_Attribute_Option::c();
			$result->setPositionOrder('asc');
			$result->setAttributeFilter($attributeId);
			$result->setStoreFilter($storeId);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Mage_Eav_Model_Entity_Attribute */
	private function getAttribute() {return $this->cfg(self::P__ATTRIBUTE);}

	/** @return array */
	private function getOptionsValuesNew() {return $this->cfg(self::P__OPTIONS_VALUES_NEW);}

	/** @return bool */
	private function isModeCaseInsensitive() {
		return $this->cfg(self::P__MODE__CASE_INSENSITIVE, false);
	}

	/** @return bool */
	private function isModeInsert() {return $this->cfg(self::P__MODE__INSERT, false);}

	/**
	 * @param string $label
	 * @return string
	 */
	private function labelNormalize($label) {
		df_param_string($label, 0);
		return $this->isModeCaseInsensitive() ? mb_strtolower($label) : $label;
	}

	/**
	 * @param array $labels1
	 * @param array $labels2
	 * @return string
	 */
	private function labelsDiff(array $labels1, array $labels2) {
		$labels1Normalized = $this->labelsNormalize($labels1);
		/** @var array $map */
		$map = array_combine($labels1Normalized, $labels1);
		/** @var array $diff */
		$diff = array_diff($labels1Normalized, $this->labelsNormalize($labels2));
		return dfa_select($map, $diff);
	}

	/**
	 * @param string[] $labels
	 * @return string[]
	 */
	private function labelsNormalize(array $labels) {
		/** @uses labelNormalize() */
		return array_map(array($this, 'labelNormalize'), $labels);
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__ATTRIBUTE, 'Mage_Eav_Model_Entity_Attribute')
			->_prop(self::P__MODE__CASE_INSENSITIVE, DF_V_BOOL, false)
			->_prop(self::P__MODE__INSERT, DF_V_BOOL, false)
			->_prop(self::P__OPTIONS_VALUES_NEW, DF_V_ARRAY)
		;
	}
	const _C = __CLASS__;
	const P__ATTRIBUTE = 'attribute';
	const P__MODE__CASE_INSENSITIVE = 'mode__case_insensitive';
	const P__MODE__INSERT = 'mode__insert';
	const P__OPTIONS_VALUES_NEW = 'options_values_new';
	/**
	 * @param Mage_Eav_Model_Entity_Attribute $attribute
	 * @param array $optionsNew
	 * @param bool $isModeInsert [optional]
	 * @param bool $caseInsensitive [optional]
	 * @return array
	 */
	public static function calculateStatic(
		Mage_Eav_Model_Entity_Attribute $attribute
		,array $optionsNew
		,$isModeInsert = false
		,$caseInsensitive = false
	) {
		df_param_boolean($isModeInsert, 2);
		df_param_boolean($caseInsensitive, 3);
		return
			self::i(
				array(
					self::P__ATTRIBUTE => $attribute
					,self::P__MODE__CASE_INSENSITIVE => $caseInsensitive
					,self::P__MODE__INSERT => $isModeInsert
					,self::P__OPTIONS_VALUES_NEW => $optionsNew
				)
			)->calculate()
		;
	}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Eav_Model_Entity_Attribute_Option_Calculator
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}
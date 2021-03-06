<?php
class Df_Eav_Model_Resource_Entity_Attribute_Option_Collection
	extends Mage_Eav_Model_Mysql4_Entity_Attribute_Option_Collection {
	/**
	 * Цель перекрытия —
	 * перевод значений товарных свойств типа «выпадающий список».
	 * @override
	 * @param string $valueKey
	 * @return array
	 */
	public function toOptionArray($valueKey = 'value') {
		return
			rm_loc()->isEnabled()
			? $this->toOptionArrayDf($valueKey)
			: parent::toOptionArray($valueKey)
		;
	}

	/**
	 * @param string $valueKey
	 * @return array(array(string => string))
	 */
	private function toOptionArrayDf($valueKey = 'value') {
		$labelField = 'label';
		/** @var array(array(string => string)) $result */
		$result = parent::toOptionArray($valueKey);
		foreach ($result as &$item) {
			/** @var array(string => string) $item */
			/** @var string $label */
			$label = df_a($item, $labelField);
			if ($label) {
				$item[$labelField] = df_mage()->eavHelper()->__($label);
			}
		}
		return $result;
	}

	/**
	 * Вынуждены сделать этот метод публичным, потому что в Magento CE 1.4.0.1 публичен родительский.
	 * @see Mage_Eav_Model_Mysql4_Entity_Attribute_Option_Collection::_construct()
	 * @override
	 * @return void
	 */
	public function _construct() {
		parent::_construct();
		$this->_init(
			Df_Eav_Model_Entity_Attribute_Option::mf()
			, Df_Eav_Model_Resource_Entity_Attribute_Option::mf()
		);
	}
	const _CLASS = __CLASS__;

	/** @return Df_Eav_Model_Resource_Entity_Attribute_Option_Collection */
	public static function i() {return new self;}
}
<?php
class Df_Core_Model_Resource_Store extends Mage_Core_Model_Mysql4_Store {
	/**
	 * 2014-11-21
	 * Цель перекрытия —
	 * устранение сбоя «Магазин с таким кодом уже существует»
	 * при сохранении административного магазина
	 * в устаревших версиях Magento CE (например, 1.6.1.0).
	 *
	 * Новая реализация метода скопирована реализация Magento CE 1.9.0.1.
	 * Она отличается от реализации метода в Magento CE 1.6.1.0 только в одном месте.
	 * В Magento CE 1.6.1.0:
			if ($object->getId()) {
				$select->where($this->getIdFieldName() . '!=?', $object->getId());
			}
	 * В Magento CE 1.9.0.1:
			if ($object->getId() || $object->getId() === '0') {
				$select->where($this->getIdFieldName() . '!=?', $object->getId());
			}
	 * Реализация из Magento CE 1.9.0.1 позволяет корректно пересохранять
	 * магазин с идентификатором «0» (административный).
	 *
	 * @override
	 * @param Mage_Core_Model_Abstract $object
	 * @return Mage_Core_Model_Resource_Db_Abstract
	 * @throws Mage_Core_Exception
	 */
	protected function _checkUnique(Mage_Core_Model_Abstract $object) {
		$existent = array();
		$fields = $this->getUniqueFields();
		if (!empty($fields)) {
			if (!is_array($fields)) {
				$this->_uniqueFields = array(
					array(
						'field' => $fields,
						'title' => $fields
					)
				);
			}
			$data = new Varien_Object($this->_prepareDataForSave($object));
			$select = $this->_getWriteAdapter()->select()->from($this->getMainTable(), $cols = '*');
			foreach ($fields as $unique) {
				$select->reset(Zend_Db_Select::WHERE);
				if (is_array($unique['field'])) {
					foreach ($unique['field'] as $field) {
						$select->where($field . '=?', trim($data->getData($field)));
					}
				}
				else {
					$select->where($unique['field'] . '=?', trim($data->getData($unique['field'])));
				}
				if ($object->getId() || $object->getId() === '0') {
					$select->where($this->getIdFieldName() . '!=?', $object->getId());
				}
				$test = $this->_getWriteAdapter()->fetchRow($select);
				if ($test) {
					$existent[] = $unique['title'];
				}
			}
		}
		if (!empty($existent)) {
			if (count($existent) == 1 ) {
				$error = Mage::helper('core')->__('%s already exists.', $existent[0]);
			}
			else {
				$error = Mage::helper('core')->__('%s already exist.', implode(', ', $existent));
			}
			Mage::throwException($error);
		}
		return $this;
	}

	const _CLASS = __CLASS__;
	/**
	 * @see Df_Core_Model_Store::_construct()
	 * @see Df_Core_Model_Resource_Store_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf_r(__CLASS__);}
	/** @return Df_Core_Model_Resource_Store */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}
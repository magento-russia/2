<?php
class Df_Core_Model_Resource_Config_Data extends Mage_Core_Model_Mysql4_Config_Data {
	/**
	 * Цель перекрытия:
	 *
	 * 2014-07-11
	 * Решает проблему невозможности установки пустого окончания адресов товарных страниц
	 * для товарных разделов и товаров:
	 * @link http://magento-forum.ru/topic/4083/
	 * @link http://magento-forum.ru/topic/4506/
	 * @link http://magento-forum.ru/topic/4515/
	 *
	 * Когда администратор устанавливает значением опции пустую строку,
	 * то система записывает в БД не пустую строку, а NULL.
	 * Так запрограммировано в методе @see Varien_Db_Adapter_Pdo_Mysql::prepareColumnValue():
			case 'varchar':
			case 'mediumtext':
			case 'text':
			case 'longtext':
				$value  = (string)$value;
				if ($column['NULLABLE'] && $value == '') {
					$value = null;
				}
				break;
	 *
	 * @see Df_Core_Model_Resource_Config::loadToXml()
	 *
	 * @override
	 * @param Mage_Core_Model_Abstract|Df_Core_Model_Config_Data $object
	 * @return array(string => string|int|null|float)
	 */
	protected function _prepareDataForSave(Mage_Core_Model_Abstract $object) {
		/** @var array(string => string|int|null|float) $result */
		$result = parent::_prepareDataForSave($object);
		/**

		 */
		if ('' === $object->getData('value')) {
			$result['value'] = '';
		}
		return $result;
	}

	const _CLASS = __CLASS__;
	/**
	 * @see Df_Core_Model_Config_Data::_construct()
	 * @see Df_Core_Model_Resource_Config_Data_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf_r(__CLASS__);}
	/** @return Df_Core_Model_Resource_Config_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}
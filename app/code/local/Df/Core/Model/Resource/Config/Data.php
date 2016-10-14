<?php
class Df_Core_Model_Resource_Config_Data extends Mage_Core_Model_Mysql4_Config_Data {
	/**
	 * Цель перекрытия:
	 *
	 * 2014-07-11
	 * Решает проблему невозможности установки пустого окончания адресов товарных страниц
	 * для товарных разделов и товаров:
	 * http://magento-forum.ru/topic/4083/
	 * http://magento-forum.ru/topic/4506/
	 * http://magento-forum.ru/topic/4515/
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

	/**
	 * 2015-02-09
	 * Возвращаем объект-одиночку именно таким способом,
	 * потому что наш класс перекрывает посредством <rewrite> системный класс,
	 * и мы хотим, чтобы вызов @see Mage::getResourceSingleton() ядром Magento
	 * возвращал тот же объект, что и наш метод @see s(),
	 * сохраняя тем самым объект одиночкой (это важно, например, для производительности:
	 * сохраняя объект одиночкой — мы сохраняем его кэш между всеми пользователями объекта).
	 * @return Df_Core_Model_Resource_Config_Data
	 */
	public static function s() {return Mage::getResourceSingleton('core/config_data');}
}
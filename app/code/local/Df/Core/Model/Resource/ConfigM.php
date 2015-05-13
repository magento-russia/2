<?php
class Df_Core_Model_Resource_ConfigM extends Mage_Core_Model_Mysql4_Config {
	/**
	 * Цель перекрытия —
	 * улучшение функциональности загрузки настроечных опций.
	 *
	 * @override
	 * @param Mage_Core_Model_Config $xmlConfig
	 * @param string|null $condition [optional]
	 * @return Df_Core_Model_Resource_ConfigM
	 */
	public function loadToXml(Mage_Core_Model_Config $xmlConfig, $condition = null) {
		/**
		 * 2013-09-27
		 * Эта заплатка вызвана историей с модулем «Почта России».
		 * Проверяю я как-то работу оформления заказа, и тут вдруг для «Почты России»:
		 * «Способ доставки недоступен для выбранной страны получения».
		 * Незадолго до возникновения этого сбоя добавлял к системе второй магазин.
		 * Исследовал код.
		 * Настройки «Ограничить область доставки конкретными странами?»
		 * у модуля «Почта России» теперь нет, а раньше была.
		 * Так вот, настройку из интерфейса убрали, а в БД осталась запись:
		 * path: df_shipping/russian-post/frontend__specificcountry
		 * value: null
		 * При этом в секции default файла config.xml модуля «Почта России» написано:
				<df_shipping>
					<russian-post>
						<frontend__sallowspecific>1</frontend__sallowspecific>
						<frontend__specificcountry>RU</frontend__specificcountry>
					</russian-post>
				</df_shipping>
		 * Однако при выполнении
		 * Mage::getStoreConfig('df_shipping/russian-post/frontend__specificcountry)
		 * я получал NULL.
		 * Оказалось, то сначала Magento загружает настройки из файлов config.xml,
		 * а затем из БД.
		 * Так вот, значение NULL из БД перетирает значение «RU» из config.xml.
		 * Мне это показалось неправильным, поэтому добавил нижеследующую заплатку.
		 */
		/**
		 * 2014-07-11
		 * Однако эта заплатка
		 * привела к невозможности установки пустого окончания адресов товарных страниц
		 * для товарных разделов и товаров:
		 * @link http://magento-forum.ru/topic/4083/
		 * @link http://magento-forum.ru/topic/4506/
		 * @link http://magento-forum.ru/topic/4515/
		 * В общем, я так понимаю, заплатка имеет следующие ограничения:
		 * если у настроечной опции значение по умолчанию не пусто,
		 * то заплатка не даёт сделать это значение пустым.
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
		 * Проблема решена методом @see Df_Core_Model_Resource_ConfigM_Data::_prepareDataForSave()
		 */
		if (is_null($condition)) {
			$condition = 'value IS NOT NULL';
		}
		parent::loadToXml($xmlConfig, $condition);
		return $this;
	}
}
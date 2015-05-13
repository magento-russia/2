<?php
/**
 * Этот класс служит основой (родителем) классов-настройщиков прикладных типов товара.
 * Классы-настройщики прикладных типов товара
 * добавляют к прикладному типу товара (параметр self::$P__ATTRIBUTE_SET)
 * некие свойства, требуемые конкретным модулем.
 *
 * ОБРАТИТЕ ВНИМАНИЕ:
 * Мы используем классы-настройщики вместо прямой реализации логики в инсталляторе модуля,
 * потому что логика класса настройщиа нужна нам не только в инсталляторе модуля.
 * В инсталляторе модуля мы можем только настроить те прикладные типы товаров,
 * которые уже присутствуют в системе на момент запуска инсталлятора.
 *
 * Однако нам зачастую нужно настраивать и те прикладные типы товара,
 * которых пока нет на момент запуска инсталлятора, но которые появятся потом.
 * Такую настройку мы делаем в методе
 * @see Df_Catalog_Model_Installer_AttributeSet::runBlankAttributeSetProcessors()
 *
 * На данный момент (2014-09-29) такие классы-настройщики есть у двух модулей:
 *
 * 1) Df_Shipping
 * Класс-настройщик @see Df_Shipping_Model_Processor_AddDimensionsToProductAttributeSet
 * добавляет к прикладному типу товара свойства «длина», «ширина» и «высота».
 *
 * 2) Df_YandexMarket
 * Класс-настройщик @see Df_YandexMarket_Model_Setup_Processor_AttributeSet
 * добавляет к прикладному типу товара свойство «Примечание к товару (sales_notes)»
 * (свойство «Категория Яндекс.Маркета» пока добавляется по старой технологии).
 */
abstract class Df_Core_Model_Setup_AttributeSet extends Df_Core_Model_Abstract {
	/** @return void */
	abstract protected function processInternal();

	/** @return Mage_Eav_Model_Entity_Attribute_Set */
	protected function getAttributeSet() {return $this->cfg(self::$P__ATTRIBUTE_SET);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__ATTRIBUTE_SET, 'Mage_Eav_Model_Entity_Attribute_Set');
	}

	/** @var string */
	private static $P__ATTRIBUTE_SET = 'attribute_set';

	/**
	 * @param string $class
	 * @param Mage_Eav_Model_Entity_Attribute_Set $attributeSet
	 * @return void
	 */
	public static function processByClass($class, Mage_Eav_Model_Entity_Attribute_Set $attributeSet) {
		/** @var Df_Core_Model_Setup_AttributeSet $processor */
		$processor = new $class(array(self::$P__ATTRIBUTE_SET => $attributeSet));
		df_assert($processor instanceof Df_Core_Model_Setup_AttributeSet);
		$processor->processInternal();
	}

	/** @return Df_Catalog_Model_Resource_Installer_Attribute */
	protected static function attribute() {return Df_Catalog_Model_Resource_Installer_Attribute::s();}
}
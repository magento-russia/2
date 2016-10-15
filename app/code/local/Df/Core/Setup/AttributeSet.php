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
 * Класс-настройщик @see Df_YandexMarket_Setup_AttributeSet
 * добавляет к прикладному типу товара свойство «Примечание к товару (sales_notes)»
 * (свойство «Категория Яндекс.Маркета» пока добавляется по старой технологии).
 */
abstract class Df_Core_Setup_AttributeSet extends Df_Core_Model {
	/**
	 * @used-by pc()
	 * @return void
	 */
	abstract protected function _process();

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
	 * @used-by runBlank()
	 * @used-by Df_Shipping_Model_Processor_AddDimensionsToProductAttributeSet::process()
	 * @used-by Df_YandexMarket_Setup_AttributeSet::p()
	 * @param string $class
	 * @param Mage_Eav_Model_Entity_Attribute_Set $attributeSet
	 * @return void
	 */
	public static function pc($class, Mage_Eav_Model_Entity_Attribute_Set $attributeSet) {
		/** @var Df_Core_Setup_AttributeSet $processor */
		$processor = df_ic($class, __CLASS__, array(self::$P__ATTRIBUTE_SET => $attributeSet));
		$processor->_process();
	}

	/**
	 * @used-by Df_Catalog_Model_Installer_AttributeSet::addAttributesDefault()
	 * @param Mage_Eav_Model_Entity_Attribute_Set $attributeSet
	 * @return void
	 */
	public static function runBlank(Mage_Eav_Model_Entity_Attribute_Set $attributeSet) {
		foreach (df_config_a('df/attribute_set_processors') as $class) {
			/** @var string $class */
			self::pc($class, $attributeSet);
		}
	}

	/** @return Df_Catalog_Model_Resource_Installer_Attribute */
	protected static function attribute() {return Df_Catalog_Model_Resource_Installer_Attribute::s();}
}
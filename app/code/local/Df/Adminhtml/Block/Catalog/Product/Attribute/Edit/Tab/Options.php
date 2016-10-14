<?php
class Df_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tab_Options
	extends Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tab_Options {
	/**
	 * 2016-03-29
	 * http://magento-forum.ru/topic/5396/
	 * Администратор варварски повредил свой магазин, удалив системное свойство («вес»).
	 * Причём через интерфейс это сделать было нельзя, и он сделал это через БД.
	 * В такой ситуации при сохранении как-то созданного свойства «вес»
	 * (неужели тоже через БД создавал?), Magento CE ведёт себя некорректно,
	 * и вместо того, чтобы показать администратору сообщение
	 * «The attribute code 'weight' is reserved by system. Please try another attribute code»
	 * падает со сбоем.
	 * Это происходит потому, что сессия содержит метки для свойства,
	 * и код $values[0] = $this->getAttributeObject()->getFrontend()->getLabel();
	 * родительского метода некорректен, потому что
	 * $this->getAttributeObject()->getFrontend()->getLabel()
	 * возвращает не строку, а массив, ключами которого являются витрины, а значениями — метки.
	 * @return array(string => string)
	 */
	public function getLabelValues() {
		$result = parent::getLabelValues();
		/** @var string|array(string => string) $r0 */
		$r0 = df_a($result, 0);
		return is_array($r0) ? $r0 : $result;
	}
}
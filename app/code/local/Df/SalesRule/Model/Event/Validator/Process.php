<?php
/**
 * Cообщение:		«salesrule_validator_process»
 * Источник:		Mage_SalesRule_Model_Validator::process()
 * [code]
		Mage::dispatchEvent('salesrule_validator_process', array(
			'rule'	=> $rule
			,'item'	=> $item
			,'address' => $address
			,'quote' => $quote
			,'qty' => $qty
			,'result' => $result
		));
 * [/code]
 *
 * Назначение:		Обработчик может изменить результат работы ценового правила
 */
class Df_SalesRule_Model_Event_Validator_Process extends Df_Core_Model_Event {
	/**
	 * Адрес данного заказа
	 * @return Df_Sales_Model_Quote_Address
	 */
	public function getAddress() {return $this->getEventParam('address');}

	/**
	 * Текущая графа из заказа
	 * @return Mage_Sales_Model_Quote_Item_Abstract
	 */
	public function getCurrentQuoteItem() {return $this->getEventParam('item');}

	/**
	 * Количество единиц товара, на которое распространяется скидка.
	 * Определяется администратором при редактировании ценового правила.
	 * Если же администратор не определил данное значение, то скидка распространяется
	 * на все единицы товара в заказе
	 *
	 * @see Mage_SalesRule_Model_Validator::_getItemQty()
	 *
	 * [code]
			$qty = $item->getTotalQty();
			return $rule->getDiscountQty() ? min($qty, $rule->getDiscountQty()) : $qty;
	 * [/code]
	 * @return int
	 */
	public function getQty() {return df_nat0($this->getEventParam('qty'));}

	/**
	 * Содержимое заказа
	 * @return Mage_Sales_Model_Quote
	 */
	public function getQuote() {return $this->getEventParam('quote');}

	/**
	 * Возвращает результат стандартной работы ценового правила.
	 * Меняя характеристики данного объекта — мы меняем результат работы ценового правила.
	 * @return Varien_Object
	 */
	public function getResult() {return $this->getObserver()->getData('result');}

	/**
	 * Возвращает текущее ценовое правило
	 * @return Mage_SalesRule_Model_Rule
	 */
	public function getRule() {return $this->getEventParam('rule');}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedEventPrefix() {return 'salesrule_validator_process';}

	/**
	 * @used-by Df_PromoGift_Observer::salesrule_validator_process()
	 * @used-by Df_PromoGift_Model_Handler_SalesRule_Validator_Process_Abstract::getEventClass()
	 */

}
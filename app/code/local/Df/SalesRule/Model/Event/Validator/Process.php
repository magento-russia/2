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
	 * @return Mage_Sales_Model_Quote_Address
	 */
	public function getAddress() {return $this->getEventParam(self::P__ADDRESS);}

	/**
	 * Текущая графа из заказа
	 * @return Mage_Sales_Model_Quote_Item_Abstract
	 */
	public function getCurrentQuoteItem() {return $this->getEventParam(self::P__ITEM);}

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
	public function getQty() {return rm_nat0($this->getEventParam(self::P__QTY));}

	/**
	 * Содержимое заказа
	 * @return Mage_Sales_Model_Quote
	 */
	public function getQuote() {return $this->getEventParam(self::P__QUOTE);}

	/**
	 * Возвращает результат стандартной работы ценового правила.
	 * Меняя характеристики данного объекта — мы меняем результат работы ценового правила.
	 * @return Varien_Object
	 */
	public function getResult() {return $this->getObserver()->getData(self::P__RESULT);}

	/**
	 * Возвращает текущее ценовое правило
	 * @return Mage_SalesRule_Model_Rule
	 */
	public function getRule() {return $this->getEventParam(self::P__RULE);}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedEventPrefix() {return self::EXPECTED_EVENT_PREFIX;}

	const _CLASS = __CLASS__;
	const EXPECTED_EVENT_PREFIX = 'salesrule_validator_process';
	const P__ADDRESS = 'address';
	const P__ITEM = 'item';
	const P__QUOTE = 'quote';
	const P__QTY = 'qty';
	const P__RESULT = 'result';
	const P__RULE = 'rule';
}
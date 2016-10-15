<?php
/** @method Df_SalesRule_Model_Event_Validator_Process getEvent() */
abstract class Df_PromoGift_Model_Handler_SalesRule_Validator_Process_Abstract
	extends Df_Core_Model_Handler {
	/** @return void */
	public function handle() {
		if ($this->isItPromoGiftingRule()) {
			$this->handlePromoGiftingRule();
		}
	}

	/**
	 * @abstract
	 * @return void
	 */
	abstract protected function handlePromoGiftingRule();

	/** @return bool */
	protected function isItPromoGiftingRule() {
		return
				$this->isItPercentOfProductPriceDiscount()
			&&
				(100 == $this->getRule()->getDiscountAmount())
		;
	}

	/** @return bool */
	protected function isItPercentOfProductPriceDiscount() {
		return 'by_percent' === $this->getRule()->getSimpleAction();
	}

	/**
	 * Меняя $result — обработчик может повлиять на работу ценового правила
	 * @return Varien_Object
	 */
	protected function getResult() {return $this->getEvent()->getResult();}

	/** @return Mage_Sales_Model_Quote_Item_Abstract */
	protected function getCurrentQuoteItem() {return $this->getEvent()->getCurrentQuoteItem();}

	/** @return Mage_Sales_Model_Quote */
	protected function getQuote() {return $this->getEvent()->getQuote();}

	/** @return Mage_SalesRule_Model_Rule */
	protected function getRule() {return $this->getEvent()->getRule();}

	/**
	 * Класс события (для валидации события)
	 * @return string
	 */
	protected function getEventClass() {return Df_SalesRule_Model_Event_Validator_Process::class;}
}
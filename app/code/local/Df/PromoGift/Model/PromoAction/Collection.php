<?php
/**
 * Применимые к текущему заказу подарочные акции
 */
class Df_PromoGift_Model_PromoAction_Collection extends Df_Varien_Data_Collection {
	/**
	 * @override
	 * @param bool $printQuery[optional]
	 * @param bool $logQuery[optional]
	 * @return  Varien_Data_Collection
	 */
	public function loadData($printQuery = false, $logQuery = false) {
		if (!$this->isLoaded()) {
			parent::loadData($printQuery, $logQuery);
			$this->_setIsLoaded(true);
			/**
			 * Наполняем коллекцию элементами
			 */
			foreach ($this->getApplicableGiftingRules() as $rule) {
				/** @var Mage_SalesRule_Model_Rule $rule */
				/** @var Df_PromoGift_Model_PromoAction $promoAction */
				$promoAction = Df_PromoGift_Model_PromoAction::i($rule);
				/**
				 * Итак, мы нашли применимые к заказу правила.
				 * Однако из этих правил надо ещё отбраковать правила без товаров.
				 *
				 * Например, правило было создано, а потом подарочный товар закончился на складе.
				 *
				 * В процессе такой проверки мы определяем для каждого правила
				 * множество относящися к нему товаров-подарков.
				 * И эти подарки неплохо бы кешировать.
				 */
				if ($promoAction->hasGifts()) {
					$this->addItem($promoAction);
				}
			}
		}
		return $this;
	}

	/** @return Df_Varien_Data_Collection */
	private function getApplicableGiftingRules() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_PromoGift_Model_Resource_Rule_Collection $result */
			$result = Df_PromoGift_Model_Resource_Rule_Collection::i();
			// Отбраковываем правила, не относящиеся к обрабатываемому сайту $website
			$result->addWebsiteFilter(array( Mage::app()->getWebsite()->getId()));
			// Отбраковываем ещё не начавшиеся правила
			$result->addNotStartedYetRulesExclusionFilter();
			// Отбираем применимые к данному заказу правила
			$this->{__METHOD__} =
				Df_PromoGift_Model_Filter_Rule_Collection_ByCurrentQuote::i()->filter($result)
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getItemClass() {return Df_PromoGift_Model_PromoAction::_CLASS;}

	const _CLASS = __CLASS__;
	/** @return Df_PromoGift_Model_PromoAction_Collection */
	public static function i() {return new self;}
}
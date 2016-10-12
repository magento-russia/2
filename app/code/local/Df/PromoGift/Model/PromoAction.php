<?php
class Df_PromoGift_Model_PromoAction extends Df_Core_Model {
	/** @return int */
	public function getId() {
		$result = (int)$this->getRule()->getId();
		/*************************************
		 * Проверка результата работы метода
		 */
		df_result_integer($result);
		/*************************************/
		return $result;
	}

	/** @return Df_Varien_Data_Collection */
	public function getGifts() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_PromoGift_Model_Resource_Gift_Collection $allGifts */
			$allGifts = Df_PromoGift_Model_Resource_Gift_Collection::i();
			/** @var Df_PromoGift_Model_Filter_Gift_Collection_ByRuleGiven $filter */
			$filter =
				Df_PromoGift_Model_Filter_Gift_Collection_ByRuleGiven::i(
					array(
						Df_PromoGift_Model_Validate_Gift_RelatedToRuleGiven::P__RULE_ID =>
							$this->getRule()->getId()
					)
				)
			;
			/** @var Df_Varien_Data_Collection $result */
			$result = $filter->filter($allGifts);
			/**
			 * Конечно, объекты класса Df_PromoGift_Model_Gift умеют сами загужать
			 * относящиеся к ним модели (правило, товар, сайт),
			 * но если они будут делать это по-отдельности — они создадут много запросов к БД.
			 * Эффективней явно дать им нужные модели.
			 */
			/** @var Mage_Core_Model_Website $website */
			$website = Mage::app()->getWebsite();
			/** @var Df_Catalog_Model_Resource_Product_Collection $products */
			$products = Df_PromoGift_Model_Filter_Gift_Collection_MapToProducts::i()->filter($result);
			foreach ($result as $gift) {
				/** @var Df_PromoGift_Model_Gift $gift */
				$gift->setWebsite($website);
				$gift->setRule($this->getRule());
				$gift->setProduct($products->getItemById($gift->getProductId()));
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Mage_SalesRule_Model_Rule */
	public function getRule() {return $this->cfg(self::P__RULE);}

	/** @return bool */
	public function hasGifts() {return !!$this->getGifts();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__RULE, Df_SalesRule_Const::RULE_CLASS);
	}
	const _CLASS = __CLASS__;
	const P__RULE = 'rule';
	/**
	 * @param Mage_SalesRule_Model_Rule $rule
	 * @return Df_PromoGift_Model_PromoAction
	 */
	public static function i(Mage_SalesRule_Model_Rule $rule) {
		return new self(array(self::P__RULE => $rule));
	}
}
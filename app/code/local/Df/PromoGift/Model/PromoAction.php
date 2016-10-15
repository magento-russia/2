<?php
class Df_PromoGift_Model_PromoAction extends Df_Core_Model {
	/** @return int */
	public function getId() {return (int)$this->getRule()->getId();}

	/** @return Df_Varien_Data_Collection */
	public function getGifts() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_PromoGift_Model_Resource_Gift_Collection $result */
			$result = Df_PromoGift_Model_Gift::c()->addRuleFilter($this->getRule()->getId());
			foreach ($result as $gift) {
				/** @var Df_PromoGift_Model_Gift $gift */
				$gift->setWebsite(df_website());
				$gift->setRule($this->getRule());
				/** @var Df_Catalog_Model_Product $product */
				$product = $result->getProducts()->getItemById($gift->getProductId());
				$gift->setProduct($product);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_PromoGift_Model_Rule */
	public function getRule() {return $this->cfg(self::P__RULE);}

	/** @return bool */
	public function hasGifts() {return !!$this->getGifts();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__RULE, Df_PromoGift_Model_Rule::class);
	}
	/** @used-by Df_PromoGift_Model_PromoAction_Collection::itemClass() */

	const P__RULE = 'rule';
	/**
	 * @param Df_PromoGift_Model_Rule $rule
	 * @return Df_PromoGift_Model_PromoAction
	 */
	public static function i(Df_PromoGift_Model_Rule $rule) {
		return new self(array(self::P__RULE => $rule));
	}
}
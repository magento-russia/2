<?php
/**
 * @method Df_PromoGift_Model_Resource_Gift getResource()
 */
class Df_PromoGift_Model_Gift extends Df_Core_Model_Abstract {
	/** @return Df_Catalog_Model_Product */
	public function getProduct() {
		return
			$this->getParamAsModel(
				self::P__PRODUCT
				,Df_Catalog_Model_Product::_CLASS
			)
		;
	}

	/** @return int */
	public function getProductId() {
		return $this->getModelId(self::P__PRODUCT);
	}

	/** @return Mage_SalesRule_Model_Rule */
	public function getRule() {
		return
			$this->getParamAsModel(
				self::P__RULE
				,Df_SalesRule_Const::RULE_CLASS
			)
		;
	}

	/** @return int */
	public function getRuleId() {
		return $this->getModelId(self::P__RULE);
	}

	/** @return Mage_Core_Model_Website */
	public function getWebsite() {
		return
			$this->getParamAsModel(
				self::P__WEBSITE
				,Df_Core_Model_Website::_CLASS
			)
		;
	}

	/** @return int */
	public function getWebsiteId() {
		return $this->getModelId(self::P__WEBSITE);
	}

	/**
	 * @param Df_Catalog_Model_Product $product
	 * @return Df_PromoGift_Model_Gift
	 */
	public function setProduct(Df_Catalog_Model_Product $product) {
		$this->setData(self::P__PRODUCT, $product);
		return $this;
	}

	/**
	 * @param Mage_SalesRule_Model_Rule $rule
	 * @return Df_PromoGift_Model_Gift
	 */
	public function setRule(Mage_SalesRule_Model_Rule $rule) {
		$this->setData(self::P__RULE, $rule);
		return $this;
	}

	/**
	 * @param Mage_Core_Model_Website $website
	 * @return Df_PromoGift_Model_Gift
	 */
	public function setWebsite(Mage_Core_Model_Website $website) {
		$this->setData(self::P__WEBSITE, $website);
		return $this;
	}

	/**
	 * @override
	 * @return Df_Core_Model_Abstract
	 */
	protected function _beforeSave() {
		foreach ($this->getParamsForSave() as $paramName) {
			/** @var string $paramName */
			$this->prepareModelForSave($paramName);
		}
		parent::_beforeSave();
		return $this;
	}

	/**
	 * @param string $paramName
	 * @return int
	 */
	private function getModelId($paramName) {
		df_param_string($paramName, 0);
		$result = $this[$paramName . self::ID_SUFFIX];
		if (is_null($result)) {
			$model = $this[$paramName];
			/** @var Mage_Core_Model_Abstract $model */
			df_assert($model instanceof Mage_Core_Model_Abstract);
			$result = $model->getId();
			df_assert($result);
			$this[$paramName . self::ID_SUFFIX] = $result;
		}
		df_result_integer($result);
		return $result;
	}

	/**
	 * @param string $paramName
	 * @param string $paramClass
	 * @return Mage_Core_Model_Abstract
	 */
	private function getParamAsModel($paramName, $paramClass) {
		df_param_string($paramName, 0);
		df_param_string($paramClass, 1);
		/** @var Mage_Core_Model_Abstract $result */
		$result = $this->cfg($paramName);
		if (is_null($result)) {
			$result = new $paramClass;
			$entityId = $this->cfg($paramName . self::ID_SUFFIX);
			/** @var int $entityId */
			df_assert(df_check_integer($entityId));
			$result->load($entityId);
			df_assert_eq($entityId, $result->getId());
			$this->setData($paramName, $result);
		}
		return $result;
	}

	/** @return string[] */
	private function getParamsForSave() {
		return
			array(
				self::P__PRODUCT
				,self::P__RULE
				,self::P__WEBSITE
			)
		;
	}

	/**
	 * @param string $paramName
	 * @return Df_PromoGift_Model_Gift
	 */
	private function prepareModelForSave($paramName) {
		df_param_string($paramName, 0);
		/** @var int $modelId */
		$modelId = $this->getModelId($paramName);
		$this[$paramName . self::ID_SUFFIX] = $modelId;
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_PromoGift_Model_Resource_Gift::mf());
		$this
			->_prop(self::P__PRODUCT, Df_Catalog_Model_Product::_CLASS, false)
			->_prop(self::P__RULE, Df_SalesRule_Const::RULE_CLASS, false)
			->_prop(self::P__WEBSITE, Df_Core_Const::WEBSITE_CLASS, false)
		;
	}
	/** @var string */
	protected $_eventObject = 'gift';
	/** @var string */
	protected $_eventPrefix = 'df_promo_gift';

	const _CLASS = __CLASS__;
	const P__ID = 'gift_id';
	const P__PRODUCT = 'product';
	const P__RULE = 'rule';
	const P__WEBSITE = 'website';

	/** @return Df_PromoGift_Model_Resource_Gift_Collection */
	public static function c() {return self::s()->getCollection();}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_PromoGift_Model_Gift
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @static
	 * @param int|string $id
	 * @param string|null $field [optional]
	 * @return Df_PromoGift_Model_Gift
	 */
	public static function ld($id, $field = null) {return df_load(self::i(), $id, $field);}
	/**
	 * @see Df_PromoGift_Model_Resource_Gift_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf(__CLASS__);}
	/** @return Df_PromoGift_Model_Gift */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}
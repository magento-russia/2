<?php
/**
 * @method string|null getDetail()
 */
class Df_Review_Model_Review extends Mage_Review_Model_Review {
	/** @return string */
	public function getDetailAsHtml() {return nl2br(df_escape($this->getDetail()));}

	/** @return Df_Catalog_Model_Product */
	public function getProduct() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_product($this->getEntityPkValue());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param int|null $value [optional]
	 * @return Df_Review_Model_Review
	 */
	public function setCustomerId($value = null) {
		if (!is_null($value)) {
			df_param_integer($value, 0);
		}
		$this->setData(self::P__CUSTOMER_ID, $value);
		return $this;
	}

	/**
	 * @param string $value
	 * @return Df_Review_Model_Review
	 */
	public function setDetail($value) {
		df_param_string($value, 0);
		$this->setData(self::P__DETAIL, $value);
		return $this;
	}

	/**
	 * @param string $value
	 * @return Df_Review_Model_Review
	 */
	public function setNickname($value) {
		df_param_string($value, 0);
		$this->setData(self::P__NICKNAME, $value);
		return $this;
	}

	/**
	 * @param int|null $value [optional]
	 * @return Df_Review_Model_Review
	 */
	public function setStoreId($value = null) {
		if (!is_null($value)) {
			df_param_integer($value, 0);
		}
		$this->setData(self::P__STORE_ID, $value);
		return $this;
	}

	/**
	 * @param int[] $value [optional]
	 * @return Df_Review_Model_Review
	 */
	public function setStores(array $value = array()) {
		$this->setData(self::P__STORES, $value);
		return $this;
	}

	/**
	 * @param string $value
	 * @return Df_Review_Model_Review
	 */
	public function setTitle($value) {
		df_param_string($value, 0);
		$this->setData(self::P__TITLE, $value);
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Review_Model_Resource_Review::mf());
	}
	const _CLASS = __CLASS__;
	const P__CUSTOMER_ID = 'customer_id';
	const P__DETAIL = 'detail';
	const P__NICKNAME = 'nickname';
	const P__STORE_ID = 'store_id';
	const P__STORES = 'stores';
	const P__TITLE = 'title';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Review_Model_Review
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/** @return string */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf(__CLASS__);}
	/** @return Df_Review_Model_Review */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}
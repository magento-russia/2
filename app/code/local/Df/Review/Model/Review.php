<?php
/**
 * @method string|null getDetail()
 * @method int getCustomerId()
 * @method int getStoreId()
 */
class Df_Review_Model_Review extends Mage_Review_Model_Review {
	/** @return string */
	public function getDetailAsHtml() {return df_t()->nl2br(df_e($this->getDetail()));}

	/** @return Df_Catalog_Model_Product */
	public function getProduct() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_product($this->getEntityPkValue());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return Df_Review_Model_Resource_Review_Collection
	 */
	public function getResourceCollection() {return self::c();}

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
	 * @return Df_Review_Model_Resource_Review
	 * 2016-10-14
	 * В родительском классе метод переобъявлен через PHPDoc,
	 * и поэтому среда разработки думает, что он публичен.
	 */
	/** @noinspection PhpHierarchyChecksInspection */
	protected function _getResource() {return Df_Review_Model_Resource_Review::s();}

	/**
	 * @used-by Df_Review_Model_Resource_Review_Collection::__construct()
	 * @used-by Df_Review_Model_Resource_Review_Collection::_construct()
	 */

	const P__CUSTOMER_ID = 'customer_id';
	const P__DETAIL = 'detail';
	const P__NICKNAME = 'nickname';
	const P__STORE_ID = 'store_id';
	const P__STORES = 'stores';
	const P__TITLE = 'title';
	/** @return Df_Review_Model_Resource_Review_Collection */
	public static function c() {return new Df_Review_Model_Resource_Review_Collection;}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Review_Model_Review
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}
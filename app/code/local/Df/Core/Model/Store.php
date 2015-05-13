<?php
class Df_Core_Model_Store extends Df_Core_Model_StoreM {
	/**
	 * @param Mage_Core_Model_Store|int|string|null $store [optional]
	 * @return string
	 */
	public function getDomain($store = null) {
		if (is_null($store)) {
			$store = $this;
			df_assert($store->getId());
		}
		else {
			$store = Mage::app()->getStore($store);
		}
		if (!isset($this->{__METHOD__}[$store->getId()])) {
			/** @var string $storeBaseUriAsText */
			$storeBaseUriAsText = $store->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
			df_assert_string_not_empty($storeBaseUriAsText);
			/** @var Zend_Uri_Http $storeBaseUri */
			$storeBaseUri = Zend_Uri::factory($storeBaseUriAsText);
			df_assert($storeBaseUri instanceof Zend_Uri_Http);
			/** @var string $result */
			$result = $storeBaseUri->getHost();
			df_result_string_not_empty($result);
			$this->{__METHOD__}[$store->getId()] = $result;
		}
		return $this->{__METHOD__}[$store->getId()];
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Core_Model_Resource_Store::mf());
	}

	const _CLASS = __CLASS__;
	const P__NAME = 'name';
	/**
	 * @static
	 * @param bool $loadDefault[optional]
	 * @return Df_Core_Model_Resource_Store_Collection
	 */
	public static function c($loadDefault = false) {
		return self::s()->getCollection()->setLoadDefault($loadDefault);
	}

	/**
	 * @param Mage_Core_Model_Store $store
	 * @return void
	 */
	public static function deleteStatic(Mage_Core_Model_Store $store) {
		df_assert($store->isCanDelete());
		$store->delete();
		Mage::dispatchEvent('store_delete', array('store' => $store));
		Mage::app()->reinitStores();
	}

	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Core_Model_Store
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @see Df_Core_Model_Resource_Store_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf(__CLASS__);}
	/** @return Df_Core_Model_Store */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}
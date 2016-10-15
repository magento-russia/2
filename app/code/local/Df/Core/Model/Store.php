<?php
class Df_Core_Model_Store extends Df_Core_Model_StoreM {
	/**
	 * @param Df_Core_Model_StoreM|int|string|bool|null $store [optional]
	 * @return string
	 */
	public function getDomain($store = null) {
		if (is_null($store)) {
			$store = $this;
			df_assert($store->getId());
		}
		else {
			$store = df_store($store);
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
	 * @return Df_Core_Model_Resource_Store_Collection
	 */
	public function getResourceCollection() {return self::c();}

	/**
	 * @override
	 * @return Df_Core_Model_Resource_Store
	 * 2016-10-14
	 * В родительском классе метод переобъявлен через PHPDoc,
	 * и поэтому среда разработки думает, что он публичен.
	 */
	/** @noinspection PhpHierarchyChecksInspection */
	protected function _getResource() {return Df_Core_Model_Resource_Store::s();}

	/** @used-by Df_Core_Model_Resource_Store_Collection::_construct() */
	const _C = __CLASS__;
	const P__NAME = 'name';
	/**
	 * @static
	 * @param bool $loadDefault [optional]
	 * @return Df_Core_Model_Resource_Store_Collection
	 */
	public static function c($loadDefault = false) {
		/** @var Df_Core_Model_Resource_Store_Collection $result */
		$result = new Df_Core_Model_Resource_Store_Collection;
		$result->setLoadDefault($loadDefault);
		return $result;
	}

	/**
	 * @param Df_Core_Model_StoreM $store
	 * @return void
	 */
	public static function deleteStatic(Df_Core_Model_StoreM $store) {
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
	/** @return Df_Core_Model_Store */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}
<?php
abstract class Df_Core_Model_Settings extends Df_Core_Model {
	/**
	 * @param string $key
	 * @param Mage_Core_Model_Store|int|string|null $store [optional]
	 * @return float
	 */
	public function getFloat($key, $store = null) {
		return rm_float($this->getValueCacheable($key, $store));
	}

	/**
	 * @param string $key
	 * @param Mage_Core_Model_Store|int|string|null $store [optional]
	 * @return int
	 */
	public function getInteger($key, $store = null) {
		return rm_int($this->getValueCacheable($key, $store));
	}

	/**
	 * @param string $key
	 * @param Mage_Core_Model_Store|int|string|null $store [optional]
	 * @return int
	 */
	public function getNatural($key, $store = null) {
		return rm_nat($this->getValueCacheable($key, $store));
	}

	/**
	 * @param string $key
	 * @param Mage_Core_Model_Store|int|string|null $store [optional]
	 * @return int
	 */
	public function getNatural0($key, $store = null) {
		return rm_nat0($this->getValueCacheable($key, $store));
	}

	/**
	 * @param string $key
	 * @param Mage_Core_Model_Store|int|string|null $store [optional]
	 * @return string
	 */
	public function getPassword($key, $store = null) {
		df_param_string_not_empty($key, 0);
		$store = $this->chooseStore($store);
		if (!isset($this->{__METHOD__}[$key][$store->getId()])) {
			$this->{__METHOD__}[$key][$store->getId()] =
				df_mage()->coreHelper()->decrypt($this->getString($key, $store))
			;
		}
		return $this->{__METHOD__}[$key][$store->getId()];
	}


	/**
	 * @param string $key
	 * @param Mage_Core_Model_Store|int|string|null $store [optional]
	 * @return string
	 */
	public function getString($key, $store = null) {
		/** @var string $result */
		$result = $this->getValueCacheable($key, $store);
		df_result_string($result);
		return $result;
	}

	/**
	 * @param string $key
	 * @param Mage_Core_Model_Store|int|string|null $store [optional]
	 * @return string
	 */
	public function getStringNullable($key, $store = null) {
		return df_nts($this->getValueCacheable($key, $store));
	}

	/**
	 * @param string $key
	 * @param Mage_Core_Model_Store|int|string|null $store [optional]
	 * @return bool
	 */
	public function getYesNo($key, $store = null) {
		return rm_bool($this->getValueCacheable($key, $store));
	}

	/** @return string */
	protected function getKeyPrefix() {return '';}
	
	/** @return Mage_Core_Model_Store */
	protected function getStore() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Mage::app()->getStore($this->cfg(self::P__STORE));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param Mage_Core_Model_Store|int|string|null $store [optional]
	 * @return Mage_Core_Model_Store
	 */
	private function chooseStore($store) {
		return is_null($store) ? $this->getStore() : Mage::app()->getStore($store);
	}

	/**
	 * @param string $key
	 * @param Mage_Core_Model_Store|int|string|null $store [optional]
	 * @return mixed|null
	 */
	private function getValueCacheable($key, $store = null) {
		$store = $this->chooseStore($store);
		/** @var int $storeId */
		$storeId = intval($store->getId());
		/**
		 * Как показывает XDebug, стандартный код работает быстрее,
		 * чем напрашивающаяся оптимизация:
				$result = @$this->_valueCacheable[$key][$storeId];
				if (!is_null($result)) {
					$result = rm_n_get($result);
				}
				else {
					$result = Mage::getStoreConfig($this->preprocessKey($key), $store);
					$this->_valueCacheable[$key][$storeId] = rm_n_set($result);
				}
				return $result;
		 */
		if (!isset($this->_valueCacheable[$key][$storeId])) {
			$this->_valueCacheable[$key][$storeId] = rm_n_set(
				Mage::getStoreConfig($this->preprocessKey($key), $store)
			);
		}
		return rm_n_get($this->_valueCacheable[$key][$storeId]);
	}
	/** @var array(string => array(int => mixed)) */
	private $_valueCacheable;

	/**
	 * @param string $key
	 * @return string
	 */
	private function preprocessKey($key) {return $this->getKeyPrefix() . $key;}
	
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__STORE, 'Mage_Core_Model_Store', false);
	}
	const _CLASS = __CLASS__;
	const P__STORE = 'store';
}
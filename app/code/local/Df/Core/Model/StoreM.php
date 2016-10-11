<?php
class Df_Core_Model_StoreM extends Mage_Core_Model_Store {
	/**
	 * Цель перекрытия —
	 * дополнительное кэширование:
	 * ядро Magento кэширует между сеансами лишь некоторые настройки,
	 * игнорируя кэширование остальных.
	 * @see Mage_Core_Model_Store::initConfigCache()
	 * @see Mage_Core_Model_Store::_configCacheBaseNodes
	 * @see Mage_Core_Model_Store::_construct()
	 *
	 * @override
	 * @param string $path
	 * @return string|null
	 */
	public function getConfig($path) {
		/** @var bool $resultInitialized */
		$resultInitialized = false;
		/** @var string|null $result */
		if (Df_Core_Boot::done()) {
			/** @var Df_Core_Model_Cache_Store $cache */
			$cache = Df_Core_Model_Cache_Store::s($this);
			if ($cache) {
				$result = $cache->getConfig($path);
				$resultInitialized = true;
			}
		}
		if (!$resultInitialized) {
			$result = parent::getConfig($path);
		}
		return $result;
	}

	/** @return array(string => string|null) */
	public function getConfigCache() {return $this->_configCache;}

	/**
	 * @param string $path
	 * @return string|null
	 */
	public function getConfigParent($path) {return parent::getConfig($path);}

	/**
	 * @override
	 * @return Df_Core_Model_StoreM
	 */
	public function resetConfig() {
		parent::resetConfig();
		/** @var string|null $result */
		if (Df_Core_Boot::done()) {
			/** @var Df_Core_Model_Cache_Store $cache */
			$cache = Df_Core_Model_Cache_Store::s($this);
			if ($cache) {
				$cache->resetConfig();
			}
		}
		return $this;
	}

	/**
	 * @override
	 * @param string $path
	 * @param mixed $value
	 * @return Df_Core_Model_StoreM
	 */
	public function setConfig($path, $value) {
		parent::setConfig($path, $value);
		/** @var string|null $result */
		if (Df_Core_Boot::done()) {
			/** @var Df_Core_Model_Cache_Store $cache */
			$cache = Df_Core_Model_Cache_Store::s($this);
			if ($cache) {
				$cache->setConfig($path, $value);
			}
		}
		return $this;
	}

	const _CLASS = __CLASS__;
}
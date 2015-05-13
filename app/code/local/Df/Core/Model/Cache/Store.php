<?php
class Df_Core_Model_Cache_Store extends Df_Core_Model_DestructableSingleton {
	/**
	 * @param string $path
	 * @return string|null
	 */
	public function getConfig($path) {
		if (!isset($this->_config[$path])) {
			/** @var string|null $result */
			$result = $this->getStore()->getConfigParent($path);
			$this->_config[$path] = rm_n_set($result);
			$this->markCachedPropertyAsModified('_config');
		}
		return rm_n_get($this->_config[$path]);
	}
	/** @var array(string => string) */
	protected $_config;

	/** @return void */
	public function resetConfig() {
		/**
		 * Если вместо
		 * $this->_config = array();
		 * написать
		 * unset($this->_config);
		 * или
		 * $this->_config = null;
		 * то свойство не будет сохранено в кэше.
		 * Мы же намеренно присваиваем свойству пустой массив, чтобы старый кэш был удалён.
		 */
		$this->_config = array();
		$this->markCachedPropertyAsModified('_config');
	}

	/**
	 * @param string $path
	 * @param mixed $value
	 * @return void
	 */
	public function setConfig($path, $value) {
		$this->_config[$path] = $value;
		$this->markCachedPropertyAsModified('_config');
	}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getCacheKeyParamsAdditional() {
		/**
		 * Здесь мы ещё не можем использовать @see Df_Core_Model_Cache_Store::getStore(),
		 * потому что @see Df_Core_Model_Abstract::cacheLoad() вызывается перед
		 * $this->_prop(self::$P__STORE, Df_Core_Model_StoreM::_CLASS);
		 */
		/** @var Df_Core_Model_StoreM $store */
		$store = $this->_getData(self::$P__STORE);
		return array($store->getCode());
	}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getCacheTagsRm() {
		/** @see Mage_Core_Model_Store::initConfigCache() */
		return array(Mage_Core_Model_Store::CACHE_TAG, Mage_Core_Model_Config::CACHE_TAG);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getCacheTypeRm() {
		/** @see Mage_Core_Model_Store::initConfigCache() */
		return 'config';
	}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getPropertiesToCache() {return array('_config');}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getPropertiesToCacheSimple() {return array('_config');}

	/** @return Df_Core_Model_StoreM */
	private function getStore() {return $this->cfg(self::$P__STORE);}
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__STORE, Df_Core_Model_StoreM::_CLASS);
		/**
		 * Обратите внимание,
		 * что $configCache может быть равно null, если кэш устарел.
		 */
		/** @var array(string => string)|null $configCache */
		$configCache = $this->getStore()->getConfigCache();
		if (isset($this->_config) && $configCache) {
			/**
			 * Добавляем к нашему кэшу кэш ядра, инициализированный как в методе
			 * @see Mage_Core_Model_Store::initConfigCache(),
			 * так и в вызовах Mage_Core_Model_Store::getConfig()
			 * до инициализации нашего класса @see Df_Core_Model_Cache_Store
			 */
			$this->_config = array_merge($this->_config, $configCache);
		}
	}
	/** @var string */
	private static $P__STORE = 'store';

	/**
	 * @param Df_Core_Model_StoreM $store
	 * @return Df_Core_Model_Cache_Store|null
	 */
	public static function s(Df_Core_Model_StoreM $store) {
		/** @var Df_Core_Model_Cache_Store $result */
		/** @var array(string => Df_Core_Model_Cache_Store) $cache */
		static $cache = array();
		/** @var bool $inConstruction */
		static $inConstruction = false;
		/** @var string $storeCode */
		$storeCode = $store->getCode();
		if (isset($cache[$storeCode])) {
			$result = $cache[$storeCode];
		}
		else {
			if ($inConstruction) {
				$result = null;
			}
			else {
				$inConstruction = true;
				$result = new self(array(self::$P__STORE => $store));
				$cache[$storeCode] = $result;
				$inConstruction = false;
			}
		}
		return $result;
	}
}
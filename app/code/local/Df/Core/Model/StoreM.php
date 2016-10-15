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
		return $resultInitialized ? $result : parent::getConfig($path);
	}

	/** @return array(string => string|null) */
	public function getConfigCache() {return $this->_configCache;}

	/**
	 * @param string $path
	 * @return string|null
	 */
	public function getConfigParent($path) {return parent::getConfig($path);}

	/**
	 * 2015-02-06
	 * По аналогии с @see Df_Catalog_Model_Product::getId()
	 * Читайте подробный комментарий в заголовке этого метода.
	 *
	 * 2015-08-03
	 * Обратите внимание,
	 * что из-за того, что полгода назад мы приняли решение
	 * возвращать идентификатор в виде целого числа, а не строки,
	 * нам пришлось добавить дополнительное условие в метод
	 * @see Df_Core_Model_Resource_Db_UniqueChecker::process()
	 * Там было:
	 * if ($model->getId() || $model->getId() === '0') {
	 * стало:
	 * if ($model->getId() || $model->getId() === '0' || 0 === $model->getId()) {
	 *
	 * @override
	 * @return int|null
	 */
	public function getId() {
		/** @var int|null $result */
		$result = parent::getId();
		return is_null($result) ? null : (int)$result;
	}

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
	 * @used-by Df_Core_Observer::core_config_data_save_commit_after()
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

	/**
	 * @used-by Df_Admin_Block_Notifier_DeleteDemoStore::_construct()
	 * @used-by Df_Admin_Config_Extractor::_construct()
	 * @used-by Df_Admin_Config_Backend_Validator_Strategy::_construct()
	 * @used-by Df_Core_Model_Settings::_construct()
	 * @used-by Df_Core_Model_Cache_Store::_construct()
	 * @used-by Df_Dataflow_Model_Category_Path::_construct()
	 * @used-by Df_Dataflow_Model_Importer_Product_Categories::_construct()
	 * @used-by Df_Dataflow_Model_Registry_Collection::_construct()
	 * @used-by Df_Localization_Onetime_Dictionary_Rule_Conditions_Store::getEntityClass()
	 * @used-by Df_Parser_Model_Category_Importer::_construct()
	 * @used-by Df_Sms_Model_Gate::_construct()
	 * @used-by Lamoda_Parser_Model_Importer_Product::_construct()
	 */

}
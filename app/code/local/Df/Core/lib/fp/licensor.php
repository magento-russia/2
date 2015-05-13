<?php
/**
 * @param string $feature
 * @param int|Mage_Core_Model_Store $store[optional]
 * @return bool
 */
function df_enabled($feature, $store = null) {
	/** @var bool $result */
	/** @var bool $inProcess */
	static $inProcess = false;
	if ($inProcess) {
		/**
		 * 2015-01-05
		 * Чтобы не попадать в рекурсию.
		 * Заметил один вариант рекурсии:
		 * 1) @see Df_Localization_Model_Settings_Area::f() приводит в вызову
		 * df_enabled(Df_Core_Feature::LOCALIZATION)
		 * 2) метод @see Df_Licensor_Model_Collection_Store::loadAll()
		 * загружает коллекцию магазинов,
		 * 3) загрузка коллекции магазинов приводит к вызову
		 * @see Df_Eav_Model_Dispatcher::core_collection_abstract_load_after()
		 * 4) и там снова вызывается df_enabled(Df_Core_Feature::LOCALIZATION)
		 */
		$result = true;
	}
	else {
		$inProcess = true;
		try {
			if (!df_module_enabled(Df_Core_Module::LICENSOR)) {
				$result = true;
			}
			else {
				static $cachedSingleton;
				if (!isset($cachedSingleton)) {
					$cachedSingleton = Df_Licensor_Model_CachedSingleton::s();
				}
				$result = $cachedSingleton->isEnabled($feature, $store);
			}
			$inProcess = false;
		}
		catch (Exception $e) {
			$inProcess = false;
			throw $e;
		}
	}
	return $result;
}

/**
 * @param string $code
 * @return Df_Licensor_Model_Feature
 */
function df_feature($code) {
	return df_h()->licensor()->getFeatureByCode($code);
}

/** @return bool */
function df_is_it_my_local_pc() {
	/** @var bool $result  */
	static $result;
	if (!isset($result)) {
		$result =
				('any value' === df_a($_SERVER, 'MAGE_IS_DEVELOPER_MODE'))
			&&
				('Apache/2.4.6 (Win64) PHP/5.6.3' === df_a($_SERVER, 'SERVER_SOFTWARE'))
		;
	}
	return $result;
}

/** @return bool */
function df_is_it_my_sever() {
	return
			('server.magento-pro.ru' === Mage::app()->getRequest()->getHttpHost())
		&&
			('5.9.188.84' === df_a($_SERVER, 'SERVER_ADDR'))
	;
}
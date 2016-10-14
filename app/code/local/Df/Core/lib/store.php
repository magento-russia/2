<?php
/**
 * 2015-02-04
 * Обратите внимание, что вряд ли мы вправе кэшировать результат при парметре $store = null,
 * ведь текущий магазин может меняться.
 * @param Df_Core_Model_StoreM|int|string|bool|null $store [optional]
 * @return Df_Core_Model_StoreM
 * @throws Mage_Core_Model_Store_Exception|Exception
 */
function rm_store($store = null) {
	/** @var Df_Core_Model_StoreM $result */
	$result = $store;
	if (is_null($result)) {
		/** @var Df_Core_Model_StoreM $coreCurrentStore */
		$coreCurrentStore = Mage::app()->getStore();
		/**
		 * 2015-08-10
		 * Доработал алгоритм.
		 * Сначала мы смотрим, не находимся ли мы в административной части,
		 * и нельзя ли при этом узнать текущий магазин из веб-адреса.
		 * По аналогии с @see Mage_Adminhtml_Block_Catalog_Product_Grid::_getStore()
		 */
		if ('admin' === $coreCurrentStore->getCode()) {
			/** @var int|null $storeIdFromRequest */
			$storeIdFromRequest = rm_request('store');
			if ($storeIdFromRequest) {
				$result = Mage::app()->getStore($result);
			}
			/**
			 * 2015-09-20
			 * При единственном магазине
			 * вызываемый ниже метод метод @uses Df_Core_State::getStoreProcessed()
			 * возвратит витрину default, однако при нахождении в административной части
			 * нам нужно вернуть витрину admin.
			 * Например, это нужно, чтобы правильно работала функция @used-by df_is_admin()
			 * Переменная $coreCurrentStore в данной точке содержит витрину admin.
			 *
			 * 2015-11-04
			 * Вообще, напрашивается вопрос: правильно ли,
			 * что при единственном магазине метод @uses Df_Core_State::getStoreProcessed()
			 * возвращает витрину default, а не admin?
			 * Кажется, что неправильно. Возможно, надо поменять.
			 * Но решил это пока не трогать, чтобы не поломать текущее поведение модулей.
			 */
			if (is_null($result) && Mage::app()->isSingleStoreMode()) {
				$result = $coreCurrentStore;
			}
		}
		/**
		 * Теперь смотрим, нельзя ли узнать текущий магазин из веба-адреса в формате РСМ.
		 * Этот формат используют модули 1С:Управление торговлей и Яндекс-Маркет.
		 */
		if (is_null($result)) {
			/**
			 * @uses Df_Core_State::getStoreProcessed()
			 * может вызывать @see rm_store() опосредованно: например, через @see df_assert().
			 * Поэтому нам важно отслеживать рекурсию и не зависнуть.
			 */
			/** @var int $recursionLevel */
			static $recursionLevel = 0;
			if (!$recursionLevel) {
				$recursionLevel++;
				try {
					$result = rm_state()->getStoreProcessed($needThrow = false);
				}
				catch (Exception $e) {
					$recursionLevel--;
					throw $e;
				}
				$recursionLevel--;
			}
		}
		if (is_null($result)) {
			$result = $coreCurrentStore;
		}
	}
	if (!is_object($result)) {
		$result = Mage::app()->getStore($result);
	}
	if (!is_object($result)) {
		/**
		 * 2015-08-14
		 * Такое бывает, например, когда текущий магазин ещё не инициализирован.
		 * @see Mage_Core_Model_App::getStore()
		 * https://github.com/OpenMage/magento-mirror/blob/1.9.2.1/app/code/core/Mage/Core/Model/App.php#L842
		 * @see Mage_Core_Model_App::_currentStore
		 */
		Mage::app()->throwStoreException();
	}
	return $result;
}

/**
 * 2015-03-19
 * @used-by Df_Payment_Config_Area_Service::getTransactionDescription()
 * @param Df_Core_Model_StoreM|int|string|bool|null $store [optional]
 * @return string
 */
function rm_store_domain($store = null) {return rm_store_uri($store)->getHost();}

/**
 * 2015-02-04
 * Обратите внимание, что вряд ли мы вправе кэшировать результат при парметре $store = null,
 * ведь текущий магазин может меняться.
 * @param Df_Core_Model_StoreM|int|string|bool|null $store [optional]
 * @return int
 * @throws Mage_Core_Model_Store_Exception
 */
function rm_store_id($store = null) {return rm_store($store)->getId();}

/**
 * 2015-03-19
 * @used-by rm_store_domain()
 * @param Df_Core_Model_StoreM|int|string|bool|null $store [optional]
 * @return Zend_Uri_Http
 */
function rm_store_uri($store = null) {
	$store = rm_store($store);
	/** @var string $key */
	$key = $store->getId();
	/** @var array(int => Zend_Uri_Http) $cache */
	static $cache;
	if (!isset($cache[$key])) {
		$cache[$key] = Zend_Uri_Http::fromString($store->getBaseUrl(
			Mage_Core_Model_Store::URL_TYPE_WEB
		));
	}
	return $cache[$key];
}

/**
 * 2015-02-04
 * Обратите внимание, что вряд ли мы вправе кэшировать результат при парметре $website = null,
 * ведь текущий сайт может меняться.
 * @param Mage_Core_Model_Website|string|int|bool|null $website [optional]
 * @return Mage_Core_Model_Website
 * @throws Mage_Core_Exception
 */
function rm_website($website = null) {return Mage::app()->getWebsite($website);}

/**
 * 2015-02-04
 * Обратите внимание, что вряд ли мы вправе кэшировать результат при парметре $website = null,
 * ведь текущий сайт может меняться.
 * @param Mage_Core_Model_Website|string|int|bool|null $website [optional]
 * @return int
 * @throws Mage_Core_Exception
 */
function rm_website_id($website = null) {return rm_website($website)->getId();}

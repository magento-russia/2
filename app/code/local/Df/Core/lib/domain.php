<?php
/**
 * 2015-08-14
 * @return Mage_Core_Model_Cache
 */
function rm_cache() {static $r; return $r ? $r : $r = Mage::app()->getCacheInstance();}

/**
 * 2015-02-10
 *
 * @uses Mage_Core_Model_Cache::flush().
 * Вызов Mage::app()->getCacheInstance()->flush()
 * соответствует действию административной кнопки «удалить веь кэш (Mаgento и другой)».
 * Например, кэш храниться в файлах (он там хранится по умолчанию),
 * то вызов Mage::app()->getCacheInstance()->flush() удалит всё содержимое папки с файлами кэша
 * (по умолчанию это папка «var/cache»).
 *
 * Вызов Mage::app()->getCache()->clean()
 * @see Mage_Core_Model_Cache::clean()
 * соответствует действию административной кнопки «удалить весь кэш Mаgento».
 * При этом удаляется кэш модулей Magento CE/EE, однако кэш сторонних модулей,
 * в том числе и кэш Российской сборки Magento, может не удаляться.
 *
 * Поэтому использовать Mage::app()->getCacheInstance()->flush() надёжнее,
 * чем Mage::app()->getCache()->clean().
 *
 * Обратите внимание, что Magento кэширует структуру базы данных.
 * При этом в Magento есть метод @see Varien_Db_Adapter_Pdo_Mysql::resetDdlCache()
 * для удаления кэша либо всей структуры базы данных (при вызове без параметров),
 * либо структуры базы данных конкретной таблицы (при вызове с параметром: именем таблицы).
 * Однако после изменения структуры базы данных опасно ограничиваться только вызовом
 * @see Varien_Db_Adapter_Pdo_Mysql::resetDdlCache(),
 * потому что после изменения структуры базы данных может оказаться устаревшим
 * и кэш объектов слоя бизнес-логики.
 * Поэтому в любом случае надёжней использовать @see rm_cache_clean().
 * Другие методы (частичная очистка кэша) могут быть эффективнее,
 * но используйте их с осторожностью.
 * @return void
 */
function rm_cache_clean() {rm_cache()->flush();}

/** @return bool */
function rm_checkout_ergonomic() {
	return df_cfg()->checkout()->_interface()->needShowAllStepsAtOnce();
}

/** @return bool */
function rm_customer_logged_in() {return rm_session_customer()->isLoggedIn();}

/**
 * @param Df_Core_Destructable $object
 * @return void
 */
function rm_destructable_singleton(Df_Core_Destructable $object) {
	Df_Core_GlobalSingletonDestructor::s()->register($object);
}

/* @return Mage_Core_Model_Design_Package */
function rm_design_package() {return Mage::getSingleton('core/design_package');}

/**
 * @param Exception|string $e
 * @return string
 */
function rm_ets($e) {
	return
		is_string($e)
		? $e
		: ($e instanceof Df_Core_Exception ? $e->getMessageRm() : $e->getMessage())
	;
}

/** @return Df_Core_Model_Units_Length */
function rm_length() {return Df_Core_Model_Units_Length::s();}

/** @return Df_Localization_Settings_Area */
function rm_loc() {static $r; return $r ? $r : $r = Df_Localization_Settings::s()->current();}

/**
 * @param float|int|string $amount
 * @return Df_Core_Model_Money
 */
function rm_money($amount) {return Df_Core_Model_Money::i($amount); }

/**
 * @used-by rm_quote()
 * @return Mage_Checkout_Model_Session
 */
function rm_session_checkout() {return Mage::getSingleton('checkout/session');}

/** @return Mage_Core_Model_Session */
function rm_session_core() {return Mage::getSingleton('core/session');}

/** @return Mage_Customer_Model_Session */
function rm_session_customer() {return Mage::getSingleton('customer/session');}

/** @return Mage_Tax_Helper_Data */
function rm_tax_h() {static $r; return $r ? $r : $r = Mage::helper('tax');}

/** @return Df_Core_Model_Units_Weight */
function rm_weight() {return Df_Core_Model_Units_Weight::s();}



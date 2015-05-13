<?php
/** @return void */
function rm_cache_clean() {
	/**
	 * Mage::app()->getCacheInstance()->flush()
	 * соответствует действию административной кнопки
	 * «удалить веь кэш (Mаgento и другой)»
	 * или, другими словами, соответствует удалению папки var/cache.
	 *
	 * Mage::app()->getCache()->clean();
	 * соответствует действию административной кнопки
	 * «удалить весь кэш Mаgento».
	 *
	 * Надёжней сносить сразу всё :-)
	 */
	Mage::app()->getCacheInstance()->flush();
}

/** @return bool */
function rm_checkout_ergonomic() {
	return df_cfg()->checkout()->_interface()->needShowAllStepsAtOnce();
}

/** @return Df_Directory_Helper_Currency */
function rm_currency() {return df_h()->directory()->currency();}

/**
 * @param Df_Core_Destructable $object
 * @return void
 */
function rm_destructable_singleton(Df_Core_Destructable $object) {
	Df_Core_Model_GlobalSingletonDestructor::s()->register($object);
}

/* @return Mage_Core_Model_Design_Package */
function rm_design_package() {return Mage::getSingleton('core/design_package');}

/**
 * Обновляет глобальный кэш EAV.
 * Это нужно, например, при добавлении новых свойств к прикладным типам товаров.
 * @param bool $reindexFlat [optional]
 * @return void
 */
function rm_eav_reset($reindexFlat = true) {
	Mage::unregister('_singleton/eav/config');
	Df_Eav_Model_Cache::s()->clean();
	if ($reindexFlat && !df_h()->eav()->isPacketUpdate()) {
		Df_Catalog_Model_Product::reindexFlat();
	}
}

/** @return int */
function rm_eav_id_product() {
	static $r; return $r ? $r : $r = Df_Eav_Model_Entity::product()->getTypeId();
}

/**
 * @param Exception $e
 * @return string
 */
function rm_ets(Exception $e) {
	return ($e instanceof Df_Core_Exception) ? $e->getMessageRm() : $e->getMessage();
}

/** @return Df_Localization_Model_Settings_Area */
function rm_loc() {static $r; return $r ? $r : $r = Df_Localization_Model_Settings::s()->current();}

/* @return Mage_Checkout_Model_Session */
function rm_session_checkout() {return Mage::getSingleton('checkout/session');}

/* @return Mage_Core_Model_Session */
function rm_session_core() {return Mage::getSingleton('core/session');}

/* @return Mage_Customer_Model_Session */
function rm_session_customer() {return Mage::getSingleton('customer/session');}

/** @return Df_Core_Model_State */
function rm_state() {
	// метод реализован именно таким способом ради ускорения
	static $r; return $r ? $r : $r = Df_Core_Model_State::s();
}

/**
 * @param mixed[]|string $text
 * @param string $moduleName
 * @return Df_Localization_Helper_Translation
 */
function rm_translate($text, $moduleName) {
	return Df_Localization_Helper_Translation::s()->translateByModule($text, $moduleName);
}

/* @return Df_Localization_Helper_Translation */
function rm_translator() {return Df_Localization_Helper_Translation::s();}



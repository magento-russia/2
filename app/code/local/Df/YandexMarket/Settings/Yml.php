<?php
namespace Df\YandexMarket\Settings;
abstract class Yml extends \Df_Core_Model_Settings {
	/**
	 * @override
	 * @see \Df_Core_Model_Settings::store()
	 * @used-by \Df_Core_Model_Settings::_construct()
	 * @used-by \Df\YandexMarket\Settings\Other::getDomain()
	 * @return \Df_Core_Model_StoreM
	 */
	protected function store() {return dfc($this, function() {return
		// Намеренно не возбуждаем исключительную ситуацию,
		// потому что настройки мы можем извлекать не только в процессе формирования файла YML,
		// а в самых разных ситуациях (например, при подсказке администратору
		//категории товарного предложения или при заполнении покупателем адреса).
		df_state()->getStoreProcessed($needThrow = false) ?: parent::store()
	;});}
}
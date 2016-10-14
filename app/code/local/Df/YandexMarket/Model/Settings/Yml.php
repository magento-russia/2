<?php
abstract class Df_YandexMarket_Model_Settings_Yml extends Df_Core_Model_Settings {
	/**
	 * @override
	 * @see Df_Core_Model_Settings::store()
	 * @used-by Df_Core_Model_Settings::_construct()
	 * @used-by Df_YandexMarket_Model_Settings_Other::getDomain()
	 * @return Df_Core_Model_StoreM
	 */
	protected function store() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Core_Model_StoreM $result */
			/**
			 * Намеренно не возбуждаем исключительную ситуацию,
			 * потому что настройки мы можем извлекать не только в процессе формирования файла YML,
			 * а в самых разных ситуациях
			 * (например, при подсказке администратору категории товарного предложения
			 * или при заполнении покупателем адреса).
			 */
			$result = rm_state()->getStoreProcessed($needThrow = false);
			if (!$result) {
				$result = parent::store();
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}
}
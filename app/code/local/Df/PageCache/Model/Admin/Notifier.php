<?php
/**
 * К сожалению, мы не может настроить полностраничное кэширование автоматически
 * перекрытием класса @see Mage_Core_Model_Cache,
 * потому что этот класс инициализируется ДО Российской сборки Magento
 * и перекрыть его в Российской сборке Magento нельзя.
 */
class Df_PageCache_Model_Admin_Notifier extends Df_Admin_Model_Notifier_Settings {
	/**
	 * @override
	 * @see Df_Admin_Model_Notifier::messageTemplate()
	 * @return string
	 */
	protected function messageTemplate() {return
		'Настройте [[полностраничное кэширование]] — '
		. 'это значительно ускорит Ваш интернет-магазин.'
	;}

	/**
	 * @override
	 * @return string
	 */
	protected function getUrlHelp() {return 'http://magento-forum.ru/topic/1953/';}

	/**
	 * @override
	 * @param Df_Core_Model_StoreM $store
	 * @return bool
	 */
	protected function isStoreAffected(Df_Core_Model_StoreM $store) {
		return !$this->isProcessorSpecifiedInConfigXml();
	}

	/** @return bool */
	private function isProcessorSpecifiedInConfigXml() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
					'Df_PageCache_Model_Processor'
				===
					df_leaf_s(df_config_node('global/cache/request_processors/rm'))
			;
		}
		return $this->{__METHOD__};
	}
}
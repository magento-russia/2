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
	 * @return string
	 */
	protected function getMessageTemplate() {
		return 'Настройте [[полностраничное кэширование]] — '
		. 'это значительно ускорит Ваш интернет-магазин.';
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getUrlHelp() {return 'http://magento-forum.ru/topic/1953/';}

	/**
	 * @override
	 * @param Mage_Core_Model_Store $store
	 * @return bool
	 */
	protected function isStoreAffected(Mage_Core_Model_Store $store) {
		return !$this->isProcessorSpecifiedInConfigXml();
	}

	/** @return bool */
	private function isProcessorSpecifiedInConfigXml() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $processorClass */
			$processorClass = (string)Mage::getConfig()->getNode('global/cache/request_processors/rm');
			$this->{__METHOD__} = 'Df_PageCache_Model_Processor' === $processorClass;
		}
		return $this->{__METHOD__};
	}
}
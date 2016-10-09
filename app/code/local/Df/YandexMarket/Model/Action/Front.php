<?php
class Df_YandexMarket_Model_Action_Front extends Df_Core_Model_Controller_Action {
	/**
	 * @override
	 * @return Df_Core_Model_Controller_Action
	 */
	protected function checkAccessRights() {
		if (!df_enabled(Df_Core_Feature::YANDEX_MARKET, rm_state()->getStoreProcessed())) {
			df_error(
				'У маг' . 'азина отсутс' . 'твует лицен' . 'зия на использ'
				. 'ование мод' . 'уля «Янд' . 'екс.Мар' . 'кет»'
			);
		}
		if (!df_cfg()->yandexMarket()->general()->isEnabled()) {
			df_error('Модуль «Яндекс.Маркет отключен в административной части магазина');
		}
		return $this;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function generateResponseBody() {return $this->getDocument()->getXml();}

	/**
	 * @override
	 * @return string
	 */
	protected function getContentType() {
		return
			$this->getDocument()->hasEncodingWindows1251()
			? Df_Core_Const::CONTENT_TYPE__XML__WINDOWS_1251
			: Df_Core_Const::CONTENT_TYPE__XML__UTF_8
		;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getResponseLogFileName() {return 'yandex.market.xml';}

	/**
	 * @override
	 * @return bool
	 */
	protected function needBenchmark() {return df_is_it_my_local_pc();}

	/**
	 * @override
	 * @return bool
	 */
	protected function needLogResponse() {return df_is_it_my_local_pc();}

	/** @return Df_YandexMarket_Model_Yml_Document */
	private function getDocument() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_YandexMarket_Model_Yml_Document::i(
				Df_YandexMarket_Product_Exporter::i()->getResult()
			);
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param Df_YandexMarket_YmlController $controller
	 * @return Df_YandexMarket_Model_Action_Front
	 */
	public static function i(Df_YandexMarket_YmlController $controller) {
		return new self(array(self::P__CONTROLLER => $controller));
	}
}
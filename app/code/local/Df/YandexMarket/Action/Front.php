<?php
class Df_YandexMarket_Action_Front extends Df_YandexMarket_Action {
	/**
	 * @override
	 * @see Df_Core_Model_Action::generateResponseBody()
	 * @used-by Df_Core_Model_Action::getResponseBody()
	 * @return string
	 */
	protected function generateResponseBody() {return $this->getDocument()->getXml();}

	/**
	 * @override
	 * @see Df_Core_Model_Action::getContentType()
	 * @used-by Df_Core_Model_Action::getResponseLogFileExtension()
	 * @used-by Df_Core_Model_Action::processPrepare()
	 * @return string
	 */
	protected function getContentType() {
		return
			$this->getDocument()->hasEncodingWindows1251()
			? 'application/xml; charset=windows-1251'
			: self::$CONTENT_TYPE__XML__UTF_8
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
	protected function needBenchmark() {return df_my_local();}

	/**
	 * @override
	 * @see Df_Core_Model_Action::needLogResponse()
	 * @used-by Df_Core_Model_Action::processFinish()
	 * @return bool
	 */
	protected function needLogResponse() {return df_my_local();}

	/**
	 * @override
	 * @see Df_Core_Model_Action::store()
	 * @used-by Df_Core_Model_Action::checkAccessRights()
	 * @used-by Df_Core_Model_Action::getStoreConfig()
	 * @return Df_Core_Model_StoreM
	 */
	protected function store() {return df_state()->getStoreProcessed();}

	/** @return Df_YandexMarket_Yml_Document */
	private function getDocument() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_YandexMarket_Yml_Document::i(
				Df_Catalog_Model_Product_Exporter::i(array(
					Df_Catalog_Model_Product_Exporter::P__RULE =>
						df_cfg()->yandexMarket()->products()->getRule()
					,Df_Catalog_Model_Product_Exporter::P__ADDITIONAL_ATTRIBUTES => array(
						Df_YandexMarket_Const::ATTRIBUTE__CATEGORY
						,Df_YandexMarket_Const::ATTRIBUTE__SALES_NOTES
					)
					,Df_Catalog_Model_Product_Exporter::P__LIMIT =>
							df_cfg()->yandexMarket()->diagnostics()->isEnabled()
						&&
							df_cfg()->yandexMarket()->diagnostics()->needLimit()
						? df_cfg()->yandexMarket()->diagnostics()->getLimit()
						: 0
					,Df_Catalog_Model_Product_Exporter::P__NEED_REMOVE_NOT_SALABLE => true
					,Df_Catalog_Model_Product_Exporter::P__NEED_REMOVE_OUT_OF_STOCK => true
				))->getResult()
			);
		}
		return $this->{__METHOD__};
	}
}
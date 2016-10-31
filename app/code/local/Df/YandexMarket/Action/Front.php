<?php
namespace Df\YandexMarket\Action;
use Df_Catalog_Model_Product_Exporter as E;
use Df\YandexMarket\ConstT as C;
use Df\YandexMarket\Settings as S;
use Df\YandexMarket\Yml\Document as Document;
class Front extends \Df\YandexMarket\Action {
	/**
	 * @override
	 * @see Df_Core_Model_Action::generateResponseBody()
	 * @used-by Df_Core_Model_Action::responseBody()
	 * @return string
	 */
	protected function generateResponseBody() {return $this->getDocument()->getXml();}

	/**
	 * @override
	 * @see Df_Core_Model_Action::contentType()
	 * @used-by Df_Core_Model_Action::getResponseLogFileExtension()
	 * @used-by Df_Core_Model_Action::processPrepare()
	 * @return string
	 */
	protected function contentType() {return
		$this->getDocument()->hasEncodingWindows1251()
		? 'application/xml; charset=windows-1251'
		: self::$CONTENT_TYPE__XML__UTF_8
	;}

	/**
	 * @override
	 * @return string
	 */
	protected function responseLogName() {return 'yandex.market.xml';}

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
	 * @return \Df_Core_Model_StoreM
	 */
	protected function store() {return df_state()->getStoreProcessed();}

	/** @return Document */
	private function getDocument() {return dfc($this, function() {return
		Document::i(E::i([
			E::P__RULE => S::s()->products()->getRule()
			,E::P__ADDITIONAL_ATTRIBUTES =>[C::ATTRIBUTE__CATEGORY, C::ATTRIBUTE__SALES_NOTES]
			,E::P__LIMIT =>
				S::s()->diagnostics()->isEnabled() && S::s()->diagnostics()->needLimit()
				? S::s()->diagnostics()->getLimit()
				: 0
			,E::P__NEED_REMOVE_NOT_SALABLE => true
			,E::P__NEED_REMOVE_OUT_OF_STOCK => true
		])->getResult())
	;});}
}
<?php
abstract class Df_Core_Xml_Generator_Part extends Df_Core_Model {
	/** @return array(string => mixed) */
	abstract public function getResult();

	/** @return bool */
	public function isEligible() {return true;}

	/**
	 * @param float $amountInBaseCurrency
	 * @return float
	 */
	protected function convertMoneyToExportCurrency($amountInBaseCurrency) {
		return $this->getDocument()->convertMoneyToExportCurrency($amountInBaseCurrency);
	}

	/** @return Df_Core_Xml_Generator_Document */
	protected function getDocument() {return $this->cfg(self::$P__DOCUMENT);}

	/** @return Df_Directory_Model_Currency */
	protected function getExportCurrency() {return $this->getDocument()->getExportCurrency();}

	/** @return string */
	protected function getOperationNameInPrepositionalCase() {
		return $this->getDocument()->getOperationNameInPrepositionalCase();
	}

	/**
	 * @param string $message
	 * @return void
	 */
	protected function log($message) {
		/** @var mixed[] $arguments */
		$arguments = func_get_args();
		$this->getDocument()->log(rm_format($arguments));
	}

	/**
	 * @param string $message
	 * @return void
	 */
	protected function notify($message) {
		/** @var mixed[] $arguments */
		$arguments = func_get_args();
		$this->getDocument()->notify(rm_format($arguments));
	}

	/**
	 * @param string $url
	 * @return string
	 */
	protected function preprocessUrl($url) {return $this->getDocument()->preprocessUrl($url);}

	/**
	 * @used-by Df_1C_Cml2_Export_Processor_Catalog_Attribute_Real::getВариантыЗначений_SourceTable()
	 * @used-by Df_Catalog_Model_XmlExport_Product::removeRootCategoryId()
	 * @return Df_Core_Model_StoreM
	 */
	protected function store() {return $this->getDocument()->store();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__DOCUMENT, Df_Core_Xml_Generator_Document::_C);
	}
	const _C = __CLASS__;
	/** @var string */
	protected static $P__DOCUMENT = 'document';
}
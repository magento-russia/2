<?php
/**
 * Экспорт товаров из интернет-магазина в 1С:Управление торговлей
 * @link http://1c.1c-bitrix.ru/blog/blog1c/catalog_import.php
 */
class Df_1C_Model_Cml2_Action_Catalog_Export extends Df_1C_Model_Cml2_Action_GenericExport {
	/**
	 * @override
	 * @return Df_1C_Model_Cml2_SimpleXml_Generator_Document
	 */
	protected function createDocument() {return Df_1C_Model_Cml2_SimpleXml_Generator_Document::i();}

	/**
	 * @override
	 * @return void
	 * @throws Exception
	 */
	protected function processInternal() {
		if (!$this->getFlag()) {
			parent::processInternal();
			$this->setFlag(true);
		}
		else {
			try {
				$this->setResponseBodyAsArrayOfStrings(array(
					$this->implodeResponseParam('finished', 'yes')
				));
				$this->setFlag(false);
			}
			catch (Exception $e) {
				$this->setFlag(false);
				throw $e;
			}
		}
	}

	/** @return bool */
	private function getFlag() {
		$this->session()->begin();
		/** @var bool $result */
		$result = $this->session()->getFlag_catalogHasJustBeenExported();
		$this->session()->end();
		return $result;
	}

	/** @return Df_1C_Model_Cml2_Session_ByIp */
	private function session() {return Df_1C_Model_Cml2_Session_ByIp::s();}

	/**
	 * @param bool $value
	 * @return void
	 */
	private function setFlag($value) {
		$this->session()->begin();
		$this->session()->setFlag_catalogHasJustBeenExported($value);
		$this->session()->end();
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_1C_Model_Cml2_Action_Catalog_Export
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}
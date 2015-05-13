<?php
abstract class Df_1C_Model_Cml2_Action_GenericExport extends Df_1C_Model_Cml2_Action {
	/** @return Df_1C_Model_Cml2_SimpleXml_Generator_Document */
	abstract protected function createDocument();

	/**
	 * @override
	 * @return void
	 */
	protected function processInternal() {
		rm_response_content_type($this->getResponse(), 'application/xml; charset=utf-8');
		$this->logDocument();
		$this->getResponse()->setBody($this->getDocument()->getXml());
	}

	/** @return Df_1C_Model_Cml2_SimpleXml_Generator_Document */
	private function createDocumentFake() {
		return Df_1C_Model_Cml2_SimpleXml_Generator_Document::i();
	}

	/** @return Df_1C_Model_Cml2_SimpleXml_Generator_Document */
	private function getDocument() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				true // false — для некоторых сценариев тестирования
				? $this->getDocumentReal()
				: $this->createDocumentFake()
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_1C_Model_Cml2_SimpleXml_Generator_Document */
	private function getDocumentReal() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =  $this->createDocument();
		}
		return $this->{__METHOD__};
	}

	/**
	 * Имя файла, в котором будет сохранена копия отправленного в 1С документа.
	 * Эти копии сохраняются в папке var/log.
	 * Формируется по имени класса так:
	 * @see Df_1C_Model_Cml2_Action_Catalog_Export => «export.catalog-{date}-{time}.xml»
	 * @see Df_1C_Model_Cml2_Action_Orders_Export => «export.orders-{date}-{time}.xml»
	 * @return string
	 */
	private function getSavedCopyFileName() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Core_Model_Fs_GetNotUsedFileName::i(
					Df_1C_Model_Cml2_FileSystem::s()->getBaseDir()
					,
					// Df_1C_Model_Cml2_Action_Catalog_Export => 'export.catalog'
					implode('.', array_map(
						array(df_text(), 'lcfirst')
						// Df_1C_Model_Cml2_Action_Catalog_Export => array('Export', 'Catalog')
						, array_reverse(
							// Df_1C_Model_Cml2_Action_Catalog_Export => array('Catalog', 'Export')
							array_slice(explode('_', get_class($this)), -2)
						)
					)) . '-{date}-{time}.xml'
					,array()
					,array(Df_Core_Model_Fs_GetNotUsedFileName::P__DATE_PARTS_SEPARATOR => '.')
				)->getResult()
			;
		}
		return $this->{__METHOD__};
	}

	/** @return void */
	private function logDocument() {
		if (df_is_it_my_local_pc()) {
			rm_file_put_contents($this->getSavedCopyFileName(), $this->getDocumentReal()->getXml());
		}
	}
}
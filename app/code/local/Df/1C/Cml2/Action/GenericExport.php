<?php
abstract class Df_1C_Cml2_Action_GenericExport extends Df_1C_Cml2_Action {
	/**
	 * @used-by getDocument()
	 * @return \Df\Xml\Generator\Document
	 */
	abstract protected function createDocument();

	/**
	 * @override
	 * @see Df_Core_Model_Action::generateResponseBody()
	 * @used-by Df_Core_Model_Action::responseBody()
	 * @return string
	 */
	protected function generateResponseBody() {
		return $this->getDocument()->getXml($reformat = $this->needLogResponse());
	}

	/**
	 * @override
	 * @see Df_Core_Model_Action::generateResponseBodyFake()
	 * @used-by Df_Core_Model_Action::responseBody()
	 * @return string
	 */
	protected function generateResponseBodyFake() {
		/** @var \Df\Xml\Generator\Document $document */
		$document = \Df\Xml\Generator\Document::_i();
		$document->setMixin(Df_1C_Cml2_Export_DocumentMixin::class);
		return $document->getXml();
	}

	/**
	 * @override
	 * @see Df_Core_Model_Action::contentType()
	 * @used-by Df_Core_Model_Action::getResponseLogFileExtension()
	 * @used-by Df_Core_Model_Action::processPrepare()
	 * @return string
	 */
	protected function contentType() {return 'UTF-8';}

	/**
	 * @override
	 * @see Df_Core_Model_Action::needLogResponse()
	 * @used-by Df_Core_Model_Action::processFinish()
	 * @return bool
	 */
	protected function needLogResponse() {return df_my_local();}

	/** @return \Df\Xml\Generator\Document */
	private function getDocument() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->createDocument();
		}
		return $this->{__METHOD__};
	}
}
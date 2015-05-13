<?php
class Df_1C_Model_Cml2_Action_Orders_Import extends Df_1C_Model_Cml2_Action {
	/**
	 * @overrode
	 * @return bool
	 */
	protected function needUpdateLastProcessedTime() {return true;}

	/**
	 * @override
	 * @return void
	 */
	protected function processInternal() {
		/**
		 * Обратите внимание,
		 * что 1С: Управление торговлей передаёт в магазин только те заказы,
		 * которые ранее были переданы из магазина в 1С: Управление торговлей
		 */
		if (df_is_it_my_local_pc()) {
			$this->logXml();
		}
		foreach ($this->getOrders() as $entityOrder) {
			/** @var Df_1C_Model_Cml2_Import_Data_Entity_Order $entityOrder */
			Df_1C_Model_Cml2_Import_Processor_Order::i($entityOrder)->process();
		}
		$this->setResponseBodyAsArrayOfStrings(array('success', ''));
	}

	/** @return string */
	private function getFileFullPath() {
		return df_concat_path(Mage::getBaseDir('var'), 'log', 'site-to-my.xml');
	}

	/** @return Df_1C_Model_Cml2_Import_Data_Collection_Orders */
	private function getOrders() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_1C_Model_Cml2_Import_Data_Collection_Orders::i($this->getSimpleXmlElement())
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Varien_Simplexml_Element */
	private function getSimpleXmlElement() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_xml($this->getXml());
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getXml() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = file_get_contents('php://input');
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return Df_1C_Model_Cml2_Action_Orders_Import */
	private function logXml() {
		rm_file_put_contents($this->getFileFullPath(), $this->getXml());
		return $this;
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_1C_Model_Cml2_Action_Orders_Import
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}
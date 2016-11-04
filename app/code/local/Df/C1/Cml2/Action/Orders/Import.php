<?php
namespace Df\C1\Cml2\Action\Orders;
class Df_C1_Cml2_Action_Orders_Import extends Df_C1_Cml2_Action {
	/**
	 * Обратите внимание,
	 * что 1С:Управление торговлей передаёт в магазин только те заказы,
	 * которые ранее были переданы из магазина в 1С:Управление торговлей
	 * @override
	 * @see Df_Core_Model_Action::_process()
	 * @used-by Df_Core_Model_Action::process()
	 * @return void
	 */
	protected function _process() {
		if (df_my_local()) {
			$this->logXml();
		}
		foreach ($this->getOrders() as $entityOrder) {
			/** @var Df_C1_Cml2_Import_Data_Entity_Order $entityOrder */
			Df_C1_Cml2_Import_Processor_Order::i($entityOrder)->process();
		}
		$this->setResponseSuccess();
	}

	/** @return string */
	private function getFileFullPath() {
		return df_cc_path(Mage::getBaseDir('var'), 'log', 'site-to-my.xml');
	}

	/** @return Df_C1_Cml2_Import_Data_Collection_Orders */
	private function getOrders() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_C1_Cml2_Import_Data_Collection_Orders::i(df_xml_parse($this->getXml()));
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

	/** @return void */
	private function logXml() {df_file_put_contents($this->getFileFullPath(), $this->getXml());}
}
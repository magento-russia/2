<?php
namespace Df\C1\Cml2\Action\Orders;
class Import extends \Df\C1\Cml2\Action {
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
			/** @var \Df\C1\Cml2\Import\Data\Entity\Order $entityOrder */
			\Df\C1\Cml2\Import\Processor\Order::i($entityOrder)->process();
		}
		$this->setResponseSuccess();
	}

	/** @return string */
	private function getFileFullPath() {return
		df_cc_path(\Mage::getBaseDir('var'), 'log', 'site-to-my.xml')
	;}

	/** @return \Df\C1\Cml2\Import\Data\Collection\Orders */
	private function getOrders() {return dfc($this, function() {return
		\Df\C1\Cml2\Import\Data\Collection\Orders::i(df_xml_parse($this->getXml()))				
	;});}

	/** @return string */
	private function getXml() {return dfc($this, function() {
		/** @var mixed $result */
		$result = file_get_contents('php://input');	
		df_result_string_not_empty($result);
		return $result;
	});}

	/** @return void */
	private function logXml() {df_file_put_contents($this->getFileFullPath(), $this->getXml());}
}
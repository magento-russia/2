<?php
class Df_Log_Model_Visitor extends Mage_Log_Model_Visitor {
	/**
	 * Цель перекрытия —
	 * предоставление администратору возможности ускорить работу системы
	 * посредством отключения учёта времени последнего посещения магазина каждым посетителем
	 *
	 * @override
	 * @param Varien_Event_Observer $observer
	 * @return Df_Log_Model_Visitor
	 */
	public function saveByRequest($observer) {
		if (!$this->_skipRequestLogging && !$this->isModuleIgnored($observer)) {
			try {
				$this->setLastVisitAt(now());
				if (!df_cfg()->speed()->general()->disableLoggingLastVisitTime()) {
					$this->save();
				}
				$this->_getSession()->setData('visitor_data', $this->getData());
			} catch (Exception $e) {
				Mage::logException($e);
			}
		}
		return $this;
	}
}
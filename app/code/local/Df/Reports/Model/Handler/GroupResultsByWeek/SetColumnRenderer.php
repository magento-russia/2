<?php
/**
 * @method Df_Core_Model_Event_Adminhtml_Block_HtmlBefore getEvent()
 */
class Df_Reports_Model_Handler_GroupResultsByWeek_SetColumnRenderer extends Df_Core_Model_Handler {
	/**
	 * Метод-обработчик события
	 * @override
	 * @return void
	 */
	public function handle() {
		/** @var Varien_Object|bool $periodColumn */
		$periodColumn = $this->getBlockAsReportGrid()->getColumn('period');
		if ($periodColumn) {
			$periodColumn->unsetData('renderer');
		}
	}

	/**
	 * Класс события (для валидации события)
	 * @override
	 * @return string
	 */
	protected function getEventClass() {
		return Df_Core_Model_Event_Adminhtml_Block_HtmlBefore::_CLASS;
	}

	/** @return Mage_Adminhtml_Block_Report_Grid_Abstract */
	private function getBlockAsReportGrid() {
		return $this->getEvent()->getBlock();
	}

	const _CLASS = __CLASS__;
}
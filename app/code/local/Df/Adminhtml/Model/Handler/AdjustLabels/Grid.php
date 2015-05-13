<?php
/**
 * @method Df_Core_Model_Event_CoreBlockAbstract_ToHtml_Before getEvent()
 */
class Df_Adminhtml_Model_Handler_AdjustLabels_Grid extends Df_Core_Model_Handler {
	/**
	 * Метод-обработчик события
	 * @override
	 * @return void
	 */
	public function handle() {
		if (df_enabled(Df_Core_Feature::LOCALIZATION)) {
			foreach ($this->getBlockAsGrid()->getColumns() as $column) {
				/** @var Varien_Object $column */
				$column
					->setData(
						Df_Adminhtml_Const::GRID_COLUMN__PARAM__HEADER
						,df_text()->formatCase(
							df_nts($column->getData(Df_Adminhtml_Const::GRID_COLUMN__PARAM__HEADER))
							,df_cfg()->admin()->_interface()->getGridLabelFont()->getLetterCase()
						)
					)
				;
			}
		}
	}

	/** @return Mage_Adminhtml_Block_Widget_Grid */
	private function getBlockAsGrid() {
		return $this->getEvent()->getBlock();
	}

	/**
	 * Класс события (для валидации события)
	 * @override
	 * @return string
	 */
	protected function getEventClass() {
		return Df_Core_Model_Event_CoreBlockAbstract_ToHtml_Before::_CLASS;
	}

	const _CLASS = __CLASS__;
}
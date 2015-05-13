<?php
class Df_Adminhtml_Model_System_Config_Source_Enabledisable
	extends Mage_Adminhtml_Model_System_Config_Source_Enabledisable {
	/**
	 * Цель перекрытия —
	 * замена в выпадающем списке значения опций
	 * «Включить» / «Отключить» на «Да» / «Нет».
	 * В Российской сборке Magento названия опций формулируются конкретно,
	 * в вопросительной форме, и значения «Да» / «Нет» становятся намного осмысленнее,
	 * чем «Включить» / «Отключить».
	 * @override
	 * @return array(string => string|int)
	 */
	public function toOptionArray() {
		return df_mage()->adminhtml()->yesNo()->toOptionArray();
	}
}
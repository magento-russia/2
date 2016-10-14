<?php
/**
 * @singleton
 * В этом классе нельзя кешировать результаты вычислений!
 */
class Df_Spsr_Model_Config_Source_Insurer extends Df_Admin_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return rm_map_to_options(array(
			self::OPTION_VALUE__CARRIER => 'служба доставки'
			,self::OPTION_VALUE__INSURANCE_COMPANY => 'страховая компания'
		));
	}
	const _C = __CLASS__;
	const OPTION_VALUE__CARRIER = 'carrier';
	const OPTION_VALUE__INSURANCE_COMPANY = 'insurance_company';
}
<?php
/**
 * @singleton
 * В этом классе нельзя кешировать результаты вычислений!
 */
class Df_Spsr_Model_Config_Source_Insurer extends Df_Admin_Model_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return
			array(
				array(
					self::OPTION_KEY__LABEL => 'служба доставки'
					,self::OPTION_KEY__VALUE => self::OPTION_VALUE__CARRIER
				)
				,array(
					self::OPTION_KEY__LABEL => 'страховая компания'
					,self::OPTION_KEY__VALUE => self::OPTION_VALUE__INSURANCE_COMPANY
				)
			)
		;
	}
	const _CLASS = __CLASS__;
	const OPTION_VALUE__CARRIER = 'carrier';
	const OPTION_VALUE__INSURANCE_COMPANY = 'insurance_company';
}
<?php
class Df_Localization_Onetime_Processor_CustomerGroup
	extends Df_Localization_Onetime_Processor_Entity {
	/**
	 * @override
	 * @return string
	 */
	protected function getTitlePropertyName() {return 'customer_group_code';}
}
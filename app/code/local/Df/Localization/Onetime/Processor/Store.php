<?php
class Df_Localization_Onetime_Processor_Store
	extends Df_Localization_Onetime_Processor_Entity {
	/**
	 * @override
	 * @return string
	 */
	protected function getTitlePropertyName() {return 'name';}
}
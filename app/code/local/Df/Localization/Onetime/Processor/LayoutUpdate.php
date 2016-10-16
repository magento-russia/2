<?php
class Df_Localization_Onetime_Processor_LayoutUpdate
	extends Df_Localization_Onetime_Processor_Entity {
	/**
	 * @override
	 * @return string
	 */
	protected function getTitlePropertyName() {df_should_not_be_here(); return null;}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getTranslatableProperties() {return array('xml');}
}
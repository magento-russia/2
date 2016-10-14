<?php
/**
 * @method Df_Rating_Model_Rating getEntity()
 */
class Df_Localization_Onetime_Processor_Rating
	extends Df_Localization_Onetime_Processor_Entity {
	/**
	 * @override
	 * @return string
	 */
	protected function getTitlePropertyName() {return 'rating_code';}
}


 
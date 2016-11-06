<?php
class Df_Admin_Config_Form_FieldInstance_Info_Urls extends Df_Admin_Config_Form_FieldInstance {
	/** @return array(string => string) */
	public function getUrls() {return dfc($this, function() {return
		df_map(function(Df_Core_Model_StoreM $s) {return [
			$s->getName()
			,df_url_frontend($this->getConfigParam('df_url_path_base', true), [], $s)
		];}, Mage::app()->getStores(), [], [], 0, true)
	;});}
}
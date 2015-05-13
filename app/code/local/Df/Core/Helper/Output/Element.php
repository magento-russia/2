<?php
class Df_Core_Helper_Output_Element extends Mage_Core_Helper_Abstract {
	/**
	 * @param array $params
	 * @return Df_Core_Block_Element_Input_Hidden
	 */
	public function createInputHidden(array $params = array()) {
		return Df_Core_Block_Element_Input_Hidden::i($params);
	}

	/** @return Df_Core_Helper_Output_Element */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}
<?php
class Df_1C_Model_Settings_Product_Description extends Df_1C_Model_Settings_Cml2 {
	/** @return string */
	public function getDefault() {return $this->getString('default');}
	/** @return boolean */
	public function preserveInUnique() {return $this->getYesNo('preserve_if_unique');}
	/** @return string */
	public function whichFieldToUpdate() {return $this->getString('which_field_to_update');}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_1c/product__description/';}
	/** @return Df_1C_Model_Settings_Product_Description */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}
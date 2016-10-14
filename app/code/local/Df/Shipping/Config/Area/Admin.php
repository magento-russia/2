<?php
class Df_Shipping_Config_Area_Admin extends Df_Shipping_Config_Area {
	/** @return float */
	public function feeFixed() {return rm_float($this->getVar('fee_fixed', 0));}

	/** @return float */
	public function feePercent() {return rm_float($this->getVar('fee_percent', 0));}

	/** @return float */
	public function getDeclaredValuePercent() {
		return rm_float($this->getVar('declared_value_percent', 0.0));
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getAreaPrefix() {return 'admin';}
}
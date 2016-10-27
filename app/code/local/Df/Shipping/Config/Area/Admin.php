<?php
namespace Df\Shipping\Config\Area;
class Admin extends \Df\Shipping\Config\Area {
	/** @return float */
	public function feeFixed() {return df_float($this->getVar('fee_fixed', 0));}

	/** @return float */
	public function feePercent() {return df_float($this->getVar('fee_percent', 0));}

	/** @return float */
	public function getDeclaredValuePercent() {return
		df_float($this->getVar('declared_value_percent', 0.0))
	;}

	/**
	 * @override
	 * @return string
	 */
	protected function getAreaPrefix() {return 'admin';}
}
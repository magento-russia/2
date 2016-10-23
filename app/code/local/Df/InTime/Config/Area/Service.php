<?php
class Df_InTime_Config_Area_Service extends Df_Shipping_Config_Area_Service {
	/**
	 * @used-by Df_InTime_Collector::_collect()
	 * @return int
	 */
	public function кодСкладаОтправителя() {return $this->nat('department');}
}
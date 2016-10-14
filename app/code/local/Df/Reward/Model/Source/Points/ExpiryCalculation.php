<?php
class Df_Reward_Model_Source_Points_ExpiryCalculation {
	/** @return array(array(string => string)) */
	public function toOptionArray() {
		return rm_map_to_options(array('static' => 'Static', 'dynamic' => 'Dynamic'), $this);
	}
}
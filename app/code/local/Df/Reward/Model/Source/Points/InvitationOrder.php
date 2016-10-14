<?php
class Df_Reward_Model_Source_Points_InvitationOrder {
	/** @return array(array(string => string)) */
	public function toOptionArray() {
		return rm_map_to_options(array('*' => 'Each', '1' => 'First'), $this);
	}
}
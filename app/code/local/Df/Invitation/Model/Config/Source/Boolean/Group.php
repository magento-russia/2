<?php
class Df_Invitation_Model_Config_Source_Boolean_Group {
	/** @return array(int => string) */
	public function toOptionArray() {
		return array(
			1 => df_h()->invitation()->__('Same as Inviter')
			,0 => df_h()->invitation()->__('Default Customer Group from System Configuration')
		);
	}
}
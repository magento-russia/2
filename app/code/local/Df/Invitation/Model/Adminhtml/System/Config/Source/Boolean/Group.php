<?php
/**
 * Invitation source for reffered customer group system configuration
 */
class Df_Invitation_Model_Adminhtml_System_Config_Source_Boolean_Group
{
	public function toOptionArray()
	{
		return array(
			1 => df_h()->invitation()->__('Same as Inviter'),0 => df_h()->invitation()->__('Default Customer Group from System Configuration')
		);
	}
}
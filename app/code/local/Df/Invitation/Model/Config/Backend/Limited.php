<?php
class Df_Invitation_Model_Config_Backend_Limited extends Mage_Core_Model_Config_Data {
	/**
	 * Validating entered value if it will be 0 (unlimited)
	 * throw notice and change it to old one
	 * @return Df_Invitation_Model_Config_Backend_Limited
	 */
	protected function _beforeSave()
	{
		parent::_beforeSave();
		if ((int)$this->getValue() <= 0) {
			$parameter = df_h()->invitation()->__('Max Invitations Allowed to be Sent at One Time');
			//if even old value is not valid we will have to you '1'
			$value = (int)$this->getOldValue();
			if ($value < 1) {
				$value = 1;
			}
			$this->setValue($value);
			df_session()->addNotice(
				df_h()->invitation()->__('Invalid value used for "%s" parameter. Previous value saved.', $parameter)
			);
		}
		return $this;
	}
}
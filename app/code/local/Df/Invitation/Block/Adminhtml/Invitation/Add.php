<?php
class Df_Invitation_Block_Adminhtml_Invitation_Add extends Mage_Adminhtml_Block_Widget_Form_Container {
	protected $_objectId = 'invitation_id';
	protected $_blockGroup = 'df_invitation';
	protected $_controller = 'adminhtml_invitation';
	protected $_mode = 'add';

	/**
	 * Prepares form scripts
	 * @return Df_Invitation_Block_Adminhtml_Invitation_Add
	 */
	protected function _prepareLayout()
	{
		$validationMessage = addcslashes(df_h()->invitation()->__('Please enter valid email addresses, separated by new line.'), "\\'\n\r");
		$this->_formInitScripts[]= "
		Validation.addAllThese([
			['validate-emails', '$validationMessage', function (v) {
				v = v.strip();
				var emails = v.split(/[\\s]+/g);
				for(var i = 0, l = emails.length; i < l; i++) {
					if (!Validation.get('validate-email').test(emails[i])) {
						return false;
					}
				}
				return true;
			}]
		]);";
		return parent::_prepareLayout();
	}

	/**
	 * Get header text
	 * @return string
	 */
	public function getHeaderText()
	{
		return df_h()->invitation()->__('New Invitations');
	}

	/** @return Df_Invitation_Block_Adminhtml_Invitation_Add */
	public static function i() {return df_block(__CLASS__);}
}
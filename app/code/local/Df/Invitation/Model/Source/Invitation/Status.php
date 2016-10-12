<?php
class Df_Invitation_Model_Source_Invitation_Status extends Df_Core_Model {
	/** @return string[] */
	public function getOptions() {
		return
			array(
				Df_Invitation_Model_Invitation::STATUS_NEW =>
					df_h()->invitation()->__('Not Sent')
				,Df_Invitation_Model_Invitation::STATUS_SENT =>
					df_h()->invitation()->__('Sent')
				,Df_Invitation_Model_Invitation::STATUS_ACCEPTED =>
					df_h()->invitation()->__('Accepted')
				,Df_Invitation_Model_Invitation::STATUS_CANCELED =>
					df_h()->invitation()->__('Discarded')
			)
		;
	}

	/**
	 * @param string $option
	 * @return string|null
	 */
	public function getOptionText($option) {
		$options = $this->getOptions();
		if (isset($options[$option])) {
			return $options[$option];
		}
		return null;
	}

	/**
	 * @param boolean $useEmpty
	 * @return string[][]
	 */
	public function toOptionsArray($useEmpty = false) {
		/** @var string[][] $result */
		$result = array();
		if ($useEmpty) {
			$result[]= array('value' => '', 'label' => '');
		}
		foreach ($this->getOptions() as $value => $label) {
			$result[]= array('value' => $value, 'label' => $label);
		}
		return $result;
	}

	const _CLASS = __CLASS__;
	/** @return Df_Invitation_Model_Source_Invitation_Status */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}
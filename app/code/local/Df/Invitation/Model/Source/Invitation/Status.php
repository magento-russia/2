<?php
class Df_Invitation_Model_Source_Invitation_Status extends Df_Core_Model {
	/** @return string[] */
	public function getOptions() {
		/** @var Df_Invitation_Helper_Data $h */
		$h = df_h()->invitation();
		return array(
			Df_Invitation_Model_Invitation::STATUS_NEW => $h->__('Not Sent')
			,Df_Invitation_Model_Invitation::STATUS_SENT => $h->__('Sent')
			,Df_Invitation_Model_Invitation::STATUS_ACCEPTED => $h->__('Accepted')
			,Df_Invitation_Model_Invitation::STATUS_CANCELED => $h->__('Discarded')
		);
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
	 * @param boolean $useEmpty [optional]
	 * @return string[][]
	 */
	public function toOptionsArray($useEmpty = false) {
		return array_merge(array(rm_option('', '')), rm_map_to_options($this->getOptions()));
	}

	const _C = __CLASS__;
	/** @return Df_Invitation_Model_Source_Invitation_Status */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}
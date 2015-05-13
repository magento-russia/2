<?php
class Df_Reward_Model_Source_Website extends Df_Core_Model_Abstract {
	/**
	 * @param bool $withAll Whether to prepend "All websites" option on not
	 * @return string[]
	 */
	public function toOptionArray($withAll = true) {
		/** @var string[] $result */
		$result = df_mage()->adminhtml()->system()->storeSingleton()->getWebsiteOptionHash();
		if ($withAll) {
			$result =
					array(0 => df_h()->reward()->__('All Websites'))
				+
				$result
			;
		}
		return $result;
	}

	const _CLASS = __CLASS__;

	/** @return Df_Reward_Model_Source_Website */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}
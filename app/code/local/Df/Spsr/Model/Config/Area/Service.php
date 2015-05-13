<?php
class Df_Spsr_Model_Config_Area_Service extends Df_Shipping_Model_Config_Area_Service {
	/** @return string */
	public function getInsurer() {
		/** @var string $result */
		$result = $this->getVar(self::KEY__VAR__INSURER);
		df_result_string_not_empty($result);
		return $result;
	}

	/** @return bool */
	public function endorseDeliveryTime() {
		return $this->getVarFlag(self::KEY__VAR__ENDORSE_DELIVERY_TIME);
	}

	/** @return bool */
	public function needPersonalHanding() {
		return $this->getVarFlag(self::KEY__VAR__PERSONAL_HANDING);
	}

	const _CLASS = __CLASS__;
	const KEY__VAR__ENDORSE_DELIVERY_TIME = 'endorse_delivery_time';
	const KEY__VAR__INSURER = 'insurer';
	const KEY__VAR__PERSONAL_HANDING = 'personal_handing';

}
<?php
class Df_Pec_Model_Config_Area_Service extends Df_Shipping_Model_Config_Area_Service {
	/** @return string */
	public function getMoscowCargoReceptionPoint() {
		/** @var string $result */
		$result =
			$this->getVar(
				self::KEY__VAR__MOSCOW_CARGO_RECEPTION_POINT
				,Df_Pec_Model_Config_Source_MoscowCargoReceptionPoint::OPTION_VALUE__OUTSIDE
			)
		;
		df_result_string($result);
		return $result;
	}

	/** @return int */
	public function getSealCount() {return rm_nat0($this->getVar(self::KEY__VAR__SEAL_COUNT, 0));}

	/** @return bool */
	public function needCargoTailLoaderAtDestination() {
		return $this->getVarFlag(self::KEY__VAR__NEED_CARGO_TAIL_LOADER_AT_DESTINATION);
	}

	/** @return bool */
	public function needCargoTailLoaderAtOrigin() {
		return $this->getVarFlag(self::KEY__VAR__NEED_CARGO_TAIL_LOADER_AT_ORIGIN);
	}

	/** @return bool */
	public function needOvernightDelivery() {
		return $this->getVarFlag(self::KEY__VAR__NEED_OVERNIGHT_DELIVERY);
	}

	/** @return bool */
	public function needRemoveAwningAtDestination() {
		return $this->getVarFlag(self::KEY__VAR__NEED_REMOVE_AWNING_AT_DESTINATION);
	}

	/** @return bool */
	public function needRemoveAwningAtOrigin() {
		return $this->getVarFlag(self::KEY__VAR__NEED_REMOVE_AWNING_AT_ORIGIN);
	}

	/** @return bool */
	public function needRigidContainer() {return $this->getVarFlag(self::KEY__VAR__NEED_RIGID_CONTAINER);}

	const _CLASS = __CLASS__;
	const KEY__VAR__MOSCOW_CARGO_RECEPTION_POINT = 'moscow_cargo_reception_point';
	const KEY__VAR__NEED_CARGO_TAIL_LOADER_AT_DESTINATION = 'need_cargo_tail_loader_at_destination';
	const KEY__VAR__NEED_CARGO_TAIL_LOADER_AT_ORIGIN = 'need_cargo_tail_loader_at_origin';
	const KEY__VAR__NEED_OVERNIGHT_DELIVERY = 'need_overnight_delivery';
	const KEY__VAR__NEED_REMOVE_AWNING_AT_DESTINATION = 'need_remove_awning_at_destination';
	const KEY__VAR__NEED_REMOVE_AWNING_AT_ORIGIN = 'need_remove_awning_at_origin';
	const KEY__VAR__NEED_RIGID_CONTAINER = 'need_rigid_container';
	const KEY__VAR__SEAL_COUNT = 'seal_count';
}
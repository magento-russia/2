<?php
namespace Df\Pec\Config\Area;
class Service extends \Df\Shipping\Config\Area\Service {
	/** @return int */
	public function moscowReceptionPoint() {return $this->nat0('moscow_cargo_reception_point');}

	/** @return int */
	public function getSealCount() {return $this->nat0('seal_count');}

	/** @return bool */
	public function needCargoTailLoaderAtDestination() {return $this->getVarFlag(
		'need_cargo_tail_loader_at_destination'
	);}

	/** @return bool */
	public function needCargoTailLoaderAtOrigin() {return $this->getVarFlag(
		'need_cargo_tail_loader_at_origin'
	);}

	/** @return bool */
	public function needOvernightDelivery() {return $this->getVarFlag('need_overnight_delivery');}

	/** @return bool */
	public function needRemoveAwningAtDestination() {return $this->getVarFlag(
		'need_remove_awning_at_destination'
	);}

	/** @return bool */
	public function needRemoveAwningAtOrigin() {return $this->getVarFlag('need_remove_awning_at_origin');}

	/** @return bool */
	public function needRigidContainer() {return $this->getVarFlag('need_rigid_container');}
}
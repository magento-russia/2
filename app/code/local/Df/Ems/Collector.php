<?php
// 2016-10-25
namespace Df\Ems;
class Collector extends \Df\Shipping\Collector\Ru {
	/**
	 * 2016-10-25
	 * @override
	 * @see \Df\Shipping\Collector::_collect()
	 * @used-by \Df\Shipping\Collector::collect()
	 * @return void
	 */
	protected function _collect() {
		$this->checkWeightIsLE(31.5);
		$this->addRate(
			$this->cond()->getRate()
			, null
			, null
			, $this->cond()->getDeliveryTimeMin()
			, $this->cond()->getDeliveryTimeMax()
		);
	}

	/** @return Cond */
	private function cond() {return dfc($this, function() {return Cond::i2(
		L::find($this->oCountry(), $this->oRegionId(), $this->oCity()) ?: $this->eUnknownOrig()
		,L::find($this->dCountry(), $this->dRegionId(), $this->dCity()) ?: $this->eUnknownDest()
		,$this->rr()->getWeightInKg(), 'att'
	);});}
}
<?php
// 2016-10-26
namespace Df\Dellin;
class Collector extends \Df\Shipping\Collector\Ru {
	/**
	 * 2016-10-26
	 * @override
	 * @see \Df\Shipping\Collector::_collect()
	 * @used-by \Df\Shipping\Collector::collect()
	 * @return void
	 */
	protected function _collect() {
		$this->checkCountryDestIsRU();
		$this->rate($this->cond()->getRate(), $this->cond()->getDeliveryTime());
	}

	/** @return Cond */
	private function cond() {return dfc($this, function() {return Cond::i($this,
		L::find($this->oCity(), $this->oRegion()) ?: $this->eUnknownOrig()
		,L::find($this->dCity(), $this->dRegion()) ?: $this->eUnknownDest()
	);});}
}
<?php
// 2016-10-30
namespace Df\Pec;
/** @method \Df\Pec\Config\Area\Service configS() */
class Collector extends \Df\Shipping\Collector\Ru {
	/**
	 * 2016-10-30
	 * @override
	 * @see \Df\Shipping\Collector::_collect()
	 * @used-by \Df\Shipping\Collector::collect()
	 * @return void
	 */
	protected function _collect() {
		$this->checkCountryDestIs('RU', 'KZ');
		Cond::collect($this,
			L::find($this->oCity()) ?: $this->eUnknownOrig()
			,L::find($this->dCity()) ?: $this->eUnknownDest()
		);
	}
}
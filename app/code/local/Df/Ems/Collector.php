<?php
// 2016-10-25
namespace Df\Ems;
use Df_Directory_Model_Country as Country;
use Df\Ems\Locator as L;
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

	/** @return Conditions */
	private function cond() {return dfc($this, function() {return
		Conditions::i2($this->orig(), $this->dest(), $this->rr()->getWeightInKg(), 'att')
	;});}

	/**
	 * 2016-10-25
	 * @return string
	 * @throws \Exception
	 */
	private function dest() {return dfc($this, function() {return
		L::find($this->dCountry(), $this->dRegionId(), $this->dCity()) ?: $this->eUnknownDest()
	;});}

	/**
	 * 2016-10-25
	 * @return string
	 * @throws \Exception
	 */
	private function orig() {return dfc($this, function() {return
		L::find($this->oCountry(), $this->oRegionId(), $this->oCity()) ?: $this->eUnknownOrig()
	;});}
}
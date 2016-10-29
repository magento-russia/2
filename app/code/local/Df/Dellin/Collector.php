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
		df_log(Locator::find('Верхоянск', 'Саха (Якутия)'));
		$this->addRate(100);
	}

	/**
	 * 2016-10-29
	 * @return int
	 * @throws \Exception
	 */
	private function dest() {return dfc($this, function() {return
		Locator::find($this->dCity(), $this->dRegion()) ?: $this->eUnknownDest()
	;});}

	/**
	 * 2016-10-29
	 * @return int
	 * @throws \Exception
	 */
	private function orig() {return dfc($this, function() {return
		Locator::find($this->oCity(), $this->oRegion()) ?: $this->eUnknownOrig()
	;});}
}
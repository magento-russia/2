<?php
namespace Df\InTime\Config\Area;
class Service extends \Df\Shipping\Config\Area\Service {
	/**
	 * @used-by \Df\InTime\Collector::_collect()
	 * @return int
	 */
	public function кодСкладаОтправителя() {return $this->nat('department');}
}
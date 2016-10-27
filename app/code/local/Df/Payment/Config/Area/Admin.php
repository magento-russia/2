<?php
namespace Df\Payment\Config\Area;
class Admin extends \Df\Payment\Config\Area {
	/**
	 * @override
	 * @return string
	 */
	protected function getAreaPrefix() {return 'admin';}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getStandardKeys() {return
		array_merge(parent::getStandardKeys(), ['order_status','payment_action'])
	;}
}
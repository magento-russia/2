<?php
namespace Df\RbkMoney;
class Method extends \Df\Payment\Method\WithRedirect {
	/**
	 * @override
	 * @return string
	 */
	public function getRmId() {return 'rbk-money';}
}
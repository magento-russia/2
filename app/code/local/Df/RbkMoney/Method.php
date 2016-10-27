<?php
class Df_RbkMoney_Method extends \Df\Payment\Method\WithRedirect {
	/**
	 * @override
	 * @return string
	 */
	public function getRmId() {return 'rbk-money';}
}
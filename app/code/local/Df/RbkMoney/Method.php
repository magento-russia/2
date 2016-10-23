<?php
class Df_RbkMoney_Method extends Df_Payment_Method_WithRedirect {
	/**
	 * @override
	 * @return string
	 */
	public function getRmId() {return 'rbk-money';}
}
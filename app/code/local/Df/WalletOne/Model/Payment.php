<?php
class Df_WalletOne_Model_Payment extends Df_Payment_Model_Method_WithRedirect {
	/**
	 * @override
	 * @return string
	 */
	public function getRmId() {return 'wallet-one';}
}
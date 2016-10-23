<?php
class Df_WalletOne_Method extends Df_Payment_Method_WithRedirect {
	/**
	 * @override
	 * @return string
	 */
	public function getRmId() {return 'wallet-one';}
}
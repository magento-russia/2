<?php
class Df_WalletOne_Method extends \Df\Payment\Method\WithRedirect {
	/**
	 * @override
	 * @return string
	 */
	public function getRmId() {return 'wallet-one';}
}
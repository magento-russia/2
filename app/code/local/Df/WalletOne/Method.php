<?php
namespace Df\WalletOne;
class Method extends \Df\Payment\Method\WithRedirect {
	/**
	 * @override
	 * @return string
	 */
	public function getRmId() {return 'wallet-one';}
}
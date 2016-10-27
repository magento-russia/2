<?php
namespace Df\Kkb\Request;
class Void extends Secondary {
	/**
	 * @override
	 * @return string
	 */
	protected function getGenericFailureMessageUniquePart() {return 'снятии блокировки средств';}

	/**
	 * @override
	 * @return string
	 */
	public function getTransactionType() {return \Df\Kkb\RequestDocument\Secondary::TRANSACTION__VOID;}
}



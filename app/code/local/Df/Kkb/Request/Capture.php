<?php
namespace Df\Kkb\Request;
class Capture extends Secondary {
	/**
	 * @override
	 * @return string
	 */
	protected function getGenericFailureMessageUniquePart() {return
		'снятии ранее зарезервированных средств с карты покупателя'
	;}

	/**
	 * @override
	 * @return string
	 */
	public function getTransactionType() {return
		\Df\Kkb\RequestDocument\Secondary::TRANSACTION__CAPTURE
	;}
}



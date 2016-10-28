<?php
namespace Df\Psbank\Request;
class Capture extends \Df\Psbank\Request\Secondary {
	/**
	 * @override
	 * @return string
	 */
	protected function getGenericFailureMessageUniquePart() {
		return 'снятии ранее зарезервированных средств с карты покупателя';
	}
	/**
	 * @override
	 * @return int
	 */
	protected function getTransactionType() {return 21;}
}



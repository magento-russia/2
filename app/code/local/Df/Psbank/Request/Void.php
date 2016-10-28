<?php
namespace Df\Psbank\Request;
class Void extends \Df\Psbank\Request\Secondary {
	/**
	 * @override
	 * @return string
	 */
	protected function getGenericFailureMessageUniquePart() {return 'возврате средств покупетелю';}
	/**
	 * @override
	 * @return int
	 */
	protected function getTransactionType() {return 22;}
}



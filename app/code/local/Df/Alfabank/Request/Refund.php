<?php
namespace Df\Alfabank\Request;
class Refund extends \Df\Alfabank\Request\Secondary {
	/**
	 * @override
	 * @return string
	 */
	protected function getGenericFailureMessageUniquePart() {return 'возврате оплаты покупателю';}

	/**
	 * @override
	 * @used-by \Df\Alfabank\Request\Secondary::getUri()
	 * @return string
	 */
	protected function getServiceName() {return 'refund';}
}
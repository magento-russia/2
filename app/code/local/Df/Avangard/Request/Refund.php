<?php
namespace Df\Avangard\Request;
class Refund extends Secondary {
	/**
	 * @override
	 * @return string
	 */
	protected function getGenericFailureMessageUniquePart() {return 'возврате оплаты покупателю';}

	/**
	 * @override
	 * @return string
	 */
	protected function getRequestId() {return 'reverse_order';}
}
<?php
namespace Df\Alfabank\Request;
class Capture extends \Df\Alfabank\Request\Secondary {
	/**
	 * @override
	 * @see \Df\Payment\Request\Secondary::getGenericFailureMessageUniquePart()
	 * @used-by \Df\Payment\Request\Secondary::getGenericFailureMessage()
	 * @return string
	 */
	protected function getGenericFailureMessageUniquePart() {return
		'снятии ранее зарезервированных средств с карты покупателя'
	;}

	/**
	 * @override
	 * @see \Df\Alfabank\Request\Secondary::getServiceName()
	 * @used-by \Df\Alfabank\Request\Secondary::getUri()
	 * @return string
	 */
	protected function getServiceName() {return 'deposit';}
}
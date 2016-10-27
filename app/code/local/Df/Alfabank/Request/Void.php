<?php
namespace Df\Alfabank\Request;
class Void extends \Df\Alfabank\Request\Secondary {
	/**
	 * @override
	 * @return string
	 */
	protected function getGenericFailureMessageUniquePart() {return 'снятии блокировки средств';}

	/**
	 * @override
	 * @used-by \Df\Alfabank\Request\Secondary::getUri()
	 * @return string
	 */
	protected function getServiceName() {return 'reverse';}
}



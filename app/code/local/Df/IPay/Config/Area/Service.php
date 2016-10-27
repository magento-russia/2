<?php
namespace Df\IPay\Config\Area;
/** @method \Df\IPay\Method main() */
class Service extends \Df\Payment\Config\Area\Service {
	/**
	 * @override
	 * @see \Df\Payment\Config\Area\Service::getUrlPaymentPage()
	 * @return string
	 */
	public function getUrlPaymentPage() {return dfc($this, function() {return
		$this->isTestMode()
		? parent::getUrlPaymentPage()
		: dfa_deep($this->constManager()->methodsCA(), [$this->main()->operator(), 'payment-page'])
	;});}
}
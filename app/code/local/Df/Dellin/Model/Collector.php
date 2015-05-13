<?php
class Df_Dellin_Model_Collector extends Df_Shipping_Model_Collector {
	/**
	 * @override
	 * @return Df_Dellin_Model_Method[]
	 * @throws Exception
	 */
	protected function getMethods() {
		if (!isset($this->{__METHOD__})) {
			/** @var array $result */
			$result = array();
			/** @var Df_Dellin_Model_Method $methodByPublicApi */
			$methodByPublicApi =
				$this->createMethod(
					Df_Dellin_Model_Method::_CLASS
					,$title = 'Посредством публичного API'
				)
			;
			/** @var bool $isMethodByPublicApiApplicable */
			$isMethodByPublicApiApplicable = false;
			try {
				$isMethodByPublicApiApplicable =
						$methodByPublicApi->isApplicable()
					&&
						0 < $methodByPublicApi->getCost()
				;
			}
			catch(Exception $e) {
				if (!($e instanceof Df_Core_Exception_Client)) {
					df_notify_exception($e);
				}
				if ($this->getRmConfig()->frontend()->needDisplayDiagnosticMessages()) {
					throw $e;
				}
			}
			if ($isMethodByPublicApiApplicable) {
				$result[]= $methodByPublicApi;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
}
<?php
class Df_UkrPoshta_Model_Collector extends Df_Shipping_Model_Collector {
	/**
	 * @override
	 * @param Df_Shipping_Model_Method|Df_Shipping_Model_Rate_Result_Error[] $methods
	 * @return Df_Shipping_Model_Method|Df_Shipping_Model_Rate_Result_Error[]
	 */
	protected function postProcessMethods(array $methods) {
		/**
		 * Если наземная доставка до дома доступна одновременно
		 * и универсальным, и легковесным методами,
		 * до оставляем только тот из них, который дешевле.
		 *
		 * Если наземная доставка до пункта выдачи одновременно
		 * и универсальным, и легковесным методами,
		 * до оставляем только тот из них, который дешевле.
		 */
		/** @var Df_UkrPoshta_Model_Method|null $methodToHome */
		$methodToHome = null;
		/** @var Df_UkrPoshta_Model_Method|null $methodToPointOfIssue */
		$methodToPointOfIssue = null;
		foreach ($methods as $method) {
			/** @var Df_UkrPoshta_Model_Method|Df_Shipping_Model_Rate_Result_Error $method */
			if (
					($method instanceof Df_UkrPoshta_Model_Method)
				&&
					/**
					 * Тариф авиадоставки оставляем в любом случае
					 */
					!($method instanceof Df_UkrPoshta_Model_Method_Universal_Air)
			) {
				if ($method->needDeliverToHome()) {
					if (
							is_null($methodToHome)
						||
							($method->getPrice() < $methodToHome->getPrice())
					) {
						$methodToHome = $method;
					}
				}
				else {
					if (
							is_null($methodToPointOfIssue)
						||
							($method->getPrice() < $methodToPointOfIssue->getPrice())
					) {
						$methodToPointOfIssue = $method;
					}
				}
			}
		}
		/** @var Df_Shipping_Model_Method|Df_Shipping_Model_Rate_Result_Error[] $result */
		$result = array();
		foreach ($methods as $method) {
			/** @var Df_UkrPoshta_Model_Method|Df_Shipping_Model_Rate_Result_Error $method */
			if (
					!($method instanceof Df_UkrPoshta_Model_Method)
				||
					/**
					 * Тариф авиадоставки оставляем в любом случае
					 */
					($method instanceof Df_UkrPoshta_Model_Method_Universal_Air)
			) {
				$result[]= $method;
			}
			else {
				if ($method->needDeliverToHome()) {
					if (
							is_null($methodToHome)
						||
							($method->getMethod() === $methodToHome->getMethod())
					) {
						$result[]= $methodToHome;
					}
				}
				else {
					if (
							is_null($methodToPointOfIssue)
						||
							($method->getMethod() === $methodToPointOfIssue->getMethod())
					) {
						$result[]= $methodToPointOfIssue;
					}
				}
			}
		}
		return $result;
	}

	const _CLASS = __CLASS__;
}
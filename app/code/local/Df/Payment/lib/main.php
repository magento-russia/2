<?php
use Mage_Payment_Model_Info as I;
use Mage_Sales_Model_Quote_Payment as QP;
use Mage_Sales_Model_Order_Payment as OP;
/**
 * 2016-05-20
 * @param I|OP|QP $payment
 * @param array $info
 */
function dfp_add_info(I $payment, array $info) {
	foreach ($info as $key => $value) {
		/** @var string $key */
		/** @var string $value */
		$payment->setAdditionalInformation($key, $value);
	}
}

/**
 * 2016-08-08
 * @param I|OP|QP $payment
 * @param string|string[]|null $keys  [optional]
 * @return mixed|array(string => mixed)
 */
function dfp_iia(I $payment, $keys = null) {
	/** @var mixed|array(string => mixed) $result */
	if (is_null($keys)) {
		$result = $payment->getAdditionalInformation();
	}
	else {
		if (!is_array($keys)) {
			$keys = df_tail(func_get_args());
		}
		$result =
			1 === count($keys)
			? $payment->getAdditionalInformation(df_first($keys))
			: dfa_select_ordered($payment->getAdditionalInformation(), $keys)
		;
	}
	return $result;
}
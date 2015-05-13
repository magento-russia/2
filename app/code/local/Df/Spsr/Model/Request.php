<?php
class Df_Spsr_Model_Request extends Df_Shipping_Model_Request {
	/**
	 * @override
	 * @return string
	 */
	protected function getQueryHost() {
		return 'www.spsr.ru';
	}

	const _CLASS = __CLASS__;
}
<?php
abstract class Df_UkrPoshta_Model_Method_Universal_Ground extends Df_UkrPoshta_Model_Method_Universal {
	/**
	 * @override
	 * @return string
	 */
	protected function getTransportType() {
		return 'Ground';
	}

	const _CLASS = __CLASS__;

}
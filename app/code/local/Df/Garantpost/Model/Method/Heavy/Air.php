<?php
class Df_Garantpost_Model_Method_Heavy_Air extends Df_Garantpost_Model_Method_Heavy {
	/**
	 * @override
	 * @return string
	 */
	public function getMethod() {
		return self::METHOD;
	}

	/**
	 * @override
	 * @return string
	 */
	public function getMethodTitle() {
		return 'воздушная:';
	}

	/**
	 * @abstract
	 * @return string
	 */
	protected function getLocationDestinationSuffix() {
		return 'авиа';
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getTitleBase() {
		return 'тяжёлый груз, воздушным транспортом';
	}

	const _CLASS = __CLASS__;
	const METHOD = 'heavy-air';
}
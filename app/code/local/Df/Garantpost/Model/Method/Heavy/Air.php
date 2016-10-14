<?php
class Df_Garantpost_Model_Method_Heavy_Air extends Df_Garantpost_Model_Method_Heavy {
	/**
	 * @override
	 * @return string
	 */
	public function getMethod() {return 'heavy-air';}

	/**
	 * @override
	 * @return string
	 */
	public function getMethodTitle() {return 'воздушная:';}

	/**
	 * @abstract
	 * @return string
	 */
	protected function getLocationDestinationSuffix() {return 'авиа';}

	/**
	 * @override
	 * @return string
	 */
	protected function getTitleBase() {return 'тяжёлый груз, воздушным транспортом';}
}
<?php
class Df_Garantpost_Model_Method_Heavy_Ground extends Df_Garantpost_Model_Method_Heavy {
	/**
	 * @override
	 * @return string
	 */
	public function getMethod() {return 'heavy-ground';}

	/**
	 * @override
	 * @return string
	 */
	public function getMethodTitle() {return 'наземная:';}

	/**
	 * @abstract
	 * @return string
	 */
	protected function getLocationDestinationSuffix() {return 'авто';}

	/**
	 * @override
	 * @return string
	 */
	protected function getTitleBase() {return 'тяжёлый груз, наземным транспортом';}
}
<?php
class Df_Pec_Model_Method_Ground extends Df_Pec_Model_Method {
	/**
	 * @override
	 * @return string
	 */
	public function getMethod() {return self::METHOD;}

	/**
	 * @override
	 * @return string
	 */
	protected function getTitleBase() {return 'наземная';}

	/** @used-by Df_Pec_Model_Collector::getMethods() */
	const METHOD = 'ground';
}
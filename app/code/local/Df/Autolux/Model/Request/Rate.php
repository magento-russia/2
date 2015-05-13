<?php
class Df_Autolux_Model_Request_Rate extends Df_Autolux_Model_Request {
	/** @return float */
	public function getFactorVolume() {return rm_float($this->response()->json('volume'));}

	/** @return float */
	public function getFactorWeight() {return rm_float($this->response()->json('weight'));}

	/**
	 * @override
	 * @param string $responseAsText
	 * @return string
	 */
	public function preprocessJson($responseAsText) {
		return preg_replace('/\x{EF}\x{BB}\x{BF}/', '', $responseAsText);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getQueryPath() {return '/Autolux/inc/Pages/PatternStd/img/rates.php';}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Autolux_Model_Request_Rate
	 */
	public static function i(array $parameters = array()) {
		return new self(array(self::P__QUERY_PARAMS => $parameters));
	}
}
<?php
class Df_Exline_Request extends Df_Shipping_Request {
	/**
	 * @override
	 * @see Df_Shipping_Request::getQueryHost()
	 * @used-by Df_Shipping_Request::getUri()
	 * @return string
	 */
	protected function getQueryHost() {return 'calculator.exline.kz';}

	/**
	 * @used-by Df_Exline_Locator::map()
	 * @used-by Df_Exline_Collector::json()
	 * @param string $pathSuffix
	 * @param array(string => string) $queryParams [optional]
	 * @return Df_Exline_Request
	 */
	public static function i($pathSuffix, array $queryParams = array()) {
		return new self(array(
			self::P__QUERY_PATH => '/api/' .$pathSuffix
			,self::P__QUERY_PARAMS => $queryParams
		));
	}
}
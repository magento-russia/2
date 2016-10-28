<?php
namespace Df\Exline;
class Request extends \Df\Shipping\Request {
	/**
	 * @override
	 * @see \Df\Shipping\Request::host()
	 * @used-by \Df\Shipping\Request::getUri()
	 * @return string
	 */
	protected function host() {return 'calculator.exline.kz';}

	/**
	 * @used-by \Df\Exline\Locator::map()
	 * @used-by \Df\Exline\Collector::json()
	 * @param string $pathSuffix
	 * @param array(string => string) $queryParams [optional]
	 * @return self
	 */
	public static function i($pathSuffix, array $queryParams = array()) {
		return new self(array(
			self::P__QUERY_PATH => '/api/' .$pathSuffix
			,self::P__PARAMS_QUERY => $queryParams
		));
	}
}
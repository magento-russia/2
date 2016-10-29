<?php
namespace Df\Exline;
class Request extends \Df\Shipping\Request {
	/**
	 * @override
	 * @see \Df\Shipping\Request::uri()
	 * @used-by \Df\Shipping\Request::zuri()
	 * @return string
	 */
	protected function uri() {return 'http://calculator.exline.kz/api/';}

	/**
	 * @used-by \Df\Exline\Locator::map()
	 * @used-by \Df\Exline\Collector::json()
	 * @param string $suffix
	 * @param array(string => string) $queryParams [optional]
	 * @return self
	 */
	public static function i($suffix, array $queryParams = []) {return new self([
		self::P__SUFFIX => $suffix, self::P__QUERY => $queryParams
	]);}
}
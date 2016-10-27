<?php
namespace Df\Checkout\Module\Config\Area;
use Df\Checkout\Module\Main as Main;
class No extends \Df\Checkout\Module\Config\Area {
	/**
	 * @override
	 * @return string
	 */
	protected function getAreaPrefix() {return '';}

	/**
	 * @used-by \Df\Checkout\Module\Config\Facade::area()
	 * @param Main $main
	 * @return self
	 */
	public static function s(Main $main) {
		/** @var array(string => self) $cache */
		static $cache;
		/** @var string $key */
		$key = get_class($main);
		if (!isset($cache[$key])) {
			$cache[$key] = self::ic(__CLASS__, $main);
		}
		return $cache[$key];
	}
}



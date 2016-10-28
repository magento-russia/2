<?php
namespace Df\Shipping;
abstract class Locator extends \Df_Core_Model {
	/**
	 * @used-by map()
	 * @param string $type
	 * @return array(string => string|int|array(string|int))
	 */
	abstract protected function _map($type);

	/**
	 * @used-by _find()
	 * @used-by \Df_Core_Model::cacheLoadProperty()
	 * @used-by \Df_Core_Model::cacheSaveProperty()
	 * @param string $type
	 * @return array(string => string|int|array(string|int))
	 */
	protected function map($type) {return dfc($this, function($type) {return
		df_cache_get_simple(df_ckey(get_class(), $type), function() use($type) {return
			df_key_uc($this->_map($type))
		;})
	;}, func_get_args());}

	/**
	 * @used-by Df_Exline_Locator::findD()
	 * @used-by Df_Exline_Locator::findO()
	 * @used-by Df_InTime_Locator::find()
	 * @used-by Df_NovaPoshta_Locator::findD()
	 * @used-by Df_NovaPoshta_Locator::findO()
	 * @param string $type
	 * @param string $name
	 * @param bool $starts [optional]
	 * @return string|int|array(string|int)|null
	 */
	protected static function _find($type, $name, $starts = false) {
		/** @var \Df\Shipping\Locator $s */
		static $s; if (!$s) {$s = df_sc(static::class, __CLASS__);}
		$name = mb_strtoupper($name);
		/** @var string|mixed $result */
		if (!$starts) {
			$result = dfa($s->map($type), $name);
		}
		else {
			foreach ($s->map($type) as $key => $value) {
				/** @var string $key */
				/** @var string $value */
				if (df_starts_with($key, $name)) {
					$result = $value;
					break;
				}
			}
		}
		return $result;
	}
}
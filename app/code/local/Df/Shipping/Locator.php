<?php
abstract class Df_Shipping_Locator extends Df_Core_Model {
	/**
	 * @used-by map()
	 * @param string $type
	 * @return array(string => string|int|array(string|int))
	 */
	abstract protected function _map($type);

	/**
	 * @override
	 * @see Df_Core_Model_Abstract::cachedGlobal()
	 * @return string[]
	 */
	protected function cachedGlobal() {return self::m(__CLASS__, 'map');}

	/**
	 * @used-by _find()
	 * @used-by Df_Core_Model_Abstract::cacheLoadProperty()
	 * @used-by Df_Core_Model_Abstract::cacheSaveProperty()
	 * @param string $type
	 * @return array(string => string|int|array(string|int))
	 */
	protected function map($type) {
		if (!isset($this->{__METHOD__}[$type])) {
			$this->{__METHOD__}[$type] = df_key_uc($this->_map($type));
		}
		return $this->{__METHOD__}[$type];
	}

	/**
	 * @used-by Df_Exline_Locator::findD()
	 * @used-by Df_Exline_Locator::findO()
	 * @used-by Df_InTime_Locator::find()
	 * @used-by Df_NovaPoshta_Locator::findD()
	 * @used-by Df_NovaPoshta_Locator::findO()
	 * @param string
	 * @param string $type
	 * @param string $cityNameUc
	 * @param bool $starts [optional]
	 * @return string|int|array(string|int)|null
	 */
	protected static function _find($class, $type, $cityNameUc, $starts = false) {
		/** Df_Shipping_Locator $s */
		static $s; if (!$s) {$s = df_sc($class, __CLASS__);}
		/** @var string|mixed $result */
		if (!$starts) {
			$result = dfa($s->map($type), $cityNameUc);
		}
		else {
			$result = null;
			foreach ($s->map($type) as $key => $value) {
				if (df_starts_with($key, $cityNameUc)) {
					$result = $value;
					break;
				}
			}
		}
		return $result;
	}

	/**
	 * @used-by Df_Exline_Locator::postProcess()
	 * @used-by Df_NovaPoshta_Locator::_map()
	 * @used-by cleanParentheses()
	 * @param string $name
	 * @return string[]
	 */
	protected static function explodeParentheses($name) {
		/** @var string[] $result */
		$result = explode('(', $name);
		/** @var int $count */
		$count = count($result);
		if (1 < $count) {
			df_assert_eq(2, $count);
			$result = df_map('df_trim', $result, '()');
		}
		return $result;
	}

	/**
	 * 2015-03-24
	 * @param array(string => mixed)
	 * @return array(string => mixed)
	 */
	protected static function cleanParenthesesK($map) {
		return array_combine(self::cleanParentheses(array_keys($map)), array_values($map));
	}

	/**
	 * 2015-03-24
	 * «Николаевка (Ширяевск рн)» => «Николаевка»
	 * @used-by cleanParenthesesK()
	 * @param string|string[] $name
	 * @return string|string[]
	 */
	private static function cleanParentheses($name) {
		return
			is_array($name)
			? array_map(__METHOD__, $name)
			: df_first(self::explodeParentheses($name))
		;
	}
}
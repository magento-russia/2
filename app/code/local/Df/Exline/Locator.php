<?php
namespace Df\Exline;
class Locator extends \Df\Shipping\Locator {
	/**
	 * @override
	 * @see \Df\Shipping\Locator:: _map()
	 * @used-by \Df\Shipping\Locator::map()
	 * @param string $type
	 * @return array(string => int)
	 */
	protected function _map($type) {
		/** @var array(string => string|int) $json */
		$json = Request::i('regions/' . $type)->response()->json('regions');
		return $this->postProcess(array_column($json, 'id', 'name'));
	}

	/**
	 * Некоторые называния записаны с альтернативой в скобках, например:
	 * «Семей (Семипалатинск)», «Усть-Каменогорск (Оскемен)».
	 * Вычленяем альтернативуи добавляем её в карту в качестве отдельного элемента.
	 * @used-by _map()
	 * @param array(string => int) $map
	 * @return array(string => int)
	 */
	private function postProcess(array $map) {
		/** @var array(string => int) $alts */
		$alts = array();
		/** @var string[] $unset */
		$unset = array();
		foreach ($map as $name => $id) {
			/** @var string $name */
			/** @var int $id */
			/** @var string[] $parts */
			$parts = df_parentheses_explode($name);
			if (1 < count($parts)) {
				$unset[]= $name;
				$alts[$parts[0]] = $id;
				$alts[$parts[1]] = $id;
			}
		}
		/** @var array(string => int) $result */
		$result = array_diff_key($map, array_flip($unset)) + $alts;
		//Mage::log($result);
		df_assert($result);
		return $result;
	}

	/**
	 * @used-by \Df\Exline\Collector::locationDestId()
	 * @param string $cityNameUc
	 * @return int|null
	 */
	public static function findD($cityNameUc) {return self::_find('destination', $cityNameUc);}

	/**
	 * @used-by \Df\Exline\Collector::locationOrigId()
	 * @param string $cityNameUc
	 * @return int|null
	 */
	public static function findO($cityNameUc) {return self::_find('origin', $cityNameUc);}
}
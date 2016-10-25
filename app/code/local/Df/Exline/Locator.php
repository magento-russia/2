<?php
class Df_Exline_Locator extends Df_Shipping_Locator {
	/**
	 * @override
	 * @see Df_Shipping_Locator:: _map()
	 * @used-by Df_Shipping_Locator::map()
	 * @param string $type
	 * @return array(string => int)
	 */
	protected function _map($type) {
		/** @var array(string => string|int) $json */
		$json = Df_Exline_Request::i('regions/' . $type)->response()->json('regions');
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
	 * @used-by Df_Exline_Collector::locationDestId()
	 * @param string $cityNameUc
	 * @return int|null
	 */
	public static function findD($cityNameUc) {return self::_find(__CLASS__, 'destination', $cityNameUc);}

	/**
	 * @used-by Df_Exline_Collector::locationOrigId()
	 * @param string $cityNameUc
	 * @return int|null
	 */
	public static function findO($cityNameUc) {return self::_find(__CLASS__, 'origin', $cityNameUc);}
}
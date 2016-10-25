<?php
class Df_NovaPoshta_Locator extends Df_Shipping_Locator {
	/**
	 * @override
	 * @see Df_Shipping_Locator:: _map()
	 * @used-by Df_Shipping_Locator::map()
	 * @param string $type
	 * @return array(string => string[])
	 */
	protected function _map($type) {
		/** @var array(string -> string[]) $result */
		$result = array();
		/** @var phpQueryObject $pqItems */
		$pqItems = self::response()->pq('#' . self::getInputIdByType($type))->parent()->find('li');
		df_assert_gt0(count($pqItems));
		foreach ($pqItems as $domLi) {
			/** @var DOMNode $domLi */
			/** @var string $value */
			/** @var string $nameOriginal */
			$nameOriginal = $domLi->textContent;
			df_assert_string_not_empty($nameOriginal);
			/** @var string $nameNormalized */
			$nameNormalized = df_first(df_parentheses_explode($nameOriginal));
			df_assert_string_not_empty($nameNormalized);
			df_assert($domLi->attributes);
			/** @var DOMNode|null $domValue */
			$domValue = $domLi->attributes->getNamedItem('data-value');
			df_assert($domValue);
			/** @var string $label */
			$value = $domValue->nodeValue;
			df_assert_string_not_empty($value);
			/**
			 * Сохраняем оригинальное написание населённого пункта,
			 * потому что иначе расчёт сроков доставки приводил к сбою,
			 * когда в качестве параметра «recipientCity» было указано «Хмельник»
			 * вместо «Хмельник (Винницкая обл.)».
			 * @see Df_NovaPoshta_Collector::date()
			 */
			$result[$nameNormalized] = array($value, $nameOriginal);
		}
		return $result;
	}

	/**
	 * @used-by Df_NovaPoshta_Collector::locationDestId()
	 * @param string $cityNameUc
	 * @return string[]|null
	 */
	public static function findD($cityNameUc) {return self::_find(self::$D, $cityNameUc);}

	/**
	 * @used-by Df_NovaPoshta_Collector::locationOrigId()
	 * @param string $cityNameUc
	 * @return string[]|null
	 */
	public static function findO($cityNameUc) {return self::_find(self::$O, $cityNameUc);}

	/**
	 * @param string $type
	 * @return string
	 */
	private static function getInputIdByType($type) {
		/** @var string $result */
		$result = dfa(array(
			self::$D => 'DeliveryForm_recipientCity'
			,self::$O => 'DeliveryForm_senderCity_id'
		), $type);
		df_result_string_not_empty($result);
		return $result;
	}

	/**
	 * @used-by _map()
	 * @return Df_Shipping_Response
	 */
	private static function response() {
		/** @var Df_Shipping_Response $r */
		static $r;
		if (!$r) {
			/** @var Df_NovaPoshta_Request $request */
			$request = new Df_NovaPoshta_Request([
				Df_NovaPoshta_Request::P__QUERY_PATH => '/ru/delivery'
			]);
			$r = $request->response();
		}
		return $r;
	}

	/** @var string */
	private static $D = 'destination';
	/** @var string */
	private static $O = 'origin';
}
<?php
namespace Df\NovaPoshta;
class Locator extends \Df\Shipping\Locator {
	/**
	 * @override
	 * @see \Df\Shipping\Locator:: _map()
	 * @used-by \Df\Shipping\Locator::map()
	 * @param string $type
	 * @return array(string => string[])
	 */
	protected function _map($type) {
		/** @var array(string -> string[]) $result */
		$result = [];
		/** @var \phpQueryObject $pqItems */
		$pqItems = self::response()->pq('#' . self::getInputIdByType($type))->parent()->find('li');
		df_assert_gt0(count($pqItems));
		foreach ($pqItems as $domLi) {
			/** @var \DOMNode $domLi */
			/** @var string $value */
			/** @var string $nameOriginal */
			$nameOriginal = $domLi->textContent;
			df_assert_string_not_empty($nameOriginal);
			/** @var string $nameNormalized */
			$nameNormalized = df_first(df_parentheses_explode($nameOriginal));
			df_assert_string_not_empty($nameNormalized);
			df_assert($domLi->attributes);
			/** @var \DOMNode|null $domValue */
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
			 * @see \Df\NovaPoshta\Collector::date()
			 */
			$result[$nameNormalized] = array($value, $nameOriginal);
		}
		return $result;
	}

	/**
	 * @used-by \Df\NovaPoshta\Collector::locationDestId()
	 * @param string $cityNameUc
	 * @return string[]|null
	 */
	public static function findD($cityNameUc) {return self::_find(self::$D, $cityNameUc);}

	/**
	 * @used-by \Df\NovaPoshta\Collector::locationOrigId()
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
	 * @return \Df\Core\Response
	 */
	private static function response() {
		/** @var \Df\Core\Response $r */
		static $r;
		if (!$r) {
			$r = (new Request([Request::P__SUFFIX => 'ru/delivery']))->response();
		}
		return $r;
	}

	/** @var string */
	private static $D = 'destination';
	/** @var string */
	private static $O = 'origin';
}
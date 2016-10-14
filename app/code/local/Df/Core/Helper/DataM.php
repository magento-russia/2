<?php
class Df_Core_Helper_DataM extends Mage_Core_Helper_Data {
	/**
	 * @override
	 * @see Mage_Core_Helper_Abstract::__()
	 * @return string
	 */
	public function __() {$a = func_get_args(); return df_translate($a, 'Mage_Core');}

	/**
	 * Ничем не отличается от родительского метода из Magento CE 1.4.0.1 - 1.8.1.0.
	 * Метод перекрыт только ради перекрытия приватного метода
	 * @see Mage_Core_Helper_Data::_decorateArrayObject()
	 * @override
	 * @param mixed $array
	 * @param string $prefix
	 * @param bool $forceSetAll
	 * @return mixed
	 */
	public function decorateArray($array, $prefix = 'decorated_', $forceSetAll = false) {
		// check if array or an object to be iterated given
		if (!(is_array($array) || is_object($array))) {
			return $array;
		}
		$keyIsFirst = "{$prefix}is_first";
		$keyIsOdd   = "{$prefix}is_odd";
		$keyIsEven  = "{$prefix}is_even";
		$keyIsLast  = "{$prefix}is_last";
		$count  = count($array); // this will force Iterator to load
		$i      = 0;
		$isEven = false;
		foreach ($array as $key => $element) {
			if (is_object($element)) {
				$this->_decorateArrayObject($element, $keyIsFirst, (0 === $i), $forceSetAll || (0 === $i));
				$this->_decorateArrayObject($element, $keyIsOdd, !$isEven, $forceSetAll || !$isEven);
				$this->_decorateArrayObject($element, $keyIsEven, $isEven, $forceSetAll || $isEven);
				$isEven = !$isEven;
				$i++;
				$this->_decorateArrayObject($element, $keyIsLast, ($i === $count), $forceSetAll || ($i === $count));
			}
			elseif (is_array($element)) {
				if ($forceSetAll || (0 === $i)) {
					$array[$key][$keyIsFirst] = (0 === $i);
				}
				if ($forceSetAll || !$isEven) {
					$array[$key][$keyIsOdd] = !$isEven;
				}
				if ($forceSetAll || $isEven) {
					$array[$key][$keyIsEven] = $isEven;
				}
				$isEven = !$isEven;
				$i++;
				if ($forceSetAll || ($i === $count)) {
					$array[$key][$keyIsLast] = ($i === $count);
				}
			}
		}
		return $array;
	}

	/**
	 * @override
	 * @param string|string[] $data
	 * @param string[]|null $allowedTags [optional]
	 * @return string|string[]
	 */
	public function escapeHtml($data, $allowedTags = null) {
		/** @var int $tagLength */
		static $tagLength;
		if (!$tagLength) {
			$tagLength = mb_strlen(self::TAG__NO_ESCAPE);
		}
		/** @var string|string[] $result */
		$result =
			!is_string($data)
			? parent::escapeHtml($data, $allowedTags)
			: (
				df_starts_with($data, self::TAG__NO_ESCAPE)
				? mb_substr($data, $tagLength)
				: parent::escapeHtml($data, $allowedTags)
			)
		;
		return $result;
	}

	/**
	 * 2015-02-13
	 * Цель перекрытия —
	 * значением по умолчанию станет страна посетителя, определённая по его адресу IP.
	 * Родительский метод: @see Mage_Core_Helper_Data::getDefaultCountry()
	 * @override
	 * @param Df_Core_Model_StoreM|string|int|bool|null $store [optional]
	 * @return string
	 */
	public function getDefaultCountry($store = null)  {
		/** @var string|null $result */
		$result = df_is_admin() ? null : rm_visitor_location()->getCountryIso2();
		/**
		 * Не вызываем родительский метод @see Mage_Core_Helper_Data::getDefaultCountry(),
		 * потому что он отсутствует в Magento 1.4.0.1.
		 * Константа @see Mage_Core_Helper_Data::XML_PATH_DEFAULT_COUNTRY
		 * также отсутствует в Magento 1.4.0.1.
		 */
		return $result ? $result : Mage::getStoreConfig('general/country/default', $store);
	}

	/**
	 * @override
	 * @return bool
	 */
	public function useDbCompatibleMode() {
		/**
		 * Как ни странно, именно таким способом мы можем инициализировать Российскую сборку Magento
		 * до вызова установочных скриптов сторонних модулей.
		 * Это позволяет избежать сбоев в установочных скриптах сторонних модулей,
		 * когда из этих скриптов поток управления
		 * каким-либо образом попадает в Российскую сборку Magento,
		 * которая на момент запуска установочных скриптах сторонних модулей может не быть
		 * (и почти наверняка не будет) инициализирована.
		 * Пример сбоя:
		 * http://magento-forum.ru/topic/4174/
		 *
		 * Других адекватных способов за часы анализа я не нашёл.
		 * А этот способ, несмотря на его странность — работает.
		 * Дело в том, что метод @see Mage_Core_Helper_Data::useDbCompatibleMode()
		 * вызывается в начале метода @see Mage_Core_Model_Resource_Setup::applyUpdates(),
		 * и только для сторонних модулей:
			if (
		 			((string)$this->_moduleConfig->codePool != 'core')
		 		&&
		 			Mage::helper('core')->useDbCompatibleMode()
		 	) {
			  	$this->_hookQueries();
		  	}
		 *
		 * ОБРАТИТЕ ВНИМАНИЕ, что данный способ НЕ РАБОТАЕТ для Magento CE ниже 1.6,
		 * потому что там отсутствует вызов @see Mage_Core_Helper_Data::useDbCompatibleMode()
		 * Однако сама по себе данная проблема ограничена как по вероятности возниконовения
		 * (установка лишь некоторых редких сторонних модулей),
		 * так и по числу пользователей
		 * (среди магазинов на Российской сборки Magento мало кто еще
		 * использует устаревшие версии Magento CE ниже 1.6).
		 *
		 * Поэтому считаем данное решение удовлетворительным.
		 * Других решений за часы анализа не нашёл вовсе.
		 * Может быть, они и есть (скорей всего есть), однако трудозатраты по их поиску и анализу
		 * не стоят получаемого результата (редкая проблема для небольшой клиентской базы).
		 */
		Df_Core_Boot::run();
		return parent::useDbCompatibleMode();
	}

	/**
	 * Этот метод имеет 2 отличия от родительского:
	 * 1) концовка
	 * 2) игнорируем $dontSkip, иначе поле всё-таки может не быть инициализировано и будет сбой:
	 * http://magento-forum.ru/topic/4316/
	 * Нам нужна поддержка синтаксиса $_attribute->decoratedIsLast,
	 * чтобы не происходило сбоев типа: Undefined property: $decoratedIsLast
	 * В Magento CE такой синтаксис обратывается магическим методом
	 * @see Varien_Object::__set(), однако в Российской сборке Magento этот метод отсутствует
	 * ради поддержки $this->{__METHOD__}.
	 * http://magento-forum.ru/topic/4293/
	 * @param Varien_Object|object $element
	 * @param string $key
	 * @param mixed $value
	 * @param bool $dontSkip
	 */
	private function _decorateArrayObject($element, $key, $value, $dontSkip) {
		/**
		 * Игнорируем $dontSkip, иначе поле всё-таки может не быть инициализировано и будет сбой:
		 * http://magento-forum.ru/topic/4316/
		 */
		$dontSkip = true;
		if ($dontSkip) {
			if ($element instanceof Varien_Object) {
				$element->setData($key, $value);
			}
			else {
				$element->$key = $value;
			}
			// вот здесь единственное отличие от родительского метода
			/** @var string $camelizedKey */
			$camelizedKey = df_t()->lcfirst(df_t()->camelize($key));
			$element->$camelizedKey = $value;
		}
	}

	const TAG__NO_ESCAPE = '{#rm-no-escape#}';

	/** @return Df_Core_Helper_DataM */
	public static function s() {return Mage::helper('core');}
}
<?php
/**
 * Обратите внимание, что эта функция должна находиться именно в модуле Df_Core,
 * а не в модуле Df_Phpquery.
 * Перемещение этой функции в модуль Df_Phpquery
 * приведёт к сбою «Call to undefined function df_pq()»,
 * потому что перед использованием глобальных функций модуля Df_Phpquery
 * надо вызывать Df_Phpquery_Lib::s(),
 * а метод Df_Phpquery_Lib::s() вызывается именно внутри функции df_pq().
 * @param $arguments
 * @param $context [optional]
 * @return phpQueryObject
 */
function df_pq($arguments, $context = null) {
	Df_Phpquery_Lib::s();
	/** @var phpQueryObject|bool $result */
	$result = null;
	if (is_null($context) && is_string($arguments)) {
		$result = phpQuery::newDocument($arguments);
	}
	else {
		/** @var mixed[] $args */
		$args = func_get_args();
		$result = call_user_func_array(array('phpQuery', 'pq'), $args);
	}
	df_assert($result instanceof phpQueryObject);
	return $result;
}

/**
 * @used-by Df_Shipping_Response::options()
 * @param phpQueryObject $pqOptions
 * @return array(string => string)
 */
function df_pq_options(phpQueryObject $pqOptions) {
	Df_Phpquery_Lib::s();
	/** @var array(string => string) $result */
	$result = array();
	foreach ($pqOptions as $domOption) {
		/** @var DOMNode $domOption */
		/** @var string $label */
		$label = df_trim($domOption->textContent);
		// Этот алгоритм должен работать быстрее, чем df_pq($domOption)->val()
		if ('' !== $label) {
			/** @var string|null $value */
			$value = null;
			if (!is_null($domOption->attributes)) {
				/** @var DOMNode|null $domValue */
				$domValue = $domOption->attributes->getNamedItem('value');
				if (!is_null($domValue)) {
					$value = $domValue->nodeValue;
				}
			}
			$result[$label] = $value;
		}
	}
	return $result;
}


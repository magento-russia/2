<?php
// 2016-10-24
namespace Df\Zf;
use Zend_Uri_Http as U;
/**
 * Обратите внимание,
 * что обычное @see Zend_Uri::__toString() здесь для сравнения использовать нельзя,
 * потому что Zend Framework свежих версий Magento CE (заметил в Magento CE 1.9.0.1)
 * зачем-то добавляет ко второму веб-адресу $this->getHttpClient()->getUri()
 * порт по-умолчанию (80), даже если в первом веб-адресе ($this->getUri())
 * порт отсутствует.
 *
 * 2015-03-20
 * Более того, некоторые службы доставки со временем меняют своё решение
 * относительно использования «.www» в домене, и тогда мы получаем мусорные предупреждения
 * о неравенстве веб-адресов типа:
		«http://kazpost.kz/calc/cost.php?from=4&obcen=1&obcentenge=35245&to=11&v=1&w=1»
		«http://www.kazpost.kz:80/calc/cost.php?from=4&obcen=1&obcentenge=35245&to=11&v=1&w=1».
 * @return bool
 */
class UriComparator {
	/**
	 * 2016-10-24
	 * @param U $u1
	 * @param U $u2
	 * @return bool
	 */
	public static function c(U $u1, U $u2) {return
		$u1->getScheme() === $u2->getScheme()
		&& self::host($u1) === self::host($u2)
		&& self::port($u1) === self::port($u2)
		// 2015-03-23
		// Бывают в разном регистре.
		&& df_strings_are_equal_ci($u1->getQuery(), $u2->getQuery())
	;}

	/**
	 * 2016-10-24
	 * @param U $u
	 * @return string
	 */
	private static function host(U $u) {return df_trim_text_left($u->getHost(), 'www.');}

	/**
	 * 2016-10-24
	 * @param U $u
	 * @return int
	 */
	private static function port(U $u) {return
		intval($u->getPort()) ?: ('https' === $u->getScheme() ? 443 : 80)
	;}
}
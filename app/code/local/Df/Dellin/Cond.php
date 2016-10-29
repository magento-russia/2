<?php
// 2016-10-29
namespace Df\Dellin;
class Cond extends \Df\Shipping\Request {
	/**
	 * 2016-10-29
	 * @override
	 * @see \Df\Shipping\Request::_getRate()
	 * @return float
	 */
	protected function _getRate() {return $this->response()->json('price');}

	/**
	 * 2016-10-29
	 * @override
	 * @see \Df\Shipping\Request::postRaw()
	 * @return string
	 */
	protected function postRaw() {return dfc($this, function() {return df_json_encode([
		// 2016-10-29
		// «ключ для вашего приложения»
		'appKey' => '38D65A64-9B59-11E6-A9F5-00505683A6D3'
		// 2016-10-29
		// «код КЛАДР пункта прибытия»
		// Обязательный параметр.
		,'arrivalPoint' => $this[self::$P__DEST]
		// 2016-10-29
		// «дополнительные услуги для доставки груза до адреса»
		// Необязательный параметр.
		,'arrivalServices' => []
		// 2016-10-29
		// «позволяет заказать разгрузку»
		// Необязательный параметр.
		,'arrivalUnloading' => []
		// 2016-10-29
		// «необходима доставка груза от адреса»
		// Необязательный параметр.
		,'derivalDoor' => true
		// 2016-10-29
		// «позволяет заказать погрузку»
		// Необязательный параметр.
		,'derivalLoading' => []
		// 2016-10-29
		// «код КЛАДР пункта отправки»
		// Обязательный параметр.
		,'derivalPoint' => $this[self::$P__ORIG]
		// 2016-10-29
		// «дополнительные услуги для доставки груза от адреса»
		// Необязательный параметр.
		,'derivalServices' => []
		// 2016-10-29
		// «высота самого высокого из мест»
		// Необязательный параметр.
		,'height' => 1
		// 2016-10-29
		// «длина самого длинного из мест»
		// Необязательный параметр.
		,'length' => 1
		// 2016-10-29
		// «вес самого тяжёлого места»
		// Необязательный параметр.
		,'maxWeight' => 1
		// 2016-10-29
		// «объём негабаритной части груза в кубических метрах»
		// Необязательный параметр.
		,'oversizedVolume' => 0
		// 2016-10-29
		// «вес негабаритной части груза в килограммах»
		// Необязательный параметр.
		,'oversizedWeight' => 0
		// 2016-10-29
		// «необходимо упаковать груз в упаковку»
		// Необязательный параметр.
		,'packages' => ['0x838FC70BAEB49B564426B45B1D216C15']
		// 2016-10-29
		// «количество мест, по-умолчанию 1»
		// Необязательный параметр.
		,'quantity' => 1
		// 2016-10-29
		// «общий объём груза в кубических метрах»
		// Обязательный параметр.
		,'sizedVolume' => 1
		// 2016-10-29
		// «общий вес груза в килограммах»
		// Обязательный параметр.
		,'sizedWeight' => 1
		// 2016-10-29
		// «Заявленная стоимость груза в рублях.
		// При отсутствии - груз не страхуется,
		// при передаче 0 - страхуется без объявленной стоимости,
		// при передаче значения больше 0 - страхуется на указанную сумму.»
		// Необязательный параметр.
		,'statedValue' => 1000
		// 2016-10-29
		// «ширина самого широкого из мест»
		// Необязательный параметр.
		,'width' => 1
	]);});}

	/**
	 * 2016-10-29
	 * http://dev.dellin.ru/api/public/calculator/
	 * @override
	 * @return void
	 * @throws \Exception
	 */
	protected function responseFailureDetect() {
		/** @var string|string[] $errors */
		if ($errors = $this->response()->json('errors')) {
			df_error_html(!is_array($errors) ? $errors : df_cc_br(array_column($errors, 'message')));
		}
	}

	/**
	 * 2016-10-29
	 * @override
	 * @see \Df\Shipping\Request::uri()
	 * @used-by \Df\Shipping\Request::zuri()
	 * @return string
	 */
	protected function uri() {return 'https://api.dellin.ru/v1/public/calculator.json';}

	/**
	 * 2016-10-29
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__DEST, DF_V_STRING_NE)
			->_prop(self::$P__ORIG, DF_V_STRING_NE)
		;
	}

	/** @var string */
	private static $P__DEST = 'dest';
	/** @var string */
	private static $P__ORIG = 'orig';

	/**
	 * 2016-10-29
	 * @param string $orig
	 * @param string $dest
	 * @return self
	 */
	public static function i($orig, $dest) {return new self([
		self::$P__DEST => $dest, self::$P__ORIG => $orig
	]);}
}
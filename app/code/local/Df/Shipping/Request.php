<?php
namespace Df\Shipping;
abstract class Request extends \Df\Core\Request {
	/**
	 * Не все запросы к серверу предназначены для получения срока доставки,
	 * однако иметь метод @see deliveryTime() в базовом классе выгодно:
	 * смотрите комментарий к методу @see rate()
	 * @uses _deliveryTimeFilter()
	 * @uses _deliveryTime()
	 * @return int
	 */
	public function deliveryTime() {return $this->call();}

	/**
	 * Не все запросы к серверу предназначены для получения срока доставки,
	 * однако иметь метод @see deliveryTimeMax() в базовом классе выгодно:
	 * смотрите комментарий к методу @see rate()
	 * @uses _deliveryTimeMaxFilter()
	 * @uses _deliveryTimeMax()
	 * @return int
	 */
	public function deliveryTimeMax() {return $this->call();}

	/**
	 * Не все запросы к серверу предназначены для получения срока доставки,
	 * однако иметь метод @see deliveryTimeMin() в базовом классе выгодно:
	 * смотрите комментарий к методу @see rate()
	 * @uses _deliveryTimeMinFilter()
	 * @uses _deliveryTimeMin()
	 * @return int
	 */
	public function deliveryTimeMin() {return $this->call();}

	/**
	 * 2015-02-20
	 * Не все запросы к серверу предназначены для получения тарифа.
	 * Например, некоторые запросы предназначекны для получения перечня пунктов доставки.
	 * Однако, иметь метод @see rate() в базовом классе нам очень удобно:
	 * это позволяет не дублировать данную функциональность в тех классах-потомках, где она требуется.
	 * Другими словами, у нас ситуация: метод @see rate() нужен примерно половине классов-потомков,
	 * однако мы не можем вынести метод @see rate() в общий подкласс-родитель той половины
	 * классов потомков класса @see \Df\Shipping\Request, которым требуется метод @see @see rate(),
	 * потому что у этих классов уже есть своя иерархия (иерархия по службе доставки: у API каждой службы
	 * доставки ведь своя специфика и своя общая функциональность для всех потомков).
	 * @uses _filterRate()
	 * @uses _rate()
	 * @return float|int
	 */
	public function rate() {return $this->call();}

	/**
	 * 2016-10-31
	 * @override
	 * @see \Df\Core\Request::cacheType()
	 * @used-by \Df\Core\Request::getCache()
	 * @used-by \Df_Shipping_Setup_2_30_0::_process()
	 * @return string
	 */
	public static function cacheTypeS() {return __CLASS__;}

	/**
	 * @used-by \Df\Shipping\Request::deliveryTime()
	 * @param string|int $value
	 * @return int
	 */
	protected function _deliveryTimeFilter($value) {return df_nat($value);}

	/**
	 * @used-by \Df\Shipping\Request::deliveryTimeMax()
	 * @param string|int $value
	 * @return int
	 */
	protected function _deliveryTimeMaxFilter($value) {return $this->_deliveryTimeFilter($value);}

	/**
	 * @used-by \Df\Shipping\Request::deliveryTimeMin()
	 * @param string|int $value
	 * @return int
	 */
	protected function _deliveryTimeMinFilter($value) {return $this->_deliveryTimeFilter($value);}

	/**
	 * @used-by \Df\Shipping\Request::rate()
	 * @param float|int|string $value
	 * @return float
	 */
	protected function _filterRate($value) {return df_float_positive($value);}

	/**
	 * Этот метод предназначен для перекрытия потомками.
	 * @used-by \Df\Shipping\Request::deliveryTime()
	 * @return int|string
	 */
	protected function _deliveryTime() {df_abstract($this); return 0;}

	/**
	 * Этот метод предназначен для перекрытия потомками.
	 * @used-by \Df\Shipping\Request::rate()
	 * @return float|int|string
	 */
	protected function _rate() {df_abstract($this); return 0;}

	/**
	 * 2016-10-31
	 * @override
	 * @see \Df\Core\Request::apiName()
	 * @used-by \Df\Core\Request::getResponse()
	 * @used-by \Df\Core\Request::report()
	 * @return string
	 */
	protected function apiName() {return $this->carrier()->getTitle();}

	/**
	 * @used-by apiName()
	 * @return Carrier
	 */
	private function carrier() {return dfc($this, function() {
		/** @var string $className */
		$className = df_con($this, 'Carrier');
		/** @var Carrier $result */
		$result = new $className;
		df_assert($result instanceof Carrier);
		$result->setStore(df_store());
		return $result;
	});}
}
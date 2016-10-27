<?php
namespace Df\KazpostEms;
class Collector extends \Df\Shipping\Collector\Conditional\Kz {
	/**
	 * 2015-03-20
	 * В прежней версии сайта (январь 2014 года) было написано:
	 * «Предельный вес посылки экспресс - отправлений ЕМS составляет 20 кг.»
	 * http://www.kazpost.kz/ru/ekspress-dostavka-otpravleniy-ems
	 * Думаю, с тех пор ограничение не изменилось.
	 * @override
	 * @see \Df\Shipping\Collector\Conditional::collectPrepare()
	 * @used-by \Df\Shipping\Collector\Conditional::collect()
	 * @return void
	 */
	protected function collectPrepare() {$this->checkWeightIsLE(20);}
}
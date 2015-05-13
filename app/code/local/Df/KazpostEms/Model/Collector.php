<?php
class Df_KazpostEms_Model_Collector extends Df_Shipping_Model_Collector_Conditional_Kz {
	/**
	 * 2015-03-20
	 * В прежней версии сайта (январь 2014 года) было написано:
	 * «Предельный вес посылки экспресс - отправлений ЕМS составляет 20 кг.»
	 * @link http://www.kazpost.kz/ru/ekspress-dostavka-otpravleniy-ems
	 * Думаю, с тех пор ограничение не изменилось.
	 * @override
	 * @see Df_Shipping_Model_Collector_Conditional::collectPrepare()
	 * @used-by Df_Shipping_Model_Collector_Conditional::collect()
	 * @return void
	 */
	protected function collectPrepare() {$this->checkWeightIsLE(20);}
}
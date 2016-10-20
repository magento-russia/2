<?php
// 2016-10-20
class Df_Robokassa_Config_Area_Service extends Df_Payment_Config_Area_Service {
	/**
	 * 2016-10-20
	 * Пришлось перекрыть родительский метод,
	 * потому что у ROBOKASSA пропала возможность проводить платежи не в рублях,
	 * а в БД магазина может присутствовать прежнее нестандартное
	 * (и поэтому недопустимое теперь) значение опции payment_service__currency,
	 * которое нам надо проигнорировать.
	 * @override
	 * @see Df_Payment_Config_Area_Service::getCurrencyCode()
	 * @return string
	 */
	public function getCurrencyCode() {return 'RUB';}
}
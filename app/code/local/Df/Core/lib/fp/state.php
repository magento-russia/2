<?php
/**
 * 2016-11-10
 * @param string|string[] $name
 * @return string|bool
 */
function df_action_is($name) {
	/** @var string $actionName */
	$actionName = df_action_name();
	return 1 === func_num_args()
		? $actionName === $name
		: in_array($name, df_args(func_get_args()))
	;
}

/**
 * 2016-11-10
 * @return string|null
 */
function df_action_name() {return
	!rm_state()->getController() ? null : rm_state()->getController()->getFullActionName();
}

/**
 * 2015-08-14
 * Мы не вправе кэшировать результат работы функции: ведь текущий магазин может меняться.
 *
 * Раньше тут стояло Mage::app()->getStore()->isAdmin(),
 * однако, isAdmin проверяет, является ли магазин административным,
 * более наивным способом: сравнивая идентификатор магазина с нулем
 * (подразумевая, что 0 — идентификатор административного магазина).
 * Как оказалось, у некоторых клиентов идентификатор административного магазина
 * не равен нулю (видимо, что-то не то делали с базой данных).
 * Поэтому используем более надёжную проверку — кода магазина.
 *
 * @return bool
 */
function df_is_admin() {return 'admin' === Mage::app()->getStore()->getCode();}

/**
 * 2016-07-08
 * Возвращает двухбуквенный код языка в нижнем регистре, например: «ru», «en», «pl».
 * @return string
 */
function df_lang() {
	static $r; return $r ? $r : $r = rm_first(explode('_', Mage::app()->getLocale()->getLocaleCode()));
}

/**
 * 2015-08-14
 * @return string
 */
function rm_ruri() {static $r; return $r ? $r : $r = Mage::app()->getRequest()->getRequestUri();}
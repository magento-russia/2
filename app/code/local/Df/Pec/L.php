<?php
// 2016-10-31
namespace Df\Pec;
/**
{
	Абакан: {
		66777: "Новоселово пос.",
		66787: "Ужур",
		104646: "Черногорск",
		104648: "Минусинск",
		131011: "Усть-Абакан",
		211479: "Краснотуранск (Краснотуранский р-н)",
		317459: "Ермаковское (Ермаковский р-н)",
		317461: "Каратузское (Каратузский р-н)",
		<...>
		-584988: "Абакан"
	},
	Аксай: {
		64329: "Большой Лог (Аксайский р-н)",
		65527: "Аксайский р-н, поле № 16, Логопарк",
		241576: "Ленина (Аксайский р-н)",
		300233: "Мишкинская (Аксайский р-н)",
		321663: "Реконструктор (Аксайский р-н)",
		-297982: "Аксай"
	},
	<...>
}
 */
class L extends \Df\Shipping\Locator {
	/**
	 * 2016-10-31
	 * @param string $name
	 * @return string|null
	 */
	public static function find($name) {return self::_find(null, $name);}

	/**
	 * 2016-10-31
	 * @override
	 * @see \Df\Shipping\Locator::_map()
	 * @used-by \Df\Shipping\Locator::map()
	 * @param string $type
	 * @return array(string => string|int|array(string|int))
	 */
	protected function _map($type) {return
		df_parentheses_clean_k(
			call_user_func_array('array_merge',
				array_map('array_flip',
					array_values(df_http_json('https://pecom.ru/ru/calc/towns.php'))
				)
			)
		)
	;}
}
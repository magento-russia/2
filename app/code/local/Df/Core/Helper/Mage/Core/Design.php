<?php
class Df_Core_Helper_Mage_Core_Design extends Mage_Core_Helper_Abstract {
	/** @return string */
	public function getThemeFrontend() {
		/**
		 * getTheme('template') работает в том случае, когда оформительская тема задана
		 * посредством административного меню «Система» → «Оформление витрины»
		 */
		/**
		 * Раньше тут стояло
		 * $theme = rm_design_package()->getTheme('template');
		 * То есть, мы использовали в качестве идентификатора темы
		 * значение опции «Нестандартная папка шаблонов».
		 * Однако в оформительской теме Gala TitanShop в одном из демо-примеров
		 * (и в других аналогично) значением опции «Нестандартная папка шаблонов» является
		 * «galatitanshop_lingries_style01»,
		 * в то время как опция «Нестандартная папка темы» имеет правильное значение
		 * «galatitanshop».
		 * Поэтому вместо
		 * $theme = rm_design_package()->getTheme('template');
		 * я решил использовать
		 * $theme = rm_design_package()->getTheme('default');
		 * Передавая в метод getTheme() параметр «default», мы извлекаем значение опции
		 * «Нестандартная папка темы».
		 */
		/** @var string $result */
		$result = rm_design_package()->getTheme('default');
		return $result ? $result : rm_design_package()->getTheme('frontend');
	}

	/** @return Df_Core_Helper_Mage_Core_Design */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}
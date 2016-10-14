<?php
/**
 * 2015-04-19
 * Значения для опции «Для расчёта размера скидки использовать стоимость заказа без НДС или с НДС?».
 * Читайте комментарий к соответствующей опции в разделе
 * «Система» → «Настройки» → «Продажи» → «НДС» → «Параметры начисления»
 * 2015-01-31
 * Обратите внимание,
 * что данный класс замещает стандартный класс @see Mage_Tax_Model_System_Config_Source_PriceType
 * Замещение происходит не посредством директивы rewrite,
 * а посредством перекрытия source_model в файле Df/Tax/etc/system.xml:
	<discount_tax>
		<source_model>Df_Tax_Config_Source_ApplyDiscountOnPrices</source_model>
	</discount_tax>
 * Обратите внимание, что в оригинальном классе @see Mage_Tax_Model_System_Config_Source_PriceType
 * англоязычные назания опций — другие («Excluding Tax» и «Including Tax»).
 * Мы переименовали их в том числе и на английском языке,
 * чтобы правильно строились фразы на русском языке.
 */
class Df_Tax_Config_Source_ApplyDiscountOnPrices {
	/** @return array(array(string => string)) */
	public function toOptionArray() {
		return df_map_to_options(array('Before Tax', 'After Tax'), 'Mage_Tax');
	}
}
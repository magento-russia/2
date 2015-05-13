<?php
abstract class Df_1C_Model_Cml2_Import_Processor_Product_Type_Simple_Abstract
	extends Df_1C_Model_Cml2_Import_Processor_Product_Type {
	/**
	 * Обратите внимание, что 1С может вполне не передавать цену.
	 * Это возможно в следующих ситуациях:
	 * 1) Когда цена на товар отсутствует в 1С
	 * 2) Когда передача цен отключена в настройках узла обмена
	 * (а это возможно, как минимум, в новых версиях модуля 1С-Битрикс (ветка 4)).
	 * 3) В новых версиях  модуля 1С-Битрикс (ветка 4, CommerceML 2.0.8)
	 * 1С передаёт цены не в файле offers.xml (как было в прежних версиях),
	 * а отдельным файлом prices_*.xml, который передаётся после файла offers_*.xml,
	 * в то время как файл offers_*.xml цен не содержит.
	 * @override
	 * @return float|null
	 */
	protected function getPrice() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set(
				is_null($this->getEntityOffer()->getPrices()->getMain())
				? null
				: $this->getEntityOffer()->getPrices()->getMain()->getPriceBase()
			);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getType() {return Mage_Catalog_Model_Product_Type::TYPE_SIMPLE;}

	/**
	 * @override
	 * @return int
	 */
	protected function getVisibility() {return Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH;}

	const _CLASS = __CLASS__;
}
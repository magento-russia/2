<?php
class Df_1C_Cml2_Import_Data_Entity_OfferPart_Price extends Df_1C_Cml2_Import_Data_Entity {
	/** @return string */
	public function getCurrencyCode() {
		return rm_1c_currency_code_to_magento_format($this->leafSne('Валюта'));
	}

	/**
	 * @override
	 * @return string
	 */
	public function getExternalId() {return $this->leafSne('ИдТипаЦены');}

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
	 * @return float|null
	 */
	public function getPrice() {
		if (!isset($this->{__METHOD__})) {
			/** @var string|null $result */
			$resultS = $this->leaf('ЦенаЗаЕдиницу');
			$this->{__METHOD__} = rm_n_set(!$resultS ? null : rm_float($resultS));
		}
		return rm_n_get($this->{__METHOD__});
	}

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
	 * @return float|null
	 */
	public function getPriceBase() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set(
				is_null($this->getPrice())
				? null
				: rm_currency_h()->convertToBase($this->getPrice(), $this->getCurrencyCode())
			);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return Df_1C_Cml2_Import_Data_Entity_PriceType */
	public function getPriceType() {
		return $this->getState()->getPriceTypes()->findByExternalId($this->getId());
	}

	/** @used-by Df_1C_Cml2_Import_Data_Collection_OfferPart_Prices::itemClass() */
	const _C = __CLASS__;
}
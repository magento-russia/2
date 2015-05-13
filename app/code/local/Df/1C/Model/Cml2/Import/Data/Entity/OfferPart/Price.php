<?php
class Df_1C_Model_Cml2_Import_Data_Entity_OfferPart_Price
	extends Df_1C_Model_Cml2_Import_Data_Entity {
	/** @return string */
	public function getCurrencyCode() {
		return df_h()->_1c()->cml2()->convertCurrencyCodeToMagentoFormat(
			$this->getEntityParam('Валюта')
		);
	}

	/**
	 * @override
	 * @return string
	 */
	public function getExternalId() {return $this->getEntityParam('ИдТипаЦены');}

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
			/** @var float|null $result */
			$result = $this->getEntityParam('ЦенаЗаЕдиницу');
			$result = !$result ? null : rm_float($result);
			$this->{__METHOD__} = rm_n_set($result);
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
				: rm_currency()->convertToBase($this->getPrice(), $this->getCurrencyCode())
			);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return Df_1C_Model_Cml2_Import_Data_Entity_PriceType */
	public function getPriceType() {
		return $this->getState()->getPriceTypes()->findByExternalId($this->getId());
	}

	/**
	 * Используется из @see Df_1C_Model_Cml2_Import_Data_Collection_OfferPart_Prices::getItemClass()
	 */
	const _CLASS = __CLASS__;
}
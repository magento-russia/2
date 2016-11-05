<?php
namespace Df\C1\Cml2\Import\Data\Entity\OfferPart;
use Df\C1\Cml2\Import\Data\Entity\PriceType;
class Price extends \Df\C1\Cml2\Import\Data\Entity {
	/** @return string */
	public function getCurrencyCode() {return
		df_c1_currency_code_to_magento_format($this->leafSne('Валюта'))
	;}

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
	public function getPrice() {return dfc($this, function() {
		/** @var string|null $result */
		$resultS = $this->leaf('ЦенаЗаЕдиницу');
		return !$resultS ? null : df_float($resultS);
	});}

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
	public function getPriceBase() {return dfc($this, function() {return
		is_null($this->getPrice()) ?: 
			df_currency_h()->convertToBase($this->getPrice(), $this->getCurrencyCode())				
	;});}

	/** @return PriceType */
	public function getPriceType() {return
		$this->getState()->getPriceTypes()->findByExternalId($this->getId())
	;}
}
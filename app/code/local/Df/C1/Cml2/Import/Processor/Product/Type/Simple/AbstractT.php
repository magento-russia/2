<?php
namespace Df\C1\Cml2\Import\Processor\Product\Type\Simple;
use Mage_Catalog_Model_Product_Visibility as Visibility;
abstract class AbstractT extends \Df\C1\Cml2\Import\Processor\Product\Type {
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
	protected function getPrice() {return dfc($this, function() {return
		is_null($this->getEntityOffer()->getPrices()->getMain())
		? null
		: $this->getEntityOffer()->getPrices()->getMain()->getPriceBase()
	;});}

	/**
	 * @override
	 * @return string
	 */
	protected function getType() {return \Mage_Catalog_Model_Product_Type::TYPE_SIMPLE;}

	/**
	 * @override
	 * @return int
	 */
	protected function getVisibility() {return Visibility::VISIBILITY_BOTH;}
}
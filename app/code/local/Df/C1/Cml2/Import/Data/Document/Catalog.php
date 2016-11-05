<?php
namespace Df\C1\Cml2\Import\Data\Document;
class Catalog extends \Df\C1\Cml2\Import\Data\Document {
	/**
	 * 2015-08-04
	 * @override
	 * @see \Df\C1\Cml2\Import\Data\Document::getExternalId_CatalogAttributes()
	 * @return string
	 */
	public function getExternalId_CatalogAttributes() {return dfc($this, function() {
		/** @var mixed $result */
		$result = $this->descendS('Каталог/ИдКлассификатора');
		df_result_string_not_empty($result);
		return $result;
	});}

	/**
	 * @override
	 * @see \Df\C1\Cml2\Import\Data\Document::getExternalId_CatalogProducts()
	 * @return string
	 */
	public function getExternalId_CatalogProducts() {return dfc($this, function() {
		df_assert($this->isOffers());
		/** @var string $result */
		$result = $this->descendS('Каталог/Ид');
		df_result_string_not_empty($result);
		return $result;
	});}

	/**
	 * @override
	 * @see \Df\C1\Cml2\Import\Data\Document::getExternalId_CatalogStructure()
	 * @return string
	 */
	public function getExternalId_CatalogStructure() {return dfc($this, function() {
		df_assert($this->isOffers());
		/** @var string $result */
		$result = $this->descendS('Каталог/ИдКлассификатора');
		df_result_string_not_empty($result);
		return $result;
	});}

	/**
	 * 2015-08-04
	 * Раньше товарные свойства импортировались при возвращении true методом @see hasStructure()
	 * Однако сегодня, тестируя версию 5.0.6 модуля 1С-Битрикс (CommerceML версии 2.09)
	 * заметил, что первый файл import__*.xml, который 1С передаёт интернет-магазину,
	 * внутри ветки Классификатор содержит подветки Группы, ТипыЦен, Склады, ЕдиницыИзмерения,
	 * однако не содержит подветку Свойства.
	 * Подветка Свойства передаётся уже следующим файлом import__*.xml.
	 * @used-by \Df\C1\Cml2\Action\Catalog\Import::_process()
	 * @return bool
	 */
	public function hasAttributes() {return dfc($this, function() {return
		$this->e()->descend('Классификатор/Свойства')
	;});}

	/**
	 * Начиная с ветки 4 модуля 1С-Битрикс
	 * http://dev.1c-bitrix.ru/community/blogs/product_features/exchange-module-with-1cbitrix-40.php
	 * и версии 2.08 стандарта CommerceML (а, может, и чуть раньше)
	 * http://www.v8.1c.ru/edi/edi_stnd/90/92.htm
	 * 1С:Управление торговлей присылает в интернет-магазин не один файл catalog.xml, как раньше,
	 * а минимум 2 таких файла, причём первый из них содержит только структуру и справочники каталога
	 * (разделы, типы цен, единицы измерения),
	 * а все последующие — уже сами товары, разбитые на пакеты.
	 * @return bool
	 */
	public function hasProducts() {return dfc($this, function() {return
		!!$this->e()->descend('Каталог/Товары')
	;});}

	/**
	 * Начиная с ветки 4 модуля 1С-Битрикс
	 * http://dev.1c-bitrix.ru/community/blogs/product_features/exchange-module-with-1cbitrix-40.php
	 * и версии 2.08 стандарта CommerceML (а, может, и чуть раньше)
	 * http://www.v8.1c.ru/edi/edi_stnd/90/92.htm
	 * 1С:Управление торговлей присылает в интернет-магазин не один файл catalog.xml, как раньше,
	 * а минимум 2 таких файла, причём первый из них содержит только структуру и справочники каталога
	 * (разделы, типы цен, единицы измерения),
	 * а все последующие — уже сами товары, разбитые на пакеты.
	 * @return bool
	 */
	public function hasStructure() {return dfc($this, function() {return
		$this->e()->descend('Классификатор/Группы')
		|| $this->e()->descend('Классификатор/ТипыЦен')
		|| $this->e()->descend('Классификатор/ЕдиницыИзмерения')
	;});}

	/**
	 * @override
	 * @return void
	 */
	public function storeInSession() {
		$this->session()->begin();
		/**
		 * Начиная с ветки 4 модуля 1С-Битрикс
		 * http://dev.1c-bitrix.ru/community/blogs/product_features/exchange-module-with-1cbitrix-40.php
		 * и версии 2.08 стандарта CommerceML (а, может, и чуть раньше)
		 * http://www.v8.1c.ru/edi/edi_stnd/90/92.htm
		 * 1С:Управление торговлей присылает в интернет-магазин не один файл catalog.xml, как раньше,
		 * а минимум 2 таких файла, причём первый из них содержит только структуру и справочники каталога
		 * (разделы, типы цен, единицы измерения),
		 * а все последующие — уже сами товары, разбитые на пакеты.
		 */
		if ($this->hasStructure()) {
			$this->session()->setFilePathById(
				self::TYPE__STRUCTURE, $this->getIdStructure(), $this->getPath()
			);
		}
		if ($this->hasProducts()) {
			$this->session()->setFilePathById(
				self::TYPE__PRODUCTS, $this->getIdProducts(), $this->getPath()
			);
		}
		/**
		 * 2015-08-04
		 * Раньше (модуль 1С-Битрикс 4 / CommerceML 2.08)
		 * 1С передавала товарные свойства вместе со структурой каталога.
		 * Однако сегодня, тестируя версию 5.0.6 модуля 1С-Битрикс (CommerceML версии 2.09)
		 * заметил, что первый файл import__*.xml, который 1С передаёт интернет-магазину,
		 * внутри ветки Классификатор содержит подветки Группы, ТипыЦен, Склады, ЕдиницыИзмерения,
		 * однако не содержит подветку Свойства.
		 * Подветка Свойства передаётся уже следующим файлом import__*.xml.
		 */
		if ($this->hasAttributes()) {
			$this->session()->setFilePathById(
				self::TYPE__ATTRIBUTES, $this->getIdAttributes(), $this->getPath()
			);
		}
		$this->session()->end();
	}

	/** @return string|null */
	private function getIdAttributes() {return dfc($this, function() {return
		$this->descendS('Классификатор/Ид')
	;});}

	/** @return string|null */
	private function getIdProducts() {return dfc($this, function() {return
		$this->descendS('Каталог/Ид')
	;});}

	/** @return string|null */
	private function getIdStructure() {return dfc($this, function() {return
		$this->descendS('Классификатор/Ид')
	;});}

	/**
	 * 2015-08-04
	 * Раньше (модуль 1С-Битрикс 4 / CommerceML 2.08)
	 * 1С передавала товарные свойства вместе со структурой каталога.
	 * Однако сегодня, тестируя версию 5.0.6 модуля 1С-Битрикс (CommerceML версии 2.09)
	 * заметил, что первый файл import__*.xml, который 1С передаёт интернет-магазину,
	 * внутри ветки Классификатор содержит подветки Группы, ТипыЦен, Склады, ЕдиницыИзмерения,
	 * однако не содержит подветку Свойства.
	 * Подветка Свойства передаётся уже следующим файлом import__*.xml.
	 * @used-by storeInSession()
	 * @used-by \Df\C1\Cml2\State\Import::getFileCatalogAttributes()
	 */
	const TYPE__ATTRIBUTES = 'catalog_attributes';
	/**
	 * @used-by storeInSession()
	 * @used-by \Df\C1\Cml2\State\Import::getFileCatalogProducts()
	 */
	const TYPE__PRODUCTS = 'catalog_products';
	/**
	 * @used-by storeInSession()
	 * @used-by \Df\C1\Cml2\State\Import::getFileCatalogStructure()
	 */
	const TYPE__STRUCTURE = 'catalog_structure';
}
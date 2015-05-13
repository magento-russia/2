<?php
class Df_1C_Model_Cml2_Import_Data_Document_Catalog extends Df_1C_Model_Cml2_Import_Data_Document {
	/**
	 * @override
	 * @return string
	 */
	public function getExternalId_CatalogProducts() {
		if (!isset($this->{__METHOD__})) {
			df_assert($this->isOffers());
			$this->{__METHOD__} = $this->descendS('Каталог/Ид');
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	public function getExternalId_CatalogStructure() {
		if (!isset($this->{__METHOD__})) {
			df_assert($this->isOffers());
			$this->{__METHOD__} = $this->descendS('Каталог/ИдКлассификатора');
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * Начиная с ветки 4 модуля 1С-Битрикс
	 * @link http://dev.1c-bitrix.ru/community/blogs/product_features/exchange-module-with-1cbitrix-40.php
	 * и версии 2.08 стандарта CommerceML (а, может, и чуть раньше)
	 * @link http://www.v8.1c.ru/edi/edi_stnd/90/92.htm
	 * 1С:Управление торговлей присылает в интернет-магазин не один файл catalog.xml, как раньше,
	 * а минимум 2 таких файла, причём первый из них содержит только структуру и справочники каталога
	 * (разделы, типы цен, единицы измерения),
	 * а все последующие — уже сами товары, разбитые на пакеты.
	 * @return bool
	 */
	public function hasProducts() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = !!$this->e()->descend('Каталог/Товары');
		}
		return $this->{__METHOD__};
	}

	/**
	 * Начиная с ветки 4 модуля 1С-Битрикс
	 * @link http://dev.1c-bitrix.ru/community/blogs/product_features/exchange-module-with-1cbitrix-40.php
	 * и версии 2.08 стандарта CommerceML (а, может, и чуть раньше)
	 * @link http://www.v8.1c.ru/edi/edi_stnd/90/92.htm
	 * 1С:Управление торговлей присылает в интернет-магазин не один файл catalog.xml, как раньше,
	 * а минимум 2 таких файла, причём первый из них содержит только структуру и справочники каталога
	 * (разделы, типы цен, единицы измерения),
	 * а все последующие — уже сами товары, разбитые на пакеты.
	 * @return bool
	 */
	public function hasStructure() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
					$this->e()->descend('Классификатор/Группы')
				||
					$this->e()->descend('Классификатор/ТипыЦен')
				||
					$this->e()->descend('Классификатор/ЕдиницыИзмерения')
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	public function storeInSession() {
		$this->session()->begin();
		/**
		 * Начиная с ветки 4 модуля 1С-Битрикс
		 * @link http://dev.1c-bitrix.ru/community/blogs/product_features/exchange-module-with-1cbitrix-40.php
		 * и версии 2.08 стандарта CommerceML (а, может, и чуть раньше)
		 * @link http://www.v8.1c.ru/edi/edi_stnd/90/92.htm
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
		$this->session()->end();
	}

	/** @return string|null */
	private function getIdProducts() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set($this->descendS('Каталог/Ид'));
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return string|null */
	private function getIdStructure() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set($this->descendS('Классификатор/Ид'));
		}
		return rm_n_get($this->{__METHOD__});
	}

	const _CLASS = __CLASS__;
	const TYPE__PRODUCTS = 'catalog_products';
	const TYPE__STRUCTURE = 'catalog_structure';
}
<?php
class Df_1C_Cml2_State_Import extends Df_Core_Model {
	/** @return Df_1C_Cml2_State_Import_Collections */
	public function collections() {return Df_1C_Cml2_State_Import_Collections::s();}

	/** @return Df_1C_Cml2_Import_Data_Document */
	public function getDocumentCurrent() {return $this->getFileCurrent()->getXmlDocument();}

	/** @return Df_1C_Cml2_Import_Data_Document_Offers */
	public function getDocumentCurrentAsOffers() {
		return $this->getFileCurrent()->getXmlDocumentAsOffers();
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
	 * @used-by getFileCatalogComposite()
	 * @used-by Df_1C_Cml2_State_Import_Collections::getAttributes()
	 * @param bool $preprareSession [optional]
	 * @return Df_1C_Cml2_File
	 */
	public function getFileCatalogAttributes($preprareSession = true) {
		if (!isset($this->{__METHOD__})) {
			if (
					$this->getDocumentCurrent()->isCatalog()
				&&
					$this->getDocumentCurrentAsCatalog()->hasAttributes()
			) {
				$this->{__METHOD__} = $this->getFileCurrent();
			}
			else {
				if ($preprareSession) {
					Df_1C_Cml2_Session_ByIp::s()->begin();
				}
				$this->{__METHOD__} =
					Df_1C_Cml2_File::i(
						Df_1C_Cml2_Session_ByIp::s()->getFilePathById(
							Df_1C_Cml2_Import_Data_Document_Catalog::TYPE__ATTRIBUTES
							, $this->getDocumentCurrent()->getExternalId_CatalogAttributes()
						)
					)
				;
				if ($preprareSession) {
					Df_1C_Cml2_Session_ByIp::s()->end();
				}
			}
		}
		return $this->{__METHOD__};
	}

	/** @return Df_1C_Cml2_File */
	public function getFileCatalogComposite() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_1C_Cml2_File $result */
			if (
				$this->getDocumentCurrent()->isCatalog()
				&& $this->getDocumentCurrentAsCatalog()->hasAttributes()
				&& $this->getDocumentCurrentAsCatalog()->hasProducts()
				&& $this->getDocumentCurrentAsCatalog()->hasStructure()
			) {
				$result = $this->getFileCurrent();
			}
			else {
				Df_1C_Cml2_Session_ByIp::s()->begin();
				/** @var Df_1C_Cml2_File $fileAttributes */
				$fileAttributes = $this->getFileCatalogAttributes($preprareSession = false);
				/** @var Df_1C_Cml2_File $fileProducts */
				$fileProducts = $this->getFileCatalogProducts($preprareSession = false);
				/** @var Df_1C_Cml2_File $fileStructure */
				$fileStructure = $this->getFileCatalogStructure($preprareSession = false);
				Df_1C_Cml2_Session_ByIp::s()->end();
				$result =
					$fileProducts->getPathRelative() === $fileStructure->getPathRelative()
					&& $fileProducts->getPathRelative() === $fileAttributes->getPathRelative()
					? $fileProducts
					: Df_1C_Cml2_File_CatalogComposite::i2($fileStructure, $fileProducts, $fileAttributes)
				;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param bool $preprareSession [optional]
	 * @return Df_1C_Cml2_File
	 */
	public function getFileCatalogProducts($preprareSession = true) {
		if (!isset($this->{__METHOD__})) {
			if (
					$this->getDocumentCurrent()->isCatalog()
				&&
					$this->getDocumentCurrentAsCatalog()->hasProducts()
			) {
				$this->{__METHOD__} = $this->getFileCurrent();
			}
			else {
				if ($preprareSession) {
					Df_1C_Cml2_Session_ByIp::s()->begin();
				}
				$this->{__METHOD__} =
					Df_1C_Cml2_File::i(
						Df_1C_Cml2_Session_ByIp::s()->getFilePathById(
							Df_1C_Cml2_Import_Data_Document_Catalog::TYPE__PRODUCTS
							, $this->getDocumentCurrent()->getExternalId_CatalogProducts()
						)
					)
				;
				if ($preprareSession) {
					Df_1C_Cml2_Session_ByIp::s()->end();
				}
			}
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param bool $preprareSession [optional]
	 * @return Df_1C_Cml2_File
	 */
	public function getFileCatalogStructure($preprareSession = true) {
		if (!isset($this->{__METHOD__})) {
			if (
					$this->getDocumentCurrent()->isCatalog()
				&&
					$this->getDocumentCurrentAsCatalog()->hasStructure()
			) {
				$this->{__METHOD__} = $this->getFileCurrent();
			}
			else {
				if ($preprareSession) {
					Df_1C_Cml2_Session_ByIp::s()->begin();
				}
				$this->{__METHOD__} =
					Df_1C_Cml2_File::i(
						Df_1C_Cml2_Session_ByIp::s()->getFilePathById(
							Df_1C_Cml2_Import_Data_Document_Catalog::TYPE__STRUCTURE
							, $this->getDocumentCurrent()->getExternalId_CatalogStructure()
						)
					)
				;
				if ($preprareSession) {
					Df_1C_Cml2_Session_ByIp::s()->end();
				}
			}
		}
		return $this->{__METHOD__};
	}

	/** @return Df_1C_Cml2_File */
	public function getFileCurrent() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * Обратите внимание,
			 * что «filename» может быть не просто именем файла (catalog.xml, offers.xml),
			 * но и именем файла с относительным путём (для файлов картинок), например:
			 * import_files/cb/cbcf4934-55bc-11d9-848a-00112f43529a_b5cfbe1a-c400-11e1-a851-4061868fc6eb.jpeg
			 * @var string $relativePath
			 */
			$relativePath = Mage::app()->getRequest()->getParam('filename');
			if (!df_check_string_not_empty($relativePath)) {
				df_error(
					'Учётная система нарушает протокол обмена данными.'
					."\nВ данном сценарии она должна была передать"
					." в адресной строке параметр «filename»."
				);
			}
			$this->{__METHOD__} = Df_1C_Cml2_File::i($relativePath);
		}
		return $this->{__METHOD__};
	}

	/** @return Df_1C_Cml2_File */
	public function getFileOffers() {
		if (!isset($this->{__METHOD__})) {
			df_assert($this->getFileCurrent()->getXmlDocument()->isOffers());
			$this->{__METHOD__} = $this->getFileCurrent();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param bool $preprareSession [optional]
	 * @return Df_1C_Cml2_File
	 */
	public function getFileOffersBase($preprareSession = true) {
		df_assert($this->getDocumentCurrent()->isOffers());
		if (!isset($this->{__METHOD__})) {
			if ($this->getDocumentCurrentAsOffers()->isBase()) {
				$this->{__METHOD__} = $this->getFileCurrent();
			}
			else {
				if ($preprareSession) {
					Df_1C_Cml2_Session_ByIp::s()->begin();
				}
				$this->{__METHOD__} =
					Df_1C_Cml2_File::i(
						Df_1C_Cml2_Session_ByIp::s()->getFilePathById(
							Df_1C_Cml2_Import_Data_Document_Offers::TYPE__BASE
							, $this->getDocumentCurrentAsOffers()->getExternalId()
						)
					)
				;
				if ($preprareSession) {
					Df_1C_Cml2_Session_ByIp::s()->end();
				}
			}
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param bool $preprareSession [optional]
	 * @return Df_1C_Cml2_File
	 */
	public function getFileOffersPrices($preprareSession = true) {
		df_assert($this->getDocumentCurrent()->isOffers());
		if (!isset($this->{__METHOD__})) {
			if ($this->getDocumentCurrentAsOffers()->hasPrices()) {
				$this->{__METHOD__} = $this->getFileCurrent();
			}
			else {
				if ($preprareSession) {
					Df_1C_Cml2_Session_ByIp::s()->begin();
				}
				$this->{__METHOD__} =
					Df_1C_Cml2_File::i(
						Df_1C_Cml2_Session_ByIp::s()->getFilePathById(
							Df_1C_Cml2_Import_Data_Document_Offers::TYPE__PRICES
							, $this->getDocumentCurrentAsOffers()->getExternalId()
						)
					)
				;
				if ($preprareSession) {
					Df_1C_Cml2_Session_ByIp::s()->end();
				}
			}
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param bool $preprareSession [optional]
	 * @return Df_1C_Cml2_File
	 */
	public function getFileOffersStock($preprareSession = true) {
		df_assert($this->getDocumentCurrent()->isOffers());
		if (!isset($this->{__METHOD__})) {
			if ($this->getDocumentCurrentAsOffers()->hasStock()) {
				$this->{__METHOD__} = $this->getFileCurrent();
			}
			else {
				if ($preprareSession) {
					Df_1C_Cml2_Session_ByIp::s()->begin();
				}
				$this->{__METHOD__} =
					Df_1C_Cml2_File::i(
						Df_1C_Cml2_Session_ByIp::s()->getFilePathById(
							Df_1C_Cml2_Import_Data_Document_Offers::TYPE__STOCK
							, $this->getDocumentCurrentAsOffers()->getExternalId()
						)
					)
				;
				if ($preprareSession) {
					Df_1C_Cml2_Session_ByIp::s()->end();
				}
			}
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Catalog_Model_Category */
	public function getRootCategory() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Catalog_Model_Category::ld($this->getRootCategoryId());
		}
		return $this->{__METHOD__};
	}

	/** @return Df_1C_Cml2_Import_Data_Document_Catalog */
	private function getDocumentCurrentAsCatalog() {
		return $this->getFileCurrent()->getXmlDocumentAsCatalog();
	}

	/** @return int */
	private function getRootCategoryId() {
		if (!isset($this->{__METHOD__})) {
			/** @var int $result */
			$result = df_nat0(rm_state()->getStoreProcessed()->getRootCategoryId());
			if (0 === $result) {
				df_error('В обрабатываемом магазине должен присутствовать корневой товарный раздел');
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_1C_Cml2_State_Import */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}
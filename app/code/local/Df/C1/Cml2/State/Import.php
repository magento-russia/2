<?php
namespace Df\C1\Cml2\State;
class Import extends \Df_Core_Model {
	/** @return \Df\C1\Cml2\State\Import\Collections */
	public function cl() {return \Df\C1\Cml2\State\Import\Collections::s();}

	/** @return \Df\C1\Cml2\Import\Data\Document */
	public function getDocumentCurrent() {return $this->getFileCurrent()->getXmlDocument();}

	/** @return \Df\C1\Cml2\Import\Data\Document\Offers */
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
	 * @used-by \Df\C1\Cml2\State\Import\Collections::getAttributes()
	 * @param bool $preprareSession [optional]
	 * @return \Df\C1\Cml2\File
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
					\Df\C1\Cml2\Session\ByIp::s()->begin();
				}
				$this->{__METHOD__} =
					\Df\C1\Cml2\File::i(
						\Df\C1\Cml2\Session\ByIp::s()->getFilePathById(
							\Df\C1\Cml2\Import\Data\Document\Catalog::TYPE__ATTRIBUTES
							, $this->getDocumentCurrent()->getExternalId_CatalogAttributes()
						)
					)
				;
				if ($preprareSession) {
					\Df\C1\Cml2\Session\ByIp::s()->end();
				}
			}
		}
		return $this->{__METHOD__};
	}

	/** @return \Df\C1\Cml2\File */
	public function getFileCatalogComposite() {
		if (!isset($this->{__METHOD__})) {
			/** @var \Df\C1\Cml2\File $result */
			if (
				$this->getDocumentCurrent()->isCatalog()
				&& $this->getDocumentCurrentAsCatalog()->hasAttributes()
				&& $this->getDocumentCurrentAsCatalog()->hasProducts()
				&& $this->getDocumentCurrentAsCatalog()->hasStructure()
			) {
				$result = $this->getFileCurrent();
			}
			else {
				\Df\C1\Cml2\Session\ByIp::s()->begin();
				/** @var \Df\C1\Cml2\File $fileAttributes */
				$fileAttributes = $this->getFileCatalogAttributes($preprareSession = false);
				/** @var \Df\C1\Cml2\File $fileProducts */
				$fileProducts = $this->getFileCatalogProducts($preprareSession = false);
				/** @var \Df\C1\Cml2\File $fileStructure */
				$fileStructure = $this->getFileCatalogStructure($preprareSession = false);
				\Df\C1\Cml2\Session\ByIp::s()->end();
				$result =
					$fileProducts->getPathRelative() === $fileStructure->getPathRelative()
					&& $fileProducts->getPathRelative() === $fileAttributes->getPathRelative()
					? $fileProducts
					: \Df\C1\Cml2\File\CatalogComposite::i2($fileStructure, $fileProducts, $fileAttributes)
				;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param bool $preprareSession [optional]
	 * @return \Df\C1\Cml2\File
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
					\Df\C1\Cml2\Session\ByIp::s()->begin();
				}
				$this->{__METHOD__} =
					\Df\C1\Cml2\File::i(
						\Df\C1\Cml2\Session\ByIp::s()->getFilePathById(
							\Df\C1\Cml2\Import\Data\Document\Catalog::TYPE__PRODUCTS
							, $this->getDocumentCurrent()->getExternalId_CatalogProducts()
						)
					)
				;
				if ($preprareSession) {
					\Df\C1\Cml2\Session\ByIp::s()->end();
				}
			}
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param bool $preprareSession [optional]
	 * @return \Df\C1\Cml2\File
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
					\Df\C1\Cml2\Session\ByIp::s()->begin();
				}
				$this->{__METHOD__} =
					\Df\C1\Cml2\File::i(
						\Df\C1\Cml2\Session\ByIp::s()->getFilePathById(
							\Df\C1\Cml2\Import\Data\Document\Catalog::TYPE__STRUCTURE
							, $this->getDocumentCurrent()->getExternalId_CatalogStructure()
						)
					)
				;
				if ($preprareSession) {
					\Df\C1\Cml2\Session\ByIp::s()->end();
				}
			}
		}
		return $this->{__METHOD__};
	}

	/** @return \Df\C1\Cml2\File */
	public function getFileCurrent() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * Обратите внимание,
			 * что «filename» может быть не просто именем файла (catalog.xml, offers.xml),
			 * но и именем файла с относительным путём (для файлов картинок), например:
			 * import_files/cb/cbcf4934-55bc-11d9-848a-00112f43529a_b5cfbe1a-c400-11e1-a851-4061868fc6eb.jpeg
			 * @var string $relativePath
			 */
			$relativePath = \Mage::app()->getRequest()->getParam('filename');
			if (!df_check_string_not_empty($relativePath)) {
				df_error(
					'Учётная система нарушает протокол обмена данными.'
					."\nВ данном сценарии она должна была передать"
					." в адресной строке параметр «filename»."
				);
			}
			$this->{__METHOD__} = \Df\C1\Cml2\File::i($relativePath);
		}
		return $this->{__METHOD__};
	}

	/** @return \Df\C1\Cml2\File */
	public function getFileOffers() {
		if (!isset($this->{__METHOD__})) {
			df_assert($this->getFileCurrent()->getXmlDocument()->isOffers());
			$this->{__METHOD__} = $this->getFileCurrent();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param bool $preprareSession [optional]
	 * @return \Df\C1\Cml2\File
	 */
	public function getFileOffersBase($preprareSession = true) {
		df_assert($this->getDocumentCurrent()->isOffers());
		if (!isset($this->{__METHOD__})) {
			if ($this->getDocumentCurrentAsOffers()->isBase()) {
				$this->{__METHOD__} = $this->getFileCurrent();
			}
			else {
				if ($preprareSession) {
					\Df\C1\Cml2\Session\ByIp::s()->begin();
				}
				$this->{__METHOD__} =
					\Df\C1\Cml2\File::i(
						\Df\C1\Cml2\Session\ByIp::s()->getFilePathById(
							\Df\C1\Cml2\Import\Data\Document\Offers::TYPE__BASE
							, $this->getDocumentCurrentAsOffers()->getExternalId()
						)
					)
				;
				if ($preprareSession) {
					\Df\C1\Cml2\Session\ByIp::s()->end();
				}
			}
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param bool $preprareSession [optional]
	 * @return \Df\C1\Cml2\File
	 */
	public function getFileOffersPrices($preprareSession = true) {
		df_assert($this->getDocumentCurrent()->isOffers());
		if (!isset($this->{__METHOD__})) {
			if ($this->getDocumentCurrentAsOffers()->hasPrices()) {
				$this->{__METHOD__} = $this->getFileCurrent();
			}
			else {
				if ($preprareSession) {
					\Df\C1\Cml2\Session\ByIp::s()->begin();
				}
				$this->{__METHOD__} =
					\Df\C1\Cml2\File::i(
						\Df\C1\Cml2\Session\ByIp::s()->getFilePathById(
							\Df\C1\Cml2\Import\Data\Document\Offers::TYPE__PRICES
							, $this->getDocumentCurrentAsOffers()->getExternalId()
						)
					)
				;
				if ($preprareSession) {
					\Df\C1\Cml2\Session\ByIp::s()->end();
				}
			}
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param bool $preprareSession [optional]
	 * @return \Df\C1\Cml2\File
	 */
	public function getFileOffersStock($preprareSession = true) {
		df_assert($this->getDocumentCurrent()->isOffers());
		if (!isset($this->{__METHOD__})) {
			if ($this->getDocumentCurrentAsOffers()->hasStock()) {
				$this->{__METHOD__} = $this->getFileCurrent();
			}
			else {
				if ($preprareSession) {
					\Df\C1\Cml2\Session\ByIp::s()->begin();
				}
				$this->{__METHOD__} =
					\Df\C1\Cml2\File::i(
						\Df\C1\Cml2\Session\ByIp::s()->getFilePathById(
							\Df\C1\Cml2\Import\Data\Document\Offers::TYPE__STOCK
							, $this->getDocumentCurrentAsOffers()->getExternalId()
						)
					)
				;
				if ($preprareSession) {
					\Df\C1\Cml2\Session\ByIp::s()->end();
				}
			}
		}
		return $this->{__METHOD__};
	}

	/** @return \Df_Catalog_Model_Category */
	public function getRootCategory() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = \Df_Catalog_Model_Category::ld($this->getRootCategoryId());
		}
		return $this->{__METHOD__};
	}

	/** @return \Df\C1\Cml2\Import\Data\Document\Catalog */
	private function getDocumentCurrentAsCatalog() {
		return $this->getFileCurrent()->getXmlDocumentAsCatalog();
	}

	/** @return int */
	private function getRootCategoryId() {
		if (!isset($this->{__METHOD__})) {
			/** @var int $result */
			$result = df_nat0(df_state()->getStoreProcessed()->getRootCategoryId());
			if (0 === $result) {
				df_error('В обрабатываемом магазине должен присутствовать корневой товарный раздел');
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}
<?php
namespace Df\C1\Cml2\State;
use Df\C1\Cml2\File;
use Df\C1\Cml2\Import\Data\Document\Catalog as DocumentCatalog;
use Df\C1\Cml2\Import\Data\Document\Offers as DocumentOffers;
use Df\C1\Cml2\Session\ByIp as SessionByIp;
use Df_Catalog_Model_Category as Category;
class Import extends \Df_Core_Model {
	/** @return \Df\C1\Cml2\State\Import\Collections */
	public function cl() {return \Df\C1\Cml2\State\Import\Collections::s();}

	/** @return \Df\C1\Cml2\Import\Data\Document */
	public function getDocumentCurrent() {return $this->getFileCurrent()->getXmlDocument();}

	/** @return \Df\C1\Cml2\Import\Data\Document\Offers */
	public function getDocumentCurrentAsOffers() {return
		$this->getFileCurrent()->getXmlDocumentAsOffers()
	;}

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
	 * @return File
	 */
	public function getFileCatalogAttributes($preprareSession = true) {return
		// 2016-11-05
		// Так в оригинале: $preprareSession не учитывается в ключе кэширования.
		// Не знаю, правильно ли это.
		dfc($this, function() use($preprareSession) {return
			$this->getDocumentCurrent()->isCatalog()
				&& $this->getDocumentCurrentAsCatalog()->hasAttributes()
			? $this->getFileCurrent()
			: self::prepareSession($preprareSession, function() {return
				File::i(SessionByIp::s()->getFilePathById(
					DocumentCatalog::TYPE__ATTRIBUTES
					,$this->getDocumentCurrent()->getExternalId_CatalogAttributes()
				))
			;})
		;})
	;}

	/** @return File */
	public function getFileCatalogComposite() {return dfc($this, function() {
		/** @var File $result */
		if (
			$this->getDocumentCurrent()->isCatalog()
			&& $this->getDocumentCurrentAsCatalog()->hasAttributes()
			&& $this->getDocumentCurrentAsCatalog()->hasProducts()
			&& $this->getDocumentCurrentAsCatalog()->hasStructure()
		) {
			$result = $this->getFileCurrent();
		}
		else {
			SessionByIp::s()->begin();
			/** @var File $fileAttributes */
			$fileAttributes = $this->getFileCatalogAttributes($preprareSession = false);
			/** @var File $fileProducts */
			$fileProducts = $this->getFileCatalogProducts($preprareSession = false);
			/** @var File $fileStructure */
			$fileStructure = $this->getFileCatalogStructure($preprareSession = false);
			SessionByIp::s()->end();
			$result =
				$fileProducts->getPathRelative() === $fileStructure->getPathRelative()
				&& $fileProducts->getPathRelative() === $fileAttributes->getPathRelative()
				? $fileProducts
				: \Df\C1\Cml2\File\CatalogComposite::i2($fileStructure, $fileProducts, $fileAttributes)
			;
		}
		return $result;
	});}

	/**
	 * @param bool $preprareSession [optional]
	 * @return File
	 */
	public function getFileCatalogProducts($preprareSession = true) {return
		// 2016-11-05
		// Так в оригинале: $preprareSession не учитывается в ключе кэширования.
		// Не знаю, правильно ли это.
		dfc($this, function() use($preprareSession) {return
			$this->getDocumentCurrent()->isCatalog()
				&& $this->getDocumentCurrentAsCatalog()->hasProducts()
			? $this->getFileCurrent()
			: self::prepareSession($preprareSession, function() {return
				File::i(SessionByIp::s()->getFilePathById(
					DocumentCatalog::TYPE__PRODUCTS
					,$this->getDocumentCurrent()->getExternalId_CatalogProducts()
				))
			;})
		;})
	;}

	/**
	 * @param bool $preprareSession [optional]
	 * @return File
	 */
	public function getFileCatalogStructure($preprareSession = true) {return
		// 2016-11-05
		// Так в оригинале: $preprareSession не учитывается в ключе кэширования.
		// Не знаю, правильно ли это.
		dfc($this, function() use($preprareSession) {return
			$this->getDocumentCurrent()->isCatalog()
				&& $this->getDocumentCurrentAsCatalog()->hasStructure()
			? $this->getFileCurrent()
			: self::prepareSession($preprareSession, function() {return
				File::i(SessionByIp::s()->getFilePathById(
					DocumentCatalog::TYPE__STRUCTURE
					,$this->getDocumentCurrent()->getExternalId_CatalogStructure()
				))
			;})
		;})
	;}

	/** @return File */
	public function getFileCurrent() {return dfc($this, function() {
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
		return File::i($relativePath);
	});}

	/** @return File */
	public function getFileOffers() {return dfc($this, function() {
		df_assert($this->getFileCurrent()->getXmlDocument()->isOffers());
		return $this->getFileCurrent();
	});}

	/**
	 * @param bool $preprareSession [optional]
	 * @return File
	 */
	public function getFileOffersBase($preprareSession = true) {return
		// 2016-11-05
		// Так в оригинале: $preprareSession не учитывается в ключе кэширования.
		// Не знаю, правильно ли это.
		dfc($this, function() use($preprareSession) {df_assert($this->getDocumentCurrent()->isOffers()); return
			$this->getDocumentCurrentAsOffers()->isBase()
			? $this->getFileCurrent()
			: self::prepareSession($preprareSession, function() {return
				File::i(SessionByIp::s()->getFilePathById(
					DocumentOffers::TYPE__BASE
					,$this->getDocumentCurrentAsOffers()->getExternalId()
				))
			;})
		;})
	;}

	/**
	 * @param bool $preprareSession [optional]
	 * @return File
	 */
	public function getFileOffersPrices($preprareSession = true) {return
		// 2016-11-05
		// Так в оригинале: $preprareSession не учитывается в ключе кэширования.
		// Не знаю, правильно ли это.
		dfc($this, function() use($preprareSession) {df_assert($this->getDocumentCurrent()->isOffers()); return
			$this->getDocumentCurrentAsOffers()->hasPrices()
			? $this->getFileCurrent()
			: self::prepareSession($preprareSession, function() {return
				File::i(SessionByIp::s()->getFilePathById(
					DocumentOffers::TYPE__PRICES
					,$this->getDocumentCurrentAsOffers()->getExternalId()
				))
			;})
		;})
	;}

	/**
	 * @param bool $preprareSession [optional]
	 * @return File
	 */
	public function getFileOffersStock($preprareSession = true) {return
		// 2016-11-05
		// Так в оригинале: $preprareSession не учитывается в ключе кэширования.
		// Не знаю, правильно ли это.
		dfc($this, function() use($preprareSession) {df_assert($this->getDocumentCurrent()->isOffers()); return
			$this->getDocumentCurrentAsOffers()->hasStock()
			? $this->getFileCurrent()
			: self::prepareSession($preprareSession, function() {return
				File::i(SessionByIp::s()->getFilePathById(
					DocumentOffers::TYPE__STOCK
					,$this->getDocumentCurrentAsOffers()->getExternalId()
				))
			;})
		;})
	;}

	/** @return Category */
	public function getRootCategory() {return dfc($this, function() {return 
		Category::ld($this->getRootCategoryId())				
	;});}

	/** @return \Df\C1\Cml2\Import\Data\Document\Catalog */
	private function getDocumentCurrentAsCatalog() {return
		$this->getFileCurrent()->getXmlDocumentAsCatalog()
	;}

	/** @return int */
	private function getRootCategoryId() {return dfc($this, function() {return
		df_nat0(df_state()->getStoreProcessed()->getRootCategoryId()) ?:
			df_error('В обрабатываемом магазине должен присутствовать корневой товарный раздел')
	;});}

	/**
	 * 2016-11-05
	 * @used-by getFileCatalogAttributes()
	 * @used-by getFileCatalogProducts()
	 * @used-by getFileCatalogStructure()
	 * @param bool $flag
	 * @param \Closure $f
	 * @return mixed
	 */
	private static function prepareSession($flag, \Closure $f) {
		/** mixed $result */
		if ($flag) {SessionByIp::s()->begin();}
		$result = $f();
		if ($flag) {SessionByIp::s()->end();}
		return $result;
	}

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}
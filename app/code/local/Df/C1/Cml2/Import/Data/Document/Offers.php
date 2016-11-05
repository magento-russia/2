<?php
namespace Df\C1\Cml2\Import\Data\Document;
class Offers extends \Df\C1\Cml2\Import\Data\Document {
	/** @return string */
	public function getExternalId() {return dfc($this, function() {
		/** @var string $result */
		$result = $this->descendS('ПакетПредложений/Ид');
		df_result_string_not_empty($result);
		return $result;
	});}

	/**
	 * 2015-08-04
	 * @override
	 * @see \Df\C1\Cml2\Import\Data\Document::getExternalId_CatalogAttributes()
	 * @return string
	 */
	public function getExternalId_CatalogAttributes() {return dfc($this, function() {
		/** @var string $result */
		$result = $this->descendS('ПакетПредложений/ИдКлассификатора');
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
		$result = $this->descendS('ПакетПредложений/ИдКаталога');
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
		$result = $this->descendS('ПакетПредложений/ИдКлассификатора');
		df_result_string_not_empty($result);
		return $result;
	});}

	/** @return bool */
	public function hasPrices() {return dfc($this, function() {return
		!!$this->e()->descend('ПакетПредложений/Предложения/Предложение/Цены/Цена')
	;});}

	/** @return bool */
	public function hasStock() {return dfc($this, function() {return
		!!$this->e()->descend('ПакетПредложений/Предложения/Предложение/Остатки/Остаток')
	;});}

	/**
	 * Базовый файл offers_*.xml содержит основные характеристики товара (например, имя),
	 * а также настраиваемые опции товара.
	 * @return bool
	 */
	public function isBase() {return dfc($this, function() {return
		!!$this->e()->descend('ПакетПредложений/Предложения/Предложение/Наименование')
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
		 * 1С:Управление торговлей присылает в интернет-магазин не один файл offers.xml, как раньше,
		 * а минимум 3 таких файла: offers_*.xml, prices_*.xml, rests_*.xml.
		 */
		if ($this->isBase()) {
			$this->session()->setFilePathById(
				self::TYPE__BASE, $this->getExternalId(), $this->getPath()
			);
		}
		if ($this->hasPrices()) {
			$this->session()->setFilePathById(
				self::TYPE__PRICES, $this->getExternalId(), $this->getPath()
			);
		}
		if ($this->hasStock()) {
			$this->session()->setFilePathById(
				self::TYPE__STOCK, $this->getExternalId(), $this->getPath()
			);
		}
		$this->session()->end();
	}

	/**
	 * @used-by storeInSession()
	 * @used-by \Df\C1\Cml2\State\Import::getFileOffersBase()
	 */
	const TYPE__BASE = 'offers_base';
	/**
	 * @used-by storeInSession()
	 * @used-by \Df\C1\Cml2\State\Import::getFileOffersPrices()
	 */
	const TYPE__PRICES = 'offers_prices';
	/**
	 * @used-by storeInSession()
	 * @used-by \Df\C1\Cml2\State\Import::getFileOffersStock()
	 */
	const TYPE__STOCK = 'offers_stock';
}
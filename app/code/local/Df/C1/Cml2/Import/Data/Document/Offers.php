<?php
namespace Df\C1\Cml2\Import\Data\Document;
class Offers extends \Df\C1\Cml2\Import\Data\Document {
	/** @return string */
	public function getExternalId() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->descendS('ПакетПредложений/Ид');
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-08-04
	 * @override
	 * @see \Df\C1\Cml2\Import\Data\Document::getExternalId_CatalogAttributes()
	 * @return string
	 */
	public function getExternalId_CatalogAttributes() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->descendS('ПакетПредложений/ИдКлассификатора');
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @see \Df\C1\Cml2\Import\Data\Document::getExternalId_CatalogProducts()
	 * @return string
	 */
	public function getExternalId_CatalogProducts() {
		if (!isset($this->{__METHOD__})) {
			df_assert($this->isOffers());
			$this->{__METHOD__} = $this->descendS('ПакетПредложений/ИдКаталога');
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @see \Df\C1\Cml2\Import\Data\Document::getExternalId_CatalogStructure()
	 * @return string
	 */
	public function getExternalId_CatalogStructure() {
		if (!isset($this->{__METHOD__})) {
			df_assert($this->isOffers());
			$this->{__METHOD__} = $this->descendS('ПакетПредложений/ИдКлассификатора');
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	public function hasPrices() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				!!$this->e()->descend('ПакетПредложений/Предложения/Предложение/Цены/Цена')
			;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	public function hasStock() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				!!$this->e()->descend('ПакетПредложений/Предложения/Предложение/Остатки/Остаток')
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * Базовый файл offers_*.xml содержит основные характеристики товара (например, имя),
	 * а также настраиваемые опции товара.
	 * @return bool
	 */
	public function isBase() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				!!$this->e()->descend('ПакетПредложений/Предложения/Предложение/Наименование')
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

	/** @used-by \Df\C1\Cml2\Import\Data\Document::create() */

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
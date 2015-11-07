<?php
class Df_YandexMarket_Model_Yml_Document extends Df_Core_Model_SimpleXml_Generator_Document {
	/**
	 * Метод публичен, потому что его использует класс @see Df_YandexMarket_Model_Yml_Processor_Offer
	 * @return Mage_Catalog_Model_Resource_Category_Collection|Mage_Catalog_Model_Resource_Category_Collection
	 */
	public function getCategories() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Catalog_Model_Resource_Category_Collection $result */
			$result = Df_Catalog_Model_Resource_Category_Collection::i();
			$result->addIdFilter($this->getProducts()->getCategoryIds());
			/** @var string[] $attributes */
			$attributes = array('name', Df_YandexMarket_Const::ATTRIBUTE__CATEGORY);
			$result->addAttributeToSelect($attributes);
			/**
			 * Вызов выше метода
			 * @see Mage_Catalog_Model_Resource_Category_Collection::addIdFilter()
			 * может отсечь товарный раздел,
			 * который хоть и не содержит товаров непосредственно,
			 * но является родительским для товарного раздела, содержащего товары,
			 * и тогда свойство parentId товарного раздела, содержащего товары,
			 * будет ссылаться на отсутствующий в файле YML товарный раздел.
			 * @link http://magento-forum.ru/topic/4572/
			 * По этой причине нам надо добавить к коллекции разделы-предки.
			 */
			$result->addAncestors($attributes);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * Метод публичен, потому что его использует класс @see Df_YandexMarket_Model_Yml_Processor_Offer
	 * @return Df_Catalog_Model_Resource_Product_Collection
	 */
	public function getProducts() {return $this->cfg(self::P__PRODUCTS);}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getAttributes() {
		return array('date' => df_dts(Zend_Date::now(), 'y-MM-dd HH:mm'));
	}

	/**
	 * @override
	 * @return array(string => mixed)
	 */
	protected function getContentsAsArray() {return array('shop' => $this->getDocumentData_Shop());}

	/**
	 * @override
	 * @return string
	 */
	protected function getDocType() {return "<!DOCTYPE yml_catalog SYSTEM 'shops.dtd'>";}

	/**
	 * @override
	 * @return string
	 */
	protected function getTagName() {return 'yml_catalog';}
	
	/** @return array(array(string => string|array(string => int))) */
	private function getDocumentData_Categories() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(array(string => string|array(string => int))) $result  */
			$result = array();
			foreach ($this->getCategories() as $category) {
				/** @var Mage_Catalog_Model_Category $category */
				if ($category->getId()) {
					/** @var array(string => int) $attributes */
					$attributes = array('id' => $category->getId());
					if (0 < $category->getParentId()) {
						$attributes['parentId'] = $category->getParentId();
					}
					$result[]=
						array(
							Df_Varien_Simplexml_Element::KEY__ATTRIBUTES => $attributes
							,Df_Varien_Simplexml_Element::KEY__VALUE =>
								rm_cdata(
									$category->getName() ? $category->getName() : $category->getId()
								)
						)
					;
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return array */
	private function getDocumentData_Currencies() {
		/** @var array $result */
		$result = array(array(Df_Varien_Simplexml_Element::KEY__ATTRIBUTES => array(
			'id' => $this->getSettings()->general()->getCurrencyCode()
			/**
			 * Параметр rate указывает курс валюты к курсу основной валюты,
			 * взятой за единицу (валюта, для которой rate="1").
			 *
			 * В качестве основной валюты (для которой установлено rate="1")
			 * могут быть использованы только рубль (RUR, RUB),
			 * белорусский рубль (BYR),
			 * гривна (UAH)
			 * или тенге (KZT).
			 */
			,'rate' => 1
		)));
		return $result;
	}
	
	/** @return Df_YandexMarket_Model_Yml_Processor_Offer[] */
	private function getDocumentData_Offers() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_YandexMarket_Model_Yml_Processor_Offer[] $result  */
			$result = array();
			df_h()->yandexMarket()->log('Рассматривается товаров: %d.', count($this->getProducts()));
			foreach ($this->getProducts() as $product) {
				/** @var Df_Catalog_Model_Product $product */
				/** @var Df_YandexMarket_Model_Yml_Processor_Offer $offer */
				$offer =
					Df_YandexMarket_Model_Yml_Processor_Offer::i(
						array(
							Df_YandexMarket_Model_Yml_Processor_Offer::P__DOCUMENT => $this
							,Df_YandexMarket_Model_Yml_Processor_Offer::P__PRODUCT => $product
						)
					)
				;
				if ($offer->isEnabled()) {
					$result[]= $offer->getDocumentData();
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return array */
	private function getDocumentData_Shop() {
		/** @var array $result */
		$result =
			array(
				'name' => $this->getSettings()->shop()->getNameForClients()
				,'company' => $this->getSettings()->shop()->getNameForAdministration()
				,'url' =>
					df_h()->yandexMarket()->preprocessUrl(
						rm_state()->getStoreProcessed()->getBaseUrl(
							Mage_Core_Model_Store::URL_TYPE_WEB
						)
					)
				,'platform' => 'Российская сборка Magento'
				,'version' => rm_version()
				,'agency' => $this->getSettings()->shop()->getAgency()
				,'email' => $this->getSettings()->shop()->getSupportEmail()
				,'currencies' => array('currency' => $this->getDocumentData_Currencies())
				,'categories' => array('category' => $this->getDocumentData_Categories())
			)
		;
		/** @link http://magento-forum.ru/topic/4201/ */
		if (0 === count($this->getDocumentData_Offers())) {
			df_h()->yandexMarket()->error_noOffers();
		}
		/** @link http://magento-forum.ru/topic/4201/ */
		if (0 === count($this->getDocumentData_Categories())) {
			/** @var string $message */
			$message =
				'Ни один из передаваемых на Яндекс.Маркет товаров'
				.' не привязан ни к одному товарному разделу. '
				."\nКаждый передаваемый на Яндекс.Маркет товар"
				.' должен быть привязан хотя бы к одному товарному разделу.'
			;
			df_h()->yandexMarket()->notify($message);
			// Всё равно файл YML будет невалидным,
			// поэтому сразу сбойно завершаем формирование этого файла.
			df_error($message);
		}
		if (0 !== $this->getSettings()->general()->getLocalDeliveryCost()) {
			$result['local_delivery_cost'] = $this->getSettings()->general()->getLocalDeliveryCost();
		}
		$result['offers'] = array('offer' => $this->getDocumentData_Offers());
		return $result;
	}

	/** @return Df_YandexMarket_Model_Settings */
	private function getSettings() {return Df_YandexMarket_Model_Settings::s();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__PRODUCTS, Df_Catalog_Model_Resource_Product_Collection::_CLASS);
	}
	const _CLASS = __CLASS__;
	const P__PRODUCTS = 'products';
	/**
	 * @static
	 * @param Df_Catalog_Model_Resource_Product_Collection $products
	 * @return Df_YandexMarket_Model_Yml_Document
	 */
	public static function i(Df_Catalog_Model_Resource_Product_Collection $products) {
		return new self(array(self::P__PRODUCTS => $products));
	}
}
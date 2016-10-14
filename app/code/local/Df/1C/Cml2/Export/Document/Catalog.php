<?php
class Df_1C_Cml2_Export_Document_Catalog extends Df_Catalog_Model_XmlExport_Catalog {
	/**
	 * @override
	 * @return string
	 */
	public function getOperationNameInPrepositionalCase() {
		return 'формировании документа для помощника импорта товаров с сайта';
	}

	/**
	 * @param Df_Catalog_Model_Resource_Eav_Attribute $attribute
	 * @return Df_1C_Cml2_Export_Processor_Catalog_Attribute_Real
	 */
	public function getProcessorForAttribute(Df_Catalog_Model_Resource_Eav_Attribute $attribute) {
		if (!isset($this->{__METHOD__}[$attribute->getName()])) {
			$this->{__METHOD__}[$attribute->getName()] =
				Df_1C_Cml2_Export_Processor_Catalog_Attribute_Real::i($attribute, $this)
			;
		}
		return $this->{__METHOD__}[$attribute->getName()];
	}

	/** @return Df_1C_Cml2_Export_Processor_Catalog_Attribute[] */
	public function getProcessorsForVirtualAttributes() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_1C_Cml2_Export_Processor_Catalog_Attribute[] $result */
			$result = array();
			foreach ($this->getVirtualAttributeProcessorClasses() as $class) {
				/** @var Df_1C_Cml2_Export_Processor_Catalog_Attribute $processor */
				$result[]= Df_1C_Cml2_Export_Processor_Catalog_Attribute::ic($class, $this);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_1C_Cml2_Export_DocumentMixin */
	protected function createMixin() {return Df_1C_Cml2_Export_DocumentMixin_Catalog::i($this);}

	/**
	 * @override
	 * @return array(string => mixed)
	 */
	protected function getContentsAsArray() {
		return array(
			// Обратите внимание,
			// что обработку классификатора намеренно осуществляем до обработки товаров,
			// чтобы перед обработкой товаров
			// товарным разделам и свойствам были присвоены внешние идентификаторы.
			'Классификатор' => $this->getКлассификатор()
			,'Каталог' => $this->getКаталог()
			,'ПакетПредложений' => $this->getПакетПредложений()
		);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getLogDocumentName() {return 'catalog.export';}

	/**
	 * @override
	 * @return string
	 */
	protected function getProcessorClass_products() {
		return Df_1C_Cml2_Export_Processor_Catalog_Product::_C;
	}

	/**
	 * Нам потребуется обновлять товарные разделы (добавлять к ним внешние идентификаторы.
	 * Для включения возможности обновления надо надо перед загрузкой коллекции
	 * отключить режим денормализации:
	 * @used-by Df_Catalog_Model_XmlExport_Catalog::getCategories()
	 * @override
	 * @return bool
	 */
	protected function needUpdateCategories() {return true;}

	/** @return array(string => array(string => array(string => mixed))) */
	private function getКаталог() {
		return df_clean_xml(array(
			'Ид' => $this->getКаталог_Ид()
			,'ИдКлассификатора' => $this->getКлассификатор_Ид()
			,'Наименование' => df_cdata($this->getКаталог_Наименование())
			,'Товары' => array('Товар' => array($this->getOutput_Products()))
			,'Описание' => df_cdata($this->getКаталог_Описание())
		));
	}

	/** @return string */
	private function getКаталог_Ид() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_t()->guid();
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getКаталог_Наименование() {return $this->store()->getFrontendName();}

	/** @return string */
	private function getКаталог_Описание() {return 'Российская сборка Magento ' . rm_version_full();}

	/** @return array(string => mixed) */
	private function getКлассификатор() {
		return df_clean_xml(array(
			'Ид' => $this->getКлассификатор_Ид()
			,'Наименование' => df_cdata($this->getКлассификатор_Наименование())
			,'Группы' => $this->getКлассификатор_Группы()
			,'Свойства' => array('Свойство' => $this->getКлассификатор_Свойства_Свойство())
			,'ТипыЦен' => array('ТипЦены' => $this->getКлассификатор_ТипыЦен_ТипЦены())
		));
	}

	/** @return array(array(string => mixed)) */
	private function getКлассификатор_Группы() {
		return Df_1C_Cml2_Export_Processor_Catalog_Category::process(
			$this->getCategoriesAsTree(), $this
		);
	}

	/** @return string */
	private function getКлассификатор_Ид() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_t()->guid();
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getКлассификатор_Наименование() {return $this->store()->getFrontendName();}

	/** @return array(array(string => mixed)) */
	private function getКлассификатор_Свойства_Свойство() {
		/** @var array(array(string => mixed)) $result */
		$result = array();
		foreach ($this->getCatalogAttributes() as $attribute) {
			/** @var Df_Catalog_Model_Resource_Eav_Attribute $attribute */
			/** @var Df_1C_Cml2_Export_Processor_Catalog_Attribute_Real $processor */
			$processor = $this->getProcessorForAttribute($attribute);
			if ($processor->isEligible()) {
				$result[]= $processor->getResult();
			}
		}
		foreach ($this->getProcessorsForVirtualAttributes() as $processor) {
			/** @var Df_1C_Cml2_Export_Processor_Catalog_Attribute $processor */
			if ($processor->isEligible()) {
				$result[]= $processor->getResult();
			}
		}
		return $result;
	}

	/** @return array(array(string => mixed)) */
	private function getКлассификатор_ТипыЦен_ТипЦены() {
		/** @var array(array(string => mixed)) $result */
		$result = array();
		foreach (Df_Customer_Model_Group::c() as $group) {
			/** @var Df_Customer_Model_Group $group */
			/** @var Df_1C_Cml2_Export_Processor_Catalog_CustomerGroup $processor */
			$processor = Df_1C_Cml2_Export_Processor_Catalog_CustomerGroup::i($group, $this);
			if ($processor->isEligible()) {
				$result[]= $processor->getResult();
			}
		}
		return $result;
	}

	/** @return array(string => array(string => array(string => mixed))) */
	private function getПакетПредложений() {
		return df_clean_xml(array(
			'Ид' => $this->getПакетПредложений_Ид()
			,'Наименование' => df_cdata($this->getПакетПредложений_Наименование())
			,'ИдКаталога' => $this->getКаталог_Ид()
			,'ИдКлассификатора' => $this->getКлассификатор_Ид()
		));
	}

	/** @return string */
	private function getПакетПредложений_Ид() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_t()->guid();
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getПакетПредложений_Наименование() {return $this->store()->getFrontendName();}

	/** @return string[] */
	private function getVirtualAttributeProcessorClasses() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array(
				Df_1C_Cml2_Export_Processor_Catalog_Attribute_Url::_C
			);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by Df_1C_Cml2_Export_Processor_Catalog_CustomerGroup::_construct()
	 * @used-by Df_1C_Cml2_Export_Processor_Catalog_Attribute_Real::_construct()
	 */
	const _C = __CLASS__;

	/**
	 * @used-by Df_1C_Cml2_Action_Catalog_Export_Process::createDocument()
	 * @param Df_Catalog_Model_Resource_Product_Collection $products
	 * @return Df_1C_Cml2_Export_Document_Catalog
	 */
	public static function i(Df_Catalog_Model_Resource_Product_Collection $products) {
		return self::ic(__CLASS__, $products);
	}
}
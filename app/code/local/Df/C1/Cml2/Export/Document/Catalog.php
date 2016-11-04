<?php
namespace Df\C1\Cml2\Export\Document;
use Df_Catalog_Model_Resource_Eav_Attribute as A;
use Df\C1\Cml2\Export\Processor\Catalog\Attribute as ProcAttribute;
use Df\C1\Cml2\Export\Processor\Catalog\Attribute\Real as ProcAttributeReal;
use Df\C1\Cml2\Export\Processor\Catalog\Attribute\Url as ProcAttributeUrl;
use Df\C1\Cml2\Export\Processor\Catalog\Product as ProcProduct;
class Catalog extends \Df_Catalog_Model_XmlExport_Catalog {
	/**
	 * @override
	 * @return string
	 */
	public function getOperationNameInPrepositionalCase() {return
		'формировании документа для помощника импорта товаров с сайта'
	;}

	/**
	 * @param A $a
	 * @return ProcAttributeReal
	 */
	public function processorForAttribute(A $a) {return dfc($this, function(A $a) {return
		ProcAttributeReal::i($a, $this)
	;}, func_get_args());}

	/** @return ProcAttribute[] */
	public function getProcessorsForVirtualAttributes() {return dfc($this, function() {return
		array_map(function($class) {return
			ProcAttribute::ic($class, $this)
		;}, $this->getVirtualAttributeProcessorClasses())
	;});}

	/** @return \Df\C1\Cml2\Export\DocumentMixin */
	protected function createMixin() {return \Df\C1\Cml2\Export\DocumentMixin\Catalog::i($this);}

	/**
	 * @override
	 * @return array(string => mixed)
	 */
	protected function getContentsAsArray() {return [
		// Обратите внимание,
		// что обработку классификатора намеренно осуществляем до обработки товаров,
		// чтобы перед обработкой товаров
		// товарным разделам и свойствам были присвоены внешние идентификаторы.
		'Классификатор' => $this->getКлассификатор()
		,'Каталог' => $this->getКаталог()
		,'ПакетПредложений' => $this->getПакетПредложений()
	];}

	/**
	 * @override
	 * @return string
	 */
	protected function getLogDocumentName() {return 'catalog.export';}

	/**
	 * @override
	 * @return string
	 */
	protected function getProcessorClass_products() {return ProcProduct::class;}

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
	private function getКаталог() {return df_clean_xml([
		'Ид' => $this->getКаталог_Ид()
		,'ИдКлассификатора' => $this->getКлассификатор_Ид()
		,'Наименование' => df_cdata($this->getКаталог_Наименование())
		,'Товары' => ['Товар' => [$this->getOutput_Products()]]
		,'Описание' => df_cdata($this->getКаталог_Описание())
	]);}

	/** @return string */
	private function getКаталог_Ид() {return dfc($this, function() {return df_t()->guid();});}

	/** @return string */
	private function getКаталог_Наименование() {return $this->store()->getFrontendName();}

	/** @return string */
	private function getКаталог_Описание() {return 'Российская сборка Magento ' . df_version_full();}

	/** @return array(string => mixed) */
	private function getКлассификатор() {return df_clean_xml([
		'Ид' => $this->getКлассификатор_Ид()
		,'Наименование' => df_cdata($this->getКлассификатор_Наименование())
		,'Группы' => $this->getКлассификатор_Группы()
		,'Свойства' => ['Свойство' => $this->getКлассификатор_Свойства_Свойство()]
		,'ТипыЦен' => ['ТипЦены' => $this->getКлассификатор_ТипыЦен_ТипЦены()]
	]);}

	/** @return array(array(string => mixed)) */
	private function getКлассификатор_Группы() {
		return \Df\C1\Cml2\Export\Processor\Catalog\Category::process(
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
			/** @var \Df_Catalog_Model_Resource_Eav_Attribute $attribute */
			/** @var \Df\C1\Cml2\Export\Processor\Catalog\Attribute\Real $processor */
			$processor = $this->processorForAttribute($attribute);
			if ($processor->isEligible()) {
				$result[]= $processor->getResult();
			}
		}
		foreach ($this->getProcessorsForVirtualAttributes() as $processor) {
			/** @var \Df\C1\Cml2\Export\Processor\Catalog\Attribute $processor */
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
		foreach (\Df_Customer_Model_Group::c() as $group) {
			/** @var \Df_Customer_Model_Group $group */
			/** @var \Df\C1\Cml2\Export\Processor\Catalog\CustomerGroup $processor */
			$processor = \Df\C1\Cml2\Export\Processor\Catalog\CustomerGroup::i($group, $this);
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
	private function getVirtualAttributeProcessorClasses() {return dfc($this, function() {return [
		ProcAttributeUrl::class
	];});}

	/**
	 * @used-by \Df\C1\Cml2\Action\Catalog\Export\Process::createDocument()
	 * @param \Df_Catalog_Model_Resource_Product_Collection $products
	 * @return \Df\C1\Cml2\Export\Document\Catalog
	 */
	public static function i(\Df_Catalog_Model_Resource_Product_Collection $products) {return
		self::ic(__CLASS__, $products)
	;}
}
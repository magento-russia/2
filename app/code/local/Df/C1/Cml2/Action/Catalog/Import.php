<?php
class Df_C1_Cml2_Action_Catalog_Import extends Df_C1_Cml2_Action_Catalog {
	/** @return \Df\Xml\X */
	protected function e() {return $this->getFileCurrent()->getXml();}

	/**
	 * @override
	 * @see Df_Core_Model_Action::_process()
	 * @used-by Df_Core_Model_Action::process()
	 * @return void
	 */
	protected function _process() {
		// на всякий случай удаляем кэш до и после импорта
		df_cache_clean();
		// Добавляем версию схемы CommerceML
		// к диагностическим отчётам в случае сбоя обмены данными.
		df_context('Cхема CommerceML', $this->getDocumentCurrent()->getSchemeVersion());
		$this->getDocumentCurrent()->storeInSession();
		/**
		 * В самом простом случае имена файлов будут «import.xml» и «offers.xml».
		 * Однако, 1С при некоторых настройках может разбивать данные на несколько файлов,
		 * и тогда будет, например, файл с именем «import1.xml».
		 *
		 * Более того, новые версии обработки 1С для обмена данными с сайтом
		 * вообще не передают файлы с именами «import.xml» и «offers.xml»,
		 * а вместо этого передают файлы с примерными именами:
		 * «import___b6ad244f-f800-4832-9bfd-9038ded742fb.xml»
		 * «offers___bdc8e792-b9e4-486b-93a6-0c369ccd4764.xml».
		 * Заметил такое поведение в следующих версиях:
		 * Версия конфигурации «Управление торговлей»: 11.1.6.20
		 * Версия дополнения 1С-Битрикс для обмена данными с интернет-магазином: 4.0.2.3
		 * Версия платформы «1С: Предприятие»: 8.3.5.1098
		 */
		if ($this->getDocumentCurrent()->isCatalog()) {
			if ($this->getDocumentCurrentAsCatalog()->hasStructure()) {
				$this->importCategories();
			}
			/**
			 * 2015-08-04
			 * Раньше товарные свойства импортировались при возвращении true методом
			 * @see Df_C1_Cml2_Import_Data_Document_Catalog::hasStructure()
			 * Однако сегодня, тестируя версию 5.0.6 модуля 1С-Битрикс (CommerceML версии 2.09)
			 * заметил, что первый файл import__*.xml, который 1С передаёт интернет-магазину,
			 * внутри ветки Классификатор содержит подветки Группы, ТипыЦен, Склады, ЕдиницыИзмерения,
			 * однако не содержит подветку Свойства.
			 * Подветка Свойства передаётся уже следующим файлом import__*.xml.
			 */
			if ($this->getDocumentCurrentAsCatalog()->hasAttributes()) {
				$this->importReferenceLists();
			}
		}
		else if ($this->getDocumentCurrent()->isOffers()) {
			/** @var int $count */
			$count = $this->getCollections()->getOffers()->count();
			if (!$count) {
				df_c1_log('Товарные предложения отсутствуют.');
			}
			else {
				df_c1_log('Товарных предложений: %d.', $count);
				$this->importProductsSimple();
				/**
				 * Товарные изображения находятся в файле import.xml (import_*.xml).
				 * Учитывая, что начиная с ветки 4 модуля 1С-Битрикс
				 * мы получаем несколько файлов с тегом «ПакетПредложений»
				 * вместо прежнего единого файла offers.xml,
				 * то нам нет смысла по нескольку раз запускать импорт товарных изображений.
				 * Импортируем товарные изображения толь один раз:
				 * при наличии в файле offers_*.xml пути
				 * «ПакетПредложений/Предложения/Предложение/Наименование».
				 */
				if ($this->getDocumentCurrentAsOffers()->isBase()) {
					$this->importProductsSimplePartImages();
				}
				$this->importProductsConfigurable();
				if ($this->getDocumentCurrentAsOffers()->isBase()) {
					$this->importProductsConfigurablePartImages();
				}
			}
		}
		else {
			df_error('Непредусмотренный файл: «%s».', $this->getFileCurrent()->getPathRelative());
		}
		$this->setResponseSuccess();
		// на всякий случай удаляем кэш до и после импорта
		df_cache_clean();
	}

	/** @return Df_C1_Cml2_State_Import_Collections */
	private function getCollections() {return $this->getState()->import()->collections();}

	/** @return Df_C1_Cml2_Action_Catalog_Import */
	private function importCategories() {
		/** @var int $count */
		$count = $this->getCollections()->getCategories()->count();
		if (!$count) {
			df_c1_log('Товарные разделы отсутствуют.');
		}
		else {
			df_c1_log('Товарных разделов: %d.', $count);
			df_c1_log('Импорт товарных разделов начат.');
			foreach ($this->getCollections()->getCategories() as $category) {
				/** @var Df_C1_Cml2_Import_Data_Entity_Category $category */
				Df_C1_Cml2_Import_Processor_Category::i(
					$this->getState()->import()->getRootCategory(), $category
				)->process();
			}
			df_c1_log('Импорт товарных разделов завершён.');
		}
		return $this;
	}

	/** @return Df_C1_Cml2_Action_Catalog_Import */
	private function importProductsConfigurable() {
		/** @var int $countParent */
		$countParent = count($this->getCollections()->getOffersConfigurableParent());
		if (!$countParent) {
			/** @var int $countChildren */
			$countChildren = count($this->getCollections()->getOffersConfigurableChild());
			if (!$countChildren) {
				df_c1_log('Настраиваемые товары отсутствуют.');
			}
			else {
				if (df_my_local()) {
					Mage::log(
						'ВНИМАНИЕ: отсутствуют настраиваемые товары, однако присутствуют их составные части!'
					);
				}
				df_c1_log(
					'ВНИМАНИЕ: отсутствуют настраиваемые товары, однако присутствуют их составные части!'
				);
			}
		}
		else {
			df_c1_log('Настраиваемых товаров: %d.', $countParent);
			df_c1_log('Импорт настраиваемых товаров начат.');
			foreach ($this->getCollections()->getOffersConfigurableParent() as $offer) {
				/** @var Df_C1_Cml2_Import_Data_Entity_Offer $offer */
				Df_C1_Cml2_Import_Processor_Product_Type_Configurable::p($offer);
			}
			df_c1_log('Импорт настраиваемых товаров завершён.');
		}
		return $this;
	}

	/** @return Df_C1_Cml2_Action_Catalog_Import */
	private function importProductsConfigurablePartImages() {
		foreach ($this->getCollections()->getOffers() as $offer) {
			/** @var Df_C1_Cml2_Import_Data_Entity_Offer $offer */
			if ($offer->isTypeConfigurableParent()) {
				Df_C1_Cml2_Import_Processor_Product_Part_Images::i($offer)->process();
			}
		}
		return $this;
	}

	/** @return Df_C1_Cml2_Action_Catalog_Import */
	private function importProductsSimple() {
		/** @var int $count */
		$count = count($this->getCollections()->getOffersSimple());
		if (!$count) {
			df_c1_log('Простые товары отсутствуют.');
		}
		else {
			df_c1_log('Простых товаров: %d.', $count);
			df_c1_log('Импорт простых товаров начат.');
			foreach ($this->getCollections()->getOffersSimple() as $offer) {
				/** @var Df_C1_Cml2_Import_Data_Entity_Offer $offer */
				Df_C1_Cml2_Import_Processor_Product_Type_Simple::i($offer)->process();
			}
			df_c1_log('Импорт простых товаров завершён.');
		}
		return $this;
	}

	/** @return Df_C1_Cml2_Action_Catalog_Import */
	private function importProductsSimplePartImages() {
		foreach ($this->getCollections()->getOffers() as $offer) {
			/** @var Df_C1_Cml2_Import_Data_Entity_Offer $offer */
			if ($offer->isTypeSimple()) {
				Df_C1_Cml2_Import_Processor_Product_Part_Images::i($offer)->process();
			}
		}
		return $this;
	}

	/** @return Df_C1_Cml2_Action_Catalog_Import */
	private function importReferenceLists() {
		df_c1_log('Импорт справочников начат.');
		df_h()->eav()->packetUpdateBegin();
		foreach ($this->getCollections()->getAttributes() as $attribute) {
			/** @var Df_C1_Cml2_Import_Data_Entity_Attribute $attribute */
			// Обратываем только свойства, у которых тип значений — «Справочник».
			if ($attribute instanceof Df_C1_Cml2_Import_Data_Entity_Attribute_ReferenceList) {
				/** @var Df_C1_Cml2_Import_Data_Entity_Attribute_ReferenceList $attribute */
				Df_C1_Cml2_Import_Processor_ReferenceList::i($attribute)->process();
			}
		}
		df_h()->eav()->packetUpdateEnd();
		df_c1_log('Импорт справочников завершён.');
		return $this;
	}
}
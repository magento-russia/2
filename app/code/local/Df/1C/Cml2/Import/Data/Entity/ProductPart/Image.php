<?php
class Df_1C_Cml2_Import_Data_Entity_ProductPart_Image extends Df_1C_Cml2_Import_Data_Entity {
	/**
	 * @override
	 * @return string
	 */
	public function getExternalId() {return $this->getFilePathRelative();}

	/** @return string */
	public function getFilePathFull() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				str_replace(
					DS, '/'
					, Df_1C_Cml2_FileSystem::s()->getFullPathByRelativePath(
						$this->getFilePathRelative()
					)
				)
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * Обратите внимание, что результат метода может быть пустой строкой.
	 * В частности, такое замечено в магазине belle.com.ua:
	 * там 1С передаёт интернет-магазину пустой тег <Картинка/>.
	 * @param bool $skipValidation [optional]
	 * @return string
	 */
	public function getFilePathRelative($skipValidation = false) {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_leaf_s($this->e());
			if (!$skipValidation && !$this->{__METHOD__}) {
				df_error(
					'1C: Управление торговлей почему-то передала в интернет-магазин'
					.' пустой путь к файлу товарного изображения.'
				);
			}
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	public function getName() {return $this->e()->getAttribute('Описание');}

	/** @return bool */
	public function isFileExist() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = file_exists($this->getFilePathFull());
		}
		return $this->{__METHOD__};
	}

	/**
	 * От разультата этого метода зависит добавление данного объекта
	 * в коллекцию @see Df_Core_Xml_Parser_Collection
	 * @used-by Df_Core_Xml_Parser_Collection::getItems()
	 * @override
	 * @return bool
	 */
	public function isValid() {
		return
				parent::isValid()
			&&
				$this->getFilePathRelative($skipValidation = true)
			&&
				/**
				 * В новых версиях модуля 1С-Битрикс (ветка 4, CommerceML 2.08)
				 * допустима ситуация, когда файл каталога (import_*.xml)
				 * содержит описания картинок, при том, что сами картинки у нас физически отсутствуют.
				 *
					<Товар>
						<Ид>cbcf4980-55bc-11d9-848a-00112f43529a</Ид>
						(...)
						<Картинка>import_files/cb/cbcf4980-55bc-11d9-848a-00112f43529a_c4b2feed-22c7-11e4-9156-4061868fc6eb.jpg</Картинка>
						<Картинка>import_files/cb/cbcf4980-55bc-11d9-848a-00112f43529a_c4b2feee-22c7-11e4-9156-4061868fc6eb.jpg</Картинка>
						(...)
					</Товар>
				 *
				 * Это возможно потому, что новые версии модуля 1С-Битрикс (ветка 4, CommerceML 2.08)
				 * передают интернет-магазину файлы картинок лишь единократно,
				 * при первой полной выгрузке товаров в интернет-магазин.
				 *
				 * При последующих выгрузках,
				 * даже если в настройках узла обмена указано, что выгрузка должна быть полной,
				 * 1С не будет передавать интернет-магазину файлы картинок!
				 *
				 * Соответственно, если файлы картинок были удалены из той папки,
				 * куда модуль 1С:Управление торговлей Российской сборки Magento складывает импортируемые файлы,
				 * то указанный 1С в теге «Картинка» путь к файлу не будет соответствовать файлу!
				 *
				 * Более того, это поведение достаточно разумно, и привело меня к мысли об оптимизации.
				 * Ведь нам нет смысла импортировать картинки при каждом сеансе обмена!
				 * Мы можем явно удалять файлы картинок сразу после их импорта,
				 * и при последующих сеансах импорта это будет говорить нам, что картинки уже импортированы
				 * и что их не надо импортировать повторно!
				 */
				$this->isFileExist()
		;
	}

	/** @used-by Df_1C_Cml2_Import_Data_Collection_ProductPart_Images::itemClass() */
	const _C = __CLASS__;
}
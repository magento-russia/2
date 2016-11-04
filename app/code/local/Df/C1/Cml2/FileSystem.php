<?php
namespace Df\C1\Cml2;
class Df_C1_Cml2_FileSystem {
	/** @return string */
	public function getBaseDir() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_cc_path(Mage::getBaseDir('var'), 'rm', '1c');
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $relativePath
	 * @return string
	 */
	public function getFullPathByRelativePath($relativePath) {
		return df_cc_path($this->getBaseDir(), $relativePath);
	}

	/**
	 * @param string $name
	 * @return \Df\Xml\X
	 */
	public function getXmlByRelativePath($name) {
		return $this->getXmlDocumentByRelativePath($name)->e();
	}

	/**
	 * @param string $relativePath
	 * @return Df_C1_Cml2_Import_Data_Document
	 */
	public function getXmlDocumentByRelativePath($relativePath) {
		df_param_string_not_empty($relativePath, 0);
		if (!isset($this->{__METHOD__}[$relativePath])) {
			/** @var string $fullPath */
			$fullPath = $this->getFullPathByRelativePath($relativePath);
			/** @var string|bool $contents */
			$contents = @file_get_contents($fullPath);
			if (false === $contents) {
				df_error('Не могу прочитать требуемый файл «%s».', $fullPath);
			}
			$this->{__METHOD__}[$relativePath] =
				Df_C1_Cml2_Import_Data_Document::create(
					/**
					 * Модуль 1С-Битрикс версии 4.0 формирует документы XML с таким заголовком:
					 * <КоммерческаяИнформация
					  		xmlns="urn:1C.ru:commerceml_2"
					 		xmlns:xs="http://www.w3.org/2001/XMLSchema"
					 		xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
					 		ВерсияСхемы="2.08"
					 		ДатаФормирования="2014-08-10T11:15:40"
					 * >
					 * Если не удалить «xmlns="urn:1C.ru:commerceml_2»,
					 * то прежний код для @see SimpleXMLElement::xpath() перстаёт работать.
					 */
					df_xml_parse(str_replace('xmlns="urn:1C.ru:commerceml_2"', '', $contents))
					, $relativePath
				)
			;
		}
		return $this->{__METHOD__}[$relativePath];
	}

	/** @return Df_C1_Cml2_FileSystem */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}
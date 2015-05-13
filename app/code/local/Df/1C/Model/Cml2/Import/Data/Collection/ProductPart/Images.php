<?php
class Df_1C_Model_Cml2_Import_Data_Collection_ProductPart_Images
	extends Df_1C_Model_Cml2_Import_Data_Collection {
	/** @return Df_1C_Model_Cml2_Import_Data_Collection_ProductPart_Images */
	public function deleteFiles() {
		foreach ($this->getFullPaths() as $fullPath) {
			/** @var string $fullPath */
			@unlink($fullPath);
		}
		return $this;
	}

	/** @return string[] */
	public function getFullPaths() {
		if (!isset($this->{__METHOD__})) {
			/** @var string[] $result  */
			$result = array();
			foreach ($this as $image) {
				/** @var Df_1C_Model_Cml2_Import_Data_Entity_ProductPart_Image $image */
				$result[]= $image->getFilePathFull();
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getItemClass() {
		return Df_1C_Model_Cml2_Import_Data_Entity_ProductPart_Image::_CLASS;
	}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getItemsXmlPathAsArray() {return array('Картинка');}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param Df_Varien_Simplexml_Element $element
	 * @return Df_1C_Model_Cml2_Import_Data_Collection_ProductPart_Images
	 */
	public static function i(Df_Varien_Simplexml_Element $element) {
		return new self(array(self::P__SIMPLE_XML => $element));
	}
}
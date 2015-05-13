<?php
class Df_1C_Model_Cml2_Import_Data_Entity_Attribute_ReferenceList
	extends Df_1C_Model_Cml2_Import_Data_Entity_Attribute {
	/**
	 * @override
	 * @return string
	 */
	public function getBackendModel() {return '';}
	/**
	 * @override
	 * @return string
	 */
	public function getBackendType() {return 'int';}
	/**
	 * @override
	 * @return string
	 */
	public function getFrontendInput() {return 'select';}
	/**
	 * @override
	 * @return string
	 */
	public function getSourceModel() {return '';}

	/** @return Df_1C_Model_Cml2_Import_Data_Collection_ReferenceListPart_Items */
	public function getItems() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_1C_Model_Cml2_Import_Data_Collection_ReferenceListPart_Items::i($this->e())
			;
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => null|array(string => string[])) */
	public function getOptionsInMagentoFormat() {
		if (!isset($this->{__METHOD__})) {
			/** @var string[] $values */
			$values = array();
			/** @var int $optionIndex */
			$optionIndex = 0;
			foreach ($this->getItems() as $item) {
				/** @var Df_1C_Model_Cml2_Import_Data_Entity_ReferenceListPart_Item $item */
				$values['option_' . $optionIndex] = array($item->getName());
				$optionIndex++;
			}
			/** @var string[] $optionNames */
			$optionNames = array_keys($values);
			/** @var array(string => null) $optionStubs */
			$optionStubs = df_array_combine($optionNames, df_array_fill(0, count($optionNames), null));
			df_assert_array($optionStubs);
			$this->{__METHOD__} =
				array(
					'value' => $values
					,'order' => $optionStubs
					,'delete' => $optionStubs
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** Используется из @see Df_1C_Model_Cml2_Import_Data_Entity_Attribute::getTypeMap() */
	const _CLASS = __CLASS__;
}
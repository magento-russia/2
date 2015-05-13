<?php
class Df_1C_Model_Cml2_Import_Data_Entity_Category extends Df_1C_Model_Cml2_Import_Data_Entity {
	/** @return Df_1C_Model_Cml2_Import_Data_Collection_Categories */
	public function getChildren() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_1C_Model_Cml2_Import_Data_Collection_Categories::i($this->e());
		}
		return $this->{__METHOD__};
	}
	/** Используется из @see Df_1C_Model_Cml2_Import_Data_Collection_Categories::getItemClass() */
	const _CLASS = __CLASS__;
}
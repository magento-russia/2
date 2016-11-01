<?php
class Df_C1_Cml2_Import_Data_Entity_Category extends Df_C1_Cml2_Import_Data_Entity {
	/** @return Df_C1_Cml2_Import_Data_Collection_Categories */
	public function getChildren() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_C1_Cml2_Import_Data_Collection_Categories::i($this->e());
		}
		return $this->{__METHOD__};
	}
	/** @used-by Df_C1_Cml2_Import_Data_Collection_Categories::itemClass() */

}
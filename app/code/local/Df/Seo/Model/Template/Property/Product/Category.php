<?php
class Df_Seo_Model_Template_Property_Product_Category
	extends Df_Seo_Model_Template_Property_Product {
	/** @return string */
	public function getValue() {
		$result =
				$this->getProduct()->getCategory()
			?
				$this->getProduct()->getCategory()->getName()
			:
				''
		;
		return $result;
	}


}
<?php
class Df_Seo_Model_Template_Property_Product_Default
	extends Df_Seo_Model_Template_Property_Product {
	/** @return string */
	public function getValue() {
		return
			!df_h()->catalog()->product()->getResource()->getAttribute($this->getName())
			? null
			: (
				$this->getAttributeText()
				? $this->getAttributeText()
				: $this->getProduct()->getData($this->getName())
			)
		;
	}

	/** @return string */
	private function getAttributeText() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getProduct()->getAttributeText($this->getName());
		}
		return $this->{__METHOD__};
	}

	const _C = __CLASS__;
}
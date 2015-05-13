<?php
class Df_Customer_Model_Attribute extends Mage_Customer_Model_Attribute {
	/**
	 * Цель перекрытия —
	 * добавление возмжности русификации свойств покупателя.
	 * @override
	 * @return string
	 */
	public function getFrontendLabel() {
		return
			(
					function_exists('df_enabled')
				&&
					df_enabled(Df_Core_Feature::LOCALIZATION)
			)
			? $this->getFrontendLabelDf()
			: parent::getData(self::P__FRONTEND_LABEL)
		;
	}

	/** @return string */
	private function getFrontendLabelDf() {
		return df_mage()->customerHelper()->__(parent::getData(self::P__FRONTEND_LABEL));
	}

	const P__FRONTEND_LABEL = 'frontend_label';
}
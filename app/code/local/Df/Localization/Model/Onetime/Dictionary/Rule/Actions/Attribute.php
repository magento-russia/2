<?php
class Df_Localization_Model_Onetime_Dictionary_Rule_Actions_Attribute
	extends Df_Localization_Model_Onetime_Dictionary_Rule_Actions {
	/**
	 * @override
	 * @return string|null
	 */
	public function getTitleNew() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set(
				$this->isChildComplex('new_title')
				? $this->descendS('new_title/admin')
				: parent::getTitleNew()
			);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return string|null */
	public function getTitleNewFrontend() {
		return
			$this->isChildComplex('new_title')
			? $this->descendS('new_title/frontend')
			: null
		;
	}
}
<?php
class Df_Pd4_Block_Document extends Df_Core_Block_Template_NoCache {
	/** @return string */
	public function getRowsHtml() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Pd4_Block_Document_Rows::i()->toHtml();
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
}
<?php
namespace Df\Pd4\Block;
class Document extends \Df_Core_Block_Template_NoCache {
	/** @return string */
	public function getRowsHtml() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_render(new Document\Rows);
		}
		return $this->{__METHOD__};
	}
}
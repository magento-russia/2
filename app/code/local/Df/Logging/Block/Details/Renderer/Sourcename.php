<?php
/**
 * Event source name renderer
 *
 */
class Df_Logging_Block_Details_Renderer_Sourcename
	extends Df_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	/**
	 * Render the grid cell value
	 *
	 * @param Varien_Object $row
	 * @return string
	 */
	public function render(Varien_Object $row)
	{
		$data = $row->getData($this->getColumn()->getIndex());
		if (!$data) {
			return '';
		}
		$html = '<div class="source-data"><span class="source-name">' . $row->getSourceName() . '</span>';
		if ($row->getSourceId()) {
			$html .= ' <span class="source-id">#' . $row->getSourceId() . '</span>';
		}
		return $html;
	}
}
<?php
/**
 * Difference columns renderer
 *
 */
class Df_Logging_Block_Adminhtml_Details_Renderer_Diff
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	/**
	 * Render the grid cell value
	 *
	 * @param Varien_Object $row
	 * @return string
	 */
	public function render(Varien_Object $row)
	{
		$data = unserialize($row->getData($this->getColumn()->getIndex()));
		$html = '';
		$specialFlag = false;
		if ($data !== false) {
			if (isset($data['__no_changes'])) {
				$html = $this->__('No changes');
				$specialFlag = true;
			}
			if (isset($data['__was_deleted'])) {
				$html = $this->__('Item was deleted');
				$specialFlag = true;
			}
			if (isset($data['__was_created'])) {
				$html = $this->__('N/A');
				$specialFlag = true;
			}
			$data = (array)$data;
			if (!$specialFlag) {
				$html = '<dl>';
				foreach ($data as $key => $value) {
					$html .= '<dt>' . $key . '</dt><dd>' . df_text()->escapeHtml($value) . '</dd>';
				}
				$html .= '</dl>';
			}
		}
		return $html;
	}
}
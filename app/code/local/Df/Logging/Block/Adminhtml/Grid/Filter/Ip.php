<?php
/**
 * Ip-address grid filter
 */
class Df_Logging_Block_Adminhtml_Grid_Filter_Ip extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Text {
	/**
	 * Collection condition filter getter
	 * @return array
	 */
	public function getCondition()
	{
		$value = $this->getValue();
		if (1 === preg_match('/^(\d+\.){3}\d+$/', $value)) {
			return ip2long($value);
		}
		return array('field_expr' => 'INET_NTOA(#?)', 'like' => "%{$this->_escapeValue($value)}%");
	}
}
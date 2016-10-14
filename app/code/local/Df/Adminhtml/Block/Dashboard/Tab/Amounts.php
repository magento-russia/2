<?php
class Df_Adminhtml_Block_Dashboard_Tab_Amounts extends Mage_Adminhtml_Block_Dashboard_Tab_Amounts {
	/**
	 * Эти переменные используются родительским классом без предварительного объявления
	 * @used-by Mage_Adminhtml_Block_Dashboard_Graph::getChartUrl()
	 * http://magento-forum.ru/topic/4291/
	 */
	/** @var null */
	public $_max = null;
	/** @var null */
	public $_min = null;
}


 
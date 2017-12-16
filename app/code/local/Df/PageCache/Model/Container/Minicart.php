<?php
class Df_PageCache_Model_Container_Minicart extends Df_PageCache_Model_Container_Sidebar_Cart
{
	/**
	 * 2017-12-16
	 * 1) "«Call to a member function setSkipRenderTag() on boolean
	 * in Df/PageCache/Model/Container/Minicart.php:13»": https://github.com/magento-russia/2/issues/2
	 * 2) Аналогичный дефект я уже устранял 2 года назад в методе
	 * @see \Df_PageCache_Model_Container_Catalognavigation::_renderBlock()
	 * https://github.com/magento-russia/2/blob/2.50.0/app/code/local/Df/PageCache/Model/Container/Catalognavigation.php#L102-L112
	 * @return string
	 */
	protected function _renderBlock() {
		$layout = $this->_getLayout('default');
		$r = ''; /** @var string $r */
		if ($b = $layout->getBlock('minicart_head')) {/** @var Mage_Checkout_Block_Cart_Minicart $b */
			$b['skip_render_tag'] = true;
			$r = $b->toHtml();
		}
		return $r;
	}
}

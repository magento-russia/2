<?php
class Df_Vk_Model_Settings_Widget_Groups extends Df_Vk_Model_Settings_Widget {
	/** @return Df_Vk_Model_Settings_Widget_Groups_Page */
	public function accountPage() {
		return $this->getPageSettings('account');
	}
	/** @return Df_Vk_Model_Settings_Widget_Groups_Page */
	public function catalogProductListPage() {
		return $this->getPageSettings('catalog_product_list');
	}
	/** @return Df_Vk_Model_Settings_Widget_Groups_Page */
	public function catalogProductViewPage() {
		return $this->getPageSettings('catalog_product_view');
	}
	/** @return Df_Vk_Model_Settings_Widget_Groups_Page */
	public function frontPage() {
		return $this->getPageSettings('front');
	}
	/** @return Df_Vk_Model_Settings_Widget_Groups_Page */
	public function otherPage() {
		return $this->getPageSettings('other');
	}
	/**
	 * @override
	 * @return string
	 */
	protected function getWidgetType() {
		return 'groups';
	}
	/**
	 * @param string $pageType
	 * @return Df_Vk_Model_Settings_Widget_Groups_Page
	 */
	private function getPageSettings($pageType) {
		df_param_string($pageType, 0);
		if (!isset($this->{__METHOD__}[$pageType])) {
			/** @var Df_Vk_Model_Settings_Widget_Groups_Page $result */
			$result = Df_Vk_Model_Settings_Widget_Groups_Page::i();
			$result->setType($pageType);
			$this->{__METHOD__}[$pageType] = $result;
		}
		return $this->{__METHOD__}[$pageType];
	}

	/** @return Df_Vk_Model_Settings_Widget_Groups */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}
<?php
class Df_Vk_Block_Frontend_Widget_Groups extends Df_Vk_Block_Frontend_Widget {
	/** @return int */
	public function getApplicationId() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df_nat(
					df_preg_match_int(
						df_sprintf(
							'#%s\([^{)]*{[^}]*}, (\d+)#m'
							,preg_quote($this->getJavaScriptObjectName())
						)
						, $this->getSettings()->getCode()
						, false
					)
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Vk_Model_Settings_Widget_Groups_Page */
	public function getSettingsForTheCurrentPage() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Vk_Model_Settings_Widget_Groups_Page::i();
			$this->{__METHOD__}->setType($this->getCurrentPageType());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	public function getJavaScriptNameSpace() {return 'groups';}

	/**
	 * @override
	 * @see Df_Core_Block_Template::defaultTemplate()
	 * @used-by Df_Core_Block_Template::getTemplate()
	 * @return string
	 */
	protected function defaultTemplate() {return 'df/vk/groups.phtml';}

	/**
	 * @override
	 * @return string
	 */
	protected function getJavaScriptObjectName() {return 'VK.Widgets.Group';}

	/**
	 * @override
	 * @return Df_Vk_Model_Settings_Widget
	 */
	protected function getSettings() {return df_cfg()->vk()->groups();}

	/**
	 * @override
	 * @return bool
	 */
	protected function needToShow() {
		return parent::needToShow() && $this->getSettingsForTheCurrentPage()->getEnabled();
	}

	/** @return string */
	private function getCurrentPageType() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = Df_Vk_Model_Widget_PageType::OTHER;
			foreach ($this->getPageTypeMap() as $type => $handle) {
				/** @var string $type */
				/** @var string $handle */
				df_assert_string($type);
				df_assert_string($handle);
				if (df_handle($handle)) {
					$result = $type;
					break;
				}
			}
			df_result_string($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => string) */
	private function getPageTypeMap() {
		return array(
			Df_Vk_Model_Widget_PageType::ACCOUNT => 'customer_account'
			,Df_Vk_Model_Widget_PageType::CATALOG_PRODUCT_LIST => 'catalog_category_view'
			,Df_Vk_Model_Widget_PageType::CATALOG_PRODUCT_VIEW => 'catalog_product_view'
			,Df_Vk_Model_Widget_PageType::FRONT => 'cms_index_index'
		);
	}

	const _C = __CLASS__;
}
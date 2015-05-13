<?php
abstract class Df_Vk_Block_Frontend_Widget extends Df_Core_Block_Template {
	/** @return string */
	abstract public function getJavaScriptNameSpace();

	/** @return string */
	abstract protected function getJavaScriptObjectName();

	/** @return Df_Vk_Model_Settings_Widget */
	abstract protected function getSettings();

	/** @return int */
	public function getApplicationId() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_nat(
				rm_preg_match_int('#apiId: (\d+)#m', $this->getSettings()->getCode(), false)
			);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getSettingsAsJson() {
		return
			rm_preg_match(
				rm_sprintf(
					'#%s\([^{)]*({[^}]*})#m'
					, preg_quote($this->getJavaScriptObjectName())
				)
				, $this->getSettings()->getCode()
			)
		;
	}

	/**
	 * @override
	 * @return string|null
	 */
	protected function getDefaultTemplate() {return 'df/vk/widget.phtml';}

	/**
	 * @override
	 * @return bool
	 */
	protected function needToShow() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * Кэшируем результат метода,
			 * потому что, как я заметил на примере товарной страницы магазина eliteclothes.ru,
			 * данный метод вызывался 5 раз для @see Df_Vk_Block_Frontend_Widget_Like,
			 * и по 3 раза для @see Df_Vk_Block_Frontend_Widget_Groups
			 * и @see Df_Vk_Block_Frontend_Widget_Comments.
			 */
			$this->{__METHOD__} = df_enabled(Df_Core_Feature::VK) && $this->getSettings()->getEnabled();
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
}
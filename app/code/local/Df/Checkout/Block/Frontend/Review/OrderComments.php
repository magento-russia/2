<?php
class Df_Checkout_Block_Frontend_Review_OrderComments extends Df_Core_Block_Template {
	/** @return string */
	public function getFloatRule() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $float */
			$float = $this->settings()->getTextareaFloat();
			$this->{__METHOD__} =
					$this->settings()->specifyTextareaPosition()
				&&
					!Df_Admin_Config_Source_Layout_Float::isNone($float)
				? Df_Core_Model_Css_Rule::compose('float', $float, null, true)
				: ''
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getMarginRule() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = '';
			if (
					$this->settings()->specifyTextareaPosition()
				&&
					$this->settings()->specifyTextareaHoriziontalShift()
				&&
					(0 < $this->settings()->getTextareaHoriziontalShiftLength())
			) {
				$result = Df_Core_Model_Css_Rule::compose(
					array('margin', $this->settings()->getTextareaHoriziontalShiftDirection())
					,-1 * $this->settings()->getTextareaHoriziontalShiftLength()
					,'px'
					,true
				);
			}
			df_result_string($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	public function getNeedInsertFormTag() {return $this->{__METHOD__};}

	/** @return int */
	public function getTextareaWidth() {return $this->settings()->getTextareaWidth();}

	/** @return int */
	public function getTextareaVisibleRows() {return $this->settings()->getTextareaVisibleRows();}

	/**
	 * @param bool $needInsertFormTag
	 * @return Df_Checkout_Block_Frontend_Review_OrderComments
	 */
	public function setNeedInsertFormTag($needInsertFormTag) {
		df_param_boolean($needInsertFormTag, 0);
		$this->{__CLASS__ . '::getNeedInsertFormTag'} = $needInsertFormTag;
		return $this;
	}

	/**
	 * @override
	 * @see Df_Core_Block_Template::defaultTemplate()
	 * @used-by Df_Core_Block_Template::getTemplate()
	 * @return string
	 */
	protected function defaultTemplate() {return 'df/checkout/review/orderComments.phtml';}

	/**
	 * @override
	 * @return bool
	 */
	protected function needToShow() {return $this->settings()->isEnabled();}


	/** @return Df_Checkout_Model_Settings_OrderComments */
	private function settings() {return Df_Checkout_Model_Settings_OrderComments::s();}
}
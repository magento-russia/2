<?php
class Df_Checkout_Block_Frontend_Review_OrderComments extends Df_Core_Block_Template {
	/** @return string */
	public function getFloatRule() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = '';
			if (
					df_cfg()->checkout()->orderComments()->specifyTextareaPosition()
				&&
					(
							Df_Admin_Model_Config_Source_Layout_Float::NONE
						!==
							df_cfg()->checkout()->orderComments()->getTextareaFloat()
					)
			) {
				$result =
					Df_Core_Model_Output_Css_Rule::compose(
						'float'
						,df_cfg()->checkout()->orderComments()->getTextareaFloat()
						,null
						,true
					)
				;
			}
			df_result_string($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getMarginRule() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = '';
			if (
					df_cfg()->checkout()->orderComments()->specifyTextareaPosition()
				&&
					df_cfg()->checkout()->orderComments()->specifyTextareaHoriziontalShift()
				&&
					(0 < df_cfg()->checkout()->orderComments()->getTextareaHoriziontalShiftLength())
			) {
				$result =
					Df_Core_Model_Output_Css_Rule::compose(
						array(
							'margin'
							,df_cfg()->checkout()->orderComments()
								->getTextareaHoriziontalShiftDirection()							
						)
						,-1 * df_cfg()->checkout()->orderComments()->getTextareaHoriziontalShiftLength()
						,'px'
						,true
					)
				;
			}
			df_result_string($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	public function getNeedInsertFormTag() {return $this->{__METHOD__};}

	/** @return int */
	public function getTextareaWidth() {return df_cfg()->checkout()->orderComments()->getTextareaWidth();}

	/** @return int */
	public function getTextareaVisibleRows() {
		return df_cfg()->checkout()->orderComments()->getTextareaVisibleRows();
	}

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
	 * @return string|null
	 */
	protected function getDefaultTemplate() {return 'df/checkout/review/orderComments.phtml';}

	/**
	 * @override
	 * @return bool
	 */
	protected function needToShow() {return df_cfg()->checkout()->orderComments()->isEnabled();}

	const _CLASS = __CLASS__;
}
<?php
abstract class Df_Core_Block_Element extends Df_Core_Block_Template {
	/**
	 * @override
	 * @return string
	 */
	public function getArea() {return Df_Core_Const_Design_Area::FRONTEND;}

	/** @return string */
	public function getCssClassesAttributeAsString() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				!$this->getCssClasses()
				? ''
				: implode(
					Df_Core_Const::T_ASSIGNMENT
					,array(
						self::HTML_ATTRIBUTE__CLASS
						,$this->quoteAttributeValue(
							implode(' ', $this->getCssClasses())
						)
					)
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string[] */
	protected function getCssClasses() {return array();}

	/**
	 * @override
	 * @return string
	 */
	protected function getDefaultTemplate() {return $this->calculateTemplatePath();}

	/** @return string[] */
	protected function getTemplatePathParts() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->splitClassNameInMagentoFormatToPathParts(
					$this->getCurrentClassNameInMagentoFormat()
				)
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * Потомки могу переопределить данный метод.
	 * Если данный метод вернёт true, то система не будет рисовать данный блок.
	 * @return bool
	 */
	protected function isBlockEmpty() {return false;}

	/**
	 * @override
	 * @return bool
	 */
	protected function needCaching() {return false;}

	/**
	 * @override
	 * @return bool
	 */
	protected function needToShow() {return parent::needToShow() && !$this->isBlockEmpty();}

	/**
	 * @param string $classNameInMagentoFormat
	 * @return string[]
	 */
	protected function splitClassNameInMagentoFormatToPathParts($classNameInMagentoFormat) {
		df_param_string($classNameInMagentoFormat, 0);
		/** @var string[] $moduleNameAndPath */
		$moduleNameAndPath =
			explode(
				Df_Core_Model_Reflection::MODULE_NAME_SEPARATOR
				,$classNameInMagentoFormat
			)
		;
		df_assert_array($moduleNameAndPath);
		df_assert_eq(2, count($moduleNameAndPath));
		/** @var string $moduleName */
		$moduleName = df_a($moduleNameAndPath, 0);
		df_assert_string($moduleName);
		/** @var string[] $moduleNameParts */
		$moduleNameParts =
			explode(
				Df_Core_Model_Reflection::PARTS_SEPARATOR
				,$moduleName
			)
		;
		df_assert_array($moduleNameParts);
		/**
		 * Заменяем только первое вхождение.
		 * df_checkout_pro/frontend_field_company
		 * надо разбить как:
		 * df/checkout_pro/frontend/field/company
		 */
		$moduleNameParts = df_array_clean(
			df_a($moduleNameParts, 0)
			,implode(Df_Core_Model_Reflection::PARTS_SEPARATOR, rm_tail($moduleNameParts)
		));
		df_assert_between(count($moduleNameParts), 1, 2);
		/** @var string[] $pathParts */
		$pathParts =
			explode(
				Df_Core_Model_Reflection::PARTS_SEPARATOR
				,df_a($moduleNameAndPath, 1)
			)
		;
		return array_merge($moduleNameParts, $pathParts);
	}

	/** @return string */
	private function calculateTemplatePath() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				implode(
					'.'
					,array(
						df_concat_path($this->getTemplatePathParts())
						,Df_Core_Const::FILE_EXTENSION__TEMPLATE
					)
				)
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $html
	 * @return string
	 */
	private function escapeHtmlWithQuotesInUtf8Mode($html) {
		df_param_string($html, 0);
		/** @var string $result */
		$result =
			htmlspecialchars(
				$html
				,ENT_QUOTES
				,Df_Core_Const::UTF_8
				,false
			)
		;
		df_result_string($result);
		return $result;
	}

	/**
	 * @param string $attributeValue
	 * @param string $quoteSymbol[optional]
	 * @return string
	 */
	private function quoteAttributeValue(
		$attributeValue, $quoteSymbol = Df_Core_Const::T_QUOTE_SINGLE
	) {
		df_param_string($attributeValue, 0);
		df_param_string($quoteSymbol, 1);
		return
			df_concat(
				$quoteSymbol
				,$this->escapeHtmlWithQuotesInUtf8Mode($attributeValue)
				,$quoteSymbol
			)
		;
	}

	const _CLASS = __CLASS__;
	const HTML_ATTRIBUTE__CLASS = 'class';
}
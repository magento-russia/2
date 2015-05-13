<?php
class Df_Tweaks_Block_Frontend_Style extends Df_Core_Block_Abstract {
	/**
	 * @override
	 * @return string[]
	 */
	public function getCacheKeyInfo() {
		return
			array_merge(
				parent::getCacheKeyInfo()
				,array(get_class($this))
				,rm_layout()->getUpdate()->getHandles()
			)
		;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function _toHtml() {
		/** @var string $result */
		$result =
				!(
						df_module_enabled(Df_Core_Module::TWEAKS)
					&&
						df_enabled(Df_Core_Feature::TWEAKS)
				)
			?
				''
			:
				$this->getStyle()->toHtml()
		;
		return $result;
	}

	/**
	 * @param Df_Admin_Model_Config_Extractor_Font $font
	 * @param string|string[] $selector
	 * @return Df_Tweaks_Block_Frontend_Style
	 */
	private function adjustLetterCase(Df_Admin_Model_Config_Extractor_Font $font, $selector) {
		if (
				Df_Admin_Model_Config_Source_Format_Text_LetterCase::_DEFAULT
			!==
				$font->getLetterCase()
		) {
			if (is_array($selector)) {
				$selector = df_concat_enum($selector);
			}
			$this->getStyle()->getSelectors()->addItem(
				Df_Core_Block_Element_Style_Selector::i(
					$selector
					,Df_Core_Model_Output_Css_Rule_Set::i()->addItem($font->getLetterCaseAsCssRule())
				)
			);
		}
		return $this;
	}

	/** @return Df_Tweaks_Block_Frontend_Style */
	private function adjustReviewsAndRatings() {
		if (df_h()->tweaks()->isItCatalogProductList()) {
			if (
					df_cfg()->tweaks()->catalog()->product()->_list()->needHideRating()
				&&
					df_cfg()->tweaks()->catalog()->product()->_list()->needHideReviews()
			) {
				$this->getStyle()->getSelectors()
					->addHider('.category-products .ratings')
				;
			}
			else if (df_cfg()->tweaks()->catalog()->product()->_list()->needHideRating()) {
				$this->getStyle()->getSelectors()
					->addHider('.category-products .ratings .rating-box')
				;
			}
			else if (df_cfg()->tweaks()->catalog()->product()->_list()->needHideReviews()) {
				$this->getStyle()->getSelectors()
					->addHider('.category-products .ratings .amount')
				;
			}
		}
		else if (rm_handle_presents(Df_Core_Model_Layout_Handle::CATALOG_PRODUCT_VIEW)) {
			if 	(
					df_cfg()->tweaks()->catalog()->product()->view()->needHideRating()
				&&
					df_cfg()->tweaks()->catalog()->product()->view()->needHideReviewsLink()
				&&
					df_cfg()->tweaks()->catalog()->product()->view()->needHideAddReviewLink()
			) {
				$this->getStyle()->getSelectors()
					->addHider('.product-view .ratings')
				;
			}
			else {
				if (df_cfg()->tweaks()->catalog()->product()->view()->needHideRating()) {
					$this->getStyle()->getSelectors()
						->addHider('.product-view .ratings .rating-box')
					;
				}
				if (
						df_cfg()->tweaks()->catalog()->product()->view()->needHideReviewsLink()
					&&
						df_cfg()->tweaks()->catalog()->product()->view()->needHideAddReviewLink()
				) {
					$this->getStyle()->getSelectors()
						->addHider('.product-view .ratings .rating-links')
					;
				}
				else {
					if (
							df_cfg()->tweaks()->catalog()->product()->view()->needHideReviewsLink()
						||
							df_cfg()->tweaks()->catalog()->product()->view()->needHideAddReviewLink()
					) {
						$this->getStyle()->getSelectors()
							->addHider('.product-view .ratings .rating-links .separator')
						;
					}
					if (df_cfg()->tweaks()->catalog()->product()->view()->needHideReviewsLink()) {
						$this->getStyle()->getSelectors()
							->addHider('.product-view .ratings .rating-links a:first-child')
							->addHider('.product-view .ratings .rating-links a.first-child')
						;
					}
					if (df_cfg()->tweaks()->catalog()->product()->view()->needHideAddReviewLink()) {
						$this->getStyle()->getSelectors()
							->addHider('.product-view .ratings .rating-links a:last-child')
							->addHider('.product-view .ratings .rating-links a.last-child')
							->addHider('.product-view p.no-rating')
						;
					}
				}
			}
		}
		return $this;
	}

	/** @return Df_Core_Block_Element_Style */
	private function getStyle() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Core_Block_Element_Style::i(__METHOD__);
			$this
				->adjustLetterCase(
					df_cfg()->tweaks()->labels()->getFontForButton(), '.button *, .buttons-set'
				)
				->adjustLetterCase(
					df_cfg()->tweaks()->labels()->getFontForSideBlockLabel(), '.sidebar .block-title'
				)
				->adjustLetterCase(
					df_cfg()->tweaks()->header()->getFont()
					, array('.header .links', '.header-container .quick-access .links a')
				)
			;
			$this->adjustReviewsAndRatings();
			if (df_cfg()->tweaks()->footer()->removeHelpUs()) {
				$this->getStyle()->getSelectors()->addHider('p.bugs');
			}
			if (
					rm_handle_presents(Df_Core_Model_Layout_Handle::CATALOG_PRODUCT_VIEW)
				&&
					df_cfg()->tweaks()->catalog()->product()->view()->needHideAvailability()
			) {
				$this->getStyle()->getSelectors()->addHider('.product-view p.availability');
			}
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		/**
		 * Чтобы блок кэшировался стандартным, заложенным в @see Mage_Core_Block_Abstract способом,
		 * продолжительность хранения кэша надо указывать обязательно,
		 * потому что значением продолжительности по умолчанию является «null»,
		 * что в контексте @see Mage_Core_Block_Abstract
		 * (и в полную противоположность Zend Framework
		 * и всем остальным частям Magento, где используется кэширование)
		 * означает, что блок не удет кэшироваться вовсе!
		 * @see Mage_Core_Block_Abstract::_loadCache()
		 */
		$this->setData('cache_lifetime', Df_Core_Block_Template::CACHE_LIFETIME_STANDARD);
	}
	const _CLASS = __CLASS__;
}
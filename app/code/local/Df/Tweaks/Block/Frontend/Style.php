<?php
class Df_Tweaks_Block_Frontend_Style extends Df_Core_Block_Abstract {
	/**
	 * @override
	 * @see Df_Core_Block_Abstract::cacheKeySuffix()
	 * @used-by Df_Core_Block_Abstract::getCacheKeyInfo()
	 * @return string|string[]
	 */
	public function cacheKeySuffix() {return rm_handles();}

	/**
	 * @override
	 * @see Mage_Core_Block_Abstract::_toHtml()
	 * @used-by Mage_Core_Block_Abstract::toHtml()
	 * @return string
	 */
	protected function _toHtml() {return $this->css()->render();}

	/**
	 * @override
	 * @see Df_Core_Block_Abstract::needToShow()
	 * @used-by Df_Core_Block_Abstract::_loadCache()
	 * @used-by Df_Core_Block_Abstract::getCacheKey()
	 * @return bool
	 */
	protected function needToShow() {return df_module_enabled(Df_Core_Module::TWEAKS);}

	/**
	 * @param Df_Admin_Config_Font $font
	 * @param string|string[] $selector
	 * @return Df_Tweaks_Block_Frontend_Style
	 */
	private function adjustLetterCase(Df_Admin_Config_Font $font, $selector) {
		if (!$font->isDefault()) {
			/**
			 * 2015-02-01
			 * Раньше опция @see Df_Admin_Config_Source_LetterCase::UCFIRST
			 * простро транслировалась в правило «text-transform: capitalize»,
			 * что приводило к результатам типа «Адрес Электронной Почты».
			 * http://htmlbook.ru/css/text-transform
			 * Однако в русском языке написание каждого слова с заглавной буквы не принято:
			 * вместо этого гораздо разумнее при значении опции
			 * @see Df_Admin_Config_Source_LetterCase::UCFIRST
			 * делать заглавной только первую букву первого слова,
			 * например: «Адрес электронной почты».
			 * Это можно сделать посредством применения селектора «:first-letter», например:
				.block:first-letter {text-transform: uppercase;}
			 * http://stackoverflow.com/a/5577380
			 * То есть нам надо к каждому $selector добавить дополнительный селектор «:first-letter».
			 */
			if ($font->isUcFirst()) {
				// тут нам удобнее работать с массивом,
				// чем отдельно разбирать случаи массива и строки
				$selector = df_array($selector);
				foreach ($selector as &$item) {
					/** @var string $item */
					// Строка не должна состоять из нескольких селекторов.
					// Если нужно применить одно правило сразу к нескольким селекторам,
					// то передавайте селекторы в виде массива $selector
					// (другими словами, передавайте в качестве $selector не строку, а массив)
					df_assert(!df_contains($item, ','));
					$item = $item . ':first-letter';
				}
			}
			$this->css()->addSelectorSimple($selector, 'text-transform', $font->getLetterCaseCss());
		}
		return $this;
	}

	/** @return Df_Tweaks_Block_Frontend_Style */
	private function adjustReviewsAndRatings() {
		/** @var Df_Tweaks_Model_Settings_Catalog_Product $s */
		$s = df_cfg()->tweaks()->catalog()->product();
		if (df_h()->tweaks()->isItCatalogProductList()) {
			if ($s->_list()->needHideRating() && $s->_list()->needHideReviews()) {
				$this->hide('.category-products .ratings');
			}
			else if ($s->_list()->needHideRating()) {
				$this->hide('.category-products .ratings .rating-box');
			}
			else if ($s->_list()->needHideReviews()) {
				$this->hide('.category-products .ratings .amount');
			}
		}
		else if (rm_handle_presents(Df_Core_Model_Layout_Handle::CATALOG_PRODUCT_VIEW)) {
			if (
				$s->view()->needHideRating()
				&& $s->view()->needHideReviewsLink()
				&& $s->view()->needHideAddReviewLink()
			) {
				$this->hide('.product-view .ratings');
			}
			else {
				if ($s->view()->needHideRating()) {
					$this->hide('.product-view .ratings .rating-box');
				}
				if ($s->view()->needHideReviewsLink() && $s->view()->needHideAddReviewLink()) {
					$this->hide('.product-view .ratings .rating-links');
				}
				else {
					if ($s->view()->needHideReviewsLink() || $s->view()->needHideAddReviewLink()) {
						$this->hide('.product-view .ratings .rating-links .separator');
					}
					if ($s->view()->needHideReviewsLink()) {
						$this->hide(
							'.product-view .ratings .rating-links a:first-child'
							,'.product-view .ratings .rating-links a.first-child'
						);
					}
					if ($s->view()->needHideAddReviewLink()) {
						$this->hide(
							'.product-view .ratings .rating-links a:last-child'
							,'.product-view .ratings .rating-links a.last-child'
							,'.product-view p.no-rating'
						);
					}
				}
			}
		}
		return $this;
	}
	
	/** @return Df_Core_Model_Css */
	private function css() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Core_Model_Css::i();
			/** @var Df_Tweaks_Model_Settings_Labels $fonts */
			$fonts = Df_Tweaks_Model_Settings_Labels::s();
			$this
				->adjustLetterCase($fonts->forButtons(), array('.button *', '.buttons-set'))
				/**
				 * 2015-02-01
				 * Раньше существовало глобальное правило CSS,
				 * которое насильно делало все подписи к полям ввода строчными буквами:
					.form-list {
						label {
							text-transform: lowercase;
						}
					}
				 * Конечно, это неправильно.
				 * Теперь у администратора есть выбор регистра отображения подписей полей ввода.
				 * http://magento-forum.ru/topic/4982/
				 */
				->adjustLetterCase($fonts->forFormInputs(), '.form-list label')
				->adjustLetterCase($fonts->forSideBlockTitles(), '.sidebar .block-title')
				->adjustLetterCase(df_cfg()->tweaks()->header()->getFont(), array(
					'.header .links', '.header-container .quick-access .links a'
				))
			;
			$this->adjustReviewsAndRatings();
			if (df_cfg()->tweaks()->footer()->removeHelpUs()) {
				$this->hide('p.bugs');
			}
			if (
					rm_handle_presents(Df_Core_Model_Layout_Handle::CATALOG_PRODUCT_VIEW)
				&&
					df_cfg()->tweaks()->catalog()->product()->view()->needHideAvailability()
			) {
				$this->hide('.product-view p.availability');
			}
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string|string[] $selector
	 * @return void
	 */
	private function hide($selector) {$this->css()->addHider($selector);}

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
		 * @used-by Mage_Core_Block_Abstract::_loadCache()
		 */
		$this->setData('cache_lifetime', Df_Core_Block_Template::CACHE_LIFETIME_STANDARD);
	}
}
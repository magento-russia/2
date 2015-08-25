<?php
class Df_Page_Helper_Head extends Mage_Core_Helper_Abstract {
	/**
	 * @param string $type
	 * @param string $name
	 * @return bool
	 */
	public function needSkipItem($type, $name) {
		/** @var bool $result */
		$result =
				$this->needSkipAsJQuery($type, $name)
			||
				/**
				 * Позволяет удалять со страницы стандартные стили CSS.
				 * На 2014-12-06 эта функциональность используется
				 * только модулем «Квитанция Сбербанка»
				 * для отображения платёжной формы ПД-4.
				 */
				$this->needSkipAsStandardCss($type, $name)
			||
				/**
				 * Позволяет сторонним модулям удалять со страницы некоторые браузерные файлы.
				 * На 2014-12-06 эта функциональность используется
				 * для удаления сторонних копий библиотеки JavaScript FancyBox
				 * (добавляемых сторонними оформительскими темами: например, TemplateMonster #43373)
				 * со страницы оформления заказа при включенности модуля «Удобное оформление заказа»,
				 * потому что сторонние копии библиотеки JavaScript FancyBox
				 * могут приводить (и приводят в случае темы TemplateMonster #43373)
				 * к неправильному отображению формы авторизации покупателя,
				 * открывающейся при нажатии на ссылку «ранее покупали у нас что-то?».
				 */
				$this->needSkipCustom($type, $name)
		;
		return $result;
	}

	/**
	 * @param string $scriptName
	 * @return bool
	 */
	private function isItJQuery($scriptName) {
		/**
		 * 2015-07-04
		 * Модуль Cmsmart_Ajaxsearch использует какую-то свою переделанную версию библиотеки jQuery
		 * и размещает её в файлах со стандартными для jQuery именами.
		 */
		if (rm_starts_with($scriptName, 'cmsmart/ajaxsearch')) {
			$result = false;
		}
		else {
			/**
			 * Как ядро библиотеки jQuery должны определяться скрипты с именами следующего вида:
			 * path/jquery.js
			 * path/jquery-1.8.3.js
			 * path/jquery-1.8.3.min.js
			 *
			 * Обратите внимание, что скрипты с именами вроде path/history.adapter.jquery.js
			 * не должны определяться, как ядро библиотеки jQuery.
			 * @link http://magento-forum.ru/topic/3979/
			 */
			/** @var string $fileName */
			$fileName = rm_last(explode('/', $scriptName));
			/** @var string $pattern */
			$pattern = '#^jquery(\-\d+\.\d+\.\d+)?(\.min)?\.js$#ui';
			/** @var string[] $matches */
			$matches = array();
			$result = (1 === preg_match($pattern, $fileName, $matches));
		}
		return $result;
	}

	/**
	 * @param string $scriptName
	 * @return bool
	 */
	private function isItJQueryNoConflict($scriptName) {
		return rm_contains(mb_strtolower($scriptName), mb_strtolower('noconflict.js'));
	}

	/**
	 * @param string $type
	 * @param string $name
	 * @return bool
	 */
	private function needSkipAsJQuery($type, $name) {
		/** @var bool $jqueryRemoveExtraneous */
		static $jqueryRemoveExtraneous;
		if (!isset($jqueryRemoveExtraneous)) {
			$jqueryRemoveExtraneous =
					(
							df_is_admin()
						&&
							df_cfg()->admin()->jquery()->needRemoveExtraneous()
						&&
							(
									Df_Admin_Model_Config_Source_JqueryLoadMode::VALUE__NO_LOAD
								!==
									df_cfg()->admin()->jquery()->getLoadMode()
							)
					)
				||
					(
							!df_is_admin()
						&&
							df_cfg()->tweaks()->jquery()->needRemoveExtraneous()
						&&
							(
									Df_Admin_Model_Config_Source_JqueryLoadMode::VALUE__NO_LOAD
								!==
									df_cfg()->tweaks()->jquery()->getLoadMode()
							)
					)

			;
		}
		/** @var bool $result */
		$result =
				$jqueryRemoveExtraneous
			&&
				(in_array($type, array('js', 'skin_js')))
			&&
				/**
				 * Обратите внимание, что Российская сборка Magento добавляет на страницу
				 * библиотеку jQuery не посредством addItem, а более низкоуровневыми методами
				 */
				(
						$this->isItJQuery($name)
					||
						$this->isItJQueryNoConflict($name)
				)
		;
		return $result;
	}

	/**
	 * Позволяет удалять со страницы стандартные стили CSS.
	 * На 2014-12-06 эта функциональность используется
	 * только модулем «Квитанция Сбербанка»
	 * для отображения платёжной формы ПД-4.
	 * @param string $type
	 * @param string $name
	 * @return bool
	 */
	private function needSkipAsStandardCss($type, $name) {
		/**
		 * 2015-08-25
		 * Крайне неряшливый модуль Ves_Blog
		 * оформительской темы Ves Super Store (ThemeForest 8002349)
		 * ломает инициализацию системы, и в данной точке программы
		 * контроллер может быть ещё не инициализирован.
		 * http://magento-forum.ru/topic/5206/
		 */
		/** @var bool $result */
		$result = !!rm_state()->getController();
		if ($result) {
			/** @var bool $needSkipStandardCss */
			static $needSkipStandardCss;
			if (!isset($needSkipStanradsCss)) {
				$needSkipStandardCss =
					rm_bool(
						(string)Mage::getConfig()->getNode(
							'df/page/skip_standard_css/' . rm_state()->getController()->getFullActionName()
						)
					)
				;
			}
			$result =
				$needSkipStandardCss
				&& in_array($type, array('skin_css', 'js_css'))
				&& !rm_contains($name, 'df/')
			;
		}
		return $result;
	}

	/**
	 * Позволяет сторонним модулям удалять со страницы некоторые браузерные файлы.
	 * На 2014-12-06 эта функциональность используется
	 * для удаления сторонних копий библиотеки JavaScript FancyBox
	 * (добавляемых сторонними оформительскими темами: например, TemplateMonster #43373)
	 * со страницы оформления заказа при включенности модуля «Удобное оформление заказа»,
	 * потому что сторонние копии библиотеки JavaScript FancyBox
	 * могут приводить (и приводят в случае темы TemplateMonster #43373)
	 * к неправильному отображению формы авторизации покупателя,
	 * открывающейся при нажатии на ссылку «ранее покупали у нас что-то?».
	 * @param string $type
	 * @param string $name
	 * @return bool
	 */
	private function needSkipCustom($type, $name) {
		/**
		 * Вообще говоря, по-правильному надо использовать здесь шаблон проектирования «наблюдатель»:
		 * вызывать @see Mage::dispatchEvent(), однако я сознательно это пока не делаю
		 * ради ускорения работы системы
		 * (сделаю, когда подобная обработка потребуется не одному модулю, а нескольким).
		 */
		/** @var bool $result */
		$result =
			 	!df_is_admin()
			&&
				rm_state()->getController()
			&&
				('checkout_onepage_index' === rm_state()->getController()->getFullActionName())
			&&
				df_cfg()->checkout()->_interface()->needShowAllStepsAtOnce()
			&&
				(in_array($name, array(
					'js/fancybox/jquery.fancybox-1.3.4.js'
					,'js/fancybox/jquery.easing-1.3.pack.js'
					,'js/fancybox/jquery.mousewheel-3.0.6.pack.js'
					,'js/fancybox/jquery.fancybox-1.3.4.css'
				)))
		;
		return $result;
	}

	/** @return Df_Page_Helper_Head */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}
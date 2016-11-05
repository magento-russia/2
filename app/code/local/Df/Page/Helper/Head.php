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
				$this->needSkipCustom($name)
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
		if (df_starts_with($scriptName, 'cmsmart/ajaxsearch')) {
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
			 * http://magento-forum.ru/topic/3979/
			 *
			 * 2015-10-19
			 * Заметил, что один из сторонних модулей подключает jQuery файлом с именем jquery-1.6.js,
			 * т.е. в номере версии 2 сегмента между точками, а не 3.
			 * Обновил регулярное выражение.
			 */
			/** @var string $fileName */
			$fileName = df_last(explode('/', $scriptName));
			/** @var string $pattern */
			$pattern = '#^jquery(\-\d+\.\d+(\.\d+)?)?(\.min)?\.js$#ui';
			/** @var string[] $matches */
			$matches = [];
			$result = (1 === preg_match($pattern, $fileName, $matches));
		}
		return $result;
	}

	/**
	 * 2015-10-19
	 * Удаление избыточных копий библиотеки jQuery Migrate.
	 * @param string $scriptName
	 * @return bool
	 */
	private function isItJQueryMigrate($scriptName) {return df_contains($scriptName, 'jquery-migrate');}

	/**
	 * @param string $scriptName
	 * @return bool
	 */
	private function isItJQueryNoConflict($scriptName) {
		return
			df_contains(mb_strtolower($scriptName), 'noconflict.js')
			// 2015-10-19
			// Модуль Qaz_Qzoom, магазин chepe.ru
			|| df_contains($scriptName, 'jqueryNoconfig.js')
		;
	}

	/**
	 * @param string $type
	 * @param string $name
	 * @return bool
	 */
	private function needSkipAsJQuery($type, $name) {
		/** @var bool $removeExtraneous */
		static $removeExtraneous;
		if (is_null($removeExtraneous)) {
			$removeExtraneous = df_cfgr()->jquery()->needRemoveExtraneous();
		}
		return
			$removeExtraneous
			&& in_array($type, array('js', 'skin_js'))
			/**
			 * Обратите внимание, что Российская сборка Magento добавляет на страницу
			 * библиотеку jQuery не посредством @see addItem(), а более низкоуровневыми методами.
			 */
			&& (
				$this->isItJQuery($name)
				|| $this->isItJQueryNoConflict($name)
				|| $this->isItJQueryMigrate($name)
			)
		;
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
		$result = !!df_action_name();
		if ($result) {
			/** @var bool $skip */
			static $skip;
			if (is_null($skip)) {
				$skip = df_leaf_b(df_config_node('df/page/skip_standard_css/', df_action_name()));
			}
			$result =
				$skip
				&& in_array($type, array('skin_css', 'js_css'))
				&& !df_contains($name, 'df/')
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
	 *
	 * Вообще говоря, по-правильному надо использовать здесь шаблон проектирования «наблюдатель»:
	 * вызывать @see Mage::dispatchEvent(), однако я сознательно это пока не делаю
	 * ради ускорения работы системы
	 * (сделаю, когда подобная обработка потребуется не одному модулю, а нескольким).
	 *
	 * @param string $name
	 * @return bool
	 */
	private function needSkipCustom($name) {
		return
				!df_is_admin()
			&&
				'checkout_onepage_index' === df_action_name()
			&&
				df_checkout_ergonomic()
			&&
				(in_array($name, array(
					'js/fancybox/jquery.fancybox-1.3.4.js'
					,'js/fancybox/jquery.easing-1.3.pack.js'
					,'js/fancybox/jquery.mousewheel-3.0.6.pack.js'
					,'js/fancybox/jquery.fancybox-1.3.4.css'
				)))
		;
	}

	/** @return Df_Page_Helper_Head */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}
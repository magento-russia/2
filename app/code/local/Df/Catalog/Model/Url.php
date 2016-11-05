<?php
/**
 * @method Df_Catalog_Model_Resource_Url getResource()
 */
class Df_Catalog_Model_Url extends Mage_Catalog_Model_Url {
	/**
	 * Российская сборка Magento обязана перекрыть этот метод
	 * для правильной обработки кириллических URL.
	 *
	 * Перекрываемый родительский метод применяет функции @see strlen() и @see substr(),
	 * что иногда, редко (для кириллических URL максимально допустимой длины)
	 * приводит к падению интрпретатора PHP.
	 *
	 * Заметил эффект падения интерпретатора PHP 26 июня 2014 года в магазине rukodeling.ru,
	 * при том, что поддержка кириллических URL была запрограммирована ещё в январе 2011 года,
	 * 3.5 года назад!
	 * Другими словами, данный эффект встречается редко, но он встречается.
	 * Причём интерпретатор PHP именно падает, безо всяких предупреждений.
	 * Браузер выдаёт сообщение:
	 * «The connection was reset»
	 * «This webpage is not available» («ERR_CONNECTION_RESET»).
	 *
	 * Данные, при которых произошло падение:
	 *
	 * $product:
		Varien_Object(
			[entity_id] => 1170
			[category_ids] => array(
				[0] => 66
				[1] => 72
				[2] => 95
				[3] => 102
				[4] => 157
				[5] => 169
			)
			[store_id] => 1
			[name] => Набор инструментов для квиллинга: шаблон для квиллинга с кругами, мягкий полимерный мат, инструмент для квиллинга Quilling Stick с раздвоенным кончиком
			[url_key] => набор-инструментов-для-квиллинга-шаблон-для-квиллинга-с-кругами-мягкий-полимерный-мат-инструмент-для-квиллинга-quilling-stick-с-раздвоенным-кончиком
			[url_path] => набор-инструментов-для-квиллинга-шаблон-для-квиллинга-с-кругами-мягкий-полимерный-мат-инструмент-для-квиллинга-quilling-stick-с-раздвое
		)
	 *
	 * Обратите внимание, что «url_path» имеет длину 136 символов UTF-8, однако @see strlen(),
	 * используемая ядром, возвращает почти в 2 раза большее значение
	 * (дефисы занимают 1 байт, а символы кириллицы — 2 байта), и таким образом,
	 * система достигает предела длины для «url_path».
	 *
	 * В программном коде ядра это выглядит так:
	 * (@see Mage_Catalog_Model_Url::getProductRequestPath())
		if (strlen($requestPath) > self::MAX_REQUEST_PATH_LENGTH + self::ALLOWED_REQUEST_PATH_OVERFLOW) {
			$requestPath = substr($requestPath, 0, self::MAX_REQUEST_PATH_LENGTH);
		}
	 * Вот здесь для указанного выше «url_path» происходит превышение
	 * @see Mage_Catalog_Model_Url::MAX_REQUEST_PATH_LENGTH +
	 * @see Mage_Catalog_Model_Url::ALLOWED_REQUEST_PATH_OVERFLOW
	 *
	 * Само по себе это не страшно.
	 * Однако дальше ядро пытается обрубить этот слишком длинный «url_path»
	 * посредством функции @see substr(),
	 * которая со многобайтовыми (в данном случае — UTF-8) символами работает неправильно,
	 * потому что обрубает строку не посимвольно, а побайтово,
	 * и многобайтовый символ UTF-8 может оказаться разрубленным на куски.
	 *
	 * Для нашего «url_path» так и происходит:
	 * после его обрубки его последний символ становится некорректным (недопустимым для UTF-8),
	 * что и приводит дальше к зависанию интерпретатора PHP.
	 *
	 * Для корректной обработки кириллических URL
	 * нам надо заменить вызов функции @see substr() на @see mb_substr(),
	 * а также, вероятно, заменить вызовы других аналогичных функций (в частности, @see strlen()).
	 *
	 * Обратите внимание,
	 * что для поддержки кириллицы в URL
	 * нам нет необходимости уменьшать лимит @see Mage_Catalog_Model_Url::MAX_REQUEST_PATH_LENGTH.
	 *
	 * В БД колонка «request_path» таблицы «core_url_rewrite» имеет тип varchar(255).
	 * Начиная с версии 4.1 в MySQL varchar(255) означает именно 255 символов
	 * (в том числе и UTF-8), а не 255 байтов:
	 * http://stackoverflow.com/a/3739871
	 * http://dev.mysql.com/doc/refman/5.0/en/string-type-overview.html
	 *
	 * «MySQL interprets length specifications in character column definitions in character units.
	 * (Before MySQL 4.1, column lengths were interpreted in bytes.)
	 * This applies to CHAR, VARCHAR, and the TEXT types.»
	 *
	 * Magento требует MySQL версии не ниже 4.1.20:
	 * http://magento.com/resources/system-requirements
	 *
	 * Поэтому можно быть уверенным, что в колонку «request_path» таблицы «core_url_rewrite»
	 * поместится 255 символов, даже если часть из этих символов будет кириллицей.
	 * Значит, и для поля «url_path» объекта $product мы тоже можем оставить прежний лимит
	 * @see Mage_Catalog_Model_Url::MAX_REQUEST_PATH_LENGTH.
	 *
	 * Обратите внимание, что мы не можем решить данную проблему посредством mbstring.func_overload,
	 * потому что значение mbstring.func_overload можно итзменить только через ini-файлы,
	 * и нельзя изменить через @see ini_set():
	 * http://stackoverflow.com/questions/8526147/utf-8-and-php-mbstring-func-overload-doesnt-work
	 *
	 * @override
	 * @param Varien_Object $product
	 * @param Varien_Object $category
	 * @return string
	 */
	public function getProductRequestPath($product, $category) {
		/** @noinspection PhpUndefinedMethodInspection */
		if ($product->getUrlKey() == '') {
			/** @noinspection PhpUndefinedMethodInspection */
			$urlKey = $this->getProductModel()->formatUrlKey($product->getName());
		} else {
			/** @noinspection PhpUndefinedMethodInspection */
			$urlKey = $this->getProductModel()->formatUrlKey($product->getUrlKey());
		}
		/** @noinspection PhpUndefinedMethodInspection */
		$storeId = $category->getStoreId();
		$suffix  = $this->getProductUrlSuffix($storeId);
		$idPath  = $this->generatePath('id', $product, $category);
		/**
		* Prepare product base request path
		*/
		/** @noinspection PhpUndefinedMethodInspection */
		if ($category->getLevel() > 1) {
			// НАЧАЛО ЗАПЛАТКИ
			/** @var bool $exists_addCategoryUrlPath */
			static $exists_addCategoryUrlPath;
			if (is_null($exists_addCategoryUrlPath)) {
				$exists_addCategoryUrlPath = method_exists($this, '_addCategoryUrlPath');
			}
			if ($exists_addCategoryUrlPath) {
				// To ensure, that category has path either from attribute or generated now
				$this->_addCategoryUrlPath($category);
			}
			// КОНЕЦ ЗАПЛАТКИ
			/** @noinspection PhpUndefinedMethodInspection */
			$categoryUrl = df_mage()->catalog()->categoryHelper()->getCategoryUrlPath(
				$category->getUrlPath(), false, $storeId
			);
			$requestPath = $categoryUrl . '/' . $urlKey;
		} else {
			$requestPath = $urlKey;
		}
		/**
		 * Обратите внимание,
		 * что для поддержки кириллицы в URL
		 * нам нет необходимости уменьшать лимит @see Mage_Catalog_Model_Url::MAX_REQUEST_PATH_LENGTH.
		 *
		 * В БД колонка «request_path» таблицы «core_url_rewrite» имеет тип varchar(255).
		 * Начиная с версии 4.1 в MySQL varchar(255) означает именно 255 символов
		 * (в том числе и UTF-8), а не 255 байтов:
		 * http://stackoverflow.com/a/3739871
		 * http://dev.mysql.com/doc/refman/5.0/en/string-type-overview.html
		 *
		 * «MySQL interprets length specifications in character column definitions in character units.
		 * (Before MySQL 4.1, column lengths were interpreted in bytes.)
		 * This applies to CHAR, VARCHAR, and the TEXT types.»
		 *
		 * Magento требует MySQL версии не ниже 4.1.20:
		 * http://magento.com/resources/system-requirements
		 *
		 * Поэтому можно быть уверенным, что в колонку «request_path» таблицы «core_url_rewrite»
		 * поместится 255 символов, даже если часть из этих символов будет кириллицей.
		 * Значит, и для поля «url_path» объекта $product мы тоже можем оставить прежний лимит
		 * @see Mage_Catalog_Model_Url::MAX_REQUEST_PATH_LENGTH.
		 */
		if (mb_strlen($requestPath) > self::MAX_REQUEST_PATH_LENGTH + self::ALLOWED_REQUEST_PATH_OVERFLOW) {
			$requestPath = mb_substr($requestPath, 0, self::MAX_REQUEST_PATH_LENGTH);
		}
		$this->_rewrite = null;
		/**
		* Check $requestPath should be unique
		*/
		if (isset($this->_rewrites[$idPath])) {

			$this->_rewrite = $this->_rewrites[$idPath];
			/** @noinspection PhpUndefinedMethodInspection */
			$existingRequestPath = $this->_rewrites[$idPath]->getRequestPath();

			if ($existingRequestPath == $requestPath . $suffix) {
				return $existingRequestPath;
			}
			$existingRequestPath = preg_replace('/' . preg_quote($suffix, '/') . '$/', '', $existingRequestPath);
			/**
			* Check if existing request past can be used
			*/
			/** @noinspection PhpUndefinedMethodInspection */
			if ($product->getUrlKey() == '' && !empty($requestPath)
				&& strpos($existingRequestPath, $requestPath) === 0
			) {
				$existingRequestPath = preg_replace(
				'/^' . preg_quote($requestPath, '/') . '/', '', $existingRequestPath
				);
				if (preg_match('#^-([0-9]+)$#i', $existingRequestPath)) {
					/** @noinspection PhpUndefinedMethodInspection */
					return $this->_rewrites[$idPath]->getRequestPath();
				}
			}
			// НАЧАЛО ЗАПЛАТКИ
			/** @var bool $exists_deleteOldTargetPath */
			static $exists_deleteOldTargetPath;
			if (is_null($exists_deleteOldTargetPath)) {
				$exists_deleteOldTargetPath = method_exists($this, '_deleteOldTargetPath');
			}
			if ($exists_deleteOldTargetPath) {
				$fullPath = $requestPath.$suffix;
				if ($this->_deleteOldTargetPath($fullPath, $idPath, $storeId)) {
					return $fullPath;
				}
			}
			// КОНЕЦ ЗАПЛАТКИ
		}
		// Check 2 variants: $requestPath and $requestPath . '-' . $productId
		$validatedPath = $this->getResource()->checkRequestPaths(
			array(
				$requestPath . $suffix
				, $requestPath . '-' . $product->getId() . $suffix
			)
			,$storeId
		);
		if ($validatedPath) {
			return $validatedPath;
		}
		/**
		* Use unique path generator
		*/
		return $this->getUnusedPath($storeId, $requestPath.$suffix, $idPath);
	}

	/**
	 * Get requestPath that was not used yet.
	 * Will try to get unique path by adding -1 -2 etc. between url_key and optional url_suffix
	 * @override
	 * @param int $storeId
	 * @param string $requestPath
	 * @param string $idPath
	 * @return string
	 */
	public function getUnusedPath($storeId, $requestPath, $idPath) {
		if (strpos($idPath, 'product') !== false) {
			$suffix = $this->getProductUrlSuffix($storeId);
		} else {
			$suffix = $this->getCategoryUrlSuffix($storeId);
		}
		if (empty($requestPath)) {
			$requestPath = '-';
		} else if ($suffix === $requestPath) {
			$requestPath = '-' . $suffix;
		}

		/**
		 * Validate maximum length of request path
		 */
		/**
		 * Обратите внимание,
		 * что для поддержки кириллицы в URL
		 * нам нет необходимости уменьшать лимит @see Mage_Catalog_Model_Url::MAX_REQUEST_PATH_LENGTH.
		 *
		 * В БД колонка «request_path» таблицы «core_url_rewrite» имеет тип varchar(255).
		 * Начиная с версии 4.1 в MySQL varchar(255) означает именно 255 символов
		 * (в том числе и UTF-8), а не 255 байтов:
		 * http://stackoverflow.com/a/3739871
		 * http://dev.mysql.com/doc/refman/5.0/en/string-type-overview.html
		 *
		 * «MySQL interprets length specifications in character column definitions in character units.
		 * (Before MySQL 4.1, column lengths were interpreted in bytes.)
		 * This applies to CHAR, VARCHAR, and the TEXT types.»
		 *
		 * Magento требует MySQL версии не ниже 4.1.20:
		 * http://magento.com/resources/system-requirements
		 *
		 * Поэтому можно быть уверенным, что в колонку «request_path» таблицы «core_url_rewrite»
		 * поместится 255 символов, даже если часть из этих символов будет кириллицей.
		 * Значит, и для поля «url_path» объекта $product мы тоже можем оставить прежний лимит
		 * @see Mage_Catalog_Model_Url::MAX_REQUEST_PATH_LENGTH.
		 */
		if (mb_strlen($requestPath) > self::MAX_REQUEST_PATH_LENGTH + self::ALLOWED_REQUEST_PATH_OVERFLOW) {
			$requestPath = mb_substr($requestPath, 0, self::MAX_REQUEST_PATH_LENGTH);
		}
		if (isset($this->_rewrites[$idPath])) {
			$this->_rewrite = $this->_rewrites[$idPath];
			/** @noinspection PhpUndefinedMethodInspection */
			if ($requestPath === $this->_rewrites[$idPath]->getRequestPath()) {
				return $requestPath;
			}
		}
		else {
			$this->_rewrite = null;
		}
		/** @var Varien_Object $rewrite */
		$rewrite = $this->getResource()->getRewriteByRequestPath($requestPath, $storeId);
		if ($rewrite && $rewrite->getId()) {
			if ($idPath === $rewrite->getData('id_path')) {
				$this->_rewrite = $rewrite;
				return $requestPath;
			}
			// match request_url abcdef1234(-12)(.html) pattern

			/** @var string[] $match */
			$match = [];
			/**
			 * Наша заплатка состоит в том,
			 * что мы добавляем в регулярное выражение русские буквы
			 */
			if (
					1
				!==
					preg_match(
							'#^([0-9a-zа-яё/-]+?)(-([0-9]+))?('
						.	preg_quote($suffix)
						.	')?$#ui'
						,$requestPath
						,$match
					)
			) {
				return $this->getUnusedPath($storeId, '-', $idPath);
			}
			/** @noinspection PhpWrongStringConcatenationInspection */
			$requestPath = $match[1].(isset($match[3])?'-'.($match[3]+1):'-1').(isset($match[4])?$match[4]:'');
			return $this->getUnusedPath($storeId, $requestPath, $idPath);
		}
		else {
			return $requestPath;
		}
	}

	/**
	 * Refresh all product rewrites for designated store
	 * @override
	 * @param int $storeId
	 * @return Mage_Catalog_Model_Url
	 */
	public function refreshProductRewrites($storeId) {
		parent::refreshProductRewrites($storeId);
		/**
		 * @todo Для подтоваров (вариантов для настраиваемых товаров)
		 * мы можем сделать перенаправление на настраиваемый товар — это самое разумное
		 */
		$this->getResource()->clearRewritesForInvisibleProducts($storeId);
		if (df_cfgr()->seo()->urls()->needRedirectToCanonicalProductUrl()) {
			$this->makeRedirectsToCanonicalProductUrl($storeId);
		}
		return $this;
	}

	/**
	 * Refresh rewrite urls
	 * @override
	 * @param int $storeId
	 * @return Mage_Catalog_Model_Url
	 */
	public function refreshRewrites($storeId = null) {
		// Делаем что-либо только при наличии параметра $storeId,
		// потому что вызов refreshRewrites без этого параметра
		// всё равно сводится к рекурсивному вызову с этим параметром
		if (!is_null($storeId)) {
			// Это позволяет нам избежать циклических перенаправлений
			// в результате нескольких смен значений параметра
			// «Use Categories Path for Product URLs»
			//
			/** @todo На самом деле, это в корне неправильно, потому что мы лишаемся возможности
			 * сделать перенаправление с прошлых URL.
			 * Нужен другой алгоритм избежания циклических перенаправлений.
			*/
		}
		return parent::refreshRewrites($storeId);
	}

	/**
	 * @override
	 * @return Df_Catalog_Model_Resource_Url
	 */
	protected function _getResource() {return Df_Catalog_Model_Resource_Url::s();}

	/**
	 * 2015-02-06
	 * Обратите внимание на типы параметров!
	 * Они именно такие.
	 * Хотя параметры $product и $category содержат данные товара и товарного раздела,
	 * они не имеют классы @see Df_Catalog_Model_Product и @see Df_Catalog_Model_Category.
	 * Загрузку товаров из базы данных смотрите, например, в методе
	 * @see Mage_Catalog_Model_Resource_Url::_getProducts()
	 * @override
	 * @param Varien_Object $product
	 * @param Varien_Object $category
	 * @return Mage_Catalog_Model_Url
	 */
	protected function _refreshProductRewrite(Varien_Object $product, Varien_Object $category) {
		//Mage::log($product->getData('entity_id') . ': ' . $product->getData('url_path'));
		// Если товар помимо основного товарного раздела
		// входит в некий вспомогательный корневой раздел «!$category->getUrlPath()»,
		// то это вхождение мы пропускаем
		// (потому что стандартный алгоритм в таком случае смотрит, что раздел - корневой, и пытается
		// создать для товара ссылку без товарного раздела, а такая ссылка уже присутствует в базе,
		// и это вызывает нарекание системы контроля целостности)
		//
		/**
		 * @todo
		 * По хорошему, мы можем всё-таки для таких вхождений детать ссылки,
		 * просто это будет посложнее, да и Magento CE этого не делает.
		 */
		if (
			!(
					!$category->getData('url_path')
				&&
					(
							$category->getId()
						!=
							$this->getStores($category->getData('store_id'))->getRootCategoryId()
					)
			)

		) {
			parent::_refreshProductRewrite($product, $category);
		}
		return $this;
	}

	/**
	 * @param array $rewrites
	 * @return array
	 */
	private function filterRewritesByCategoryPresence(array $rewrites) {
		$result = [];
		foreach ($rewrites as $rewrite) {
			/** @var Varien_Object $rewrite */
			if (null !== $rewrite->getData('category_id')) {
				$result[]= $rewrite;
			}
		}
		return $result;
	}

	/**
	 * @param array $rewrites
	 * @return Varien_Object
	 */
	private function findRewriteWithMaxCategoryNestingLevel(array $rewrites) {
		$result = null;
		/** @var Varien_Object $result */

		$this->preloadCategoriesLevelInfo($rewrites);
		$resultNestingLevel = -1;
		/** @var integer $resultNestingLevel */
		foreach ($rewrites as $rewrite) {
			/** @var Varien_Object $rewrite */
			$nestingLevel = $this->getCategoryNestingLevel($rewrite);
			/** @var integer $nestingLevel */
			if ($nestingLevel > $resultNestingLevel) {
				$resultNestingLevel = $nestingLevel;
				$result = $rewrite;
			}
		}
		return $result;
	}
	/** @var array */
	private $_preloadedCategoriesLevelInfo = [];

	/**
	 * @param Varien_Object[] $rewrites
	 * @return int[]
	 */
	private function getCategoryIdsForRewrites(array $rewrites) {
		/** @uses Varien_Object::getData() */
		return df_each($rewrites, 'getData', 'category_id');
	}

	/**
	 * @param Varien_Object $rewrite
	 * @return integer
	 */
	private function getCategoryNestingLevel(Varien_Object $rewrite) {
		return dfa($this->_preloadedCategoriesLevelInfo, $rewrite->getData("category_id"), 0);
	}

	/**
	 * @param array $rewrites
	 * @return int|string
	 */
	private function getIndexOfRewriteWithCategory(array $rewrites) {
		$result = null;
		// Если администратор хочет,
		// чтобы система добавляла в товарный адрес название товарного раздела,
		// то система должна добавлять в товарный адрес ВСЕ разделы, которые стоят в иерархии
		// от корневого раздела до данного товара.
		//	Пример:
		//	«Корневой раздел» → «Женщинам» → «Колготы» → «Колготы FALKE Cotton Touch»
		//	Система должа сделать для этого товара основным следующий адрес:
		//	http://leggeri.com.ua/woman/tights/cotton-touch-111.html
		//
		//	При посещении любого другого адреса данного товара
		//  система должна перенаправлять посетителя на указанный выше адрес.
		//
		//	Сейчас же система перенаправляет посетителей по адресу:
		//	http://leggeri.com.ua/woman/cotton-touch-111.html
		//	что неправильно
		//  (система выбирает адрес наобум, лишь бы в нём содержалась какой-либо товарный раздел)
		//
		//	Видимо, это происходит потому, что товар «Колготы FALKE Cotton Touch»
		//  отнесён администратором как к разделу «Корневой раздел» → «Женщинам» → «Колготы»,
		//  так и к родительскому разделу «Корневой раздел» → «Женщинам».
		//
		//	Поэтому, когда товар относится сразу к нескольким разделам,
		//  надо, видимо, выбирать раздел с наибольшим уровнем вложенности?
		$rewriteWithMaxCategoryNestingLevel =
			$this->findRewriteWithMaxCategoryNestingLevel(
				$this->filterRewritesByCategoryPresence(
					$rewrites
				)
			)
		;
		/** @var Varien_Object $rewriteWithMaxCategoryNestingLevel */

		if ($rewriteWithMaxCategoryNestingLevel) {
			foreach ($rewrites as $index => $rewrite) {
				/** @var Varien_Object $rewrite */
				if (
						$rewrite->getData("url_rewrite_id")
					==
						$rewriteWithMaxCategoryNestingLevel->getData("url_rewrite_id")
				) {
					$result = $index;
					break;
				}
			}
		}
		return $result;
	}

	/**
	 * @param array $rewrites
	 * @return int|string
	 */
	private function getIndexOfRewriteWithoutCategory(array $rewrites) {
		$result = null;
		foreach ($rewrites as $index => $rewrite) {
			/** @var Varien_Object $rewrite */
			if (is_null($rewrite->getData('category_id'))) {
				$result = $index;
				break;
			}
		}
		return $result;
	}

	/**
	 * @param int $storeId
	 * @return Df_Catalog_Model_Url
	 */
	private function makeRedirectsToCanonicalProductUrl($storeId) {
		$lastEntityId = 0;
		// Обработка ведётся порциями по 250 адресов (видимо, чтобы система не перегружалась).
		// $lastEntityId - последний адрес текущей порции
		while (true) {
			$productIds =
				array_keys(
					$this->getResource()->getProductsByStore($storeId, $lastEntityId)
				)
			;
			if (!$productIds) {
				break;
			}
			$rewrites = $this->getResource()->getRewritesForProducts($productIds, $storeId);
			foreach ($productIds as $productId) {
				/** @var int $productId */
				$this->makeRedirectsToCanonicalProductUrlForConcreteProduct(
					df_nta(dfa($rewrites, $productId)), $storeId
				);
			}

			unset($products);
		}
		return $this;
	}

	/**
	 * @param array $rewrites
	 * @param int $storeId
	 * @return Df_Catalog_Model_Url
	 */
	private function makeRedirectsToCanonicalProductUrlForConcreteProduct(array $rewrites, $storeId) {
		// Если перенаправлений меньше 2, то переписывать нечего
		if (1 < count($rewrites)) {
			$indexOfMainRewrite =
				df_cfgr()->seo()->urls()->getAddCategoryToProductUrl($storeId)
				? $this->getIndexOfRewriteWithCategory($rewrites)
				: $this->getIndexOfRewriteWithoutCategory($rewrites)
			;
			if (!is_null($indexOfMainRewrite)) {
				$mainRewrite = dfa($rewrites, $indexOfMainRewrite);
				/** @var Varien_Object $mainRewrite */
				foreach ($rewrites as $index => $rewrite) {
					if ($index !== $indexOfMainRewrite) {
						$this->getResource()->makeRedirect(array(
							'from' => $rewrite, 'to' => $mainRewrite
						));
					}
				}

			}
		}
		return $this;
	}

	/**
	 * @param Varien_Object[] $rewrites
	 * @return void
	 */
	private function preloadCategoriesLevelInfo(array $rewrites) {
		$this->_preloadedCategoriesLevelInfo = $this->getResource()->getCategoriesLevelInfo(
			$this->getCategoryIdsForRewrites($rewrites)
		);
	}
}
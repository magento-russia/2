<?php
class Df_Tax_Setup_3_0_0 extends Df_Core_Setup {
	/**
	 * 2015-04-09
	 * Добавляем налоговые ставки и правила для СНГ
	 * @override
	 * @see Df_Core_Setup::_process()
	 * @used-by Df_Core_Setup::process()
	 * @return void
	 */
	protected function _process() {
		/**
		 * 2015-04-11
		 * Добавляем возможность указывать для налоговой ставки страну,
		 * чтобы в дальнейшем, при использовании налоговых ставок в выпадающих списках,
		 * (например, при назначении налоговой ставки товару)
		 * не показывать администраторам интернет-магазинам одной страны налоговые ставки других стран).
		 */
		df_conn()->addColumn(
			df_table('tax/tax_class'), Df_Tax_Model_Class::P__ISO2, 'varchar(2) null default null'
		);
		self::deleteDemoData();
		self::addRules();
		self::настроитьДляСНГ();
		rm_cache_clean();
		rm_store()->resetConfig();
	}

	/**
	 * @used-by _process()
	 * @return void
	 */
	private static function addRules() {
		foreach (self::$rates as $iso2 => $rates) {
			/** @var string $iso2 */
			/** @var float[] $rates */
			foreach ($rates as $rate) {
				/** @var float $rate */
				df_model_insert('tax/calculation_rule', array(
					/**
					 * Обратите внимание, что мы бы рады были
					 * не включать в заголовок налогового правила название страны
					 * (заголовок налогового правила будет видеть покупатель,
					 * и название страны будет для покупателя ненужным информационным шумом, избыточной информацией),
					 * однако ядро Magento CE требует уникальности заголовков налоговых правил:
					 * @see Mage_Tax_Model_Resource_Calculation_Rule::_initUniqueFields(),
					 * поэтому мы вынуждны включить в заголовок название страны.
					 * Что ж, скроем название страны от покупателя как-то иначе...
					 */
					'code' => self::rateCode($iso2, $rate),
					'priority' => 0,
					'position' => 0,
					'calculate_subtotal' => 0/** @used-by Mage_Tax_Model_Calculation_Rule::saveCalculationData() */,
					'tax_customer_class' => array(self::customerClassId()),
					'tax_product_class' => array(self::productClassId($iso2, $rate)),
					'tax_rate' => array(self::rateId($iso2, $rate))
				));
			}
		}
	}

	/**
	 * @used-by _process()
	 * @return int
	 */
	private static function customerClassId() {
		static $result;
		if (!$result) {
			/** @var int $result */
			$result = df_model_insert('tax/class', array(
				'class_name' => 'обычная'
				,'class_type' => Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER
			))->getId();
			// Привязываем к только что созданному налоговому классу те категории покупателей,
			// которые ранее были привязанны к демо-классам.
			df_conn()->update(
				df_table('customer/customer_group')
				, array('tax_class_id' => $result)
				, array('tax_class_id IN (?)' => self::demoCustomerTaxClassIds())
			);
		}
		return $result;
	}

	/**
	 * @used-by deleteDemoTaxClasses()
	 * @param string $column
	 * @param int[] $ids
	 * @return void
	 */
	private static function deleteCalculation($column, $ids) {
		df_table_delete('tax/tax_calculation', $column, $ids);
	}

	/**
	 * @used-by _process()
	 * @return void
	 */
	private static function deleteDemoData() {
		self::deleteDemoTaxClasses();
		self::deleteDemoRules();
		self::deleteDemoRates();
	}

	/**
	 * Удаляем налоговые правила демо-данных (для России и СНГ они бессмысленны).
	 * @used-by deleteDemoData()
	 * @return void
	 */
	private static function deleteDemoRates() {
		$t_RATE = 'tax/tax_calculation_rate';
		$f_RATE_ID = 'tax_calculation_rate_id';
		/** @var int[] $demoIds */
		$demoIds = df_fetch_col_int($t_RATE, $f_RATE_ID, 'tax_country_id', 'US');
		df_table_delete($t_RATE, $f_RATE_ID, $demoIds);
		self::deleteCalculation($f_RATE_ID, $demoIds);
	}

	/**
	 * Удаляем налоговые правила демо-данных (для России и СНГ они бессмысленны).
	 * @used-by deleteDemoData()
	 * @return void
	 */
	private static function deleteDemoRules() {
		$t_RULE = 'tax/tax_calculation_rule';
		$f_RULE_ID = 'tax_calculation_rule_id';
		/** @var int[] $demoIds */
		$demoIds = df_fetch_col_int($t_RULE, $f_RULE_ID, 'code', array(
			'Retail Customer - Taxable Good - Rate 1',
			'Wholesale Customer - Tax Exempt',
			'Private Sales - Shipping Taxes',
			'Private Sales - Taxable Goods - Rate 2',
			'Not Logged In - Taxable Goods'
		));
		df_table_delete($t_RULE, $f_RULE_ID, $demoIds);
		self::deleteCalculation($f_RULE_ID, $demoIds);
	}

	/**
	 * Удаляем налоговые группы покупателей и товаров, которые были добавлены скриптом демо-данных
	 * (для России и СНГ эти налоговые группы бессмысленны).
	 * @used-by deleteDemoData()
	 * @return void
	 */
	private static function deleteDemoTaxClasses() {
		df_table_delete('tax/tax_class', 'class_id', array_merge(
			self::demoCustomerTaxClassIds(), self::demoProductTaxClassIds()
		));
		self::deleteCalculation('customer_tax_class_id', self::demoCustomerTaxClassIds());
		self::deleteCalculation('product_tax_class_id', self::demoProductTaxClassIds());
		/**
		 * Для товаров, у которых был установлен один из удалённых налоговых классов,
		 * надо сбросить налоговый класс: установить значение «0».
		 * @see Df_Tax_Model_Class_Source_Product::getAllOptions()
		 *
		 * Для категорий покупателей, у которых  был установлен один из удалённых налоговых классов,
		 * мы меняем налоговый класс в методе @see customerClassId()
		 */
		/** @var Df_Catalog_Model_Resource_Product_Collection $result */
		$products = Df_Catalog_Model_Product::c($disableFlat = true);
		$products->addAttributeToSelect('tax_class_id');
		$products->addAttributeToFilter('tax_class_id', array('in' => self::demoProductTaxClassIds()));
		/** @var int[] $ids */
		$ids = $products->getAllIds();
		rm_products_update(array('tax_class_id' => '0'), $ids);
	}

	/**
	 * @used-by customerClassId()
	 * @return int[]
	 */
	private static function demoCustomerTaxClassIds() {
		static $r; return !is_null($r) ? $r : $r = self::taxClassIds(array(
			'Retail Customer', 'Wholesale Customer', 'General'
			, 'Members Only', 'Private Sales', 'Not Logged-in'
		));
	}

	/**
	 * @return int[]
	 */
	private static function demoProductTaxClassIds() {
		static $r; return !is_null($r) ? $r : $r = self::taxClassIds(array(
			'Taxable Goods', 'Shipping', 'Tax Exempt'
		));
	}

	/**
	 * Обновляет значение опции в разделе «Продажи» → «НДС».
	 * @used-by optionС()
	 * @used-by optionForCartAndDocuments()
	 * @used-by настроитьДляСНГ()
	 * @param string $path
	 * @param string|int $value
	 * @param string $scope
	 * @param int $scopeId
	 * @return void
	 */
	private static function option($path, $value, $scope, $scopeId) {
		Df_Core_Model_Config_Data::saveInScope("tax/{$path}", $value, $scope, $scopeId);
	}

	/**
	 * 2015-04-19
	 * Обновляет значение опции в разделе «Продажи» → «НДС» → «Параметры начисления».
	 * @used-by настроитьДляСНГ()
	 * @param string $name
	 * @param string|int $value
	 * @param string $scope
	 * @param int $scopeId
	 * @return void
	 */
	private static function optionС($name, $value, $scope, $scopeId) {
		self::option("calculation/{$name}", $value, $scope, $scopeId);
	}

	/**
	 * 2015-04-20
	 * Обновляет значение опции в разделах:
	 * «Продажи» → «НДС» → «Корзина и оформление заказа»
	 * «Продажи» → «НДС» → «Документы»
	 * Опции в этих разделах идентичны.
	 * @used-by настроитьДляСНГ()
	 * @param string $name
	 * @param string|int $value
	 * @param string $scope
	 * @param int $scopeId
	 * @return void
	 */
	private static function optionForCartAndDocuments($name, $value, $scope, $scopeId) {
		// раздел настроек «Продажи» → «НДС» → «Корзина и оформление заказа»
		self::option("cart_display/{$name}", $value, $scope, $scopeId);
		// раздел настроек «Продажи» → «НДС» → «Документы»
		self::option("sales_display/{$name}", $value, $scope, $scopeId);
	}

	/**
	 * @used-by _process()
	 * @param string $iso2
	 * @param float $rate
	 * @return int
	 */
	private static function productClassId($iso2, $rate) {
		/** @var array(string => int) $cache */
		static $cache;
		/** @var string $code */
		$code = self::rateCode($iso2, $rate);
		if (!isset($cache[$code])) {
			$cache[$code] = df_model_insert('tax/class', array(
				'class_name' => $code
				,'class_type' => Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT
				,Df_Tax_Model_Class::P__ISO2 => $iso2
			))->getId();
		}
		return $cache[$code];
	}

	/**
	 * Этот метод задаёт коды (и заголовки) налоговых ставок и налоговых правил.
	 * 2015-04-15
	 * Обратите внимание, что мы бы рады были
	 * не включать в заголовок налогового правила название страны
	 * (заголовок налогового правила будет видеть покупатель,
	 * и название страны будет для покупателя ненужным информационным шумом, избыточной информацией),
	 * однако ядро Magento CE требует уникальности заголовков налоговых правил:
	 * @see Mage_Tax_Model_Resource_Calculation_Rule::_initUniqueFields(),
	 * поэтому мы вынуждны включить в заголовок название страны.
	 * Что ж, скроем название страны от покупателя как-то иначе...
	 * @used-by addRules()
	 * @used-by _process()
	 * @used-by productClassId()
	 * @used-by rateId()
	 * @param string $iso2
	 * @param float $rate
	 * @return string
	 */
	private static function rateCode($iso2, $rate) {
		return sprintf('%s. НДС %s%%', rm_country_ctn_ru($iso2), rm_number_f($rate));
	}

	/**
	 * @used-by _process()
	 * @param string $iso2
	 * @param float $rate
	 * @return int
	 */
	private static function rateId($iso2, $rate) {
		/** @var array(string => int) $cache */
		static $cache;
		/** @var string $code */
		$code = self::rateCode($iso2, $rate);
		if (!isset($cache[$code])) {
			$cache[$code] = df_model_insert('tax/calculation_rate', array(
				'tax_country_id' => $iso2
				,'tax_region_id' => 0
				,'tax_postcode' => '*'
				,'code' => $code
				,'rate' => $rate
			))->getId();
		}
		return $cache[$code];
	}

	/**
	 * @used-by demoCustomerTaxClassIds()
	 * @used-by demoProductTaxClassIds()
	 * @param string[] $names
	 * @return int[]
	 */
	private static function taxClassIds(array $names) {
		return df_fetch_col_int('tax/tax_class', 'class_id', 'class_name', $names);
	}

	/**
	 * @used-by _process()
	 * @return void
	 */
	private static function настроитьДляСНГ() {
		/** @var array(array(string => string)) $entries */
		$entries = df_fetch_all('core/config_data', 'path', 'general/store_information/merchant_country');
		foreach ($entries as $entry) {
			/** @var array(string => string) $entry */
			/** @var string $iso2 */
			$iso2 = $entry['value'];
			if (isset(self::$rates[$iso2])) {
				/** @var string $scope */
				$scope = $entry['scope'];
				/** @var int $scopeId */
				$scopeId = $entry['scope_id'];
				/**
				 * 2015-04-15
				 * Для услуг доставки грузов в пределах страны
				 * во всех автоматически настраиваемых этим модулем стран СНГ
				 * действует основная ставка НДС этих стран.
				 * (Обратите внимание, ставка НДС на экспортные перевозки из этих стран всегда 0%,
				 * за исключением перевозок внутри Таможенного союза ЕАЭС).
				 */
				/** @var string $taxClassId */
				$taxClassId = self::productClassId($iso2, df_first(self::$rates[$iso2]));
				self::option('classes/shipping_tax_class', $taxClassId, $scope, $scopeId);
				self::optionС('shipping_includes_tax', 1, $scope, $scopeId);
				self::optionС('price_includes_tax', 1, $scope, $scopeId);
				/**
				 * 2015-04-19
				 * «По законодательству какой страны начислять НДС?»
				 * @see Df_Tax_Config_Source_AddressType::toOptionArray()
				 * Обоснование читайте в комментарии к соответствующему настроечному полю в разделе
				 * «Система» → «Настройки» → «Продажи» → «НДС» →
				 * «Параметры начисления» → «По законодательству какой страны начислять НДС?».
				 */
				self::optionС('based_on', 'origin', $scope, $scopeId);
				/**
				 * 2015-04-19
				 * «Налогооблагаемой базой является стоимость заказа
				 * до вычета скидок или после вычета скидок?»
				 * @see Mage_Tax_Model_System_Config_Source_Apply::__construct()
				 * Обоснование читайте в комментарии к соответствующему настроечному полю в разделе
				 * «Система» → «Настройки» → «Продажи» → «НДС» → «Параметры начисления».
				 */
				self::optionС('apply_after_discount', 1, $scope, $scopeId);
				/**
				 * 2015-04-19
				 * «Для расчёта размера скидки использовать стоимость заказа без НДС или с НДС?»
				 * @see Df_Tax_Config_Source_ApplyDiscountOnPrices::toOptionArray()
				 * Обоснование читайте в комментарии к соответствующему настроечному полю в разделе
				 * «Система» → «Настройки» → «Продажи» → «НДС» → «Параметры начисления».
				 */
				self::optionС('discount_tax', 0, $scope, $scopeId);
				/**
				 * 2015-04-19
				 * «Если администратор при редактировании заказа из административной части
				 * установил покупателю нестандартные цены на позиции заказа,
				 * то на основании каких цен расчитывать налогооблагаемую базу:
				 * цен заказа или цен каталога?»
				 * @see Mage_Adminhtml_Model_System_Config_Source_Tax_Apply_On::toOptionArray()
				 * Обоснование читайте в комментарии к соответствующему настроечному полю в разделе
				 * «Система» → «Настройки» → «Продажи» → «НДС» → «Параметры начисления».
				 * Значение «0» — «на основании цен заказа».
				 */
				self::optionС('apply_tax_on', 0, $scope, $scopeId);
				/**
				 * 2015-04-19
				 * «Подгонять ли стоимость товара без НДС под ставку НДС страны покупателя
				 * таким образом, чтобы цена товара с НДС не зависела от страны покупателя?»
				 * Обоснование читайте в комментарии к соответствующему настроечному полю в разделе
				 * «Система» → «Настройки» → «Продажи» → «НДС» → «Параметры начисления».
				 */
				self::optionС('cross_border_trade_enabled', 0, $scope, $scopeId);
				/**
				 * 2015-04-20
				 * Значения блока «Предположительная налоговая принадлежность покупателя»
				 * использутся только когда значением опции «Продажи» → «НДС» →
				 * «Параметры начисления» → «По законодательству какой страны начислять НДС?»
				 * является либо «страна доставки», либо «страна плательщика»,
				 * и указанная страна ещё неизвестна системе
				 * (посетитель интернет-магазина ещё не зарегистрировался либо не авторизовался).
				 * Как уже было сказано в комментарии к этой опции,
				 * для России и СНГ правильным её значением является «страна интернет-магазина»,
				 * поэтому опции блока «Предположительная налоговая принадлежность покупателя»
				 * для России и СНГ опции настраивать не нужно.
				 * Однако на всякий случай настраиваем.
				 */
				self::option('defaults/country', $iso2, $scope, $scopeId);
				self::option('defaults/region', 0, $scope, $scopeId);
				self::option('defaults/postcode', '*', $scope, $scopeId);
				/**
				 * 2015-04-20
				 * «НДС» → «Витрина» → «С НДС или без отображать цены на витрине?»
				 * «2» — «с НДС»
				 * @see Df_Tax_Config_Source_DisplayType::toOptionArray()
				 */
				self::option('display/type', 2, $scope, $scopeId);
				/**
				 * 2015-04-20
				 * «НДС» → «Витрина» → «С НДС или без отображать стоимость доставки?»
				 * «2» — «с НДС»
				 * @see Df_Tax_Config_Source_DisplayType::toOptionArray()
				 */
				self::option('display/shipping', 2, $scope, $scopeId);
				/**
				 * 2015-04-20
				 * «С НДС или без отображать стоимость отдельных позиций заказа?»
				 * «2» — «с НДС»
				 * @see Df_Tax_Config_Source_DisplayType::toOptionArray()
				 */
				self::optionForCartAndDocuments('price', 2, $scope, $scopeId);
				/**
				 * 2015-04-20
				 * «С НДС или без отображать совокупную стоимость позиций заказа?»
				 * «2» — «с НДС»
				 * @see Df_Tax_Config_Source_DisplayType::toOptionArray()
				 */
				self::optionForCartAndDocuments('subtotal', 2, $scope, $scopeId);
				/**
				 * 2015-04-20
				 * «С НДС или без отображать стоимость доставки?»
				 * «2» — «с НДС»
				 * @see Df_Tax_Config_Source_DisplayType::toOptionArray()
				 */
				self::optionForCartAndDocuments('shipping', 2, $scope, $scopeId);
				/**
				 * 2015-04-20
				 * «С НДС или без отображать итоговую стоимость заказа?»
				 * «1» — «с НДС»
				 * Обратите внимание, что в отличие от остальных опций,
				 * значению «с НДС» соответствует код «1», а не «2».
				 * @see Df_Tax_Config_Source_DisplayTypeYesNo::toOptionArray()
				 */
				self::optionForCartAndDocuments('grandtotal', 1, $scope, $scopeId);
				/**
				 * 2015-04-20
				 * «Показывать ли каждый налог отдельной строкой
				 * (помимо показа совокупного размера всех налогов)?»
				 * Обоснование читайте в комментарии к соответствующему настроечному полю.
				 */
				self::optionForCartAndDocuments('full_summary', 0, $scope, $scopeId);
				/**
				 * 2015-04-20
				 * «Выделять ли НДС при нулевой ставке
				 * и показывать ли пометку «без налога (НДС)» при неприменимости НДС?»
				 * Обоснование читайте в комментарии к соответствующему настроечному полю.
				 */
				self::optionForCartAndDocuments('zero_tax', 1, $scope, $scopeId);
			}
		}
	}

	/** @var array(string => array(string => float|float[])) $rates */
	private static $rates = array(
		/**
		 * 2015-04-17
		 * http://www.parliament.am/legislation.php?sel=show&ID=1607&lang=rus#3
		 * Статья 9 документа «О налоге на добавленную стоимость» закона Республики Армения.
		 */
		'AM' => array(20)
		/**
		 * 2015-04-15
		 * Все ставки описаны в статье 102 Налогового кодекса Республики Беларусь:
		 * http://kodeksy-by.com/nalogovyj_kodeks_rb/102.htm
		 *
		 * Уплачивать НДС не нужно в перечисленных статье 94 Налогового кодекса Республики Беларусь:
		 * http://kodeksy-by.com/nalogovyj_kodeks_rb/94.htm
		 * Эти случаи очень многочислены (51 пункт), но посмотрев их мельком,
		 * ничего доступного интернет-магазинам я там не увидел.
		 *
		 * Пониженная ставка 10% применяется для многочисленных групп товаров:
		 * http://www.buhuchet.by/perechen10.aspx
		 *
		 * Ставка 0.5% применяется при импорте из Таможенного союза ЕАЭС в Беларусь драгоценных камней
		 * (пункт 1.5 статьи 102 Налогового кодекса Республики Беларусь).
		 * Интернет-магазинам не нужно.
		 *
		 * Ставки 9,09% и 16.67% применяются при продаже товаров по регулируемым розничным ценам
		 * (пункт 1.4 статьи 102 Налогового кодекса Республики Беларусь).
		 * Думаю, интернет-магазинам это не нужно, а будет нужно — сами добавят.
		 */
		,'BY' => array(20, 10)
		/**
		 * 2015-04-15
		 * Основная ставка: 12%.
		 * Она закреплена в статье 227 Налогового кодекса Киргизии:
		 * http://www.kenesh.kg/Articles/2085-_Nalogovyj_Kodeks_Kyrgyzskoj_Respubliki.aspx
		 *
		 * Ставка 0% применяется при экспорте, международных перевозках
		 * и обслуживанию международных преевозок (статьи 261-263 Налогового кодекса Киргизии):
		 * http://www.kenesh.kg/Articles/2085-_Nalogovyj_Kodeks_Kyrgyzskoj_Respubliki.aspx
		 */
		,'KG' => array(12)
		/**
		 * 2015-04-15
		 * Ставка 0% кроме экспорта применяется только в редких случаях:
		 * http://www.profi-forex.org/wiki/nalog-na-dobavlennuju-stoimost-nds.html
		 * Поэтому в Российскую сборку Magento автоматически добавлять ставку 0% не будем:
		 * ставку 0% при экспорте Российская сборка Magento применяет автоматически,
		 * а если кому-то вдруг она понадобится в других редких случаях — сам добавит.
		 *
		 * На сайте Ernst & Young хорошо описаны правила применения НДС в Казахстане:
		 * http://www.ey.com/KZ/ru/Issues/Business-environment/Doing-business-in-KZ---Value-added-tax
		 */
		,'KZ' => array(12)
		/**
		 * 2015-04-15
		 * Ставка 0% кроме экспорта применяется только в редких случаях.
		 * Поэтому в Российскую сборку Magento автоматически добавлять ставку 0% не будем:
		 * ставку 0% при экспорте Российская сборка Magento применяет автоматически,
		 * а если кому-то вдруг она понадобится в других редких случаях — сам добавит.
		 */
		,'RU' => array(18, 10)
		/**
		 * 2015-04-15
		 * Ставка 0% кроме экспорта применяется только в редких случаях,
		 * перечисленных в статье 195 Налогового кодекса Украины:
		 * http://meget.kiev.ua/kodeks/nalogoviy-kodeks/statya-195/
		 * Поэтому в Российскую сборку Magento автоматически добавлять ставку 0% не будем:
		 * ставку 0% при экспорте Российская сборка Magento применяет автоматически,
		 * а если кому-то вдруг она понадобится в других редких случаях — сам добавит.
		 *
		 * Ставка 7% применяется с 2014 года для лекарств:
		 * http://www.apteka.ua/article/285580
		 *
		 * Основную ставку 20% уже давно собираются снизить до 17%, но всё никак не снизят.
		 */
		,'UA' => array(20, 7)
	);
}
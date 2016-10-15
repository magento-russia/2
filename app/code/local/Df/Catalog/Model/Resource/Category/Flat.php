<?php
class Df_Catalog_Model_Resource_Category_Flat extends Mage_Catalog_Model_Resource_Category_Flat {
	/**
	 * @param string $type
	 * @return string
	 */
	public function getTableByType($type) {
		df_param_string_not_empty($type, 0);
		df_assert_in($type, self::getAttributeTypes());
		return $this->getTable(array('catalog/category', $type));
	}

	/**
	 * Перекрываем данный метод для устранения сбоя
	 * «Undefined offset (index) in Mage/Catalog/Model/Resource/Category/Flat.php»
	 * Про этот сбой можно найти в интернете, например:
	 * http://magento.stackexchange.com/questions/26169/after-removing-category-attributes-i-get-errors-when-reindexing
	 * Также про него есть и на форуме Российской сборки:
	 * http://magento-forum.ru/topic/4577/page__view__findpost__p__17833
	 * http://magento-forum.ru/topic/4721/
	 *
	 * Обратите внимание, что 2014-10-10 я добавил класс
	 * @see Df_Catalog_Model_Processor_DeleteOrphanCategoryAttributesData()
	 * который чинит базу данных, чтобы не происходил описанный выше сбой.
	 *
	 * @override
	 * @param int|int[] $entityIds
	 * @param int $store_id
	 * @return array
	 */
	protected function _getAttributeValues($entityIds, $store_id) {
		$entityIds = df_array($entityIds);
		/** @var array(int => mixed[]) $values */
		$values = array_fill_keys($entityIds, array());
		$attributes = $this->_getAttributes();
		foreach (self::getAttributeTypes() as $type) {
			foreach ($this->_getAttributeTypeValues($type, $entityIds, $store_id) as $row) {
				$this->_currentRow = $row;
				/** @var mixed $entityId */
				$entityId = $this->a($row, 'entity_id');
				/** @var mixed $attributeId */
				$attributeId = $this->a($row, 'attribute_id');
				/** @var mixed $attributeData */
				if (!isset($attributes[$attributeId])) {
					df_error(
						"База данных интернет-магазина повреждена некорректными правками администратора"
						." либо некорректными действиями некоего некачественного стороннего модуля."
						."\nИз базы данных было некорректно удалено некое свойство товарного раздела"
						." с идентификатором «{attributeId}»."
						."\nДля восстановления базы данных интернет-магазина"
						." Вам надо теперь вручную удалить из таблицы {table} базы данных"
						." оставшуюся там информацию данного товарного свойства."
						."\nДля этого надо выполнить следующий запрос SQL:"
						."\nDELETE FROM {table} WHERE '{attributeId}' = attribute_id;"
						."\nПосле выполнения запроса надо удалить кэш"
						." (например, вручную удалить всё содержимое папки var/cache)."
						,array(
							'{attributeId}' => $attributeId
							,'{table}' => $this->getTableByType($type)
						)
					);
				}
				$attributeData = $this->a($attributes, $attributeId, true);
				/** @var $mixed $attributeCode */
				$attributeCode = $this->a($attributeData, 'attribute_code');
				/** @var mixed $value */
				$value = $row['value'];
				$values[$entityId][$attributeCode] = $value;
			}
		}
		return $values;
	}

	/**
	 * @param array(mixed => mixed) $array
	 * @param mixed $key
	 * @param bool $resultMustBeAnArray [optional]
	 * @return mixed
	 * @throws Exception
	 */
	private function a($array, $key, $resultMustBeAnArray = false) {
		if (!isset($array[$key])) {
			df_error(
				"При обработке свойства товарного раздела произошёл сбой."
				."\nВ массиве отсутствует ключ «{key}»."
				."\nМассив:\n{array}"
				."\nПолный пакет данных свойства:\n{row}"
				,array(
					'{key}' => $key
					,'{array}' => print_r($array, true)
					,'{row}' => print_r($this->_currentRow, true)
				)
			);
		}
		/** @var mixed $result */
		$result = $array[$key];
		if ($resultMustBeAnArray && !is_array($result)) {
			df_error(
				"При обработке свойства товарного раздела произошёл сбой."
				."\nЗначение ключа «{key}» массива должно быть массивом."
				."\nМассив:\n{array}"
				."\nПолный пакет данных свойства:\n{row}"
				,array(
					'{key}' => $key
					,'{array}' => print_r($array, true)
					,'{row}' => print_r($this->_currentRow, true)
				)
			);
		}
		return $result;
	}

	/** @var mixed[]|null */
	private $_currentRow = null;

	/** @return string[] */
	public static function getAttributeTypes() {
		return array('varchar', 'int', 'decimal', 'text', 'datetime');
	}

	/**
	 * 2015-02-09
	 * Возвращаем объект-одиночку именно таким способом,
	 * потому что наш класс перекрывает посредством <rewrite> системный класс,
	 * и мы хотим, чтобы вызов @see Mage::getResourceSingleton() ядром Magento
	 * возвращал тот же объект, что и наш метод @see s(),
	 * сохраняя тем самым объект одиночкой (это важно, например, для производительности:
	 * сохраняя объект одиночкой — мы сохраняем его кэш между всеми пользователями объекта).
	 * @return Df_Catalog_Model_Resource_Category_Flat
	 */
	public static function s() {return Mage::getResourceSingleton('catalog/category_flat');}
}
<?php
class Df_Index_Model_Dispatcher {
	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function catalog_product_flat_prepare_columns(Varien_Event_Observer $observer) {
		try {
			if (df_installed() && df_enabled(Df_Core_Feature::TWEAKS_ADMIN)) {
				/** @var int $varcharLength */
				$varcharLength = df_cfg()->index()->getVarcharLength();
				if (0 < $varcharLength) {
					/** @var string $keyColumns */
					$keyColumns = 'columns';
					/** @var string $keyType */
					$keyType = 'type';
					/** @var Varien_Object $columnsDTO */
					$columnsDTO = $observer->getData($keyColumns);
					df_assert($columnsDTO instanceof Varien_Object);
					/** @var array $columns */
					$columns = $columnsDTO->getData($keyColumns);
					df_assert_array($columns);
					/** @var array $notToChange */
					$notToChange = array('name');
					foreach ($columns as $columnName => $columnData) {
						/** @var string $columnName */
						/** @var array $columnData */
						if (!in_array($columnName, $notToChange)) {
							df_assert_array($columnData);
							/** @var string $columnDbType */
							$columnDbType = df_a($columnData, $keyType);
							df_assert_string($columnDbType);
							if ('varchar(255)' === $columnDbType) {
								$columnDbType = rm_sprintf('varchar(%d)', $varcharLength);
							}
							$columnData[$keyType] = $columnDbType;
							$columns[$columnName] = $columnData;
						}
					}
					$columnsDTO->setData($keyColumns, $columns);
					$observer->setData($keyColumns, $columnsDTO);
				}
			}
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}
}
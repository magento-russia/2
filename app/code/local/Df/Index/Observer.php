<?php
class Df_Index_Observer {
	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function catalog_product_flat_prepare_columns(Varien_Event_Observer $o) {
		try {
			if (df_installed()) {
				/** @var int $varcharLength */
				$varcharLength = df_cfg()->index()->getVarcharLength();
				if (0 < $varcharLength) {
					/** @var string $keyColumns */
					$keyColumns = 'columns';
					/** @var string $keyType */
					$keyType = 'type';
					/** @var Varien_Object $columnsDTO */
					$columnsDTO = $o[$keyColumns];
					df_assert($columnsDTO instanceof Varien_Object);
					/** @var array $columns */
					$columns = $columnsDTO[$keyColumns];
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
								$columnDbType = sprintf('varchar(%d)', $varcharLength);
							}
							$columnData[$keyType] = $columnDbType;
							$columns[$columnName] = $columnData;
						}
					}
					$columnsDTO[$keyColumns] = $columns;
					$o[$keyColumns] = $columnsDTO;
				}
			}
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}
}
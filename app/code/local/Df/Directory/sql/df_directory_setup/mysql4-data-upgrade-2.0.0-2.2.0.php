<?php
/**
 * Обратите внимание, что Magento CE версий ранее 1.6 не работает со скриптами из папки data.
 * Российская сборка Magento должна поддерживать Magento CE 1.4 и 1.5,
 * поэтому для установки данных используем устаревший,
 * но всё ещё поддерживаемый всеми современными версиями Magento CE подход:
 * размещаем их в файлах вида mysql4-data-{install или upgrade}-{версия}.php внутри папки sql
 */
/** @var Df_Core_Model_Resource_Setup $this */
$this->startSetup();
Df_Directory_Model_Setup_2_2_0::i($this)->process();
$this->endSetup();
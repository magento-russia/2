<?php
/**
 * 2015-08-03
 * Модуль Df_Tax при установке данных использует справочник стран,
 * который модуль Mage_Directory добавляет скриптом с префиксом «data-»,
 * поэтому и мы должны использовать префикс «data-»,
 * иначе наш скрипт выполнится раньше скрипта Mage_Directory.
 * Также указал зависимость модуля Df_Tax от модуля Mage_Directory:
	<Df_Tax>
		(...)
		<depends>
			(...)
			<Mage_Directory/>
			(...)
		</depends>
	</Df_Tax>
 */
/** @var Df_Core_Model_Resource_Setup $this */
$this->startSetup();
$this->p(__FILE__);
$this->endSetup();

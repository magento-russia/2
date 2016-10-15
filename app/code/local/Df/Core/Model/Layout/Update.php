<?php
class Df_Core_Model_Layout_Update extends Mage_Core_Model_Layout_Update {
	/**
	 * Цель перекрытия —
	 * оповещение разработчика о сбоях
	 * при синтаксическом разборе макетных файлов (layout/*.xml) оформитетельской темы.
	 * @override
	 * @param $area
	 * @param $package
	 * @param $theme
	 * @param null $storeId
	 * @return Mage_Core_Model_Layout_Element|null|SimpleXMLElement
	 */
	public function getFileLayoutUpdatesXml($area, $package, $theme, $storeId = null) {
		/** @var bool $patchNeeded */
		static $patchNeeded;
		if (is_null($patchNeeded)) {
			$patchNeeded = df_magento_version('1.4.2.0', '>=');
		}
		return
			$patchNeeded
			? $this->getFileLayoutUpdatesXml_Df($area, $package, $theme, $storeId)
			: parent::getFileLayoutUpdatesXml($area, $package, $theme, $storeId)
		;
	}

	/**
	 * @throws Exception
	 * @param $area
	 * @param $package
	 * @param $theme
	 * @param null $storeId
	 * @return null|SimpleXMLElement
	 */
	public function getFileLayoutUpdatesXml_Df($area, $package, $theme, $storeId = null) {
		$storeId = is_null($storeId) ? df_store_id() : $storeId;
		/* @var $design Mage_Core_Model_Design_Package */
		$design = Mage::getSingleton('core/design_package');
		$elementClass = $this->getElementClass();
		$updatesRoot = rm_config_node($area, 'layout/updates');
		Mage::dispatchEvent('core_layout_update_updates_get_after', array('updates' => $updatesRoot));
		$updateFiles = array();
		foreach ($updatesRoot->children() as $updateNode) {
			if ($updateNode->file) {
				$module = $updateNode->getAttribute('module');
				if ($module && Mage::getStoreConfigFlag('advanced/modules_disable_output/' . $module, $storeId)) {
					continue;
				}
				$updateFiles[]= (string)$updateNode->file;
			}
		}
		// custom local layout updates file - load always last
		$updateFiles[]= 'local.xml';
		$layoutStr = '';
		foreach ($updateFiles as $file) {
			$filename = $design->getLayoutFilename($file, array(
				'_area'	=> $area,'_package' => $package,'_theme'   => $theme
			));
			if (!is_readable($filename)) {
				continue;
			}
			$fileStr = file_get_contents($filename);
			$fileStr = str_replace($this->_subst['from'], $this->_subst['to'], $fileStr);
			libxml_use_internal_errors(true);
			$fileXml = simplexml_load_string($fileStr, $elementClass);
			if (!$fileXml) {
				$errors = array();
				foreach (libxml_get_errors() as $error) {
					$errors[]= $error->message;
				}
				/** @var string $message */
				$message = df_sprintf(
					"Файл XML «%s» содержит синтаксические ошибки:\n«%s»\n%s"
					,$filename
					,implode("\n\n", $errors)
					,$fileStr
				);
				df_notify($message);
				df_error($message);
			}
			if (!$fileXml instanceof SimpleXMLElement) {
				continue;
			}
			$layoutStr .= $fileXml->innerXml();
		}
		$layoutXml = simplexml_load_string('<layouts>'.$layoutStr.'</layouts>', $elementClass);
		return $layoutXml;
	}
}
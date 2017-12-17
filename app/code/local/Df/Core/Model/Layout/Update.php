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
		if (!isset($patchNeeded)) {
			$patchNeeded = df_magento_version("1.4.2.0", '>=');
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
		if (null === $storeId) {
			$storeId = Mage::app()->getStore()->getId();
		}
		/* @var $design Mage_Core_Model_Design_Package */
		$design = Mage::getSingleton('core/design_package');
		$elementClass = $this->getElementClass();
		$updatesRoot = Mage::app()->getConfig()->getNode($area.'/layout/updates');
		Mage::dispatchEvent('core_layout_update_updates_get_after', array('updates' => $updatesRoot));
		/**
		 * 2017-12-16
		 * Обновил этот блок кодом из Magento >= 1.9:
		 * https://github.com/OpenMage/magento-mirror/blob/1.9.3.7/app/code/core/Mage/Core/Model/Layout/Update.php#L420-L435
		 * Раньше тут стоял код из Magento < 1.9:
		 * https://github.com/OpenMage/magento-mirror/blob/1.8.1.0/app/code/core/Mage/Core/Model/Layout/Update.php#L420-L429
		 * https://github.com/magento-russia/2/blob/2.50.1/app/code/local/Df/Core/Model/Layout/Update.php#L44-L53
		 */
        $updates = $updatesRoot->asArray();
        $themeUpdates = Mage::getSingleton('core/design_config')->getNode("$area/$package/$theme/layout/updates");
        if ($themeUpdates && is_array($themeUpdates->asArray())) {
            //array_values() to ensure that theme-specific layouts don't override, but add to module layouts
            $updates = array_merge($updates, array_values($themeUpdates->asArray()));
        }
        $updateFiles = array();
        foreach ($updates as $updateNode) {
            if (!empty($updateNode['file'])) {
                $module = isset($updateNode['@']['module']) ? $updateNode['@']['module'] : false;
                if ($module && Mage::getStoreConfigFlag('advanced/modules_disable_output/' . $module, $storeId)) {
                    continue;
                }
                $updateFiles[] = $updateNode['file'];
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
				df_notify(
					"Failed loading XML from %s.\n\n%s\n\n%s"
					,$filename
					,implode("\n\n", $errors)
					,$fileStr
				);
				throw new Exception(
					rm_sprintf(
						"Invalid-formatted XML file: %s\n\nErrors:\n%s"
						,$filename
						,implode("\n\n", $errors)
					)
				);
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
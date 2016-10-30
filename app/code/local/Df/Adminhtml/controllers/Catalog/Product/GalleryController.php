<?php
require_once BP . '/app/code/core/Mage/Adminhtml/controllers/Catalog/Product/GalleryController.php';
/**
 * В этом переопределении класса в данный момент нет необходимости,
 * потому что при добавлении картинок к товары
 * мой SEO-модуль всё равно переименует картинку по названию товара
 *
 */
class Df_Adminhtml_Catalog_Product_GalleryController extends Mage_Adminhtml_Catalog_Product_GalleryController {
	/**
	 * @override
	 * @see Mage_Adminhtml_Catalog_Product_GalleryController::uploadAction()
	 * @return void
	 */
	public function uploadAction() {
		/** @var bool $patchNeeded */
		static $patchNeeded;
		if (is_null($patchNeeded)) {
			$patchNeeded =  df_cfgr()->seo()->images()->getUseDescriptiveFileNames();
		}
		$patchNeeded ? $this->uploadActionDf() : parent::uploadAction();
	}

	/** @return void */
	private function uploadActionDf() {
		//We are unable to change Varien_File_Uploader, so in case when DB storage allowed we will do next:
		//We upload image to local Magento FS, then we check whether this file exists in DB
		//If it exists, we are getting unique name from DB, and change them on FS
		//After this we upload file to DB storage
		$result = array();
		try {
			$uploader = new Varien_File_Uploader('image');
			$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
			$uploader->addValidateCallback('catalog_product_image', df_mage()->catalogImageHelper(), 'validateUploadFile');
			$uploader->setAllowRenameFiles(true);
			$uploader->setFilesDispersion(true);
			/** @var string $imageName */
			$imageName = dfa(dfa($_FILES, 'image', array()), 'name', '');
			// Начало заплатки
			/** @var string $imageName */
			/**
			 * Аналогичный алгоритм:
			 * @see Df_Catalog_Model_Product_Attribute_Backend_Media::moveImageFromTmpDf()
			 */
			$imageName = df_ccc('.'
				,df_output()->transliterate(df_strip_ext($imageName))
				,df_file_ext($imageName)
			);
			// Конец заплатки
			$result =
				$uploader->save(
					df_mage()->catalog()->productMediaConfig()->getBaseTmpMediaPath()
					// начало заплатки
					,$imageName
					// конец заплатки
				)
			;
			if (df_magento_version('1.5.0.1')) {
				/** @noinspection PhpUndefinedMethodInspection */
				$result['file'] =
					Mage::helper('core/file_storage_database')->saveUploadedFile($result)
				;
			}
			/**
			 * Workaround for prototype 1.7 methods "isJSON", "evalJSON" on Windows OS
			 */
			$result['tmp_name'] = str_replace(DS, "/", $result['tmp_name']);
			$result['path'] = str_replace(DS, "/", $result['path']);
			$result['url'] = df_mage()->catalog()->productMediaConfig()->getTmpMediaUrl($result['file']);
			$result['file'] = $result['file'] . '.tmp';
			$result['cookie'] = array(
				'name'=> session_name()
				,'value' => $this->_getSession()->getSessionId()
				,'lifetime' => $this->_getSession()->getCookieLifetime()
				,'path'	=> $this->_getSession()->getCookiePath()
				,'domain' => $this->_getSession()->getCookieDomain()
			);
		} catch (Exception $e) {
			$result = array('error' => df_ets($e), 'errorcode' => $e->getCode());
		}
		$this->getResponse()->setBody(df_mage()->coreHelper()->jsonEncode($result));
	}
}
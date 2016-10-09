<?php
// 2016-10-09
class Df_MoySklad_ExportController extends Df_Core_Controller_Admin {
	/**
	 * 2016-10-09
	 * @return void
	 */
	public function indexAction() {
		try {
			$this->loadLayout();
			$this->renderLayout();
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * 2016-10-09
	 * @return void
	 */
	public function productsAction() {
		/** @var string[] $resultA */
		$resultA = array();
		foreach (Df_MoySklad_Product_Exporter::i()->getResult() as $product) {
			/** @var Df_Catalog_Model_Product $product */
			$resultA[]= $product->getName();
		}
		$this->getResponse()->clearHeaders()->setHeader('Content-type','application/json', true);
		$this->getResponse()->setBody(json_encode($resultA));
	}
}
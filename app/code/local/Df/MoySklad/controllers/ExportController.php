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
	 * 2016-10-10
	 * @return void
	 */
	public function productAction() {
		/** @var int $id */
		$id = df_request('id');
		/** @var Df_Catalog_Model_Product $p */
		$p = df_product($id);
		rm_log($p->getName());
		$this->json('OK');
	}

	/**
	 * 2016-10-09
	 * @return void
	 */
	public function productsAction() {
		/** @var array(array(string => string|int)) $resultA */
		$resultA = array();
		foreach (Df_MoySklad_Product_Exporter::i()->getResult() as $p) {
			/** @var Df_Catalog_Model_Product $p */
			$resultA[]= array(
				'id' => $p->getId()
				,'name' => $p->getName()
				,'sku' => $p->getSku()
			);
		}
		$this->json($resultA);
	}

	/**
	 * 2016-10-10
	 * @param mixed $result
	 * @return void
	 */
	private function json($result) {
		$this->getResponse()->clearHeaders()->setHeader('Content-type','application/json', true);
		$this->getResponse()->setBody(json_encode($result));
	}
}
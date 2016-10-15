<?php
// 2016-10-09
class Df_MoySklad_ExportController extends Df_Core_Controller_Admin {
	/**
	 * 2016-10-10
	 * @return void
	 */
	public function deleteAllAction() {
		/** @var Zend_Http_Response $response */
		$response = $this->request(Zend_Http_Client::GET);
		/** @var array(string => mixed) $responseA */
		$responseA = df_json_decode($response->getBody());
		/** @var string[] $ids */
		$ids = array_column($responseA['rows'], 'id');
		foreach ($ids as $id) {
			/** @var string $id */
			$this->request(Zend_Http_Client::DELETE, $id);
		}
		$this->json('OK');
	}

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
		$id = rm_request('id');
		/** @var Df_Catalog_Model_Product $p */
		$p = df_product($id);
		df_log($this->sessionGet('prev'));
		$this->sessionSet('prev', $p->getName());
		/** @var Zend_Http_Response $response */
		$response = $this->request(Zend_Http_Client::POST, '', array(
			'article' => $p->getSku()
			/**
			 * 2016-10-11
			 * Код должен быть строкой, иначе получим сбой:
			 * «Ошибка формата: значение поля 'code' не соответствует типу строка»:
			 * https://support.moysklad.ru/hc/ru/requests/82438
			 *
			 * При этом код должен быть уникальным.
			 */
			,'code' => Df_MoySklad_Settings_Export_Products::s()->codePrefix() . strval($p->getId())
			,'name' => $p->getName()
			,'salePrices' => array(
				'currency' => array(
					'meta' => array(
						'href'=> 'https://online.moysklad.ru/api/remap/1.1/entity/currency/2b50da23-296b-11e6-8a84-bae500000055'
						,'mediaType' => 'application/json'
						,'metadataHref' => 'https://online.moysklad.ru/api/remap/1.1/entity/currency/metadata'
						,'type' => 'currency'

					)
				)
				,'priceType' => 'Цена продажи'
				,'value' => 100
			)
		));
		rm_report(df_fs_name($p->getName()) . '.json', df_json_prettify($response->getBody()));
		$responseA = df_json_decode($response->getBody());
		/** @var bool $isSuccessful */
		$isSuccessful = 200 === $response->getStatus();
		/** @var array(string => mixed) $resultA */
		$resultA = array('success' => $isSuccessful);
		if ($isSuccessful) {
			$resultA += array('id' => $responseA['id']);
		}
		else {
			$resultA += array('errors' => array_column($responseA['errors'], 'error'));
		}
		$this->json($resultA);
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
	private function json($result) {$this->jsonRaw(json_encode($result));}

	/**
	 * 2016-10-10
	 * @param mixed $result
	 * @return void
	 */
	private function jsonRaw($result) {
		$this->getResponse()->clearHeaders()->setHeader('Content-type','application/json', true);
		$this->getResponse()->setBody($result);
	}

	/**
	 * 2016-10-11
	 * @param string $method
	 * @param string $suffix [optional]
	 * @param array[string => mixed] $data [optional]
	 * @return Zend_Http_Response
	 */
	private function request($method, $suffix = '', array $data = array()) {
		/** @var Df_MoySklad_Settings_General $s */
		$s = Df_MoySklad_Settings_General::s();
		/** @var Zend_Http_Client $c */
		$c = new Zend_Http_Client;
		$c->setUri(df_cc_path('https://online.moysklad.ru/api/remap/1.1/entity/product', $suffix));
		$c->setAuth($s->login(), $s->password());
		$c->setHeaders('content-type', 'application/json');
		if ($data) {
			$c->setRawData(json_encode($data));
		}
		return $c->request($method);
	}

	/**
	 * 2016-10-11
	 * @param string $key
	 * @return mixed
	 */
	private function sessionGet($key) {return df_session()->getData($key);}

	/**
	 * 2016-10-11
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	private function sessionSet($key, $value) {df_session()->setData($key, $value);}
}
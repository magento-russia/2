<?php
namespace Df\YandexMarket\Action\Category;
use Df\YandexMarket\Categories as C;
class Suggest extends \Df_Core_Model_Action {
	/**
	 * @override
	 * @see Df_Core_Model_Action::generateResponseBody()
	 * @used-by Df_Core_Model_Action::responseBody()
	 * @return string
	 */
	protected function generateResponseBody() {
		/** @var string $q */
		$q = df_request('query');
		return df_json_encode(['query' => $q, 'suggestions' =>
			df_cache_get_simple(md5($q), function() use ($q) {return
				array_filter(C::paths(), function($path) use($q) {return
					df_contains($path, $q)
				;})
			;})
		]);
	}

	/**
	 * @override
	 * @see Df_Core_Model_Action::contentType()
	 * @used-by Df_Core_Model_Action::getResponseLogFileExtension()
	 * @used-by Df_Core_Model_Action::processPrepare()
	 * @return string
	 */
	protected function contentType() {return 'application/json';}
}
<?php
namespace Df\Ems;
class Request extends \Df\Shipping\Request {
	/**
	 * @param string $name
	 * @param mixed $d [optional]
	 * @return mixed
	 */
	public function p($name, $d = null) {return $this->response()->json("rsp/{$name}", $d);}

	/**
	 * @override
	 * @return void
	 * @throws \Exception
	 */
	protected function responseFailureDetect() {
		if ('ok' !== $this->p('stat')) {
			/** @var string $errorMessage */
			// EMS сообщает о сбоях на английском языке
			df_error(\Df_Ems_Helper_Data::s()->__(
				$this->p('err/msg', df_mage()->shippingHelper()->__(
					'This shipping method is currently unavailable. '
					.'If you would like to ship using this shipping method, please contact us.'
				))
			));
		}
	}

	/**
	 * 2016-10-29
	 * @override
	 * @see \Df\Shipping\Request::uri()
	 * @used-by \Df\Shipping\Request::zuri()
	 * @return string
	 */
	protected function uri() {return 'http://emspost.ru/api/rest';}

	/**
	 * @static
	 * @param array(string => mixed) $p [optional]
	 * @return \Df\Ems\Request
	 */
	public static function i(array $p = []) {return new self([self::P__QUERY => $p]);}
}
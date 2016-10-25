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
	 * @see \Df\Shipping\Request::host()
	 * @return string
	 */
	protected function host() {return 'emspost.ru';}

	/**
	 * @override
	 * @see \Df\Shipping\Request::path()
	 * @return string
	 */
	protected function path() {return '/api/rest';}

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
	 * @static
	 * @param array(string => mixed) $p [optional]
	 * @return \Df\Ems\Request
	 */
	public static function i(array $p = []) {return new self([self::P__PARAMS_QUERY => $p]);}
}
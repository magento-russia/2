<?php
namespace Df\InTime;
/** @method \Df\InTime\Config\Area\Service configS() */
class Collector extends \Df\Shipping\Collector\Ua {
	/**
	 * @override
	 * @see \Df\Shipping\Collector::_collect()
	 * @used-by \Df\Shipping\Collector::collect()
	 * @return void
	 */
	protected function _collect() {
		$this->checkCountryDestIs('UA');
		$this->checkStreetDest();
		//rm_log(\Df\InTime\Api::s()->списокСправочников());
		//rm_log(\Df\InTime\Api::s()->подразделения());
		//rm_log(\Df\InTime\Api::s()->населённыеПункты());
		//rm_log(\Df\InTime\Api::s()->типыОплаты());
		//rm_log(\Df\InTime\Api::s()->типыОплатыПостСервис());
		//rm_log(\Df\InTime\Api::s()->видыПеревозок());
		//rm_log(\Df\InTime\Api::s()->упаковки());
		//rm_log(\Df\InTime\Api::s()->дополнительныеУслуги());
		//rm_log(\Df\InTime\Api::s()->грузы());
		//rm_log(\Df\InTime\Api::s()->грузыЕдиничные());
		//rm_log(\Df\InTime\Api::s()->условияДоставки());
		/** @var array(string => mixed) $terms */
		$terms = Api::s()->условияДоставки(array(
			'Sender' => array(
				'WarehouseSenderCode' => $this->configS()->кодСкладаОтправителя()
				// Почему-то SettlementCode вроде бы на тариф не влияет
				//,'SettlementCode' => '000000155'
				//,'SenderAddress' => 'Новомосковская дорога 5/3'
				// Обязательно для заполнения.
				// Пустое значение не разрешается,
				// однако непустое значение можно указать любое.
				,'PhoneSender' => '067-619-78-30'
			)
			,'Receiver' => array(
				'ReceiverClient' => 'Иванов'
				,'WarehouseReceiverCode' => '0206'
				,'SettlementCode' => '000000021'
				,'ReceiverAddress' => $this->streetDest()
				// Обязательно для заполнения.
				// Пустое значение не разрешается,
				// однако непустое значение можно указать любое.
				,'PhoneReceiver' => '067-617-22-28'
			)
			,'PaymentType' => 'OTP'
			,'DispatchDate' => \Zend_Date::now()->toString('yyyy-MM-dd+03:00')
			,'POD' => array(
				'PodPays' => ''
				,'PodAmount' => 0
				,'ReceiverPODThird' => array(
					'ReceiverPODThird' => ''
					,'WarehouseReceiverPODThird' => ''
					,'PhoneReceiverPODThird' => ''
				)
			)
			,'InsuranceCost' => 200
			,'TransportationType' => '01'
			,'PaymentMethod' => 't'
			,'Packages' => array(
				'PackagesTypeCode' => '00008'
				,'PackageQuantity' => 1
			)
			/**
			 * 2015-04-05
			 * Тег «AdditionalServices» допустимо включать только ели он непуст.
			 * Включение пустого тега «AdditionalServices» приведёт к сбою:
			 * «219 неверный код дополнительной услуги!».
			 *
				,'AdditionalServices' => array(
					'AdditionalServicesCode' => ''
					,'AdditionalServicesParametr' => ''
				)
			 */
			,'Cargo' => array (
				'CargoType' => '00001'
				,'CargoDescription' => 'в коробке'
			)
			,'CargoParams' => array(
				'Quantity' => 1
				,'Weight' => 1
				,'Volume' => 1
			)
			,'CargoItems' => array(
				'CargoItemsCode' => ''
				,'CargoItemsQuantity' => ''
			)
		));
		/** @var \Zend_Date|null $date */
		try {
			$date = new \Zend_Date($terms['DeliveryDate'], 'yyyy-MM-ddTHH:mm:ss');
		}
		catch (\Exception $e) {
			$date = null;
		}
		$this->addRate($terms['Amount'], null, null, $date);
	}
}
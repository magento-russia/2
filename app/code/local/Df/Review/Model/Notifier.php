<?php
class Df_Review_Model_Notifier extends Df_Core_Model {
	/** @return void */
	public function process() {$this->getMailer()->send();}

	/** @return Mage_Core_Model_Email_Template_Mailer */
	private function getMailer() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Core_Model_Email_Template_Mailer $result */
			$result = df_model('core/email_template_mailer');
			$result->addEmailInfo($this->getMailInfo());
			$result->setSender(Mage::getStoreConfig('contacts/email/sender_email_identity'));
			$result->setStoreId(rm_store_id());
			$result->setTemplateId(Mage::getStoreConfig('df_tweaks_admin/reviews/notification_template'));
			$result->setTemplateParams(array(
				'review' => $this->getReview()
				,'product' => $this->getReview()->getProduct()
			));
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Mage_Core_Model_Email_Info */
	private function getMailInfo() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Core_Model_Email_Info $result */
			$result = df_model('core/email_info');
			$result->addTo($this->getRecipientAddress());
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getRecipientAddress() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Mage::getStoreConfig('contacts/email/recipient_email');
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Review_Model_Review */
	private function getReview() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Review_Model_Review::c()->limitLast()->getFirstItem();
			df_assert($this->{__METHOD__} instanceof Df_Review_Model_Review);
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Review_Model_Notifier */
	public static function i() {return new self;}
}
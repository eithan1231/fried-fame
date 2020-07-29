<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\payment\gateway\paypal.php
//
// ======================================


/**
* Gateway for paypal. Just remember, ALL non-generic methods must be called
* statically.
*
* NOTE: IPN Variables can be found below
* https://developer.paypal.com/docs/classic/ipn/integration-guide/IPNandPDTVariables/#buyer-information-variables
*/
class payment_gateway_paypal extends payment_gateway_abstract
{
	// Generic constants.
	const METHOD_NAME = 'paypal';

	// Paypal constants, non-generic.
	const VERIFIED = 'VERIFIED';
	const INVALID = 'INVALID';
	const URL_IPN_CB_SANDBOX = 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr';
	const URL_IPN_CB_PRODUCTION = 'https://ipnpb.paypal.com/cgi-bin/webscr';
	const URL_BUYNOW_SANDBOX = 'https://www.sandbox.paypal.com/cgi-bin/webscr';// The form client submits to (sandbox)
	const URL_BUYNOW_PRODUCTION = 'https://www.paypal.com/cgi-bin/webscr';// The form client submits to

	public function getName()
	{
		return self::METHOD_NAME;
	}

	/**
	* Payment requires processing
	*
	* @param payment_state $state
	*		Payment state
	* @param array $parameters
	*		Further parameters
	*/
	public function process(payment_state $state, array $parameters)
	{
		global $ff_context;
		$plan = $state->getPlan();
		if(!$plan) {
			// Something went wrong. This should NEVER happen.
			$ff_context->getLogger()->error('PayPal-IPN-Process - State provided bad plan.');
			throw new Exception('Provided plan of $state was bad.');
		}

		$coupon = $state->getCoupon();
		if(!$coupon) {
			$coupon = null;
		}

		$affiliate = null;

		$user = $state->getUser();
		if(!$user) {
			// the fuck? should never happen/
			$ff_context->getLogger()->error('PayPal-IPN-Process - State provided bad user.');
			throw new Exception('$state provided bad user');
		}

		$pp_payment_status = strtolower($parameters['payment_status']);

		if(
			isset($parameters['test_ipn']) &&
			$parameters['test_ipn'] == '1' &&
			!ff_isDevelopment()
		) {
			// Sending test IPN's outside of development. We should never get to here,
			// but just in-case.
			$ff_context->getLogger()->error('PayPal-IPN-Process - Test IPN in production.');
			return false;
		}

		switch($pp_payment_status)
		{
			/**
			* Canceled_Reversal: A reversal has been canceled. For example, you won a
			* dispute with the customer, and the funds for the transaction that was
			* reversed have been returned to you.
			*/
			case 'canceled_reversal': {
				$pp_currency = strtolower($parameters['mc_currency']);
				$pp_fee = floatval($parameters['mc_fee']);
				$pp_gross = floatval($parameters['mc_gross']); // with discount applied from pp
				$pp_quantity = intval($parameters['quantity']);

				$state->markCompleted();

				$payment = payment::getPaymentByState($state);
				if(!$payment) {
					// It may not be convenient to get all information about payment from
					// 'custom' field, but it is easiest with a generic payment table. Plus,
					// this should function fine. Let's just hope paypal always sends custom
					// data
					throw new Exception('no payment recorded with state');
				}

				// Currency Check
				if(strtolower($plan->getCurrency()) != $pp_currency) {
					// problem with currency
					$payment->setStatus(payment::STATUS_BAD_INPUT);

					// Send email to user.
					notification::push(
						null,
						'notification-paypal-bad-currency',
						[],
						FF_MONTH,
						'cp_support_new'
					);

					return;
				}

				// Discount amount user will receive.
				$actualCost = $plan->getPrice();
				$discountedCost = $plan->getPrice();

				if($coupon && $plan->getDiscountable()) {
					$coupon->incrementUsageCount();

					// Applying discount amount.
					$discountedCost -= ($plan->getPrice() / 100) * $coupon->getDiscountPercentage();
				}

				// TODO: affiliate.
				if($affiliate) {
					// TODO: This haha
				}

				if($pp_gross >= $discountedCost) {
					// amount paid is equal to or above the discounted price, so everything
					// is okay. Payment was valid.
					$payment->setStatus(payment::STATUS_REVERSED);

					$user->giveSubscription($plan);
				}
				else {
					// provided invalid amount of pennies! haha
					$payment->setStatus(payment::STATUS_BAD_INPUT);
				}

				break;
			}

			/**
			* Completed: The payment has been completed, and the funds have been added
			* successfully to your account balance.
			*/
			case 'completed': {
				$pp_currency = strtolower($parameters['mc_currency']);
				$pp_fee = floatval($parameters['mc_fee']);
				$pp_gross = floatval($parameters['mc_gross']); // with discount applied from pp
				$pp_quantity = intval($parameters['quantity']);

				if($state->hasCompleted()) {
					// Already completed.
					break;
				}
				$state->markCompleted();

				// Currency Check
				if(strtolower($plan->getCurrency()) != $pp_currency) {
					// problem with currency

					$ff_context->getLogger()->error('PayPal-IPN-process - Unmatched Currency');

					// Send email to user.
					notification::push(
						null,
						'notification-paypal-bad-currency',
						[],
						FF_MONTH,
						'cp_support_new'
					);

					payment::logPayment(
						payment::STATUS_BAD_INPUT,
						$user,
						$plan->getCurrency(),
						$discountedCost,
						$pp_fee,
						self::METHOD_NAME,
						$parameters,
						$state,
						$coupon,
						$affiliate
					);

					return;
				}

				// Discount amount user will receive.
				$actualCost = $plan->getPrice();
				$discountedCost = $plan->getPrice();

				if($coupon && $plan->getDiscountable()) {
					// NOTE: Don't check if it's valid. This could fuck the user, as it's
					// not imposible for it to become invalid after user pays.

					$coupon->incrementUsageCount();

					// Applying discount amount.
					$discountedCost -= ($plan->getPrice() / 100) * $coupon->getDiscountPercentage();

					$ff_context->getLogger()->error('PayPal-IPN-process - Discounted');
				}

				// TODO: affiliate.
				if($affiliate) {
					// TODO: This haha
				}

				if($pp_gross >= $discountedCost) {
					// amount paid is equal to or above the discounted price, so everything
					// is okay. Payment was valid.
					payment::logPayment(
						payment::STATUS_SUCCESSFUL,
						$user,
						$plan->getCurrency(),
						$discountedCost,
						$pp_fee,
						self::METHOD_NAME,
						$parameters,
						$state,
						$coupon,
						$affiliate
					);

					$user->giveSubscription($plan, false);

					// Send email to user.
					notification::push(
						null,
						'notification-paypal-new-payment',
						[],
						FF_MONTH,
						// TODO: Route path
						'cp_landing'
					);

					$ff_context->getLogger()->log(
						'PayPal-IPN-process - Payment successful (subscription given, '.
						'notification pushed, and payment logged)'
					);
				}
				else {
					// provided invalid amount of pennies! haha
					payment::logPayment(
						payment::STATUS_BAD_INPUT,
						$user,
						$plan->getCurrency(),
						$discountedCost,
						$pp_fee,
						self::METHOD_NAME,
						$parameters,
						$state,
						$coupon,
						$affiliate
					);

					// Send email to user.
					notification::push(
						null,
						'notification-paypal-bad-amount',
						[],
						FF_MONTH,
						// TODO: Route path
						'cp_landing'
					);

					$ff_context->getLogger()->error(
						'PayPal-IPN-process - Bad Price (possible alteration when '.
						'redirecting to paypal)'
					);
				}

				break;
			}

			/**
			* Refunded: You refunded the payment.
			*/
			case 'refunded': {
				$payment = payment::getPaymentByState($state);
				if(!$payment) {
					// It may not be convenient to get all information about payment from
					// 'custom' field, but it is easiest with a generic payment table. Plus,
					// this should function fine. Let's just hope paypal always sends custom
					// data
					$ff_context->getLogger()->error(
						'PayPal-IPN-Process - No payment linked with state for refund so '.
						'therefore unable to remove subscription.'
					);
					throw new Exception('bad state');
				}
				$payment->setStatus(payment::STATUS_REFUNDED);

				$subject = $payment->getUser();
				if(!$subject) {
					throw new Exception('corrupt payment - bad user');
				}

				$subject->removeSubscription($plan);
				break;
			}

			/**
			* Reversed: A payment was reversed due to a chargeback or other type of
			* reversal. The funds have been removed from your account balance and
			* returned to the buyer. The reason for the reversal is specified in the
			* ReasonCode element.
			*/
			case 'reversed': {
				$payment = payment::getPaymentByState($state);
				if(!$payment) {
					// It may not be convenient to get all information about payment from
					// 'custom' field, but it is easiest with a generic payment table. Plus,
					// this should function fine. Let's just hope paypal always sends custom
					// data
					$ff_context->getLogger()->error(
						'PayPal-IPN-Process - No payment linked with state for reversal so '.
						'therefore unable to remove subscription.'
					);
					throw new Exception('bad state');
				}
				$payment->setStatus(payment::STATUS_CHARGEBACKED);

				$subject = $payment->getUser();
				if(!$subject) {
					throw new Exception('corrupt payment - bad user');
				}

				$subject->removeSubscription($plan);
				break;
			}

			/**
			* Processed: A payment has been accepted.
			*/
			case 'processed': {
				$ff_context->getLogger()->error('PayPal-IPN-Process - Unimplemented payment_status '. $pp_payment_status);
				// NOTE: This is a payout feature according to:
				// https://stackoverflow.com/a/14117376
				break;
			}

			/**
			* Voided: This authorization has been voided.
			*/
			case 'voided': {
				$ff_context->getLogger()->error('PayPal-IPN-Process - Unimplemented payment_status '. $pp_payment_status);
				// Not sure :\
				break;
			}

			/**
			* Created: A German ELV payment is made using Express Checkout.
			*/
			case 'created': {
				$ff_context->getLogger()->error('PayPal-IPN-Process - Unimplemented payment_status '. $pp_payment_status);
				// Dont know what this means. Doubt it is important.
				break;
			}

			/**
			* Denied: The payment was denied. This happens only if the payment was
			* previously pending because of one of the reasons listed for the
			* pending_reason variable or the Fraud_Management_Filters_x variable.
			*/
			case 'denied': {
				$ff_context->getLogger()->error('PayPal-IPN-Process - Unimplemented payment_status '. $pp_payment_status);
				// Basically a response to pending. We are not implementing pending,
				// so this doesnt matter too much.
				break;
			}

			/**
			* Expired: This authorization has expired and cannot be captured.
			*/
			case 'expired': {
				$ff_context->getLogger()->error('PayPal-IPN-Process - Unimplemented payment_status '. $pp_payment_status);
				// Not sure what this is.
				break;
			}

			/**
			* Failed: The payment has failed. This happens only if the payment was
			* made from your customer's bank account.
			*/
			case 'failed': {
				$ff_context->getLogger()->error('PayPal-IPN-Process - Unimplemented payment_status '. $pp_payment_status);
				// Don't think it matters too much if this is implemented.
				break;
			}

			/**
			* Pending: The payment is pending. See pending_reason for more information.
			*/
			case 'pending': {
				$ff_context->getLogger()->error('PayPal-IPN-Process - Unimplemented payment_status '. $pp_payment_status);
				// We do not need to imlement this. Whether it is meaningless or not, is
				// meaningless. We could maybe send a notification, but meh.
				break;
			}

			default: {
				// OKIE!
				break;
			}
		}
	}

	public static function getBuyNow()
	{
		return (ff_isDevelopment()
			? self::URL_BUYNOW_SANDBOX
			: self::URL_BUYNOW_PRODUCTION
		);
	}

	public static function verifyIPN(request &$request)
	{
		global $ff_config;


		// Checking paypal is enabled. It should be, but who knows?
		if(!$ff_config->get('paypal-enabled')) {
			// Paypal has been disabled.
			return false;
		}

		// Getting and verifying submitted data
		$submittedData = $request->getAllFields(request::METHOD_POST);
		if(!$submittedData) {
			// Bad data.
			return false;
		}

		// Form Validation (removing non-strings. All should be strings)
		foreach ($submittedData as $key => $value) {
			if(!is_string($value)) {
				unset($submittedData[$key]);
			}
		}

		// Building resubmit data.
		$formData = http_build_query(array_merge([
			'cmd' => '_notify-validate'
		], $submittedData));


		// Getting the url
		$verifUrl = (ff_isDevelopment() ? self::URL_IPN_CB_SANDBOX : self::URL_IPN_CB_PRODUCTION);


		// Building curl request
		$ch = curl_init($verifUrl);
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $formData);
		curl_setopt($ch, CURLOPT_SSLVERSION, 6);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			// Useragent can stay generic.
			'User-Agent: PHP-IPN-Verification-Script',
			'Connection: Close',
		]);


		// Sending request
		$res = curl_exec($ch);
		if(!$res) {
			$ff_context->getLogger()->error('PayPal-IPN-verifyIPN - cURL error ');
			curl_close($ch);
			return false;
		}


		// Checking response
		$info = curl_getinfo($ch);
		if($info['http_code'] != 200) {
			$ff_context->getLogger()->error('PayPal-IPN-verifyIPN - PayPal responded with '. $info['http_code']);
			curl_close($ch);
			return false;
		}


		// All appears well, respond whether or not it compares to verify string
		curl_close($ch);
		return $res == self::VERIFIED;
	}
}

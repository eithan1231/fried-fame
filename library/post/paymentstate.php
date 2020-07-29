<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\post\paymentstate.php
//
// ======================================


class post_paymentstate extends post_abstract
{
	public function getName()
	{
		return ff_filename(__FILE__);
	}

	/**
	* Runs a post route
	*/
	public function run(request &$request, response &$response)
	{
		global $ff_context, $ff_router;

		if(!parent::validateAuthenticated()) {
			return true;
		}

		$session = $ff_context->getSession();
		$user = $session->getActiveLinkUser();
		if(!$user) {
			// No active link with session, so there's not much we can do.
			$response->json([
				'cmd' => 'permission-denied'
			]);
			return true;
		}

		$token = $request->get('token');
		if(!$token) {
			$response->json([
				'cmd' => 'missing-parameters'
			]);
			return true;
		}

		$state = payment_state::getStateByToken($token);
		if(!$state) {
			$response->json([
				'cmd' => 'bad-state'
			]);
			return true;
		}

		if($state->getUserId() != $user->getId()) {
			// Belongs to another user.
			$response->json([
				'cmd' => 'permission-denied'
			]);
			return true;
		}

		if($state->hasCompleted()) {
			$payment = payment::getPaymentByState($state);
			if($payment) {
				$response->json([
					'id' => $payment->getId(),
					'cmd' => 'completed'
				]);
				return true;
			}
			else {
				$response->json([
					'cmd' => 'bad-state'
				]);
				return true;
			}
		}
		else {
			$response->json([
				'cmd' => 'processing'
			]);
			return true;
		}

		return true;
	}
}

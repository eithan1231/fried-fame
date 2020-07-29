<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\captcha\recaptcha2.php
//
// ======================================


class captcha_recaptcha2 implements captcha_interface
{
	private $clientAPI = 'https://www.google.com/recaptcha/api.js';
	private $serverAPI = 'https://www.google.com/recaptcha/api/siteverify';

	/**
	* Renders the script include HTML elements.
	*/
	public function renderScriptElements()
	{
		global $ff_context;

		?>
		<script src="<?= ff_esc($this->clientAPI) ?>?hl=<?= ff_esc($ff_context->getLanguage()->languageCode()) ?>" async defer></script>
		<?php
	}

	/**
	* Renders the form elements
	*/
	public function renderFormElement($parameters = [])
	{
		global $ff_config;

		$containerAttributes = '';
		if(isset($parameters['recaptcha2']['attributes'])) {
			foreach($parameters['recaptcha2']['attributes'] as $key => $value) {
				$containerAttributes .= "{$key}=\"". ff_esc($value) ."\" ";
			}
		}

		?>
		<div <?= $containerAttributes ?>>
			<div class="g-recaptcha" data-sitekey="<?= ff_esc($ff_config->get('recaptcha2-site-key')) ?>" style="margin-bottom: 7px;"></div>
		</div>
		<?php
	}

	/**
	* Gets the parameter name. Like,
	*/
	public function getParmaterName()
	{
		return 'g-recaptcha-response';
	}

	/**
	* Validates a parameter name to make sure the user input wasnt faked.
	*
	* @param string $parameterData
	*		The data of the parameter we want to verify
	*/
	public function validate(string $parameterData)
	{
		global $ff_config;
		$ret = null;

		$postData = http_build_query([
			'secret' => $ff_config->get('recaptcha2-secret-key'),
			'response' => strval($parameterData)
		]);

		$urlParsed = parse_url($this->serverAPI);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->serverAPI);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-type: application/x-www-form-urlencoded',
			'Content-length: '. strlen($postData),
			"Host: {$urlParsed['host']}",
			'User-agent: '. ff_buildUserAgent(),
		]);

		if($response = curl_exec($ch)) {
			$responseCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
			if($responseCode === 200) {
				$responseParsed = json_decode($response);
				if($responseParsed->success) {
					$ret = ff_return(true);
				}
				else {
					$ret = ff_return(false);
				}
			}
			else {
				$ret = ff_return(false);
			}
		}
		else {
			$ret = ff_return(false);
		}

		curl_close($ch);

		return $ret;
	}
}

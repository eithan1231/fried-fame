<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\email\builder.php
//
// ======================================


class email_builder
{
	const CID_HEADER_IMG = 'xehw564s56v';

	private $_html = null;
	private $_text = null;
	private $_subject = null;
	private $_recipients = null;
	private $_isbcc = true;
	private $_priority = 'normal';
	private $_isAutomated = true;

	/**
	* Sets subjct.
	*/
	public function setSubject($subject)
	{
		$this->_subject = $subject;
	}

	/**
	* Sets HTML for the body of the templated HTML function.
	*/
	public function setHtml($html)
	{
		$this->_html = $this->buildHtml($html);
	}

	/**
	* Sets raw text boxy
	*/
	public function setBody($text)
	{
		$this->_text = $text;
	}

	/**
	* Adds a recipient.
	*/
	public function setRecipient($address)
	{
		$this->_recipients[] = $address;
	}

	/**
	* Makes recipient list BCC (Blind-carbon-copy)
	*/
	public function makeBCC()
	{
		$this->_isbcc = true;
	}

	public function makeCC()
	{
		$this->_isbcc = false;
	}

	public function setPriority($priority)
	{
		$this->_priority = $priority;
	}

	private function getParameterOptions()
	{
		global $ff_router;

		$params = [
			'attachments' => [],
			'priority' => $this->_priority
		];

		if($this->_isAutomated) {
			$params['headers'] = [
				'Auto-Submitted' => 'auto-replied'
			];
		}

		if($this->_html) {
			$params['attachments'][] = [
				'filename' => 'header.png',
				'cid' => self::CID_HEADER_IMG,
				'contentType' => 'image/png',
				'contentDisposition' => 'inline',

				'path' => $ff_router->getPath('asset', [
					'asset' => 'email-header',
					'extension' => 'png'
				], [
					'allowForceParam' => false,
					'mode' => 'host'
				]),
			];
			$params['html'] = $this->_html;
		}

		if($this->_text) {
			$params['text'] = $this->_text;
		}

		if($this->_recipients) {
			$params['to'] = implode(', ', $this->_recipients);

			if(count($this->_recipients) > 1) {
				if($this->_isbcc) {
					$params['bcc'] = $params['to'];
				}
				else {
					$params['cc'] = $params['to'];
				}
			}
		}

		if($this->_subject) {
			$params['subject'] = $this->_subject;
		}

		return $params;
	}

	public function send()
	{
		$rpc = ffrpc::getRpc(ffrpc::TYPE_TASK);
		return $rpc->do('email', $this->getParameterOptions());
	}

	private function buildHtml($bodyhtml)
	{
		global $ff_router, $ff_config, $ff_context;
		$language = $ff_context->getLanguage();
		$backgroundColor = 'white';//'#f5f5f5';

		ob_start();
		?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
		<title></title>
		<style>
			* {
				font-family: sans-serif;
			}

			a {
				text-decoration: none;
				color: #007bff;
			}

			.quote	{
					border-radius: 3px;
					position: relative;
					font-style: italic;
					text-align: center;
					padding: 1rem 1.2rem;
					width: 80%;
					color: #4a4a4a;
					margin: 1rem auto 2rem;
					color: #4a4a4a;
					background: #E8E8E8;
			}
		</style>
	</head>
	<body style="background-color:<?= ff_esc($backgroundColor) ?>;">
		<div style="display:table;margin:auto;min-wdith:500px;padding-bottom: 10px;">
			<!-- Header image -->
			<div>
				<img width="500px" src="cid:<?= ff_esc(self::CID_HEADER_IMG) ?>">
			</div>

			<!-- Primary body -->
			<div style="padding:5px;padding-top:15px;padding-bottom:15px;max-width:500px">
				<?= $bodyhtml ?>
			</div>

			<!-- Footer -->
			<div style="text-align:center;">
				<hr>
				<div>
					&copy; <?= date('Y', FF_TIME) ?> Copyright <a href="<?= $ff_router->getPath('landing', [], [
						'allowForceParam' => false,
						'mode' => 'host'
					]) ?>"><?= $ff_config->get('project-name') ?></a>
				</div>
			</div>
		</div>
	</body>
</html>
		<?php

		$ret = ob_get_contents();
		ob_end_clean();
		return $ret;
	}
}

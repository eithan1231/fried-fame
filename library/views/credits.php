<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\credits.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

$language = $ff_context->getLanguage();

$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc(ff_buildTitle('title-credits', $language)) ?></title>

		<?php snippets_cssincl::render() ?>
		<?php snippets_htmlheader::render([
			'description' => 'meta-og-credits-description',
		]) ?>
		<?php snippets_altlang::render() ?>
		<?php snippets_opengraph::render([
			'title' => 'meta-og-credits-title',
			'description' => 'meta-og-credits-description',
		]) ?>
	</head>
	<body>
		<?php snippets_navbar::render() ?>

		<div lang="en" dir="ltr" class="container">
			<h1><?= $language->getPhrase('footer-information-credits') ?></h1>

			<ul>
				<li>
					<strong>Bootswatch</strong> - <a target="_blank" href="https://bootswatch.com/">https://bootswatch.com/</a> A bootstrap compatible CSS framework
				</li>

				<li>
					<strong>HTML Purifier</strong> - <a target="_blank" href="http://htmlpurifier.org/">http://htmlpurifier.org/</a>
				</li>

				<li>
					<strong>PhpUserAgent</strong> - <a target="_blank" href="https://github.com/donatj/PhpUserAgent">donatj</a>
				</li>

				<li>
					<strong>Gaming Picture</strong> - <a target="_blank" href="https://www.flaticon.com/free-icon/gaming_771298">https://www.flaticon.com/free-icon/gaming_771298</a>
				</li>

				<li>
					<strong>Security Image</strong> - <a target="_blank" href="https://www.flaticon.com/free-icon/login_166970">https://www.flaticon.com/free-icon/login_1669703</a>
				</li>

				<li>
					<strong>Support Image</strong> - <a target="_blank" href="https://www.flaticon.com/free-icon/social-care_921305">https://www.flaticon.com/free-icon/social-care_921305</a>
				</li>

				<li>
					<strong>OpenVPN</strong> - <a target="_blank" href="https://openvpn.net/">https://openvpn.net/</a>
				</li>

				<li>
					<strong>Nodemailer</strong> - <a target="_blank" href="https://www.nodemailer.com/">https://www.nodemailer.com/</a> Nodemailer is a module for Node.js applications to allow easy as cake email sending.
				</li>

				<li>
					<strong>Pell (Heavily Modified)</strong> - <a target="_blank" href="https://github.com/jaredreich/pell">https://github.com/jaredreich/pell</a> The simplest and smallest WYSIWYG text editor for web, with no dependencies
				</li>
			</ul>
		</div>

		<?php snippets_footer::render() ?>
		<?php snippets_scriptincl::render() ?>
	</body>
</html>
<?php
$ff_response->stopOutputBuffer();

// Unsetting global variables defined in this class.
unset($language);

?>

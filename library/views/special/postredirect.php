<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\special\postredirect.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');
$language = $ff_context->getLanguage();

$hasMessage = (isset($GLOBALS['ff_redirectmsg']) && strlen($GLOBALS['ff_redirectmsg']) > 0);

$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>">
	<head>
		<meta charset="utf-8">
		<title><?= $language->getPhrase('postredir-title') ?></title>

		<?php snippets_cssincl::render() ?>

		<?php snippets_htmlheader::render() ?>
		<script type="text/javascript">
			setTimeout(function() {
				window.location = <?= json_encode($GLOBALS['ff_redirecto']) ?>;
			}, <?= $hasMessage ? 1500 : 1000 ?>);
		</script>
	</head>
	<body>
		<h1 class="text-center"><?= $language->getPhrase('postredir-header') ?></h1>

		<?php if ($hasMessage): ?>
			<p class="text-center">
				<?= $language->getPhrase($GLOBALS['ff_redirectmsg']) ?>
			</p>
			<hr>
		<?php endif; ?>

		<p class="text-center">
			<?= $language->getPhrase('postredir-body', [
				'redirect' => $GLOBALS['ff_redirecto']
			]) ?>
		</p>
		<?php snippets_scriptincl::render() ?>
	</body>
</html>
<?php
$ff_response->stopOutputBuffer();

// Unsetting global variables defined in this class.
unset($language);

?>

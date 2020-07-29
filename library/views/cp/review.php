<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\cp\review.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

$language = $ff_context->getLanguage();

$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>" style="--sidebar-width: <?= $ff_config->get('sidebar-width') ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc(ff_buildTitle('title-review', $language)) ?></title>

		<?php snippets_cssincl::render() ?>
		<?php snippets_htmlheader::render() ?>
		<?php snippets_altlang::render() ?>
	</head>
	<body>
		<?php snippets_cp_sidebar::render() ?>

		<div id="sidebar-body">
			<?php snippets_navbar::render(['cp_landing' => 1, 'sidebar' => 1]) ?>

			<div class="container" style="padding-top: 10px">
				<?php snippets_invalidemail::render() ?>

				<div class="card card-info">
					<div class="card-header">
						<h4><?= $language->getPhrase('review-title') ?></h4>
					</div>
					<div class="card-body">
						<form action="<?= $ff_router->getPath('post', [
							'security_token' => $ff_context->getSession()->getSecurityToken(),
							'action' => 'newreview'
						]) ?>" method="post">
							<?php snippets_alert::render(['phrase' => strval($ff_request->get('phrase', request::METHOD_GET))]) ?>

							<!-- TODO: Star slider. -->
							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" style="width: 145px"><?= $language->getPhrase('oneword-stars') ?></span>
								</div>
								<select class="form-control" name="stars">
									<option value="1">1</option>
									<option value="2">2</option>
									<option value="3">3</option>
									<option value="4">4</option>
									<option value="5" selected>5</option>
								</select>
							</div>

							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" style="width: 145px"><?= $language->getPhrase('oneword-body') ?></span>
								</div>
								<textarea class="form-control" name="body" rows="3" cols="80" maxlength="512"></textarea>
							</div>

							<button type="submit" class="btn btn-primary"><?= $language->getPhrase('oneword-submit') ?></button>

							<p class="float-right"><?= $language->getPhrase('other-subject-to-review') ?></p>
						</form>
					</div>
				</div>
			</div>
		</div>

		<?php snippets_scriptincl::render() ?>
		<script type="text/javascript">
		document.addEventListener('load', () => {

		});
		</script>
	</body>
</html>
<?php
$ff_response->stopOutputBuffer();

// Unsetting global variables defined in this class.
unset($language, $security_token);

?>

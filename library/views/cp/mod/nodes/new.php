<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\cp\mod\nodes\new.php
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
		<title><?= ff_esc(ff_buildTitle('title-mod-node-new', $language)) ?></title>

		<?php snippets_cssincl::render() ?>
		<?php snippets_htmlheader::render() ?>
		<?php snippets_altlang::render() ?>
	</head>
	<body>
		<?php snippets_cp_sidebar::render() ?>

		<div id="sidebar-body">
			<?php snippets_navbar::render(['cp_landing' => 1, 'sidebar' => 1]) ?>

			<div class="container" stlye="padding-top: 10px;">
				<div class="card card-info">
					<div class="card-header">
						<h4><?= ff_esc($language->getPhrase('misc-new-node')) ?></h4>
					</div>
					<div class="card-body">
						<form action="<?= $ff_router->getPath('post', [
							'security_token' => $ff_context->getSession()->getSecurityToken(),
							'action' => 'newnode'
						]) ?>" method="post">
							<?php snippets_alert::render(['phrase' => strval($ff_request->get('phrase', request::METHOD_GET))]) ?>

							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" style="width: 135px"><?= ff_esc($language->getPhrase('oneword-country')) ?></span>
								</div>
								<input name="country" type="text" maxlength="2" class="form-control" placeholder="<?= ff_esc($language->getPhrase('misc-country-iso-2')) ?>" autofocus>
							</div>

							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" style="width: 135px"><?= ff_esc($language->getPhrase('oneword-city')) ?></span>
								</div>
								<input name="city" type="text" class="form-control" placeholder="<?= ff_esc($language->getPhrase('oneword-city')) ?>">
							</div>

							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" style="width: 135px"><?= ff_esc($language->getPhrase('oneword-ip')) ?></span>
								</div>
								<input name="ip" type="text" class="form-control" placeholder="<?= ff_esc($language->getPhrase('oneword-ip')) ?>">
							</div>

							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" style="width: 135px"><?= ff_esc($language->getPhrase('oneword-hostname')) ?></span>
								</div>
								<input name="hostname" type="text" class="form-control" placeholder="<?= ff_esc($language->getPhrase('oneword-hostname')) ?>">
							</div>

							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" style="width: 135px"><?= ff_esc($language->getPhrase('oneword-max-load')) ?></span>
								</div>
								<input name="maximum_load" type="number" class="form-control" placeholder="<?= ff_esc($language->getPhrase('oneword-max-load')) ?>" value="128">
							</div>

							<hr>

							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" style="width: 170px"><?= ff_esc($language->getPhrase('other-enable-openvpn')) ?></span>
								</div>
								<select name="ovpn_enable" class="form-control">
									<option value="1"><?= $language->getPhrase('oneword-true') ?></option>
									<option value="0"><?= $language->getPhrase('oneword-false') ?></option>
								</select>
							</div>

							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" style="width: 170px"><?= ff_esc($language->getPhrase('oneword-protocol')) ?></span>
								</div>
								<select name="ovpn_protocol" class="form-control">
									<option value="tcp">tcp</option>
									<option value="udp">udp</option>
								</select>
							</div>

							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" style="width: 170px"><?= ff_esc($language->getPhrase('oneword-openvpn-port')) ?></span>
								</div>
								<input type="number" class="form-control" name="ovpn_port" placeholder="<?= ff_esc($language->getPhrase('oneword-openvpn-port')) ?>" value="1194">
							</div>

							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" style="width: 170px"><?= ff_esc($language->getPhrase('oneword-openvpn-auth')) ?></span>
								</div>
								<input class="form-control" name="ovpn_auth" placeholder="<?= ff_esc($language->getPhrase('oneword-openvpn-auth')) ?>" value="SHA256">
							</div>

							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" style="width: 170px"><?= ff_esc($language->getPhrase('oneword-openvpn-cipher')) ?></span>
								</div>
								<input class="form-control" name="ovpn_cipher" placeholder="<?= ff_esc($language->getPhrase('oneword-openvpn-cipher')) ?>" value="AES-128-GCM">
							</div>

							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" style="width: 170px"><?= ff_esc($language->getPhrase('oneword-openvpn-tls-cipher')) ?></span>
								</div>
								<input class="form-control" name="ovpn_tls_cipher" placeholder="<?= ff_esc($language->getPhrase('oneword-openvpn-tls-cipher')) ?>">
							</div>

							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" style="width: 170px"><?= ff_esc($language->getPhrase('oneword-openvpn-compression')) ?></span>
								</div>
								<input class="form-control" name="ovpn_compression" placeholder="<?= ff_esc($language->getPhrase('oneword-openvpn-compression')) ?>">
							</div>

							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" style="width: 170px"><?= ff_esc($language->getPhrase('oneword-openvpn-ca')) ?></span>
								</div>
								<textarea class="form-control" name="ovpn_ca" rows="4" placeholder="<?= ff_esc($language->getPhrase('oneword-openvpn-ca')) ?>"></textarea>
							</div>

							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" style="width: 170px"><?= ff_esc($language->getPhrase('oneword-openvpn-cert')) ?></span>
								</div>
								<textarea class="form-control" name="ovpn_cert" rows="4" placeholder="<?= ff_esc($language->getPhrase('oneword-openvpn-cert')) ?>"></textarea>
							</div>

							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" style="width: 170px"><?= ff_esc($language->getPhrase('oneword-openvpn-key')) ?></span>
								</div>
								<textarea class="form-control" name="ovpn_key" rows="4" placeholder="<?= ff_esc($language->getPhrase('oneword-openvpn-key')) ?>"></textarea>
							</div>

							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" style="width: 170px"><?= ff_esc($language->getPhrase('oneword-openvpn-tls-auth')) ?></span>
								</div>
								<textarea class="form-control" name="ovpn_tls_auth" rows="4" placeholder="<?= ff_esc($language->getPhrase('other-openvpn-tls-auth')) ?>"></textarea>
							</div>

							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" style="width: 170px"><?= ff_esc($language->getPhrase('oneword-openvpn-tls-crypt')) ?></span>
								</div>
								<textarea class="form-control" name="ovpn_tls_crypt" rows="4" placeholder="<?= ff_esc($language->getPhrase('other-openvpn-tls-crypt')) ?>"></textarea>
							</div>

							<button type="submit" class="btn btn-primary"><?= $language->getPhrase('oneword-submit') ?></button>
						</form>
					</div>
			</div>
			</div>
		</div>
		<?php snippets_scriptincl::render(['include' => ['pell']]) ?>
		<script type="text/javascript">
			window.addEventListener('load', () => {
				let internalBody = document.getElementById('sv-internal-body');
				ff_pell.init({
					element: document.getElementById('sv-body-pell'),
					onChange: content => internalBody.value = content
				});
			});
		</script>
	</body>
</html>
<?php
$ff_response->stopOutputBuffer();
unset($language);
?>

<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\cp\install\linux.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

$language = $ff_context->getLanguage();

$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="en" dir="ltr" style="--sidebar-width: <?= $ff_config->get('sidebar-width') ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc(ff_buildTitle('title-linux-install', $language)) ?></title>

		<?php snippets_cssincl::render() ?>
		<?php snippets_htmlheader::render() ?>
		<?php snippets_altlang::render() ?>
	</head>
	<body>
		<?php snippets_cp_sidebar::render() ?>

		<div id="sidebar-body">
			<?php snippets_navbar::render(['cp_landing' => 1, 'sidebar' => 1]) ?>

			<div class="container" style="padding-top: 30px; padding-bottom: 50px;">
	      <div class="row">
	        <div class="col-sm-8">

	          <div>
	            <h1>Linux Installation</h1>

	            <p>This is a guide to installing <?= $ff_config->get('project-name') ?> on Linux.</p>
	            <hr>

							<!-- Prerequisite -->
							<div id="prerequisite">
								<h4><a href="#prerequisite">#</a> Prerequisites</h4>
								<p>
									Before we begin, you will need a few things. If you are not sure whether you have these things or not, click on the item to see how to check. The following prerequisites you need are:
									<ul>
										<li>Sudo Permissions;</li>
										<li>Ubuntu Installtion;</li>
										<li>Internet Conectivity;</li>
									</ul>
								</p>
							</div>
							<hr>

							<!-- Downloads -->
							<div id="install">
								<h4><a href="#install">#</a> Installation</h4>
								<p>We are in thep rogress of developing all clients. So hopefully the Linux client will be available to public soon.</p>
								<!-- <p>This guide has been done on Ubuntu 18.04. We will be adding tutorials for other distributions soon.</p> -->
							</div>
							<hr>

							<!-- Installation Video -->
							<div id="installvideo">
								<h4><a href="#installvideo">#</a> Installation Video</h4>
								Coming soon.
							</div>

	          </div>
	        </div>

					<?php snippets_installsidebar::render() ?>
	      </div>
	    </div>
		</div>

		<?php snippets_scriptincl::render() ?>
		<?php $ff_context->getCaptcha()->renderScriptElements() ?>
	</body>
</html>
<?php
$ff_response->stopOutputBuffer();

// Unsetting global variables defined in this class.
unset($language, $security_token);

?>

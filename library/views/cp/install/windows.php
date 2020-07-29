<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\cp\install\windows.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

$language = $ff_context->getLanguage();
$latestWindowsPackage = packages::getPlatformMostRecent(packages::PLATFORM_WINDOWS);
$windowsPackages = packages::getPlatformPackages(packages::PLATFORM_WINDOWS);

if(!$latestWindowsPackage || !$windowsPackages) {
	throw new Exception('Packages not found');
	return;
}

$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="en" dir="ltr" style="--sidebar-width: <?= $ff_config->get('sidebar-width') ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc(ff_buildTitle('title-windows-install', $language)) ?></title>

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
	            <h1>Windows Installation</h1>
							<p style="color:#ec2f2f">Notice: In the upcomming months, we are going to be migrating to a new design. As a result, all installation guides will change.</p>

	            <p>This is a guide to installing <?= $ff_config->get('project-name') ?> on Windows.</p>
	            <hr>

							<!-- Prerequisite -->
							<div id="prerequisite">
								<h4><a href="#prerequisite">#</a> Prerequisites</h4>
								<p>
									Before we begin, you will need a few things. If you are not sure whether you have these things or not, click on the item to see how to check. The following prerequisites you need are:
									<ul>
										<li>Administrative Permissions;</li>
										<li>Windows vista or higher;</li>
										<li>Internet Conectivity;</li>
										<li>.NET Framework 4.5;</li>
									</ul>
								</p>
							</div>
							<hr>

							<!-- Downloads -->
							<div id="downloads">
								<h4><a href="#downloads">#</a> Downloads</h4>
								<ul>
									<?php foreach ($windowsPackages as $package): ?>
										<li><a href="<?= $ff_router->getPath('cp_package_download', [
											'id' => $package['id'],
											'filename' => $package['filename'],
											'version' => $package['version'],
											'platform' => $package['platform'],
										]) ?>"><?= ff_esc($package['version']) ?> Windows Package</a></li>
									<?php endforeach; ?>
								</ul>
							</div>
							<hr>

							<!-- Downloads -->
							<div id="install">
								<h4><a href="#install">#</a> Installation</h4>
								<ol>
									<li>First you need to download <a href="<?= $ff_router->getPath('cp_package_download', [
										'id' => $latestWindowsPackage['id'],
										'filename' => $latestWindowsPackage['filename'],
										'version' => $latestWindowsPackage['version'],
										'platform' => $latestWindowsPackage['platform'],
									]) ?>">this package</a>.</li>
									<li>Extract zip file to a desired path.</li>
									<li>Naviage to extracted folder.</li>
									<li>Run lechr.exe.</li>
									<li>Click &quot;Relaunch as Administrator&quot;</li>
									<li>You should be prompted with a screen (UAC) asking whether you want to run this file as Administrator. Click yes.</li>
									<li>Once the program has been run as administrator, it may show a button asking to install &quot;TAP Driver&quot;. Click the button. <span style="color: #d22727">Do not exit during TAP installation.</span></li>
									<li>Once TAP driver has been installed successfully, the button will be replaced with a login button. Enter you credentials and login.</li>
								</ol>

								<p>Congratulations! you have now successfully installed the Windows Client. If you have any enquiries or problems, do not hesitate to contacts us by <a href="<?= $ff_router->getPath('cp_support_new') ?>">Clicking Here</a>.</p>
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
unset($language);

?>

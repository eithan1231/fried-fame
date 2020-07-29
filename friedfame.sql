SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `admin_audit_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL COMMENT 'User who has caused this log',
  `date` int(10) UNSIGNED NOT NULL,
  `name` varchar(32) NOT NULL,
  `value` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `announcements` (
  `id` int(10) UNSIGNED NOT NULL,
  `date` int(10) UNSIGNED NOT NULL,
  `expiry` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `subject` varchar(256) NOT NULL,
  `body` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `autoapi` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `token` varchar(32) NOT NULL,
  `date` int(10) UNSIGNED NOT NULL,
  `last_heartbeet` int(10) UNSIGNED NOT NULL,
  `enabled` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `cache` (
  `key` varchar(64) NOT NULL,
  `original_key` varchar(256) NOT NULL,
  `expiry` int(11) NOT NULL,
  `value` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `connections` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `node_id` int(11) UNSIGNED NOT NULL,
  `connect_date` int(11) UNSIGNED NOT NULL,
  `disconnect_date` int(11) UNSIGNED NOT NULL,
  `localip` varchar(39) NOT NULL,
  `data_sent` int(10) UNSIGNED NOT NULL,
  `data_received` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `coupons` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(32) NOT NULL,
  `discount` float NOT NULL COMMENT 'Percentage.',
  `creator` int(11) NOT NULL COMMENT 'The ID of the user, who created this coupon',
  `date` int(10) UNSIGNED NOT NULL,
  `expiry` int(10) UNSIGNED NOT NULL,
  `usage_count` int(11) NOT NULL,
  `max_usage_count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `email_history` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `date` int(10) UNSIGNED NOT NULL,
  `email` varchar(256) NOT NULL,
  `was_valid` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `email_verification` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `date` int(10) UNSIGNED DEFAULT NULL,
  `expiry` int(10) UNSIGNED DEFAULT NULL,
  `token` varchar(256) DEFAULT NULL,
  `used` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `ff_rpc` (
  `id` int(10) UNSIGNED NOT NULL,
  `type` varchar(32) NOT NULL,
  `auth_token` varchar(128) NOT NULL,
  `endpoint` varchar(256) NOT NULL,
  `port` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `general_feedback` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `date` int(10) UNSIGNED NOT NULL,
  `body` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `giftcodes` (
  `id` int(10) UNSIGNED NOT NULL,
  `plan_id` int(10) UNSIGNED NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `date` int(10) UNSIGNED NOT NULL,
  `expiry` int(10) UNSIGNED NOT NULL,
  `creator_user_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `activation_message` varchar(512) NOT NULL,
  `code` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `groups` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(32) CHARACTER SET utf8 NOT NULL,
  `color` varchar(32) CHARACTER SET utf8 NOT NULL,
  `can_mod_language` tinyint(1) NOT NULL,
  `can_mod_support` tinyint(1) NOT NULL,
  `can_mod_groups` tinyint(1) NOT NULL,
  `can_mod_users` tinyint(1) NOT NULL,
  `can_mod_reviews` tinyint(1) NOT NULL,
  `can_mod_audit` int(11) NOT NULL,
  `can_mod_payments` tinyint(1) NOT NULL,
  `can_mod_feedback` tinyint(1) NOT NULL,
  `can_mod_giftcode` tinyint(1) NOT NULL,
  `can_mod_ffrpc` tinyint(1) NOT NULL,
  `can_mod_announcement` tinyint(1) NOT NULL,
  `can_mod_packages` tinyint(1) NOT NULL,
  `can_mod_nodes` tinyint(1) NOT NULL,
  `can_purchase` tinyint(1) NOT NULL,
  `can_api` tinyint(1) NOT NULL,
  `can_refer` tinyint(1) NOT NULL,
  `can_support` tinyint(1) NOT NULL,
  `can_review` tinyint(1) NOT NULL,
  `can_feedback` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `groups` (`id`, `name`, `color`, `can_mod_language`, `can_mod_support`, `can_mod_groups`, `can_mod_users`, `can_mod_reviews`, `can_mod_audit`, `can_mod_payments`, `can_mod_feedback`, `can_mod_giftcode`, `can_mod_ffrpc`, `can_mod_announcement`, `can_mod_packages`, `can_mod_nodes`, `can_purchase`, `can_api`, `can_refer`, `can_support`, `can_review`, `can_feedback`) VALUES
(1, 'Awaiting Verification', '#ffffff', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 1, 0, 0),
(2, 'Normal', 'red', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 1, 0, 1),
(3, 'Administrator', 'lightblue', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(4, 'Disabled', 'black', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1);

CREATE TABLE `mailing_list` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT '0',
  `email` varchar(254) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `removal_token` varchar(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `date` int(10) UNSIGNED NOT NULL,
  `expiry` int(10) UNSIGNED NOT NULL,
  `hidden` tinyint(1) NOT NULL,
  `phrase_name` varchar(64) NOT NULL,
  `phrase_parameters` varchar(1024) NOT NULL,
  `route_name` varchar(64) NOT NULL,
  `route_parameters` varchar(1024) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `notification_tokens` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `token` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `packages` (
  `id` int(10) UNSIGNED NOT NULL,
  `date` int(10) UNSIGNED NOT NULL,
  `creator` int(10) UNSIGNED NOT NULL,
  `platform` varchar(32) NOT NULL,
  `version` varchar(16) NOT NULL,
  `filesize` int(10) UNSIGNED NOT NULL,
  `filename` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `password_history` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `date` int(11) UNSIGNED NOT NULL,
  `password` varchar(512) NOT NULL,
  `ip` varchar(39) NOT NULL,
  `useragent` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `password_reset` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `token` varchar(64) NOT NULL,
  `date` int(11) NOT NULL,
  `expiry` int(11) NOT NULL,
  `ip` varchar(39) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `payments` (
  `id` int(10) UNSIGNED NOT NULL,
  `status` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `date` int(10) UNSIGNED NOT NULL,
  `affiliate_id` int(10) UNSIGNED NOT NULL,
  `coupon_id` int(10) UNSIGNED NOT NULL,
  `payments_state_id` int(10) UNSIGNED NOT NULL,
  `currency` varchar(3) NOT NULL,
  `gross` decimal(13,2) NOT NULL COMMENT 'Original cost in currency column, excluding all fees (including coupon discounts and affiliates)',
  `fee` decimal(13,2) NOT NULL COMMENT 'Transaction fee',
  `gateway_name` varchar(32) NOT NULL,
  `gateway_info` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `payment_state` (
  `id` int(10) UNSIGNED NOT NULL,
  `token` varchar(32) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `payment_method` varchar(32) NOT NULL,
  `subscription_plan_id` int(10) UNSIGNED NOT NULL,
  `coupon_id` int(10) UNSIGNED NOT NULL,
  `affiliate_id` int(10) UNSIGNED NOT NULL,
  `has_completed` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `phrases` (
  `id` int(10) UNSIGNED NOT NULL,
  `rev` int(11) NOT NULL,
  `language_code` varchar(2) CHARACTER SET utf8 NOT NULL,
  `phrase_name` varchar(64) CHARACTER SET utf8 NOT NULL,
  `phrase` varchar(4096) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `phrases` (`id`, `rev`, `language_code`, `phrase_name`, `phrase`) VALUES
(1, 0, 'en', '404-title', '404 - Page not found!'),
(2, 0, 'en', '404-header', 'The requested page cannot be found.'),
(3, 0, 'en', '404-body', 'The requested page cannot be found. If you wish to go home, <a href=\"{home}\">Click Here</a>.'),
(4, 2, 'en', 'footer-description', '{name} is an extremely powerful VPN supporting multiple platforms, over five languages, and has many unique features.'),
(5, 0, 'en', 'navbar-item-home', 'Home'),
(6, 0, 'en', 'navbar-item-login', 'Login'),
(7, 0, 'en', 'postredir-title', 'Redirecting you'),
(8, 0, 'en', 'postredir-body', 'Please wait while we redirect you to <code>{redirect}</code>'),
(9, 0, 'en', 'postredir-header', 'Rerouting'),
(10, 0, 'en', 'misc-invalid-username', 'Invalid Username.'),
(11, 0, 'en', 'misc-taken-username', 'Username has been taken.'),
(12, 0, 'en', 'misc-password-mismatch', 'Password mismatch.'),
(13, 0, 'en', 'misc-invalid-email', 'Invalid Email Address.'),
(14, 0, 'en', 'misc-email-taken', 'The entered email address has been taken'),
(15, 0, 'en', 'default', 'Default Language String. Please report this to administration.'),
(16, 0, 'en', 'misc-invalid-captcha', '<strong>Captcha</strong>. We encountered an error with your captcha, please try again.'),
(17, 0, 'en', 'misc-parameters-missing', '<strong>Missing Parameters!</strong> The request is missing parameters, and is therefore unable to complete.'),
(18, 0, 'en', 'misc-username-not-found', 'Username cannot be found'),
(19, 0, 'en', 'misc-invalid-first-name', 'Invalid First Name'),
(20, 0, 'en', 'misc-invalid-last-name', 'Invalid Last Name'),
(21, 0, 'en', 'misc-created-user-success', 'Successfully created user.'),
(22, 0, 'en', 'email-verification-subject', 'Email Verification for {project}!'),
(23, 0, 'en', 'email-verification-header', 'Email Verification'),
(24, 0, 'en', 'email-verification-message-html', 'Welcome to {project}!\n<br/><br/>\n{username}, to verify your email, <a href=\"{verify_url}\">click here</a>.'),
(25, 0, 'en', 'email-verification-message-text', 'Welcome to {project}!\n\nTo verify your email, go to the link below.\n\n{verify_url}'),
(26, 0, 'en', 'misc-emailverif-token-missing', '<strong>Missing verification token!</strong> Hello user, we are having trouble finding your email verification token!'),
(27, 0, 'en', 'misc-emailverif-token-expired', 'You email verification token has expired!'),
(28, 0, 'en', 'misc-emailverif-token-used', 'Your email verification token has already been used!'),
(29, 0, 'en', 'misc-emailverif-success', 'Successfully verified your email.'),
(30, 1, 'en', 'misc-pending-emailverif', '<strong>Pending Verification!</strong> Your email is pending verification. Be sure to check spam folder.'),
(31, 0, 'en', 'misc-userid-not-found', 'User ID cannot be found'),
(32, 0, 'en', 'misc-wrong-password', 'The entered password is incorrect.'),
(33, 0, 'en', 'misc-login-successful', 'Login was successful'),
(34, 0, 'en', 'navbar-item-cp', 'Control Panel'),
(35, 0, 'en', 'footer-information-column', 'Information'),
(36, 0, 'en', 'footer-information-tos', 'Terms of Service'),
(37, 0, 'en', 'footer-information-pp', 'Privacy Policy'),
(38, 0, 'en', 'pp-header', 'Privacy Policy'),
(39, 0, 'en', 'pp-header-description', 'This Privacy Policy governs the manner in which {name} collects, uses, maintains and discloses information collected from users (each, a \"User\") of the {hostname} website (\"Site\"). This privacy policy applies to the Site and all products and services offered by {name}'),
(40, 0, 'en', 'pp-header-cookies', 'Web browser cookies'),
(41, 0, 'en', 'pp-header-cookies-description', 'Our Site uses \"cookies\" to enhance User experience. User\'s web browser places cookies on their local storage (Such as \"Hard Drive\", or \"SSD\") for record-keeping purposes and sometimes to track information about them. User may choose to set their web browser to refuse cookies, or to alert you when cookies are being sent. If they do so, note that some parts of the Site will not function properly.'),
(42, 0, 'en', 'pp-header-collect', 'How we use collected information'),
(43, 0, 'en', 'pp-header-collect-description', '{name} may collect and use Users\r\n				personal information for the following purposes:<br />\r\n				- To improve customer service<br />\r\n				Information you provide helps us respond to your customer service requests and support needs more efficiently.<br />\r\n				- To improve our Site<br />\r\n				We may use feedback you provide to improve our products and services.<br />\r\n				- To send periodic emails<br />\r\n				We may use the email address you provide to respond to your inquiries,\r\n				questions, and/or other requests.'),
(44, 0, 'en', '', ''),
(45, 0, 'en', 'pp-header-protect', 'How we protect your information'),
(46, 0, 'en', 'pp-header-protect-description', 'We adopt appropriate data collection, storage and processing practices\r\n				and security measures to protect against unauthorized access, alteration,\r\n				disclosure or destruction of your personal information, username, password,\r\n				transaction information and data stored on our Site.'),
(47, 0, 'en', 'pp-header-sharing-info', 'Sharing your personal information'),
(48, 0, 'en', 'pp-header-sharing-description', 'We may share generic aggregated demographic information not linked to\r\n				any personal identification information regarding visitors and users\r\n				to our parent domains.'),
(49, 0, 'en', 'pp-header-3rdparty', 'Third party websites'),
(50, 0, 'en', 'pp-header-3rdparty-description', 'Users may find advertising or other content on our Site that link to the\r\n				sites and services of our partners, suppliers, advertisers, sponsors,\r\n				licensors and other third parties. We do not control the content or links\r\n				that appear on these sites and are not responsible for the practices\r\n				employed by websites linked to or from our Site. In addition, these sites\r\n				or services, including their content and links, may be constantly changing.\r\n				These sites and services may have their own privacy policies and customer\r\n				service policies. Browsing and interaction on any other website, including\r\n				websites which have a link to our Site, is subject to that website\'s own\r\n				terms and policies.'),
(51, 0, 'en', 'pp-header-changes', 'Changes to this privacy policy'),
(52, 0, 'en', 'pp-header-changes-description', '{name}\r\n				has the discretion to update this privacy policy at any time.\r\n				When we do, we will revise the updated date at the bottom of this page.\r\n				We encourage Users to frequently check this page for any changes to stay\r\n				informed about how we are helping to protect the personal information we\r\n				collect. You acknowledge and agree that it is your responsibility to\r\n				review this privacy policy periodically and become aware of\r\n				modifications.'),
(53, 0, 'en', 'pp-header-acceptance', 'Your acceptance of these terms'),
(54, 0, 'en', 'pp-header-acceptance-description', 'By using this Site, you signify your acceptance of this policy. If you\r\n				do not agree to this policy, please do not use our Site. Your continued\r\n				use of the Site following the posting of changes to this policy will be\r\n				deemed your acceptance of those changes.'),
(55, 0, 'en', 'pp-header-contact', 'Contacting us'),
(56, 0, 'en', 'pp-header-contact-description', 'If you have any questions about this Privacy Policy, the practices of\r\n				this site, or your dealings with this site, please contact us through\r\n				our contact form.'),
(57, 0, 'en', 'default-og-title', '{name}!'),
(58, 0, 'en', 'default-og-description', 'Welcome to {name}! The new an upcoming VPN!'),
(59, 0, 'en', 'vultr-status-200', 'Function successfully executed.'),
(60, 0, 'en', 'vultr-status-400', 'Invalid API location. Check the URL that you are using.'),
(61, 0, 'en', 'vultr-status-403', 'Invalid or missing API key. Check that your API key is present and matches your assigned key.'),
(62, 0, 'en', 'vultr-status-405', 'Invalid HTTP method. Check that the method (POST|GET) matches what the documentation indicates.'),
(63, 0, 'en', 'vultr-status-412', 'Request failed. Check the response body for a more detailed description.'),
(64, 0, 'en', 'vultr-status-500', 'Internal Server Error'),
(65, 0, 'en', 'vultr-status-503', 'Rate limit hit. API requests are limited to an average of 2/s. Try your request again later.'),
(66, 0, 'en', 'vultr-unknown-response', 'Unknown Response'),
(67, 0, 'en', 'misc-additional-auth-not-found', 'Additional authentication not found.'),
(68, 0, 'en', 'email-twofactorverification-subject', '{project} Additional Verification.'),
(69, 0, 'en', 'email-twofactorverification-header', 'Additional Email Verification'),
(70, 0, 'en', 'email-twofactorverification-message-html', 'Hello {username},\n<br /><br />\nWe noticed you have tried to login, and since you have the email additional authentication enabled, you are required to enter a email code upon login. The code is as follows,\n<br/>\n{code}\n<br/><br/>\nThanks,\n<br/>\n{project}'),
(71, 0, 'en', 'email-twofactorverification-message-text', 'Hello {username},\n\nWe noticed you have tried to login, and since you have the email additional authentication enabled, you are required to enter a email code upon login. The code is as follows,\n{code}\n\nThanks,\n{project}'),
(72, 0, 'en', 'misc-additionalauth-email-invalid-code', 'The code you entered is wrong.'),
(73, 0, 'en', 'misc-additionalauth-email-expired-code', 'The code you entered has expired.'),
(74, 0, 'en', 'misc-additionalauth-email-title', 'Email Authentication'),
(75, 0, 'en', 'misc-additionalauth-email-description', 'Your code has been emailed to you.'),
(76, 0, 'en', 'misc-additionalauth-email-input-code-label', 'Code'),
(77, 0, 'en', 'oneword-username', 'Username'),
(78, 0, 'en', 'oneword-password', 'Password'),
(79, 0, 'en', 'oneword-email', 'Email'),
(80, 0, 'en', 'oneword-recovery', 'Recovery'),
(81, 0, 'en', 'oneword-registration', 'Registration'),
(82, 0, 'en', 'oneword-login', 'Login'),
(83, 0, 'en', 'oneword-submit', 'Submit'),
(84, 0, 'en', 'oneword-first-name', 'First-Name'),
(85, 0, 'en', 'oneword-last-name', 'Last-Name'),
(86, 0, 'en', 'oneword-verify-password', 'Verify Password'),
(87, 0, 'en', 'register-tos-pp-agree-snippet', 'I agree to the <a href=\"{tos}\">Terms of Service</a> and <a href=\"{pp}\">Privacy Policy</a>.'),
(88, 0, 'en', 'misc-email-not-found', 'Email cannot be found'),
(89, 0, 'en', 'misc-try-again-later', 'We are a little busy right now, try back later.'),
(90, 0, 'en', 'misc-password-short', 'Password is too short, it must be of 6 characters.'),
(91, 0, 'en', 'misc-password-long', 'Password is too long, must be between 6 and 256 characters long.'),
(92, 0, 'en', 'misc-recovery-sent', 'If the email address was found, the recovery email will be sent.'),
(93, 0, 'en', 'email-passwordresetlink-subject', '{project} Recovery.'),
(94, 0, 'en', 'email-passwordresetlink-header', 'Account Recovery.'),
(95, 0, '', '', ''),
(96, 0, 'en', 'email-passwordresetlink-message-html', 'Hello {username}\n<br /><br />\nWe have received a request to reset your password. If this was you, please click the link below. If this was not you, ignore this message.\n<br />\n<a href=\"{recovery_url}\">Click here to send temporary password</a>\n<br /><br />\nInformation about the recovery request:\n<br />\nIP Address: {ip_address} (Not Stored)\n<br />\nUser Agent: {user_agent}\n<br /><br />\nThanks,\n<br />\n{project}'),
(97, 0, 'en', 'email-passwordresetlink-message-text', 'Hello {username}\n\nWe have received a request to reset your password. If this was you, copy the link below into your browser. If this was not you, ignore this message.\n{recovery_url}\n\nInformation about the recovery request:\nIP Address: {ip_address} (Not Stored)\nUser Agent: {user_agent}\n\nThanks,\n{project}'),
(98, 0, 'en', 'meta-description', '{project} is the new and upcoming vpn. We offer the best performance, for the lowest price.'),
(99, 0, 'en', 'misc-password-reset-disabled', 'The password reset token is not enabled. This could be because you have already used it, or that it was disabled by administration.'),
(100, 0, 'en', 'misc-password-reset-token-invalid', 'Your password reset token is invalid.'),
(101, 0, 'en', 'misc-password-reset-expired', 'Your password reset token has expired.'),
(102, 0, 'en', 'misc-password-reset-ip-mismatch', 'Your IP does not match the request IP address.'),
(103, 0, 'en', 'email-temporarypassword-subject', '{project} Temporary Password'),
(104, 0, 'en', 'email-temporarypassword-header', 'Temporary Password'),
(105, 0, 'en', 'email-temporarypassword-message-html', 'Hello {username},\r\n<br /><br />\r\nThere has been a temporary password assigned to your account, your password is as follows\r\n<br />\r\n{password}\r\n<br /><br />\r\nWe <strong>highly</strong> advise you to change your password as soon as you get this message.\r\n<br /><br />\r\nThanks,\r\n<br />\r\n{project}'),
(106, 0, 'en', 'email-temporarypassword-message-text', 'Hello {username},\r\n\r\nThere has been a temporary password assigned to your account, your password is as follows\r\n{password}\r\n\r\nWe <strong>highly</strong> advise you to change your password as soon as you get this message.\r\n\r\nThanks,\r\n{project}'),
(107, 0, 'en', 'email-passwordchange-subject', 'Your password has been updated - {project}'),
(108, 0, 'en', 'email-passwordchange-header', 'Password Update'),
(109, 1, 'en', 'email-passwordchange-message-html', 'Hello {username},\r\n<br /><br />\r\nWe are just sending you this message to notify you of a password change to your account. If this is an unauthorized action, you are able to reset your password <a href=\"{recovery_url}\">here (Click Here)</a>. To prevent this happening again, consider implementing out Additional Authentication, in <a href=\"{settings_url}\">Settings</a>.\r\n<br /><br />\r\nThanks,\r\n<br />\r\n{project}'),
(110, 0, 'en', 'email-passwordchange-message-text', 'Hello {username},\r\n\r\nWe are just sending you this message to notify you of a password change to your account. If this is an unauthorized action, you are able to reset your password (See link below). To prevent this happening again, consider implementing out Additional Authentication, in Settings.\r\n\r\nAccount recovery,\r\n{recovery_url}\r\n\r\nThanks,\r\n{project}'),
(111, 0, 'en', 'snippet-invalidemail', '<strong>Invalid Email!</strong> The mailing address you assigned to your account is invalid, we cannot find it on the remote server. So please update it to a email address that exists by <a href=\"{email_reset}\">Clicking Here</a>. Leaving it in its current state is a <strong>huge</strong> security concern. If in the event you lost your password, you will be unable to recover your account.'),
(112, 0, 'en', '--removed', 'Your password does not match our records.'),
(113, 0, 'en', 'email-emailchange-subject', 'Your email address has been changed - {project}'),
(114, 0, 'en', 'email-emailchange-header', 'Email change.'),
(115, 0, 'en', 'email-emailchange-message-html', 'Hello {username},\n<br /><br />\nWe are sending this email to notify you of your email change. Your old email address was <a href=\"mailto:{old_email}\">{old_email}</a>, and your new email is <a href=\"mailto:{new_email}\">{new_email}</a>.\n<br /><br />\nThanks,\n<br />\n{project}'),
(116, 0, 'en', 'email-emailchange-message-text', 'Hello {username},\n\nWe are sending this email to notify you of your email change. Your old email address was {old_email}, and your new email is {new_email}.\n\nThanks,\n{project}'),
(117, 0, 'en', 'password-history-title', 'Password History'),
(118, 0, 'en', 'oneword-date', 'Date'),
(119, 0, 'en', 'oneword-ip-address', 'IP Address'),
(120, 0, 'en', 'oneword-platform', 'Platform'),
(121, 0, 'en', 'oneword-browser', 'Browser'),
(122, 0, 'en', 'change-email-title', 'Change Email'),
(123, 0, 'en', 'oneword-current-password', 'Current Password'),
(124, 0, 'en', 'misc-success', 'Successful!'),
(125, 0, 'en', 'navbar-item-add-user', 'Add User'),
(126, 0, 'en', 'navbar-item-add-user-title', 'Links another user to your session.'),
(127, 0, 'en', 'unauthorized-title', 'You are not authorized to view this page.'),
(128, 0, 'en', 'unauthorized-header', 'Unauthorized.'),
(129, 0, 'en', 'unauthorized-body', 'We are sorry, but you are not authorized to access this page. <a href=\"{home}\">Click Here</a> to return home.'),
(130, 0, 'en', 'oneword-additional-auth', 'Additional Auth'),
(131, 0, 'en', 'db-title', 'Database error - Internal Error'),
(132, 0, 'en', 'db-header', 'Database Error'),
(133, 0, 'en', 'db-body', 'We are currently experiencing an issue with our database. Try back later.'),
(134, 0, 'en', 'oneword-support-thread', 'Support Thread'),
(135, 0, 'en', 'oneword-subject', 'Subject'),
(136, 0, 'en', 'support-new-body-placeholder', 'Please describe why you are creating this support ticket.'),
(137, 0, 'en', 'misc-permission-denied', 'Permission Denied'),
(138, 0, 'en', 'misc-subject-too-long', 'Subject too long.'),
(139, 0, 'en', 'misc-subject-too-long', 'Subject too long.'),
(140, 0, 'en', 'oneword-thread-messages', 'Thread Messages'),
(141, 0, 'en', 'support-view-body-placeholder', 'Enter your reply here'),
(142, 0, 'en', 'oneword-staff', 'Staff'),
(143, 0, 'en', 'oneword-general', 'General'),
(144, 0, 'en', 'oneword-support', 'Support'),
(145, 0, 'en', 'oneword-subscriptions', 'Subscriptions'),
(146, 0, 'en', 'oneword-purchase', 'Purchase'),
(147, 0, 'en', 'oneword-giftcodes', 'Giftcodes'),
(148, 0, 'en', 'oneword-manage', 'Manage'),
(149, 0, 'en', 'oneword-administration', 'Administration'),
(150, 0, 'en', 'oneword-language', 'Language'),
(151, 0, 'en', 'oneword-groups', 'Groups'),
(152, 0, 'en', 'oneword-users', 'Users'),
(153, 0, 'en', 'support-view-closed', 'This support thread is closed.'),
(154, 0, 'en', 'oneword-last-reply', 'Last Reply'),
(155, 0, 'en', 'oneword-status', 'Status'),
(156, 0, 'en', 'oneword-open', 'Open'),
(157, 0, 'en', 'oneword-closed', 'Closed'),
(158, 0, 'en', 'oneword-deleted', 'Deleted'),
(159, 0, 'en', 'supportlist-by-date', '<strong>{username}</strong> on {date}'),
(160, 0, 'en', 'oneword-settings', 'Settings'),
(161, 0, 'en', 'email-supportpostreply-subject', '[Ticket:{ticket_id}] Re: {subject}'),
(162, 0, 'en', 'email-supportpostreply-message-html', 'Hello {username},\n<br/ ><br/ >\nYour support ticket titled &quot;{subject}&quot; has been responded to by {replier} [<span style=\"color: {replier_group_color}\">{replier_group}</span>]\n<br/ ><br/ >\nTo view your thread, <a href=\"{thread_url}\">Click Here</a>.\n<br/ ><br/ >\nMessage Preview,\n<br />\n<blockquote class=\"quote\">\n{message_quote}\n</blockquote>\n<br/ ><br/ >\nBest regards,\n<br/ >\n{project}'),
(163, 0, 'en', 'email-supportpostreply-message-text', 'Hello {username},\n\nYour support ticket titled \"{subject}\" has been responded to by {replier} [{replier_group}]\n{thread_url}\n\nMessage Preview\n{message_quote}\n\nBest Regards,\n{project}'),
(164, 0, 'en', 'misc-invalid-stars', 'Invalid star rating, must be within 0 and 5.'),
(165, 0, 'en', 'misc-body-too-long', 'Body too long.'),
(166, 0, 'en', 'oneword-audit', 'Audit'),
(167, 0, 'en', 'mod-audit-title', 'Moderation Audit'),
(168, 0, 'en', 'oneword-action', 'Action'),
(169, 0, 'en', 'oneword-group', 'Group'),
(170, 0, 'en', 'oneword-information', 'Information'),
(171, 0, 'en', 'misc-no-audit-found', 'No audit logs found'),
(172, 0, 'en', 'audit-admin-review-approved-invalid', 'Approved invalid review. ({id})'),
(173, 0, 'en', 'audit-admin-review-approved', 'Approved a review written by {writer}.'),
(174, 0, 'en', 'audit-admin-review-deleted-invalid', 'Invalid review deleted.'),
(175, 0, 'en', 'audit-admin-review-deleted', 'A review by {writer} was deleted.'),
(176, 0, 'en', 'audit-admin-newplan-invalid', 'Created invalid plan'),
(177, 0, 'en', 'audit-admin-newplan', 'Created plan titled &quot;{name}&quot;'),
(178, 2, 'en', '--test', 'Testing with revisions'),
(179, 2, 'de', '--test', 'Testing with revisions'),
(180, 0, 'en', 'mod-lang-outdated-title', 'Outdated Language Phrases'),
(181, 1, 'en', 'mod-lang-outdated-title', 'Outdated Language Phrases'),
(182, 0, 'en', 'oneword-no-results-found', 'No Results Found'),
(183, 0, 'en', 'oneword-id', 'ID'),
(184, 0, 'en', 'mod-audit-on-user', 'Getting audit logs for <a href=\"{user_mod_page}\">{username}</a> who&#39;s apart of the group {group}.'),
(185, 1, 'en', 'snippet-pendingemailverif', '<b>Pending Email Verification!</b> The email you provided is pending verification. To verify your email, navigate to your email and find an email sent from us. Ensure it\'s not in the spam folder. Once you find it, follow the directions. If you would like to resend the verification email, click the button below.'),
(186, 0, 'en', 'email-newsubscription-subject', 'New subscription - {project}'),
(187, 0, 'en', 'email-newsubscription-message-text', 'Hello {username},\r\n\r\nWe are sending you this email as notification to your new subscription. Your subscription plan is {plan_name}, and is set to expire {expiry}. This plan costs {plan_price} {plan_currency}.\r\n\r\nThanks,\r\n{project}'),
(188, 0, 'en', 'email-newsubscription-message-html', 'Hello {username},\r\n<br /><br />\r\nWe are sending you this email as notification to your new subscription. Your subscription plan is {plan_name}, and is set to expire {expiry}. This plan costs {plan_price} {plan_currency}.\r\n<br /><br />\r\nThanks,\r\n<br />\r\n{project}'),
(189, 0, 'en', 'email-supportthreadclosed-subject', '[Ticket:{ticket_id}] Support Ticket Closed - {project}'),
(190, 0, 'en', 'email-supportthreadclosed-message-html', 'Hello {username},<br /><br />We are sending you this email to notify you that your support ticket titled &quot;{thread_subject}&quot; has been closed.<br /><br />If you believe this action was a mistake, open a support ticket <a href=\"{new_ticket_url}\">here<a>.<br /><br />Thanks,<br />{project}'),
(191, 0, 'en', 'email-supportthreadclosed-message-text', 'Hello {username},\r\n\r\nWe are sending you this email to notify you that your support ticket titled \"{thread_subject}\" has been closed.\r\n\r\nIf you believe this action was a mistake, open a support ticket.\r\n\r\nThanks,\r\n{project}'),
(192, 0, 'en', 'email-supportthreadopened-subject', '[Ticket:{ticket_id}] Support Ticket Opened - {project}'),
(193, 0, 'en', 'email-supportthreadopened-message-html', 'Hello {username},<br /><br />We are sending you this email to notify you that your support ticket titled &quot;{thread_subject}&quot; has been opened.<br /><br />Thanks,<br />{project}'),
(194, 0, 'en', 'email-supportthreadopened-message-text', 'Hello {username},\r\n\r\nWe are sending you this email to notify you that your support ticket titled \"{thread_subject}\" has been opened.\r\n\r\nThanks,\r\n{project}'),
(195, 0, 'en', 'oneword-phrase-name', 'Phrase Name'),
(196, 0, 'en', 'oneword-phrase-preview-language', 'Phrase Preview Language'),
(197, 0, 'en', 'oneword-intended-language', 'Intended Language'),
(198, 0, 'en', 'oneword-update', 'Update'),
(199, 0, 'en', 'audit-admin-setphrase-invalid', 'Updated invalid phrase {id}'),
(200, 0, 'en', 'audit-admin-setphrase', 'Set phrase &quot;{phrase_name}&quot; to revision {revision}.'),
(201, 0, 'en', 'mod-lang-new-title', 'Create New Phrase'),
(202, 0, 'en', 'oneword-phrase', 'Phrase'),
(203, 0, 'en', 'oneword-list', 'List'),
(204, 0, 'en', 'oneword-outdated', 'Outdated'),
(205, 0, 'en', 'oneword-unfound', 'Unfound'),
(206, 0, 'en', 'oneword-new', 'New'),
(207, 0, 'en', 'misc-no-outdated-phrases', 'No outdated phrases found'),
(208, 0, 'en', 'misc-no-phrase-found', 'No phrases found'),
(209, 0, 'en', 'oneword-revision', 'Revision'),
(210, 0, 'en', 'oneword-language-code', 'Language Code'),
(211, 0, 'en', 'oneword-options', 'Options'),
(212, 0, 'en', 'oneword-edit', 'Edit'),
(213, 0, 'en', 'mod-phrase-list-title', 'Phrase List'),
(214, 0, 'en', 'mod-phrase-edit-title', 'Edit a phrase'),
(215, 0, 'en', 'misc-phrase-not-found', 'Phrase not found'),
(216, 0, 'en', 'misc-no-unfound-phrases', 'No unfound phrases found'),
(217, 2, 'pt', '--test', 'Testing with revisions'),
(218, 2, 'es', '--test', 'Testing with revisions'),
(219, 0, 'en', '--test2', 'this is a test in English'),
(220, 0, 'de', '--test2', 'this is a test in English'),
(221, 0, 'pt', '--test2', 'this is a test in English'),
(222, 0, 'es', '--test2', 'this is a test in EspaÃƒÂ±ol'),
(223, 0, 'en', 'mod-lang-unfound-title', 'Unfound language phrases'),
(224, 2, 'fr', '--test', 'Testing with FranÃƒÂ§ais'),
(225, 0, 'en', 'misc-body-too-short', 'Body is too short'),
(226, 0, 'en', 'misc-subject-too-short', 'Subject is too short'),
(227, 0, 'en', 'change-password-title', 'Change Password'),
(228, 0, 'en', 'oneword-new-password', 'New Password'),
(229, 0, 'en', 'oneword-retype-new-password', 'Retype new password'),
(230, 0, 'en', 'misc-new-passwords-dont-match', 'New passwords do not match'),
(231, 0, 'en', 'mod-audit-cache-notice', 'Cache is enabled for this page. You may be viewing outdated infroamtion for the cost of performance. If you need to update the data listed, <a href=\"{clear_cache_url}\">Click Here<a>.'),
(232, 2, 'de', 'footer-description', '{name} ist ein extrem leistungsfÃƒÂ¤higes VPN, das mehrere Plattformen in fÃƒÂ¼nf Sprachen unterstÃƒÂ¼tzt und viele einzigartige Funktionen bietet.'),
(233, 1, 'de', 'email-passwordchange-message-html', 'Hallo {username},\n<br /><br />\nWir senden Ihnen diese Nachricht, um Sie ÃƒÂ¼ber eine KennwortÃƒÂ¤nderung in Ihrem Konto zu informieren. Wenn dies eine nicht autorisierte Aktion ist, kÃƒÂ¶nnen Sie Ihr Passwort <a href=\"{recovery_url}\">hier (hier klicken)</a> zurÃƒÂ¼cksetzen. Um dies erneut zu verhindern, sollten Sie die ZusÃƒÂ¤tzliche Authentifizierung in <a href=\"{settings_url}\"> Einstellungen </a> implementieren.\n<br /> <br />\nVielen Dank,\n<br />\n{project}'),
(234, 0, 'de', 'vultr-status-503', 'Ratenbegrenzung getroffen. API-Anfragen sind auf durchschnittlich 2/s beschrÃƒÂ¤nkt. Versuchen Sie Ihre Anfrage spÃƒÂ¤ter noch einmal.'),
(235, 0, 'de', 'pp-header-protect-description', 'Wir wenden angemessene Verfahren zur Erhebung, Speicherung, Verarbeitung von Daten, SicherheitsmaÃƒÅ¸nahmen zum Schutz vor unbefugtem Zugriff und VerÃƒÂ¤nderung, Offenlegung oder Vernichtung Ihrer persÃƒÂ¶nlichen Daten, Benutzername, Kennwort, Transaktionsinformationen und Daten, die auf unserer Website gespeichert sind an.'),
(236, 0, 'de', 'oneword-registration', 'Registrierung'),
(237, 0, 'de', 'email-emailchange-message-html', 'Hallo {username},\n<br /><br />\nWir senden diese E-Mail, um Sie ÃƒÂ¼ber Ihre E-Mail-Ãƒâ€žnderung zu informieren. Ihre alte E-Mail-Adresse war <a href=\"mailto:{old_email}\">{old_email}</a>, und Ihre neue E-Mail lautet <a href=\"mailto:{new_email}\">{new_email}</a>.\n<br /><br />\nDanke,\n<br />\n{project}'),
(238, 0, 'de', 'oneword-retype-new-password', 'Neues Passwort erneut eingeben'),
(239, 0, 'de', 'oneword-closed', 'Geschlossen'),
(240, 0, 'de', 'misc-password-reset-disabled', 'Das Kennwort Reset-Token ist nicht aktiviert. Dies kann daran liegen, dass Sie es bereits verwendet haben oder dass es von der Administration deaktiviert wurde.'),
(241, 0, 'de', 'misc-invalid-email', 'UngÃƒÂ¼ltige E-Mail-Adresse.'),
(242, 0, 'de', 'pp-header', 'DatenschutzerklÃƒÂ¤rung'),
(243, 0, 'de', 'email-supportthreadopened-message-text', 'Hallo {username},\n\nWir senden Ihnen diese E-Mail, um Sie darÃƒÂ¼ber zu informieren, dass Ihre Supportanfrage mit dem Titel \"{thread_subject}\" erÃƒÂ¶ffnet wurde.\n\nDanke,\n{project}'),
(244, 0, 'de', 'oneword-manage', 'Verwalten'),
(245, 0, 'de', 'mod-lang-outdated-title', 'Veraltete Sprachphrasen'),
(246, 0, 'de', 'misc-phrase-not-found', 'Phrase nicht gefunden'),
(247, 0, 'de', 'oneword-language-code', 'Sprachcode\n'),
(248, 0, 'de', 'misc-username-not-found', 'Benutzername kann nicht gefunden werden'),
(249, 0, 'de', 'misc-wrong-password', 'Das eingegebene Kennwort ist falsch.'),
(250, 0, 'de', 'misc-email-taken', 'Die eingegebene E-Mail-Adresse wurde bereits angewendet'),
(251, 0, 'de', 'vultr-status-403', 'UngÃƒÂ¼ltiger oder fehlender API-SchlÃƒÂ¼ssel. ÃƒÅ“berprÃƒÂ¼fen Sie, ob Ihr API-SchlÃƒÂ¼ssel vorhanden ist und mit dem zugewiesenen SchlÃƒÂ¼ssel ÃƒÂ¼bereinstimmt.'),
(252, 0, 'de', 'email-supportpostreply-message-text', 'Hallo {username},\n\nIhr Support-Ticket mit dem Titel \"{subject}\" wurde von {replier} beantwortet. [{replier_group}]\n{thread_url}\n\nNachrichtenvorschau\n{message_quote}\n\nMit freundlichen GrÃƒÂ¼ÃƒÅ¸en,\n{project}'),
(253, 0, 'de', 'oneword-verify-password', 'Kennwort bestÃƒÂ¤tigen'),
(254, 0, 'de', '404-body', 'Die angeforderte Seite konnte nicht gefunden werden. Wenn Sie nach Hause gehen mÃƒÂ¶chten, <a href=\"{home}\">Klicken Sie hier</a>.'),
(255, 0, 'de', 'email-emailchange-message-text', 'Hallo {username},\n\nWir senden diese E-Mail, um Sie ÃƒÂ¼ber Ihre E-Mail-Ãƒâ€žnderung zu informieren. Ihre alte E-Mail-Adresse lautete {old_email}, und Ihre neue E-Mail-Adresse lautet {new_email}.\n\nMit freundlichen GrÃƒÂ¼ÃƒÅ¸en,\n{project}'),
(256, 0, 'de', 'oneword-revision', 'Revision'),
(257, 0, 'de', 'oneword-current-password', 'Aktuelles Kennwort'),
(258, 0, 'de', 'oneword-ip-address', 'IP-Adresse'),
(259, 0, 'de', 'misc-try-again-later', 'Wir sind gerade etwas beschÃƒÂ¤ftigt, versuchen Sie es spÃƒÂ¤ter noch einmal.'),
(260, 0, 'de', 'misc-created-user-success', 'Benutzer erfolgreich erstellt.'),
(261, 0, 'de', 'vultr-status-200', 'Funktion erfolgreich ausgefÃƒÂ¼hrt.'),
(262, 0, 'de', 'misc-subject-too-long', 'Das Subjekt ist zu lang.'),
(263, 0, 'de', 'support-view-closed', 'Dieser Support-Thread ist geschlossen.\n'),
(264, 0, 'de', 'misc-additionalauth-email-invalid-code', 'Der von Ihnen eingegebene Code ist falsch.'),
(265, 0, 'de', 'email-passwordchange-message-text', 'Hallo {username},\n\nWir senden Ihnen gerade diese Nachricht, um Sie ÃƒÂ¼ber eine PasswortÃƒÂ¤nderung in Ihrem Konto zu informieren. Wenn es sich um eine nicht autorisierte Aktion handelt, kÃƒÂ¶nnen Sie Ihr Kennwort zurÃƒÂ¼cksetzen (siehe Link unten). Um zu verhindern, dass sich dies wiederholt, sollten Sie die Implementierung von zusÃƒÂ¤tzlicher Authentifizierung in den Einstellungen in Betracht ziehen.\n\nWiederherstellung des Kontos,\n{recovery_url}\n\nMit freundlichen GrÃƒÂ¼ÃƒÅ¸en,\n{project}'),
(266, 0, 'de', 'supportlist-by-date', '<strong>{username}</strong> am {date}'),
(267, 0, 'de', 'email-passwordchange-subject', 'Ihr Passwort wurde aktualisiert - {project}'),
(268, 0, 'de', 'oneword-group', 'Gruppe'),
(269, 0, 'de', 'oneword-action', 'Aktion'),
(270, 0, 'de', 'misc-no-phrase-found', 'Keine Phrasen gefunden'),
(271, 0, 'de', 'email-twofactorverification-message-text', 'Hallo {username},\n\nWir haben bemerkt, dass Sie versucht haben, sich anzumelden, und da Sie die zusÃƒÂ¤tzliche Authentifizierung per E-Mail aktiviert haben, mÃƒÂ¼ssen Sie beim Anmelden einen E-Mail-Code eingeben. Der Code lautet wie folgt,\n{code}\n\nMit freundlichen GrÃƒÂ¼ÃƒÅ¸en,\n{project}'),
(272, 0, 'de', 'pp-header-contact', 'Uns kontaktieren'),
(273, 0, 'de', 'db-header', 'Datenbankfehler'),
(274, 0, 'de', 'oneword-phrase-name', 'Phrasenname'),
(275, 0, 'de', 'oneword-general', 'Allgemeines'),
(276, 0, 'de', 'misc-additionalauth-email-description', 'Ihr Code wurde Ihnen per E-Mail zugeschickt.'),
(277, 0, 'de', 'support-new-body-placeholder', 'Bitte beschreiben Sie, warum Sie dieses Support-Ticket erstellen.'),
(278, 0, 'de', 'misc-additionalauth-email-expired-code', 'Der eingegebene Code ist abgelaufen.\n'),
(279, 0, 'de', 'email-passwordchange-header', 'Passwort aktualisieren\n'),
(280, 0, 'de', 'oneword-submit', 'Einreichen'),
(281, 0, 'de', 'pp-header-collect', 'Wie wir gesammelte Informationen anwenden'),
(282, 0, 'de', 'change-password-title', 'Passwort ÃƒÂ¤ndern'),
(283, 0, 'de', 'oneword-password', 'Passwort'),
(284, 0, 'de', 'navbar-item-home', 'Startseite'),
(285, 0, 'de', 'misc-new-passwords-dont-match', 'Neue PasswÃƒÂ¶rter stimmen nicht ÃƒÂ¼berein'),
(286, 0, 'de', 'email-twofactorverification-header', 'ZusÃƒÂ¤tzliche E-Mail-Verifizierung'),
(287, 0, 'de', 'email-passwordresetlink-subject', '{project} Wiederherstellung.'),
(288, 0, 'de', 'oneword-username', 'Nutzername'),
(289, 0, 'de', 'oneword-intended-language', 'Vorgesehene Sprache'),
(290, 0, 'de', 'email-verification-subject', 'E-Mail-BestÃƒÂ¤tigung fÃƒÂ¼r {project}!'),
(291, 0, 'de', 'pp-header-protect', 'Wie wir Ihre Informationen schÃƒÂ¼tzen'),
(292, 0, 'de', 'oneword-recovery', 'Wiederherstellung'),
(293, 0, 'de', 'oneword-browser', 'Browser'),
(294, 0, 'de', 'misc-password-mismatch', 'Die PasswÃƒÂ¶rter stimmen nicht ÃƒÂ¼berein.'),
(295, 0, 'de', 'mod-lang-new-title', 'Neue Phrase erstellen'),
(296, 0, 'en', 'mod-user-title', 'User Insights'),
(297, 0, 'en', 'oneword-search-user', 'Search User'),
(298, 0, 'en', 'misc-no-results', 'No Results Found'),
(299, 0, 'en', 'mod-user-find-title', 'Results for &quot;{query}&quot;'),
(300, 0, 'en', 'mod-manage-user-title', 'Manage User'),
(301, 0, 'en', 'mod-manage-user', 'Administration Control for &quot;{user}&quot;.'),
(302, 0, 'en', 'misc-data-sent-mb', 'Data Sent (Megabytes)'),
(303, 0, 'en', 'misc-data-received-mb', 'Data Received (Megabytes)'),
(304, 0, 'en', 'mod-manage-user-apart-group', '{user} is currently apart of the group <a href=\"{group_page}\">{group_name}</a>'),
(305, 0, 'en', 'oneword-manage-usergroup', 'Manage Usergroup'),
(306, 0, 'en', 'oneword-analytics-and-statistics', 'Analytics and Statistics'),
(307, 0, 'en', 'oneword-data-usage', 'Data Usage'),
(308, 0, 'en', 'oneword-general-information', 'General Information'),
(309, 0, 'en', 'mod-manage-email-verif-notice', 'User is pending email verification.'),
(310, 0, 'en', 'mod-manage-node-auth-hover', 'Hover to view authentication key'),
(311, 0, 'en', 'misc-outdated-info-notice', 'The information displayed may be outdated. If you would like to get the newest information, refresh this page.'),
(312, 0, 'en', 'audit-admin-changeusergroup-invalid', 'Changed a users usergroup.'),
(313, 1, 'en', 'audit-admin-changeusergroup', 'Changed {username} group to {new_group}, from {old_group}.'),
(314, 0, 'en', 'misc-redirect-loading', 'Please wait while we redirect you. If you are not redirected automatically, click below.'),
(315, 0, 'en', 'oneword-click-here', 'Click Here'),
(316, 0, 'en', 'notification-paypal-bad-currency', 'We are unable to process your PayPal purchase due to a currency mismatch. Click to open support ticket for it to be manually resolved.'),
(317, 0, 'en', 'email-subscriptionsuspended-subject', 'Your subscription was suspended - {project}'),
(318, 0, 'en', 'email-subscriptionsuspended-message-text', 'Hello {username},\r\n\r\nYour {plan_name} subscription has been suspended due to one of the following reasons: Payment Charge-back; Refund; Security Issues; Manually disabled for unspecified reason.\r\n\r\nIf you would like a further explanation into why your subscription has been disabled, feel free to create a support ticket by clicking the link below.\r\n\r\n{create_support}\r\n\r\nThanks,\r\n{project}'),
(319, 0, 'en', 'email-subscriptionsuspended-message-html', 'Hello {username},\r\n<br /><br />\r\n\r\nYour {plan_name} subscription has been suspended due to one of the following reasons: Payment Charge-back; Refund; Security Issues; Manually disabled for unspecified reason.\r\n<br /><br />\r\n\r\nIf you would like a further explanation into why your subscription has been disabled, feel free to create a support ticket by <a href=\"{create_support}\">Clicking Here<a/>.\r\n<br /><br />\r\n\r\nThanks,\r\n{project}'),
(320, 0, 'en', 'notification-paypal-new-payment', 'New Payment via PayPal.'),
(321, 0, 'en', 'notification-paypal-bad-amount', 'New PayPal payment with input.'),
(322, 0, 'en', 'misc-processing-payment', 'Processing Payment'),
(323, 0, 'en', 'paypal-success-processed', 'Hello user. We have processed your payment, and it is available to be seen <a href=\"{payment_view}\">here</a>. Any subscriptions will be automatically added to your account. If you have any questions, feel free to contact us via support by <a href=\"{new_support}\">clicking here</a>.'),
(324, 1, 'en', 'paypal-success-processing', 'Please wait while we process your payment. We are currently waiting for a response from PayPal, so bare with us. If you would like to leave this page, feel free to. Your payment will be processed in the background.\r\n<br/><br/>\r\nIf your purchase was done using a credit card or bank account, this can take up to 5 week days for your bank to process the payment. Generally it will take a few minutes, but if it takes longer, we are sorry for the inconvenience.\r\n<br/><br/>\r\nIf you have any further questions, feel free to contact us via our support ticketing system. We will be sure to get back to you as quickly as possible.'),
(325, 0, 'en', 'title-login', 'Login - {project}'),
(326, 0, 'en', 'title-pp', 'Privacy Policy - {project}'),
(327, 0, 'en', 'title-recovery', 'Account Recovery - {project}'),
(328, 0, 'en', 'title-register', 'Registration - {project}'),
(329, 0, 'en', 'title-tos', 'Terms of Service - {project}'),
(330, 0, 'en', 'title-email-history', 'History of Email Addresses - {project}'),
(331, 0, 'en', 'title-password-history', 'Your Password History - {project}'),
(332, 0, 'en', 'title-lang-edit', 'Edit Lingual Phrase - {project}'),
(333, 0, 'en', 'title-lang-list', 'List Lingual Phrases - {project}'),
(334, 0, 'en', 'title-lang-new', 'New Lingual Phrase - {project}'),
(335, 0, 'en', 'title-lang-oudated', 'Phrases to be updated - {project}'),
(336, 0, 'en', 'title-lang-unfound', 'Lingual Phrases Pending Translation - {project}'),
(337, 0, 'en', 'title-user-query', 'Search Users - {project}'),
(338, 0, 'en', 'title-user-manage', '{user} Management - {project}'),
(339, 0, 'en', 'title-audit', 'Moderators Audit Logs - {project}'),
(340, 0, 'en', 'title-paypal-success', 'PayPal Transaction Processing - {project}'),
(341, 0, 'en', 'title-email-change', 'Email Change - {project}'),
(342, 0, 'en', 'title-password-change', 'Password Change - {project}'),
(343, 0, 'en', 'title-support-landing', 'Support - {project}'),
(344, 0, 'en', 'title-support-new', 'New Support Ticket - {project}'),
(345, 0, 'en', 'title-support-view', '{subject} - Support - {project}'),
(346, 0, 'en', 'title-additional-auth', 'Additional Authentication - {project}'),
(347, 0, 'en', 'title-mlt', 'Mailing List Terms - {project}'),
(348, 0, 'en', 'misc-address-too-long', 'Address too long'),
(349, 0, 'en', 'register-mlt-agree-snippet', 'Signup to <a href=\"{mlt}\">mailing list</a>.'),
(350, 0, 'en', 'misc-describe-contact-reason', 'Describe why you are contacting us.'),
(351, 0, 'en', 'oneword-name', 'Name'),
(352, 0, 'en', 'oneword-contact-us', 'Contact Us'),
(353, 0, 'en', 'title-contact-us', 'Contact Us - {project}'),
(354, 0, 'en', 'navbar-item-signout', 'Sign Out'),
(355, 0, 'en', 'misc-try-again', 'Please try agin'),
(356, 0, 'en', 'title-reauth', 'Reauthenticate  - {project}'),
(357, 0, 'en', 'misc-reauthenticate', 'Reauthenticate'),
(358, 0, 'en', 'misc-get-now', 'Get Now!'),
(359, 0, 'en', 'misc-signup-sub', 'Signup now!'),
(360, 0, 'en', 'misc-price-per', '{symbol}{price} per {duration}'),
(361, 0, 'en', 'misc-year', 'year'),
(362, 0, 'en', 'misc-num-years', '{num} years'),
(363, 0, 'en', 'misc-month', 'month'),
(364, 0, 'en', 'misc-num-months', '{num} months'),
(365, 0, 'en', 'misc-day', 'day'),
(366, 0, 'en', 'misc-num-days', '{num} days'),
(367, 0, 'en', 'misc-week', 'week'),
(368, 0, 'en', 'misc-num-weeks', '{num} weeks'),
(369, 0, 'en', 'misc-hour', 'hour'),
(370, 0, 'en', 'misc-num-hours', '{num} hours'),
(371, 0, 'en', 'oneword-coupon', 'Coupon'),
(372, 0, 'en', 'misc-create-new-thread', 'Create Thread'),
(373, 0, 'en', 'misc-payment-configuration', 'Payment Options'),
(374, 0, 'en', 'oneword-paypal', 'PayPal'),
(375, 0, 'en', 'title-payments-method', 'Payment Options - {project}'),
(376, 0, 'en', 'misc-purchase-tos-agreement', ' By proceeding with the purchase of {plan}, you agree and comply with both our <a href=\"{tos_url}\">Terms of Service</a> and <a href=\"{pp_url}\">Privacy Policy</a>.'),
(377, 0, 'en', 'tos-title', 'Terms of Service'),
(378, 0, 'en', 'tos-title-sub', 'This page was last edited on {date}.'),
(379, 0, 'en', 'tos-basic-compliance-title', 'Basic Compliance'),
(380, 0, 'en', 'tos-payments-title', 'Payments'),
(381, 0, 'en', 'tos-basic-compliance-1', 'These terms and conditions govern your use of this website; by using this website, you accept these terms and conditions in full and without reservation. If you disagree with these terms and conditions or any part of these terms and conditions, you must not use this website.'),
(382, 0, 'en', 'tos-basic-compliance-2', 'You must be at least 13 years of age to use this website. By using this website and by agreeing to these terms and conditions, you warrant and represent that you are at least 13 years of age.'),
(383, 0, 'en', 'tos-payments-1', 'You must be above the age of 18 to proceed with a purchase on this website. If you are not of the age of 18, you MUST gain parental consent.'),
(384, 0, 'en', 'tos-payments-2', 'You agree that by purchasing on this site, you reserve the right for us (\"{project}\") to terminate, reset, or otherwise disable your account.'),
(385, 0, 'en', 'tos-warranties-p1', 'This website is provided Ã¢â‚¬Å“as isÃ¢â‚¬Â without any representations or warranties, express or implied. Your site .com makes no representations or warranties in relation to this website or the information and materials provided on this website.'),
(386, 0, 'en', 'tos-warranties-p2', 'Without prejudice to the generality of the foregoing paragraph, {project} does not warrant that:'),
(387, 0, 'en', 'tos-warranties-ul1-li1', 'this website will be constantly available, or available at all; or'),
(388, 0, 'en', 'tos-warranties-ul1-li2', 'the information on this website is complete, true, accurate or non-misleading.'),
(389, 0, 'en', 'tos-warranties-ul1-li3', 'Nothing on this website constitutes, or is meant to constitute, advice of any kind. If you require advice in relation to any legal, financial or medical matter you should consult an appropriate professional.'),
(390, 0, 'en', 'tos-warranties-title', 'No Warranties'),
(391, 0, 'en', 'title-mod-support-list', 'Support Administration - {project}'),
(392, 0, 'en', 'misc-admin-support-page-header', 'Admin - Support Management'),
(393, 0, 'en', 'misc-hide-deleted', 'Hide Deleted'),
(394, 0, 'en', 'misc-show-deleted', 'Show Deleted'),
(395, 0, 'en', 'misc-hide-closed', 'Hide Closed'),
(396, 0, 'en', 'misc-show-closed', 'Show Closed'),
(397, 0, 'en', 'oneword-starter', 'Starter'),
(398, 0, 'en', 'title-mod-support-view', '{subject} - Admin Support - {project}'),
(399, 0, 'en', 'misc-open-thread', 'Reopen Thread'),
(400, 0, 'en', 'misc-close-thread', 'Close Thread'),
(401, 0, 'en', 'misc-delete-thread', 'Delete Thread'),
(402, 0, 'en', 'misc-undelete-thread', 'Undelete Thread'),
(403, 0, 'en', 'email-supportthreaddeleted-subject', 'Your support thread has been deleted - {project}'),
(404, 0, 'en', 'email-supportthreaddeleted-message-text', 'Hello {username},\r\n\r\nWe are sorry to inform you, but your thread entitled \"{thread_subject}\" has been deleted by administration. If you do not understand this administrative decision, please feel free to contact us at the link below!\r\n\r\n{new_ticket_url}\r\n\r\nRegards,\r\n{project}'),
(405, 0, 'en', 'email-supportthreaddeleted-message-html', 'Hello {username},\r\n<br/><br/>\r\nWe are sorry to inform you, but your thread entitled &quot;{thread_subject}&quot; has been deleted by administration. If you do not understand this administrative decision, please feel free to contact us <a href=\"{new_thread_url}\">here!</a>\r\n<br/><br/>\r\nRegards,\r\n<br/>\r\n{project}'),
(406, 0, 'en', 'email-supportthreadundeleted-subject', 'Support Ticket Undeleted - {project}'),
(407, 0, 'en', 'email-supportthreadundeleted-message-text', 'Hello {username},\r\n\r\nWe are pleased to inform you that your support ticket entitled \"{thread_subject}\" has been undeleted by our administration. The link to your ticket can be found below!\r\n\r\n{ticket_url}\r\n\r\nRegards,\r\n{project}'),
(408, 0, 'en', 'email-supportthreadundeleted-message-html', 'Hello {username},\r\n<br/><br/>\r\n\r\nWe are pleased to inform you that your support ticket entitled &quot;{thread_subject}&quot; has been undeleted by our administration. <a href=\"{ticket_url}\">Click Here</a> to visit your support thread\r\n<br/><br/>\r\n\r\nRegards,\r\n<br/>\r\n{project}'),
(409, 0, 'en', 'audit-admin-thread-closed', 'Closed support ticket (id: {ticket_id}) &quot;{thread_subject}&quot;'),
(410, 0, 'en', 'audit-admin-thread-opened', 'Opened support ticket (id: {ticket_id}) &quot;{thread_subject}&quot;'),
(411, 0, 'en', 'audit-admin-thread-deleted', 'Deleted support ticket (id: {ticket_id}) &quot;{thread_subject}&quot;'),
(412, 0, 'en', 'audit-admin-thread-undeleted', 'Undeleted support ticket (id: {ticket_id}) &quot;{thread_subject}&quot;'),
(413, 0, 'en', 'misc-audit-logs', 'Audit Logs'),
(414, 0, 'en', 'misc-password-and-email-history', 'Password & Email History'),
(415, 0, 'en', 'misc-payments', 'Payments'),
(416, 0, 'en', 'misc-data-usage', 'Data Usage'),
(417, 0, 'en', 'misc-information', 'Information'),
(418, 0, 'en', 'title-paypal-cancel', 'PayPal Payment Canceled - {project}'),
(419, 0, 'en', 'misc-feedback', 'Feedback'),
(420, 0, 'en', 'missc-pp-cancel-text', 'We noticed that you didn\'t want to proceed with your purchase. Is there a particular reason why? Do you feel we are missing a feature? If you got a minute, tell us why at the form below! We seriously appreciate all feedback!'),
(421, 0, 'en', 'title-feedback', 'All Feedback  - {project}'),
(422, 0, 'en', 'oneword-body', 'Body'),
(423, 0, 'en', 'mod-feedback-title', 'Feedback'),
(424, 0, 'en', 'oneword-notification', 'Notification'),
(425, 0, 'en', 'oneword-notifications', 'Notifications'),
(426, 0, 'en', 'title-payments-list', 'Payment History - {project}'),
(427, 0, 'en', 'oneword-complete', 'Complete\r\n'),
(428, 0, 'en', 'misc-chargeback-processing', 'Chargeback (Processing)'),
(429, 0, 'en', 'misc-chargeback', 'Chargeback'),
(430, 0, 'en', 'misc-failed', 'Failed'),
(431, 0, 'en', 'misc-refunded', 'Refunded'),
(432, 0, 'en', 'misc-bad-input', 'Bad Input'),
(433, 0, 'en', 'misc-chargeback-reversal', 'Chargeback Reversal'),
(434, 0, 'en', 'misc-unknown', 'Unknown'),
(435, 0, 'en', 'oneword-gateway', 'Gateway'),
(436, 0, 'en', 'oneword-amount', 'Amount'),
(437, 0, 'en', 'misc-billing-history', 'Billing History'),
(438, 0, 'en', 'title-plans-list', 'Plans - {project}'),
(439, 0, 'en', 'purchase-constant-support', '24/7 Support'),
(440, 0, 'en', 'purchase-servers-worldwide', 'Servers Worldwide'),
(441, 0, 'en', 'purchase-active-development', 'Active Development'),
(442, 0, 'en', 'purchase-constant-uptime', '100% availability Guaranteed'),
(443, 0, 'en', 'purchase-cross-platform', 'Cross-platform Support'),
(444, 0, 'en', 'purchase-bast-value', 'Best Value!'),
(445, 0, 'en', 'purchase-unlimited', 'Unlimited data'),
(446, 0, 'en', 'purchase-no-traffic-logs', 'No traffic logs'),
(447, 0, 'en', 'oneword-history', 'History'),
(448, 0, 'en', 'redeem-giftcard-title', 'Redeem Giftcard'),
(449, 0, 'en', 'oneword-giftcode', 'Giftcode'),
(450, 0, 'en', 'misc-giftcode-disabled', 'Giftcode Disabled'),
(451, 0, 'en', 'misc-giftcode-expired', 'Giftcode expired'),
(452, 0, 'en', 'misc-goftcode-generate-too-much', 'Cannot generate that many giftcodes'),
(453, 0, 'en', 'title-redeem-giftcard', 'Redeem Giftcard - {project}'),
(454, 0, 'en', 'misc-giftcode-not-found', 'Giftcode not found'),
(455, 0, 'en', 'title-mod-giftcard', 'Giftcard Admin Panel - {project}'),
(456, 0, 'en', 'misc-generate-giftcards', 'Generate Giftcards'),
(457, 0, 'en', 'oneword-count', 'Count'),
(458, 0, 'en', 'oneword-message', 'Message'),
(459, 0, 'en', 'oneword-plan', 'Plan'),
(460, 0, 'en', 'misc-message-optional', 'Message (Optional)'),
(461, 0, 'en', 'audit-admin-deleteffrpcnode', 'Deleted {node_id} {type} {endpoint}:{port}'),
(462, 0, 'en', 'audit-admin-newffrpcnode', 'New {type} {endpoint}:{port}'),
(463, 0, 'en', 'misc-ff-rpc', 'FF RPC'),
(464, 0, 'en', 'oneword-type', 'Type'),
(465, 0, 'en', 'oneword-endpoint', 'Endpoint'),
(466, 0, 'en', 'oneword-port', 'Port'),
(467, 0, 'en', 'misc-endpoint-example', '127.0.0.1 or endpoint.example.com'),
(468, 0, 'en', 'title-ffrpc-new', 'New FF-RPC Node - {project}'),
(469, 0, 'en', 'mod-ffrpc-new-title', 'New FF-RPC Node'),
(470, 0, 'en', 'mod-ffrpc-list', 'FF-RPC Nodes'),
(471, 0, 'en', 'title-ffrpc-nodes', 'FF-RPC Nodes - {project}'),
(472, 0, 'en', 'oneword-auth-token', 'Authentication Token'),
(473, 0, 'en', 'misc-hover', 'Hover to view'),
(474, 0, 'en', 'mod-ffrpc-list-create-new', 'Register new Node'),
(475, 0, 'en', 'purchase-concurrent-connections', '{concurrent-users} Concurrent Connections'),
(476, 0, 'en', 'audit-admin-announcement', 'New announcement &quot;{subject}&quot;'),
(477, 0, 'en', 'title-mod-announcement', 'Announcement - {project}'),
(478, 0, 'en', 'misc-new-announcement', 'Create new announcement'),
(479, 0, 'en', 'oneword-duration', 'Duration'),
(480, 0, 'en', 'misc-1-day', '1 day'),
(481, 0, 'en', 'misc-1-week', '1 week'),
(482, 0, 'en', 'misc-1-month', '1 month'),
(483, 0, 'en', 'misc-1-year', '1 year'),
(484, 0, 'en', 'misc-3-months', '3 months'),
(485, 0, 'en', 'misc-6-months', '6 months'),
(486, 0, 'en', 'oneword-announcement', 'Announcement'),
(487, 0, 'en', 'cp-landing-view-other-announcements', 'View other announcements'),
(488, 0, 'en', 'misc-send-email', 'Send Email'),
(489, 0, 'en', 'audit-admin-sendmail', '{username} received admin email entitled &quot;{subject}&quot;'),
(490, 0, 'en', 'misc-not-pending-email-verification', 'You have already verified your email.'),
(491, 0, 'en', 'title-android-install', 'Android Installation - {project}'),
(492, 0, 'en', 'title-ios-install', 'IOS Installation - {project}'),
(493, 0, 'en', 'title-linux-install', 'Linux Installation - {project}'),
(494, 0, 'en', 'title-osx-install', 'OSX Installation - {project}'),
(495, 0, 'en', 'title-windows-install', 'Windows Installation - {project}'),
(496, 0, 'en', 'installsidebar-about', 'About'),
(497, 0, 'en', 'installsidebar-about-paragraph', 'Installation guides for {project}.'),
(498, 0, 'en', 'installsidebar-platforms', 'Platforms'),
(499, 0, 'en', 'oneword-windows', 'Windows'),
(500, 0, 'en', 'oneword-android', 'Android'),
(501, 0, 'en', 'oneword-mac-osx', 'Mac OSX'),
(502, 0, 'en', 'oneword-linux', 'Linux'),
(503, 0, 'en', 'oneword-ios', 'IOS'),
(504, 0, 'en', 'oneword-downloads', 'Downloads'),
(505, 0, 'en', 'oneword-windows-package', 'Windows Package');
INSERT INTO `phrases` (`id`, `rev`, `language_code`, `phrase_name`, `phrase`) VALUES
(506, 0, 'en', 'title-mod-new-package', 'New Package - {project}'),
(507, 0, 'en', 'oneword-version', 'Version'),
(508, 0, 'en', 'oneword-package', 'Package'),
(509, 0, 'en', 'misc-upload-package', 'Upload Package'),
(510, 0, 'en', 'misc-platform', 'Platform'),
(511, 0, 'en', 'misc-storage-consumption', 'Storage Consumption'),
(512, 0, 'en', 'misc-count', 'Count'),
(513, 0, 'en', 'misc-latest-version', 'Latest Version'),
(514, 0, 'en', 'audit-admin-uploadpackage', 'Uploaded package for {platform}, version {version}. (id: {id})'),
(515, 0, 'en', 'misc-package-list', 'Package List'),
(516, 0, 'en', 'misc-new-package', 'New Package'),
(517, 0, 'en', 'oneword-creator', 'Creator'),
(518, 0, 'en', 'oneword-size', 'Size'),
(519, 0, 'en', 'oneword-download', 'Download'),
(520, 0, 'en', 'title-mod-package-list', 'Package List - {project}'),
(521, 0, 'en', 'title-mod-node-new', 'New Node - {project}'),
(522, 0, 'en', 'misc-new-node', 'New Node'),
(523, 0, 'en', 'misc-country-iso-2', 'Country (ISO 2)'),
(524, 0, 'en', 'oneword-country', 'Country'),
(525, 0, 'en', 'other-enable-openvpn', 'Enable OpenVPN'),
(526, 0, 'en', 'oneword-hostname', 'Hostname'),
(527, 0, 'en', 'oneword-ip', 'IP'),
(528, 0, 'en', 'oneword-city', 'City'),
(529, 0, 'en', 'oneword-true', 'True'),
(530, 0, 'en', 'oneword-false', 'False'),
(531, 0, 'en', 'oneword-openvpn-ca', 'CA'),
(532, 0, 'en', 'oneword-openvpn-cert', 'Cert'),
(533, 0, 'en', 'oneword-openvpn-key', 'Key'),
(534, 0, 'en', 'other-openvpn-tls-auth', 'TLS Auth (Optional)'),
(535, 0, 'en', 'oneword-openvpn-tls-auth', 'TLS Auth'),
(536, 0, 'en', 'other-openvpn-tls-crypt', 'TLS Crypt (Optional)'),
(537, 0, 'en', 'oneword-openvpn-tls-crypt', 'TLS Crypt'),
(538, 0, 'en', 'oneword-openvpn-auth', 'Auth'),
(539, 0, 'en', 'oneword-openvpn-cipher', 'Cipher'),
(540, 0, 'en', 'oneword-openvpn-tls-cipher', 'TLS Cipher'),
(541, 0, 'en', 'oneword-openvpn-compression', 'Compression'),
(542, 0, 'en', 'oneword-protocol', 'Protocol'),
(543, 0, 'en', 'oneword-openvpn-port', 'Port'),
(544, 0, 'en', 'title-credits', 'Licenses & Credits - {project}'),
(545, 0, 'en', 'footer-information-credits', 'Licenses & Credits'),
(546, 0, 'en', 'landing-catch-phrase', 'Are you ready to get hold of your privacy?'),
(547, 0, 'en', 'landing-enter-email', 'Enter your email...'),
(548, 0, 'en', 'landing-signup', 'Signup!'),
(549, 0, 'en', 'landing-speed', 'Speed'),
(550, 0, 'en', 'landing-speed-description', 'Our network of servers spreadout over the entire world have an estimated throughput limit of 50gbp/s'),
(551, 0, 'en', 'landing-privacy', 'Your privacy'),
(552, 0, 'en', 'landing-privacy-description', 'Your privacy is important to us. We store absolutely 0 traffic logs.'),
(553, 0, 'en', 'landing-simplicity', 'Simplicity'),
(554, 0, 'en', 'landing-simplicity-description', 'We make complexity simple. With us, nothing is impossible.'),
(555, 0, 'en', 'landing-games-optim', 'Optimized for Games'),
(556, 0, 'en', 'landing-games-optim-desc', 'Our complex array inter-connected servers have been engineered to deliver the best performance possible. In some cases, we improve internet speeds due to our implemented compression algorithms'),
(557, 0, 'en', 'landing-privacy-desc', 'To provide the best service possible, without invading privacy, we have designed our core infrastructure with privacy in mind. If someone gained physical access to our datacenters, your data would still remain secure.'),
(558, 0, 'en', 'landing-gradea-support', 'Grade A+ Support'),
(559, 0, 'en', 'landing-gradea-support-desc', 'Our support is one of the best in the field. We got teams of people, all around the globe, dedicated to helping you have the best service possible!'),
(560, 0, 'en', 'landing-review-title', 'What people are saying...'),
(561, 0, 'en', 'oneword-stars', 'Stars'),
(562, 0, 'en', 'title-review', 'Review - {project}'),
(563, 0, 'en', 'review-title', 'New Review'),
(564, 0, 'en', 'other-subject-to-review', 'Note that all reviews are subject to review.'),
(565, 0, 'en', 'oneword-review', 'Review'),
(566, 0, 'en', 'misc-review-created', 'Your review has been added to approval queue.'),
(567, 0, 'en', 'misc-show-approved', 'Show Approved'),
(568, 0, 'en', 'misc-hide-approved', 'Hide Approved'),
(569, 0, 'en', 'oneword-pending-action', 'Pending Action'),
(570, 0, 'en', 'mod-reviews-title', 'Reviews'),
(571, 0, 'en', 'title-mod-reviews', 'Administration Control for Reviews - {project}'),
(572, 0, 'en', 'oneword-approved', 'Aproved'),
(573, 0, 'en', 'audit-admin-review-undeleted', 'Review by {writer} undeleted'),
(574, 0, 'en', 'notif-review-approved', 'Your review has been approved.'),
(575, 0, 'en', 'notif-review-undeleted', 'Your review has been undeleted.'),
(576, 0, 'en', 'notif-review-deleted', 'Your review has been deleted.'),
(577, 0, 'en', 'oneword-delete', 'Delete'),
(578, 0, 'en', 'oneword-approve', 'Approve'),
(579, 0, 'en', 'oneword-undelete', 'Undelete'),
(580, 0, 'en', 'oneword-reviews', 'Reviews'),
(581, 0, 'en', 'title-status-page', 'Status Page - {project}'),
(582, 0, 'en', 'oneword-status-page', 'Status Page'),
(583, 0, 'en', 'notice-paypal-test-ipn', '<strong>This is a test transaction!</strong> This is not genuine, and was sent via PalPal\'s test IPN.'),
(584, 0, 'en', 'title-payments-view', '{id} Transaction - {project}'),
(585, 0, 'en', 'payment-view-h', 'Receipt for purchase #{id}'),
(586, 0, 'en', 'oneword-payment-information', 'Payment Information'),
(587, 0, 'en', 'payment-view-support', 'If you have any enquiries feel free to contact us via a <a href=\"{support-create-url}\">support ticket</a>. We have dedicated staff here to help resolve any issues you may encounter.'),
(588, 0, 'en', 'oneword-plan-is-disabled', 'Plan is disabled'),
(589, 0, 'en', 'oneword-method', 'Method'),
(590, 0, 'en', 'oneword-payment-status', 'Payment Status'),
(591, 0, 'en', 'oneword-transaction', 'Transaction'),
(592, 0, 'en', 'oneword-your-email', 'Your Email'),
(593, 0, 'en', 'oneword-you-paid', 'You Paid'),
(594, 0, 'en', 'oneword-plan-name', 'Plan Name'),
(595, 0, 'en', 'oneword-price', 'Price'),
(596, 0, 'en', 'oneword-avg-month-price', 'Avg. Monthly Price'),
(597, 0, 'en', 'oneword-item-name', 'Item Name'),
(598, 0, 'en', 'oneword-item-price', 'Item Price'),
(599, 0, 'en', 'oneword-item-quantity', 'Item Quantity'),
(600, 0, 'en', 'oneword-total', 'Total'),
(601, 0, 'en', 'oneword-subtotal', 'Subtotal'),
(602, 0, 'en', 'oneword-affiliation', 'Affiliation'),
(603, 0, 'en', 'oneword-order-summary', 'Order Summary'),
(604, 0, 'en', 'payments-subtotal-indication', 'Subtotal: {total}'),
(605, 0, 'en', 'payments-plan-heading', '<i>{name}</i> Plan'),
(606, 0, 'en', 'payments-non-discountable', 'This plan is not eligible for discounts'),
(607, 0, 'en', 'email-history-title', 'Email History'),
(608, 0, 'en', 'oneword-was-valid', 'Was Valid'),
(609, 0, 'en', 'oneword-new-email', 'New Email'),
(610, 0, 'en', 'misc-email-verif-sent', '<strong>Success!</strong> We sent your email verification.'),
(611, 0, 'en', 'misc-email-verif-failed', '<strong>Failed!</strong> We were unable to send an email verification to your email!'),
(612, 0, 'en', 'meta-og-login-title', 'Login - {name}'),
(613, 0, 'en', 'meta-og-login-description', 'Click to view the login page for {name}, a highly efficient VPN that offers nothing but the best!'),
(614, 0, 'en', 'meta-og-tos-title', 'Terms of Service - {name}'),
(615, 0, 'en', 'meta-og-tos-description', 'Terms of Service page for {name}. Contains policies, and essential information.'),
(616, 0, 'en', 'meta-og-status-title', 'Status Page - {name}'),
(617, 0, 'en', 'meta-og-status-description', 'Operational service state for {name}.'),
(618, 0, 'en', 'meta-og-register-title', 'Registration - {name}'),
(619, 0, 'en', 'meta-og-register-description', 'Registration for {name}, a premium and reliable VPN.'),
(620, 0, 'en', 'meta-og-recovery-title', 'Recovery - {name}'),
(621, 0, 'en', 'meta-og-recovery-description', 'Account recovery for {name}. '),
(622, 0, 'en', 'page-recovery-paragraph', 'If you have forgotten your username or password, you can request to have your email emailed to you and a reset to your password. When you fill your registered email address, you will be sent an email instructing you how to recover your account.'),
(623, 0, 'en', 'meta-og-pp-description', 'Privacy policy page for {name}. This page contains all information regarding your privacy.'),
(624, 0, 'en', 'meta-og-pp-title', 'Privacy Policy - {name}'),
(625, 0, 'en', 'meta-og-credits-title', 'Credits and Licenses for {name}'),
(626, 0, 'en', 'meta-og-credits-description', 'Page containing licenses and credits for {name}.'),
(627, 0, 'en', 'meta-og-contact-title', 'Contact Page - {name}'),
(628, 0, 'en', 'meta-og-contact-description', 'Contact Page for {name}. All enquiries and requests should be made via this page.'),
(629, 0, 'en', 'oneword-guides', 'Guides'),
(630, 0, 'en', 'page-reauth-paragraph', 'You are shown this page because your account has been logged out. In order to access the account again, you are required to enter the password. The account you\'re trying to access is named <i>{account-name}</i>.'),
(631, 0, 'en', 'oneword-password-history', 'Password History'),
(632, 0, 'en', 'oneword-email-history', 'Email History'),
(633, 0, 'en', 'payment-method-concurrent', '{number} Concurrent connections'),
(634, 0, 'en', 'giftcard-activation-msg-indicator', 'Activation Message:'),
(635, 0, 'en', 'change-email-paragraph', 'Enter your new email address, and existing password. You are required to enter your password to ensure the security of your account. If you\'ve having any issues, contact support by <a href=\"{support-url}\">clicking here</a>.'),
(636, 0, 'en', 'oneword-home', 'Home'),
(637, 0, 'en', 'cp-landing-account-details', 'Account Details'),
(638, 0, 'en', 'cp-landing-username-indication', 'Username:'),
(639, 0, 'en', 'cp-landing-email-indication', 'Email:'),
(640, 0, 'en', 'cp-landing-subscription-indication', 'Subscription:'),
(641, 0, 'en', 'cp-landing-subscription-exp-indication', 'Subscription Expiration:'),
(642, 0, 'en', 'oneword-expired', 'Expired'),
(643, 0, 'en', 'cp-landing-user-group-indication', 'User Group:'),
(644, 0, 'en', 'cp-landing-local-time-indication', 'Local Time:'),
(645, 0, 'en', 'cp-landing-data-usage', 'Data Usage'),
(646, 0, 'en', 'util-agreement', 'By proceeding, you agree with both our <a href=\"{tos_url}\">Terms of Service</a> and <a href=\"{pp_url}\">Privacy Policy</a>.'),
(647, 0, 'en', 'audit-admin-publicsupportstatus', 'Public support entitled &quot;{title}&quot; had a status update. New Status: {status} (ID: {id})'),
(648, 0, 'en', 'email-publicsupportverification-subject', 'Public support verification for {project}'),
(649, 0, 'en', 'email-publicsupportverification-message-text', 'Hello {name},\r\n\r\nWe have received a request for a support ticket. You are receiving this email as validation that you requested this support. If you do not remember requesting a ticket, ignore this message. Otherwise click the link below.\r\n\r\n{validate_url}\r\n\r\nRegards,\r\n{project}'),
(650, 0, 'en', 'email-publicsupportverification-message-html', 'Hello {name},\r\n<br/><br/>\r\n\r\nWe have received a request for a support ticket. You are receiving this email as validation that you requested this support. If you do not remember requesting a ticket, ignore this message. Otherwise <a href=\"{validate_url}\">click here</a> to validate request.\r\n<br/><br/>\r\n\r\nRegards,\r\n<br/>\r\n{project}'),
(651, 0, 'en', 'misc-name-too-long', 'Name too long'),
(652, 0, 'en', 'misc-contact-validated', 'Your email has been validated. Now allow time for administration to check the enquiry.'),
(653, 0, 'en', 'navbar-item-register', 'Register');

CREATE TABLE `postlimiter` (
  `name` varchar(32) NOT NULL,
  `date` int(10) UNSIGNED NOT NULL,
  `ip` varchar(39) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `reviews` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `date` int(10) UNSIGNED NOT NULL,
  `language_code` varchar(2) NOT NULL,
  `stars` int(10) UNSIGNED NOT NULL,
  `approved` tinyint(1) NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  `body` varchar(512) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `security_tokens` (
  `id` int(10) UNSIGNED NOT NULL,
  `session_id` int(10) UNSIGNED NOT NULL,
  `ip` varchar(39) NOT NULL,
  `date` int(10) UNSIGNED NOT NULL,
  `expiry` int(10) UNSIGNED NOT NULL,
  `token` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `sessions` (
  `id` int(10) UNSIGNED NOT NULL,
  `token` varchar(256) CHARACTER SET utf8 NOT NULL,
  `date` int(10) UNSIGNED DEFAULT NULL,
  `expiry` int(10) UNSIGNED DEFAULT NULL,
  `active_link_id` int(10) UNSIGNED NOT NULL,
  `language_code` varchar(2) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `session_links` (
  `id` int(10) UNSIGNED NOT NULL,
  `session_id` int(10) UNSIGNED DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `pending_auth` varchar(32) CHARACTER SET utf8 NOT NULL,
  `require_reauth` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `subscription_plans` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(16) NOT NULL,
  `currency` varchar(4) NOT NULL,
  `price` float NOT NULL,
  `duration` int(10) UNSIGNED NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `discountable` tinyint(1) NOT NULL,
  `maximum_concurrent_connections` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `support_posts` (
  `id` int(10) UNSIGNED NOT NULL,
  `thread_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `date` int(10) UNSIGNED NOT NULL,
  `is_deleted` tinyint(1) NOT NULL,
  `body` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `support_public` (
  `id` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `email` varchar(256) NOT NULL,
  `email_verif` varchar(32) NOT NULL,
  `subject` varchar(512) NOT NULL,
  `body` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `support_threads` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL COMMENT 'Creator of thread',
  `subject` varchar(126) NOT NULL,
  `date` int(11) NOT NULL,
  `last_post_date` int(10) UNSIGNED NOT NULL,
  `is_closed` tinyint(1) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tasks` (
  `id` int(10) UNSIGNED NOT NULL,
  `taskjob_object` mediumtext,
  `date` int(10) UNSIGNED NOT NULL,
  `run_count` int(10) UNSIGNED NOT NULL,
  `is_running` tinyint(1) NOT NULL,
  `has_complete` int(1) DEFAULT NULL,
  `task_handler_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `task_handlers` (
  `id` int(10) UNSIGNED NOT NULL,
  `date` int(10) UNSIGNED NOT NULL,
  `last_seen` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(32) CHARACTER SET utf8 NOT NULL,
  `username_lower` varchar(32) CHARACTER SET utf8 NOT NULL,
  `email` varchar(256) CHARACTER SET utf8 NOT NULL,
  `email_valid` tinyint(1) NOT NULL,
  `group_id` int(10) UNSIGNED NOT NULL,
  `password` varchar(512) CHARACTER SET utf8 NOT NULL,
  `node_auth` varchar(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `user_auth` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `date` int(10) UNSIGNED NOT NULL,
  `method` varchar(32) NOT NULL,
  `context` varchar(4096) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `user_settings` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `time_difference` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `user_subscriptions` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `subscrption_plan_id` int(10) UNSIGNED NOT NULL,
  `date` int(10) UNSIGNED NOT NULL,
  `expiry` int(10) UNSIGNED NOT NULL,
  `enabled` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `vpn_nodes` (
  `id` int(10) UNSIGNED NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `country` varchar(2) NOT NULL,
  `city` varchar(32) NOT NULL,
  `ip` varchar(39) NOT NULL,
  `hostname` varchar(256) NOT NULL,
  `has_ovpn` tinyint(1) NOT NULL,
  `has_pptp` tinyint(1) NOT NULL,
  `maximum_load` int(10) UNSIGNED NOT NULL,
  `ovpn_ca` text NOT NULL,
  `ovpn_cert` text NOT NULL,
  `ovpn_key` text NOT NULL,
  `ovpn_tls_auth` text NOT NULL,
  `ovpn_tls_crypt` text NOT NULL,
  `ovpn_auth` varchar(8) NOT NULL DEFAULT 'SHA512',
  `ovpn_cipher` varchar(16) DEFAULT 'AES-256-CBC',
  `ovpn_tls_cipher` varchar(64) NOT NULL,
  `ovpn_compression` varchar(16) NOT NULL,
  `ovpn_protocol` varchar(3) NOT NULL,
  `ovpn_port` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `admin_audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `autoapi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `token` (`token`);

ALTER TABLE `cache`
  ADD KEY `key` (`key`);

ALTER TABLE `connections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `node_id` (`node_id`);

ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `code` (`code`);

ALTER TABLE `email_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `email_verification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `token` (`token`(255)),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `ff_rpc`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type` (`type`);

ALTER TABLE `general_feedback`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `user_id` (`user_id`) USING BTREE,
  ADD KEY `date` (`date`) USING BTREE;

ALTER TABLE `giftcodes`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `mailing_list`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `password_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `password_reset`
  ADD PRIMARY KEY (`id`),
  ADD KEY `token` (`token`);

ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `payments_state_id` (`payments_state_id`);

ALTER TABLE `payment_state`
  ADD PRIMARY KEY (`id`),
  ADD KEY `token` (`token`);

ALTER TABLE `phrases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `language_code` (`language_code`),
  ADD KEY `phrase_name` (`phrase_name`);

ALTER TABLE `postlimiter`
  ADD KEY `name` (`name`) USING BTREE,
  ADD KEY `ip` (`ip`);

ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `approved` (`approved`);

ALTER TABLE `security_tokens`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `token` (`token`) USING BTREE,
  ADD KEY `session_id` (`session_id`);

ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `token` (`token`(255));

ALTER TABLE `session_links`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `subscription_plans`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `support_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `thread_id` (`thread_id`);

ALTER TABLE `support_public`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `support_threads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `date` (`date`),
  ADD KEY `task_handler_id` (`task_handler_id`);

ALTER TABLE `task_handlers`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `username` (`username`),
  ADD KEY `username_lower` (`username_lower`);

ALTER TABLE `user_auth`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `method` (`method`);

ALTER TABLE `user_settings`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_id` (`user_id`) USING BTREE;

ALTER TABLE `user_subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `vpn_nodes`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `admin_audit_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `announcements`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `autoapi`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `connections`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `coupons`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `email_history`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `email_verification`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `ff_rpc`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `general_feedback`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `giftcodes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `groups`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `mailing_list`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `packages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `password_history`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `password_reset`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `payments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `payment_state`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `phrases`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=654;

ALTER TABLE `reviews`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `security_tokens`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `sessions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `session_links`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `subscription_plans`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `support_posts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `support_public`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `support_threads`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `tasks`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `task_handlers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `user_auth`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `user_subscriptions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=137;

ALTER TABLE `vpn_nodes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

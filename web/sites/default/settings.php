<?php

global $content_directories;

/**
 * Load services definition file.
 */
$settings['container_yamls'][] = __DIR__ . '/services.yml';



/**
 * Skipping permissions hardening will make scaffolding
 * work better, but will also raise a warning when you
 * install Drupal.
 *
 * https://www.drupal.org/project/drupal/issues/3091285
 */
// $settings['skip_permissions_hardening'] = TRUE;

/**
 * If there is a local settings file, then include it
 */
$local_settings = __DIR__ . "/settings.local.php";
if (file_exists($local_settings)) {
  include $local_settings;
}
$settings['config_sync_directory'] = dirname(DRUPAL_ROOT) . '/config';

$content_directories['sync'] = dirname(DRUPAL_ROOT) . '/contentsync';

$settings['hash_salt'] = 'Xvkff_KYsw4TPclyLDd00_eXc0HPyivmf2OYmiO39fz6UNAgQwz09yfro7D1KbK4mQHtZe4_BQ';

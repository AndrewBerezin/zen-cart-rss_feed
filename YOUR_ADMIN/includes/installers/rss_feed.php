<?php
/**
 * rss_feed.php
 *
 * @package rss feed
 * @copyright Copyright 2004-2015 Andrew Berezin eCommerce-Service.com
 * @copyright Portions Copyright 2003-2015 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: rss_feed.php, v 2.4.2 29.03.2015 18:19:02 AndrewBerezin $
 */

$current_version = '2.4.2';

if (!defined('RSS_FEED_VERSION') || RSS_FEED_VERSION != $current_version) {

  $install_type = 'install';

  $sql = "SELECT configuration_group_id
          FROM " . TABLE_CONFIGURATION . "
          WHERE configuration_key LIKE 'RSS\_FEED\_%'
            AND configuration_group_id != 6
          LIMIT 1";
  $configuration_group = $db->Execute($sql);
  if (!$configuration_group->EOF) {
    $configuration_group_id = $configuration_group->fields['configuration_group_id'];
  } else {
    $sql = "INSERT INTO " . TABLE_CONFIGURATION_GROUP . " (configuration_group_id, configuration_group_title, configuration_group_description, sort_order, visible) VALUES (NULL, :language_key:, :configuration_group_description:, '1', '1')";
    $sql = $db->bindVars($sql, ':language_key:', RSS_FEED_CONFIGURATION_GROUP_TITLE, 'string');
    $sql = $db->bindVars($sql, ':configuration_group_description:', RSS_FEED_CONFIGURATION_GROUP_DESCRIPTION, 'string');
    $db->Execute($sql);
    $configuration_group_id = $db->insert_ID();
    $sql = "UPDATE " . TABLE_CONFIGURATION_GROUP . " SET sort_order = :configuration_group_id: WHERE configuration_group_id = :configuration_group_id: LIMIT 1";
    $sql = $db->bindVars($sql, ':configuration_group_id:', $configuration_group_id, 'integer');
    $db->Execute($sql);
    define('RSS_FEED_VERSION', $current_version);
  }

  require_once(DIR_WS_CLASSES . 'ecs_plugin.php');
  $install = new ecs_plugin($configuration_group_id);

  $install->add_configuration('RSS_FEED_VERSION', $current_version, NULL, 'zen_cfg_read_only(', -10);

  $sort_order = 10;
  $install->add_configuration('RSS_FEED_TITLE', '', NULL, NULL, $sort_order);
  $sort_order++;
  $install->add_configuration('RSS_FEED_DESCRIPTION', '', NULL, NULL, $sort_order);
  $sort_order++;
  $install->add_configuration('RSS_FEED_IMAGE', '', NULL, NULL, $sort_order);
  $sort_order++;
  $install->add_configuration('RSS_FEED_COPYRIGHT', '', NULL, NULL, $sort_order);
  $sort_order++;
  $install->add_configuration('RSS_FEED_MANAGING_EDITOR', '', NULL, NULL, $sort_order);
  $sort_order++;
  $install->add_configuration('RSS_FEED_WEBMASTER', '', NULL, NULL, $sort_order);
  $sort_order++;
  $install->add_configuration('RSS_FEED_AUTHOR', '', NULL, NULL, $sort_order);
  $sort_order++;

  $sort_order = 20;
  $install->add_configuration('RSS_FEED_HOMEPAGE_FEED', 'new_products', NULL, 'zen_cfg_select_option(array(\'news\', \'new_products\', \'upcoming\', \'featured\', \'specials\', \'products\', \'categories\'),', $sort_order);
  $sort_order++;
  $install->add_configuration('RSS_FEED_DEFAULT_FEED', 'new_products', NULL, 'zen_cfg_select_option(array(\'news\', \'new_products\', \'upcoming\', \'featured\', \'specials\', \'products\', \'categories\'),', $sort_order);
  $sort_order++;
  $install->add_configuration('RSS_FEED_STRIP_TAGS', 'false', NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),', $sort_order);
  $sort_order++;
  $install->add_configuration('RSS_FEED_ITEMS_DESCRIPTION', 'true', NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),', $sort_order);
  $sort_order++;
  $install->add_configuration('RSS_FEED_ITEMS_DESCRIPTION_MAX_LENGTH', '0', NULL, NULL, $sort_order);
  $sort_order++;
  $install->add_configuration('RSS_FEED_TTL', '1440', NULL, NULL, $sort_order);
  $sort_order++;

  $sort_order = 20;
  $install->add_configuration('RSS_FEED_PRODUCTS_LIMIT', '10', NULL, NULL, $sort_order);
  $sort_order++;
  $install->add_configuration('RSS_FEED_PRODUCTS_DESCRIPTION_IMAGE', 'true', NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),', $sort_order);
  $sort_order++;
  $install->add_configuration('RSS_FEED_PRODUCTS_DESCRIPTION_BUYNOW', 'true', NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),', $sort_order);
  $sort_order++;


  $sort_order = 30;
  $install->add_configuration('RSS_FEED_PRODUCTS_CATEGORIES', 'master', NULL, 'zen_cfg_select_option(array(\'master\', \'all\'),', $sort_order);
  $sort_order++;

  $sort_order = 40;
  $install->add_configuration('RSS_FEED_PRODUCTS_PRICE', 'true', NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),', $sort_order);
  $sort_order++;
  $install->add_configuration('RSS_FEED_PRODUCTS_ID', 'true', NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),', $sort_order);
  $sort_order++;
  $install->add_configuration('RSS_FEED_PRODUCTS_WEIGHT', 'true', NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),', $sort_order);
  $sort_order++;
  $install->add_configuration('RSS_FEED_PRODUCTS_BRAND', 'true', NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),', $sort_order);
  $sort_order++;
  $install->add_configuration('RSS_FEED_PRODUCTS_CURRENCY', 'true', NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),', $sort_order);
  $sort_order++;
  $install->add_configuration('RSS_FEED_PRODUCTS_QUANTITY', 'true', NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),', $sort_order);
  $sort_order++;
  $install->add_configuration('RSS_FEED_PRODUCTS_MODEL', 'true', NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),', $sort_order);
  $sort_order++;
  $install->add_configuration('RSS_FEED_PRODUCTS_RATING', 'true', NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),', $sort_order);
  $sort_order++;
  $install->add_configuration('RSS_FEED_PRODUCTS_IMAGES', 'true', NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),', $sort_order);
  $sort_order++;
  $install->add_configuration('RSS_FEED_DEFAULT_IMAGE_SIZE', 'large', NULL, 'zen_cfg_select_option(array(\'small\', \'medium\', \'large\'),', $sort_order);
  $sort_order++;
  $install->add_configuration('', 'true', NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),', $sort_order);
  $sort_order++;

  if (RSS_FEED_VERSION != $current_version) {
    $sql = "UPDATE " . TABLE_CONFIGURATION . " SET configuration_value=:configurationValue, last_modified=NOW() WHERE configuration_key='RSS_FEED_VERSION'";
    $sql = $db->bindVars($sql, ':configurationValue', $current_version, 'string');
    $db->Execute($sql);
    $install_type = 'update';
  }

  if ($install_type = 'update') {
    $msg = 'RSS Feed v ' . $current_version . ' updated!';
  } else {
    $msg = 'RSS Feed v ' . $current_version . ' install completed!';
  }
  $messageStack->add($msg, 'success');

}

if (defined('TABLE_ADMIN_PAGES')) {
  zen_deregister_admin_pages('configRSSfeed');
  if (!zen_page_key_exists('configRSSfeed')) {
    $sql = "SELECT configuration_group_id
            FROM " . TABLE_CONFIGURATION . "
            WHERE configuration_key = 'RSS_FEED_VERSION'
              AND configuration_group_id != 6
            LIMIT 1";
    $configuration_group = $db->Execute($sql);
    if (!$configuration_group->EOF) {
      zen_register_admin_page('configRSSfeed', // page_key
                              'RSS_FEED_CONFIGURATION_GROUP_TITLE', // language_key
                              'FILENAME_CONFIGURATION', // main_page
                              'gID=' . $configuration_group->fields['configuration_group_id'], // page_params
                              'configuration', // menu_key
                              'Y', // display_on_menu
                              $configuration_group->fields['configuration_group_id']); // sort_order
    }
  }
}

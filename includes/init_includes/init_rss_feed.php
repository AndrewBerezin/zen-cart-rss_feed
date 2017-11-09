<?php
/**
 * rss_feed.php
 *
 * @package rss feed
 * @copyright Copyright 2004-2012 Andrew Berezin eCommerce-Service.com
 * @copyright Copyright 2003-2012 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: init_rss_feed.php, v 2.1.4 14.02.2008 15:26 Andrew Berezin $
 */

if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}
if ($_GET['main_page'] == FILENAME_RSS_FEED) {
  define('GZIP_LEVEL', '0');
  define('SEOX_ANCHOR_PROCESSING_ACTIVE', 'false');
}

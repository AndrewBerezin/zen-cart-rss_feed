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

define('RSS_FEED_CONFIGURATION_GROUP_DESCRIPTION', 'RSS Feed');
define('RSS_FEED_CONFIGURATION_GROUP_TITLE', 'RSS-Лента');

define('CFGTITLE_RSS_FEED_VERSION', 'Version');
define('CFGDESC_RSS_FEED_VERSION', '<img src="images/icon_popup.gif" border="0">&nbsp;<a href="http://ecommerce-service.com/" target="_blank" style="text-decoration: underline; font-weight: bold;">eCommerce Service</a>');
define('CFGTITLE_RSS_FEED_TITLE', 'RSS Title');
define('CFGTITLE_RSS_FEED_TITLE', 'RSS Title');
define('CFGDESC_RSS_FEED_TITLE', 'RSS Title (if empty use Store Name)');
define('CFGTITLE_RSS_FEED_DESCRIPTION', 'RSS Description');
define('CFGDESC_RSS_FEED_DESCRIPTION', 'RSS description');
define('CFGTITLE_RSS_FEED_IMAGE', 'RSS Image');
define('CFGDESC_RSS_FEED_IMAGE', 'A GIF, JPEG or PNG image that represents the channel');
define('CFGTITLE_RSS_FEED_COPYRIGHT', 'RSS Copyright');
define('CFGDESC_RSS_FEED_COPYRIGHT', 'RSS Copyright (if empty use Store Owner)');
define('CFGTITLE_RSS_FEED_MANAGING_EDITOR', 'RSS Managing Editor Email');
define('CFGDESC_RSS_FEED_MANAGING_EDITOR', 'RSS Managing Editor (if empty use Store Owner Email Address and Store Owner)');
define('CFGTITLE_RSS_FEED_WEBMASTER', 'RSS Webmaster Email');
define('CFGDESC_RSS_FEED_WEBMASTER', 'RSS Webmaster (if empty use Store Owner Email Address and Store Owner)');
define('CFGTITLE_RSS_FEED_AUTHOR', 'RSS Author Email');
define('CFGDESC_RSS_FEED_AUTHOR', 'RSS Author (if empty use Store Owner Email Address and Store Owner)');
define('CFGTITLE_RSS_FEED_HOMEPAGE_FEED', 'RSS Home Page Feed');
define('CFGDESC_RSS_FEED_HOMEPAGE_FEED', 'RSS Home Page Feed');
define('CFGTITLE_RSS_FEED_DEFAULT_FEED', 'RSS Default Feed');
define('CFGDESC_RSS_FEED_DEFAULT_FEED', 'RSS Default Feed');
define('CFGTITLE_RSS_FEED_STRIP_TAGS', 'Strip tags');
define('CFGDESC_RSS_FEED_STRIP_TAGS', 'Strip tags');
define('CFGTITLE_RSS_FEED_ITEMS_DESCRIPTION', 'Generate Descriptions');
define('CFGDESC_RSS_FEED_ITEMS_DESCRIPTION', 'Generate Descriptions');
define('CFGTITLE_RSS_FEED_ITEMS_DESCRIPTION_MAX_LENGTH', 'Descriptions Length');
define('CFGDESC_RSS_FEED_ITEMS_DESCRIPTION_MAX_LENGTH', 'How many characters in description (0 for no limit)');
define('CFGTITLE_RSS_FEED_TTL', 'Time to live');
define('CFGDESC_RSS_FEED_TTL', 'Time to live - time after reader should refresh the info in minutes. Also using for caching time.');
define('CFGTITLE_RSS_FEED_PRODUCTS_LIMIT', 'Default Products Limit');
define('CFGDESC_RSS_FEED_PRODUCTS_LIMIT', 'Default Limit to Products Feed');
define('CFGTITLE_RSS_FEED_PRODUCTS_DESCRIPTION_IMAGE', 'Add Product image');
define('CFGDESC_RSS_FEED_PRODUCTS_DESCRIPTION_IMAGE', 'Add product image to product description tag');
define('CFGTITLE_RSS_FEED_PRODUCTS_DESCRIPTION_BUYNOW', 'Add "buy now" button');
define('CFGDESC_RSS_FEED_PRODUCTS_DESCRIPTION_BUYNOW', 'Add "buy now" button to product description tag');
define('CFGTITLE_RSS_FEED_PRODUCTS_CATEGORIES', 'Categories for Products');
define('CFGDESC_RSS_FEED_PRODUCTS_CATEGORIES', 'Use \'all\' or only \'master\' Categories for Products when specified cPath parameter');
define('CFGTITLE_RSS_FEED_PRODUCTS_PRICE', 'Generate Products Price');
define('CFGDESC_RSS_FEED_PRODUCTS_PRICE', 'Generate Products Price (extended tag &lt;g:price&gt;)');
define('CFGTITLE_RSS_FEED_PRODUCTS_ID', 'Generate Products ID');
define('CFGDESC_RSS_FEED_PRODUCTS_ID', 'Generate Products ID (extended tag &lt;g:id&gt;)');
define('CFGTITLE_RSS_FEED_PRODUCTS_WEIGHT', 'Generate Products Weight');
define('CFGDESC_RSS_FEED_PRODUCTS_WEIGHT', 'Generate Products Weight (extended tag &lt;g:weight&gt;)');
define('CFGTITLE_RSS_FEED_PRODUCTS_BRAND', 'Generate Products Brand');
define('CFGDESC_RSS_FEED_PRODUCTS_BRAND', 'Generate Products Manufacturers Name (extended tag &lt;g:brand&gt;)');
define('CFGTITLE_RSS_FEED_PRODUCTS_CURRENCY', 'Generate Products Currency');
define('CFGDESC_RSS_FEED_PRODUCTS_CURRENCY', 'Generate Products Currency (extended tag &lt;g:currency&gt;)');
define('CFGTITLE_RSS_FEED_PRODUCTS_QUANTITY', 'Generate Products Quantity');
define('CFGDESC_RSS_FEED_PRODUCTS_QUANTITY', 'Generate Products Quantity (extended tag &lt;g:quantity&gt;)');
define('CFGTITLE_RSS_FEED_PRODUCTS_MODEL', 'Generate Products Model');
define('CFGDESC_RSS_FEED_PRODUCTS_MODEL', 'Generate Products Model (extended tag &lt;g:model_number&gt;)');
define('CFGTITLE_RSS_FEED_PRODUCTS_RATING', 'Generate Products Rating');
define('CFGDESC_RSS_FEED_PRODUCTS_RATING', 'Generate Products Rating (extended tag &lt;g:rating&gt;)');
define('CFGTITLE_RSS_FEED_PRODUCTS_IMAGES', 'Generate Products Images');
define('CFGDESC_RSS_FEED_PRODUCTS_IMAGES', 'Generate Products Images (extended tag &lt;g:image_link&gt;)');
define('CFGTITLE_RSS_FEED_DEFAULT_IMAGE_SIZE', 'Generate Products Images Size');
define('CFGDESC_RSS_FEED_DEFAULT_IMAGE_SIZE', 'What image size Generate (extended tag &lt;g:image_link&gt;)');

// EOF
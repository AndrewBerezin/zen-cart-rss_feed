<?php
/**
 * @package admin
 * @copyright Copyright 2005-2017 Andrew Berezin eCommerce-Service.com
 * @copyright Copyright 2003-2017 Zen Cart Development Team
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @Localization: Andrew Berezin http://eCommerce-Service.com
 * @version $Id: ecs_plugin.php v 1.2.5 27.09.2017 11:42:52 AndrewBerezin $
 */

class ecs_plugin {
  var $configuration_group_id;
  function __construct($configuration_group_id=null) {
    $this->_set_configuration_group_id($configuration_group_id);
  }
  function _set_configuration_group_id($configuration_group_id=null) {
    if (!empty($configuration_group_id)) {
      $this->configuration_group_id = $configuration_group_id;
    }
  }
  function _check_configuration_key($configuration_key) {
    global $db;
    if (!defined($configuration_key)) {
      $check = $db->Execute("SELECT * FROM " . TABLE_CONFIGURATION . " where configuration_key='" . $configuration_key . "'");
      if ($check->EOF) {
        return false;
      }
    }
    return true;
  }
  function add_configuration($configuration_key, $configuration_value, $use_function, $set_function, $sort_order) {
    global $db;
    if (zen_not_null($use_function)) {
      $sql_use_function = "'" . zen_db_input($use_function) . "'";
    } else {
      $sql_use_function = "NULL";
    }
    if (zen_not_null($set_function)) {
      $sql_set_function = "'" . zen_db_input($set_function) . "'";
    } else {
      $sql_set_function = "NULL";
    }

    $configuration_title = constant('CFGTITLE_' . $configuration_key);
    $configuration_description = constant('CFGDESC_' . $configuration_key);

    $sql = "INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) VALUES (:cfgTitle, :configurationKey, :configurationValue, :cfgDesc, :configurationGroupID, :sortOrder, :configurationSetFunction, :configurationUseFunction, NOW());";
    $sql = $db->bindVars($sql, ':cfgTitle', $configuration_title, 'string');
    $sql = $db->bindVars($sql, ':cfgDesc', $configuration_description, 'string');
    $sql = $db->bindVars($sql, ':configurationKey', $configuration_key, 'string');
    $sql = $db->bindVars($sql, ':configurationGroupID', $this->configuration_group_id, 'integer');
    $sql = $db->bindVars($sql, ':configurationValue', $configuration_value, 'string');
    $sql = $db->bindVars($sql, ':sortOrder', $sort_order, 'integer');
    $sql = $db->bindVars($sql, ':configurationSetFunction', $sql_set_function, 'passthru');
    $sql = $db->bindVars($sql, ':configurationUseFunction', $sql_use_function, 'passthru');

    $check = $db->Execute("SELECT * FROM " . TABLE_CONFIGURATION . " WHERE configuration_key='" . zen_db_input($configuration_key) . "'");
    if ($check->EOF) {
      $db->Execute($sql);
    } else {
      $update = array();
      if ($sort_order != $check->fields['sort_order']) {
        $update['sort_order'] = $sort_order;
  //      echo '<pre>';var_dump('sort_order', $check->fields['sort_order'], $sort_order);echo '<pre>';
      }
  //    if (is_null($check->fields['use_function'])) $check->fields['use_function'] = "NULL";
      if ($use_function != $check->fields['use_function']) {
        $update['use_function'] = $sql_use_function;
  //      echo '<pre>';var_dump('use_function', $check->fields['use_function'], $use_function);echo '<pre>';
      }
  //    if (is_null($check->fields['set_function'])) $check->fields['set_function'] = "NULL";
      if ($set_function != $check->fields['set_function']) {
        $update['set_function'] = $sql_set_function;
  //      echo '<pre>';var_dump('set_function', $check->fields['set_function'], $set_function);echo '<pre>';
      }
      if ($configuration_title != $check->fields['configuration_title']) {
        $update['configuration_title'] = "'" . zen_db_input($configuration_title) . "'";
  //      echo '<pre>';var_dump('configuration_title', $check->fields['configuration_title'], $configuration_title);echo '<pre>';
      }
      if ($configuration_description != $check->fields['configuration_description']) {
        $update['configuration_description'] = "'" . zen_db_input($configuration_description) . "'";
  //      echo '<pre>';var_dump('configuration_description', $check->fields['configuration_description'], $configuration_description);echo '<pre>';
      }
      if (sizeof($update) > 0) {
  //      echo '<pre>';var_dump($update);echo '<pre>';
        $sql = "UPDATE " . TABLE_CONFIGURATION . " SET ";
        foreach ($update as $key => $val) {
          $sql .= $key . "=" . $val . ", ";
        }
        $sql = substr($sql, 0, -2);
        $sql .= " WHERE configuration_key='" . zen_db_input($configuration_key) . "'";
  //      echo '<pre>';var_dump($sql);echo '<pre>';
        $db->Execute($sql);
      }
    }
  }
  function upd_configuration($configuration_key, $configuration_value) {
    global $db;
    $sql = "SELECT * FROM " . TABLE_CONFIGURATION . " WHERE configuration_key=:configurationKey";
    $sql = $db->bindVars($sql, ':configurationKey', $configuration_key, 'string');
    $check = $db->Execute($sql);
    if (!$check->EOF) {
      if ($check->fields['configuration_value'] != $configuration_value) {
        $sql = "UPDATE " . TABLE_CONFIGURATION . " SET configuration_value=:configurationValue WHERE configuration_key=:configurationKey";
        $sql = $db->bindVars($sql, ':configurationKey', $configuration_key, 'string');
        $sql = $db->bindVars($sql, ':configurationValue', $configuration_value, 'string');
        $db->Execute($sql);
      }
    } else {
      return false;
    }
  }
  function del_configuration($configuration_key) {
    global $db;
    $sql = "SELECT * FROM " . TABLE_CONFIGURATION . " WHERE configuration_key=:configurationKey";
    $sql = $db->bindVars($sql, ':configurationKey', $configuration_key, 'string');
    $check = $db->Execute($sql);
    if (!$check->EOF) {
      $sql = "DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key=:configurationKey";
      $sql = $db->bindVars($sql, ':configurationKey', $configuration_key, 'string');
      $db->Execute($sql);
    }
  }
}

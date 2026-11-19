<?php

define('MODX_API_MODE', true);
define('IN_MANAGER_MODE', true);

include_once(__DIR__ . "/../../../index.php");

if (empty($modx->config)) {
    $modx->getSettings();
}

if (!isset($_SESSION['mgrValidated'])) {
    die();
}

//начинаем...
$out = '';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
$docid = isset($_REQUEST['docid']) && (int)$_REQUEST['docid'] > 0 ? (int)$_REQUEST['docid'] : 0;
$tvid = isset($_REQUEST['tvid']) && (int)$_REQUEST['tvid'] > 0 ? (int)$_REQUEST['tvid'] : 0;
$defaults = array('param_id' => '9', 'cat_name' => '', 'list_yes' => '0', 'fltr_yes' => '1', 'fltr_type' => '1', 'fltr_name' => '', 'fltr_many' => '0', 'fltr_href' => '0');
$fields = array_keys($defaults);

switch ($action) {
    case 'update':
        $arr = array();
        foreach ($fields as $field) {
            if (isset($_REQUEST[$field])) {
                $arr[$field] = $_REQUEST[$field];
            }
        }
        $operation = isset($_REQUEST['webix_operation']) ? $_REQUEST['webix_operation'] : 'update';

        if (!$docid || !$tvid) {
            break;
        }

        $tmp = $modx->getTemplateVar("tovarparams", "*", $docid, "all");
        $curr = is_array($tmp) && isset($tmp['value']) ? json_decode($tmp['value'], true) : array();
        if (!is_array($curr)) {
            $curr = array('fieldValue' => array());
        }

        $iteration = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] - 1 : 0;

        if ($operation === 'update') {
            if (isset($curr['fieldValue'][$iteration])) {
                $curr['fieldValue'][$iteration] = $arr;
            }
        } elseif ($operation === 'insert') {
            if (!isset($curr['fieldValue'])) {
                $curr['fieldValue'] = array();
            }
            if ($iteration >= 0 && $iteration < count($curr['fieldValue'])) {
                array_splice($curr['fieldValue'], $iteration, 0, array($arr));
            } else {
                $curr['fieldValue'][] = $arr;
            }
        } elseif ($operation === 'delete') {
            if (isset($curr['fieldValue'][$iteration])) {
                array_splice($curr['fieldValue'], $iteration, 1);
            }
        }

        $curr['fieldValue'] = array_values($curr['fieldValue']);
        $modx->db->update(
            array('value' => $modx->db->escape(json_encode($curr))),
            $modx->getFullTableName("site_tmplvar_contentvalues"),
            "contentid={$docid} AND tmplvarid={$tvid}"
        );
        break;

    case 'list':
        $tmp = $modx->getTemplateVar("tovarparams", "*", $docid, "all");
        $arr = is_array($tmp) && isset($tmp['value']) ? json_decode($tmp['value'], true) : array();
        $rows = array();
        $total = 0;
        if (is_array($arr) && isset($arr['fieldValue']) && is_array($arr['fieldValue'])) {
            foreach ($arr['fieldValue'] as $k => $v) {
                $rows[$k] = is_array($v) ? $v : array();
                $rows[$k]['id'] = $k + 1;
                $total++;
            }
        }
        $out = json_encode(array("data" => $rows, "pos" => 0, "total_count" => $total));
        break;

    case 'get_tv_value':
        $tmp = $modx->getTemplateVar("tovarparams", "*", $docid, "all");
        $out = is_array($tmp) && isset($tmp["value"]) ? $tmp["value"] : '';
        break;

    case 'get_tv_list':
        $out = $modx->runSnippet("multiParams", array("action" => "getParamsToWebix"));
        break;

    case 'save_order':
        $order = isset($_REQUEST['order']) ? (string)$_REQUEST['order'] : '';
        $new_order = array_map("trim", explode(',', $order));
        $tmp = $modx->getTemplateVar("tovarparams", "*", $docid, "all");
        $curr = is_array($tmp) && isset($tmp['value']) ? json_decode($tmp['value'], true) : array();
        $new = array();
        if (is_array($curr) && isset($curr['fieldValue']) && is_array($curr['fieldValue'])) {
            foreach ($new_order as $v) {
                $iteration = (int)$v - 1;
                if (isset($curr['fieldValue'][$iteration])) {
                    $new[] = $curr['fieldValue'][$iteration];
                }
            }
        }
        $curr['fieldValue'] = $new;
        $modx->db->update(
            array('value' => $modx->db->escape(json_encode($curr))),
            $modx->getFullTableName("site_tmplvar_contentvalues"),
            "contentid={$docid} AND tmplvarid={$tvid}"
        );
        break;

    case 'add_row':
        $tmp = $modx->getTemplateVar("tovarparams", "*", $docid, "all");
        $curr = is_array($tmp) && isset($tmp['value']) ? json_decode($tmp['value'], true) : array();
        $num = isset($_REQUEST['num']) ? (int)$_REQUEST['num'] : 0;
        $new = array();
        if ($num > 0) {
            $num = $num - 1;
        }
        if (is_array($curr) && isset($curr['fieldValue']) && is_array($curr['fieldValue']) && !empty($curr['fieldValue'])) {
            $tmpList = array();
            foreach ($curr['fieldValue'] as $k => $v) {
                if ($k == $num) {
                    $tmpList[] = $v;
                    $tmpList[] = $defaults;
                } else {
                    $tmpList[] = $v;
                }
            }
            $new = array('fieldValue' => $tmpList, 'autoincrement' => '1');
        } else {
            $new = array('fieldValue' => array('0' => $defaults), 'autoincrement' => '1');
        }

        if (!empty($new)) {
            $value = $modx->db->escape(json_encode($new));
            $modx->db->query("INSERT INTO " . $modx->getFullTableName("site_tmplvar_contentvalues") . " (`contentid`,`tmplvarid`,`value`) VALUES ('" . $docid . "','" . $tvid . "','" . $value . "') ON DUPLICATE KEY UPDATE `value` = '" . $value . "'");
        }
        $out = json_encode($defaults);
        break;

    case 'del_row':
        $tmp = $modx->getTemplateVar("tovarparams", "*", $docid, "all");
        $curr = is_array($tmp) && isset($tmp['value']) ? json_decode($tmp['value'], true) : array();
        $num = isset($_REQUEST['num']) ? (int)$_REQUEST['num'] : 0;
        $value = '';
        if ($num > 0) {
            $num = $num - 1;
        }
        if (is_array($curr) && isset($curr['fieldValue']) && is_array($curr['fieldValue']) && !empty($curr['fieldValue'])) {
            $tmpList = array();
            foreach ($curr['fieldValue'] as $k => $v) {
                if ($k != $num) {
                    $tmpList[] = $v;
                }
            }
            if (!empty($tmpList)) {
                $curr['fieldValue'] = $tmpList;
                $value = $modx->db->escape(json_encode($curr));
                $modx->db->query("INSERT INTO " . $modx->getFullTableName("site_tmplvar_contentvalues") . " (`contentid`,`tmplvarid`,`value`) VALUES ('" . $docid . "','" . $tvid . "','" . $value . "') ON DUPLICATE KEY UPDATE `value` = '" . $value . "'");
            } else {
                $modx->db->query("DELETE FROM " . $modx->getFullTableName("site_tmplvar_contentvalues") . " WHERE contentid={$docid} AND tmplvarid={$tvid}");
            }
        }
        $out = $value;
        break;

    case 'remove':
        $modx->db->query("DELETE FROM " . $modx->getFullTableName("site_tmplvar_contentvalues") . " WHERE contentid={$docid} AND tmplvarid={$tvid}");
        $out = '';
        break;

    default:
        break;
}

echo $out;

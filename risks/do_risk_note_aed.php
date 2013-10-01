<?php /* $Id$ $URL$ */
if (!defined('W2P_BASE_DIR')) {
	die('You should not access this file directly.');
}

$risk_id = (int) w2PgetParam($_GET, 'risk_id', 0);
$path = 'm=risks&a=view&risk_id=' . $risk_id;

$controller = new w2p_Controllers_Base(
                    new CRisk_Note(), false, 'Risk Note', $path, $path
                  );

$AppUI = $controller->process($AppUI, $_POST);
$AppUI->redirect($controller->resultPath);
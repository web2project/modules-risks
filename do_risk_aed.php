<?php /* $Id$ $URL$ */
if (!defined('W2P_BASE_DIR')) {
	die('You should not access this file directly.');
}

$delete = (int) w2PgetParam($_POST, 'del', 0);

$_POST['risk_task'] = $_POST['new_task'];
$controller = new w2p_Controllers_Base(
                    new CRisk(), $delete, 'Risks', 'm=risks', 'm=risks&a=addedit'
                  );

$AppUI = $controller->process($AppUI, $_POST);
$AppUI->redirect($controller->resultPath);
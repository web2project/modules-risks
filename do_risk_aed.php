<?php /* $Id$ $URL$ */
if (!defined('W2P_BASE_DIR')) {
	die('You should not access this file directly.');
}

$del = (int) w2PgetParam($_POST, 'del', 0);

$obj = new CRisk();
if (!$obj->bind($_POST)) {
    $AppUI->setMsg($obj->getError(), UI_MSG_ERROR);
    $AppUI->redirect();
}
$obj->risk_task = (int) w2PgetParam($_POST, 'new_task', 0);

$action = ($del) ? 'deleted' : 'stored';
$result = ($del) ? $obj->delete($AppUI) : $obj->store($AppUI);

if (is_array($result)) {
    $AppUI->setMsg($result, UI_MSG_ERROR, true);
    $AppUI->holdObject($obj);
    $AppUI->redirect('m=risks&a=addedit');
}
if ($result) {
    $AppUI->setMsg('Risks '.$action, UI_MSG_OK, true);
    $AppUI->redirect('m=risks');
} else {
    $AppUI->redirect('m=public&a=access_denied');
}
<?php /* $Id$ $URL$ */
if (!defined('W2P_BASE_DIR')) {
	die('You should not access this file directly.');
}
include W2P_BASE_DIR . '/modules/risks/risknotes.class.php';

$del = 0;
$obj = new CRiskNote();
if (!$obj->bind($_POST)) {
    $AppUI->setMsg($obj->getError(), UI_MSG_ERROR);
    $AppUI->redirect();
}

$action = ($del) ? 'deleted' : 'stored';
$result = ($del) ? $obj->delete($AppUI) : $obj->store($AppUI);

if (is_array($result)) {
    $AppUI->setMsg($result, UI_MSG_ERROR, true);
    $AppUI->holdObject($obj);
    $AppUI->redirect('m=risks&a=view&risk_id='.$obj->risk_note_id);
}
if ($result) {
    $AppUI->setMsg('Risks '.$action, UI_MSG_OK, true);
    $AppUI->redirect('m=risks');
} else {
    $AppUI->redirect('m=public&a=access_denied');
}
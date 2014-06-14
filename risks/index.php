<?php
if (!defined('W2P_BASE_DIR')) {
	die('You should not access this file directly.');
}

$canRead = canView($m);
if (!$canRead) {
    $AppUI->redirect(ACCESS_DENIED);
}

$tab = $AppUI->processIntState('risksIdxTab', $_GET, 'tab', 0);

$riskStatus = array(-1 => $AppUI->_('All Risks')) + w2PgetSysVal('RiskStatus');
$durnTypes = array(1=>'Hours', 24=>'Days', 168=>'Weeks');

// setup the title block
$titleBlock = new w2p_Theme_TitleBlock( 'Risks', 'scales.png', $m, $m.$a );
$titleBlock->addButton('New risk', '?m=risks&a=addedit&date=');
$titleBlock->show();

$tabBox = new CTabBox("?m=$m", W2P_BASE_DIR . "/modules/$m/", $tab);
foreach ($riskStatus as $status) {
	$tabBox->add('vw_idx_risks', $AppUI->_($status));
}
$tabBox->show();
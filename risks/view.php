<?php /* $Id$ $URL$ */
if (!defined('W2P_BASE_DIR')) {
    die('You should not access this file directly.');
}

$risk_id = (int) w2PgetParam($_GET, 'risk_id', 0);

// check permissions for this company
$perms = &$AppUI->acl();

$canView = $perms->checkModuleItem($m, 'view', $risk_id);
$canAdd = canAdd($m);
$canEdit = $perms->checkModuleItem($m, 'edit', $risk_id);
$canDelete = $perms->checkModuleItem($m, 'delete', $risk_id);

if (!$canView) {
	$AppUI->redirect( "m=public&a=access_denied" );
} 

$tab = $AppUI->processIntState('RiskVwTab', $_GET, 'tab', 0);
$df = $AppUI->getPref('SHDATEFORMAT');
$tf = $AppUI->getPref('TIMEFORMAT');
$format = $df . ' ' . $tf;

$obj = new CRisk();
$obj->loadFull($AppUI, $risk_id);

if (!$obj) {
	$AppUI->setMsg('Risk');
	$AppUI->setMsg('invalidID', UI_MSG_ERROR, true);
	$AppUI->redirect();
} else {
	$AppUI->savePlace();
}

// setup the title block
$titleBlock = new w2p_Theme_TitleBlock('View Risk', 'scales.png', $m, $m . '.' . $a);
$titleBlock->addCell();
if ($canAdd) {
    $titleBlock->addCell('<input type="submit" class="button" value="' . $AppUI->_('new risk') . '" />', '', '<form action="?m=risks&amp;a=addedit" method="post" accept-charset="utf-8">', '</form>');
}
$titleBlock->addCrumb('?m='.$m, 'risks list');
if ($canEdit) {
	$titleBlock->addCrumb('?m=risks&amp;a=addedit&amp;risk_id=' . $risk_id, 'edit this risk');
}
if ($canDelete) {
	$titleBlock->addCrumbDelete('delete risk', $canDelete, $msg);
}
$titleBlock->show();

$htmlHelper = new w2p_Output_HTMLHelper($AppUI);
$htmlHelper->stageRowData($obj);


$riskProbability = w2PgetSysVal( 'RiskProbability' );
$riskStatus = w2PgetSysVal( 'RiskStatus' );
$riskImpact = w2PgetSysVal( 'RiskImpact' );
$riskDuration = array(1=>'Hours', 24=>'Days', 168=>'Weeks');
$customLookups = array('risk_probability' => $riskProbability, 'risk_status' => $riskStatus,
        'risk_impact' => $riskImpact, 'risk_duration' => $riskDuration);
?>
<script type="text/javascript">
function delIt(){
	var form = document.frmDelete;
	if (confirm( "<?php echo $AppUI->_('doDelete', UI_OUTPUT_JS).' '.$AppUI->_('Risk', UI_OUTPUT_JS).'?';?>" )) {
		form.submit();
	}
}
</script>

<form name="frmDelete" action="?m=risks" method="post">
    <input type="hidden" name="dosql" value="do_risk_aed" />
    <input type="hidden" name="del" value="1" />
    <input type="hidden" name="risk_id" value="<?php echo $risk_id; ?>" />
</form>

<link rel="stylesheet" type="text/css" href="./modules/risks/risks.css" />

<table border="0" cellpadding="4" cellspacing="0" width="100%" class="std view">
    <tr>
        <td width="50%">
            <table width="100%" cellspacing="1" cellpadding="2">
                <tr>
                    <td nowrap="nowrap" colspan=2><strong><?php echo $AppUI->_('Details'); ?></strong></td>
                </tr>
                <?php if ($obj->risk_project) { ?>
                <tr>
                    <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Project');?>:</td>
                    <td style="background-color:#<?php echo $obj->project_color_identifier; ?>">
                        <font color="<?php echo bestColor($obj->project_color_identifier); ?>">
                            <?php echo '<a href="?m=projects&amp;a=view&amp;project_id=' . $obj->risk_project . '">' . htmlspecialchars($obj->project_name, ENT_QUOTES) . '</a>'; ?>
                        </font>
                    </td>
                </tr>
                <?php } ?>
                <?php if ($obj->risk_task) { ?>
                <tr>
                    <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Task');?>:</td>
                    <td class="hilite">
                        <?php echo '<a href="?m=projects&amp;a=view&amp;task_id=' . $obj->risk_task . '">' . htmlspecialchars($obj->task_name, ENT_QUOTES) . '</a>'; ?>
                    </td>
                </tr>
                <?php } ?>
                <tr>
                    <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Risk Name'); ?>:</td>
                    <?php echo $htmlHelper->createCell('risk_name-nolink', $obj->risk_name); ?>
                </tr>
                <tr>
                    <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Owner');?>:</td>
                    <?php echo $htmlHelper->createCell('risk_owner_name-nolink', $obj->risk_owner_name); ?>
                </tr>
                <tr>
                    <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Probability'); ?>:</td>
                    <?php echo $htmlHelper->createCell('risk_probability', $obj->risk_probability, $customLookups); ?>
                </tr>
                <tr>
                    <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Impact'); ?>:</td>
                    <?php echo $htmlHelper->createCell('risk_impact', $obj->risk_impact, $customLookups); ?>
                </tr>
                <tr>
                    <td align="right"><?php echo $AppUI->_('Risk Priority'); ?>:</td>
                    <?php echo $htmlHelper->createCell('risk_priority', $obj->risk_priority); ?>
                </tr>
                <tr>
                    <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Status'); ?>:</td>
                    <?php echo $htmlHelper->createCell('risk_status', $obj->risk_status, $customLookups); ?>
                </tr>
                <tr>
                    <td nowrap="nowrap" colspan="2"><strong><?php echo $AppUI->_('Dates and Targets'); ?></strong></td>
                </tr>
                <tr>
                    <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Mitigation Date');?>:</td>
                    <?php echo $htmlHelper->createCell('risk_mitigation_date', $obj->risk_mitigation_date); ?>
                </tr>
                <tr>
                    <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Create Date');?>:</td>
                    <?php echo $htmlHelper->createCell('risk_created', $obj->risk_created); ?>
                </tr>
                <tr>
                    <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Update Date');?>:</td>
                    <?php echo $htmlHelper->createCell('risk_updated', $obj->risk_updated); ?>
                </tr>
            </table>
        </td>
        <td width="50%" valign="top">
            <table cellspacing="1" cellpadding="2" border="0" width="100%">
                <tr><td></td></tr>
                <tr>
                    <td>
                        <strong><?php echo $AppUI->_('Description'); ?></strong><br />
                    </td>
                </tr>
                <tr>
                    <?php echo $htmlHelper->createCell('risk_description', $obj->risk_description); ?>
                </tr>
            </table>
        </td>
    </tr>
</table>

<?php

// Last parameter makes tab box javascript based.
$moddir = W2P_BASE_DIR . "/modules/$m/";
$tabBox = new CTabBox( "?m=$m&amp;a=view&amp;risk_id=$risk_id", '', $tab);
$tabBox->add($moddir.'vw_notes', $AppUI->_('Risk Notes' ));
$tabBox->add($moddir.'vw_note_add', $AppUI->_('Add Risk Note'));
$tabBox->show();
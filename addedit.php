<?php /* $Id$ $URL$ */
if (!defined('W2P_BASE_DIR')) {
	die('You should not access this file directly.');
}

$item_id = (int) w2PgetParam($_GET, 'risk_id', 0);

$item = new CRisk();
$item->risk_id = $item_id;

$canEdit = ($item_id) ? $item->canEdit() : $item->canCreate();
if (!$canEdit) {
	$AppUI->redirect(ACCESS_DENIED);
}

$obj = $AppUI->restoreObject();
if ($obj) {
    $item = $obj;
    $item_id = $item->risk_id;
} else {
    $item->loadFull($item_id);
}

if (!$item && $item_id > 0) {
    $AppUI->setMsg('invalidID', UI_MSG_ERROR, true);
    $AppUI->redirect();
}

$riskProbability = w2PgetSysVal( 'RiskProbability' );
$riskStatus = w2PgetSysVal( 'RiskStatus' );
$riskImpact = w2PgetSysVal( 'RiskImpact' );
$priority = w2PgetSysVal('RiskPriority');
$riskDuration = array(1=>'Hours', 24=>'Days', 168=>'Weeks');
$users = $perms->getPermittedUsers('risks');

// setup the title block
$ttl = $item_id ? 'Edit Risk' : 'Add Risk';
$titleBlock = new w2p_Theme_TitleBlock($AppUI->_($ttl), 'scales.png', $m, $m . '.' . $a);
$titleBlock->addCrumb('?m=' . $m, 'risks list');
$canDelete = $perms->checkModuleItem($m, 'delete', $item_id);
if ($canDelete && $item_id) {
    $titleBlock->addCrumbDelete('delete risk', $canDelete, $msg);
}
$titleBlock->show();

$prj = new CProject();
$projects = $prj->getAllowedProjects($AppUI->user_id);
foreach ($projects as $project_id => $project_info) {
	$projects[$project_id] = $project_info['project_name'];
}
$projects = arrayMerge(array('0' => $AppUI->_('All', UI_OUTPUT_JS)), $projects);

?>
<script src="./modules/risks/addedit.js" type="text/javascript"></script>

<form name="form" action="?m=risks" method="post" accept-charset="utf-8">
	<input type="hidden" name="dosql" value="do_risk_aed" />
	<input type="hidden" name="del" value="0" />
	<input type="hidden" name="risk_id" value="<?php echo $item->risk_id;?>" />
    <table border="0" cellpadding="4" cellspacing="0" width="100%" class="std">
		<tr>
			<td align="right"><?php echo $AppUI->_('Risk Name');?>:</td>
			<td>
				<input type="text" class="text" size="75" name="risk_name" value="<?php echo $item->risk_name; ?>" maxlength="50">
			</td>
		</tr>
		<tr>
			<td align="right"><?php echo $AppUI->_('Probability');?>:</td>
			<td>
				<?php
				echo arraySelect( $riskProbability, 'risk_probability', 'size="1" class="text"', $item->risk_probability);
				?>
			</td>
		</tr>
		<tr>
			<td align="right"><?php echo $AppUI->_('Impact');?>:</td>
			<td>
				<?php
				echo arraySelect( $riskImpact, 'risk_impact', 'size="1" class="text"', $item->risk_impact);
				?>
			</td>
		</tr>
		<tr>
			<td align="right"><?php echo $AppUI->_('Risk Priority');?>:</td>
			<td>
                <?php echo arraySelect($priority, 'risk_priority', 'size="1" class="text"', ($item->risk_priority ? $item->risk_priority : 0) , true); ?>
			</td>
		</tr>
		<tr>
			<td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Status');?>:</td>
			<td>
				<?php
				echo arraySelect( $riskStatus, 'risk_status', 'size="1" class="text"', $item->risk_status);
				?>
			</td>
		</tr>
		<tr>
			<td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Owner');?>:</td>
			<td>
				<?php
				echo arraySelect( $users, 'risk_owner', 'size="1" class="text"', ($item->risk_owner ? $item->risk_owner : $AppUI->user_id) );
				?>
			</td>
		</tr>
		<tr>
			<td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Project');?>:</td>
			<td>
				<?php
				echo arraySelect( $projects, 'risk_project', 'size="1" class="text" onChange="updateTasks();"', $item->risk_project);
				?>
			</td>
		</tr>
		<tr>
			<td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Task');?>:</td>
			<td>
                <?php
                $tasks = array();
                if ($item->risk_project) {
                    $task = new CTask();
                    $taskList = $task->loadAll(null, 'task_project = ' . $item->risk_project);
//TODO: At some point, we should change this to use the task tree structure
                    foreach ($taskList as $id => $values) {
                        $tasks[$id] = $values['task_name'];
                    }
                }
                $tasks = arrayMerge(array('0' => $AppUI->_('Not Specified', UI_OUTPUT_JS)), $tasks);
                echo arraySelect($tasks, 'new_task', 'size="1" class="text"', $item->risk_task);
                ?>
            </td>
        </tr>
		<tr>
			<td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Description');?>:</td>
			<td>
				<textarea cols="73" rows="6" class="textarea" name="risk_description"><?php echo $item->risk_description; ?></textarea>
			</td>
		</tr>
		<tr>
			<td>
				<input class="text" type="submit" value="back">
			</td>
			<td align="right">
				<input class="text" type="submit" value="submit">
			</td>
		</tr>
    </table>
</form>
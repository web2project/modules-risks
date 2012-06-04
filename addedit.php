<?php /* $Id$ $URL$ */
if (!defined('W2P_BASE_DIR')) {
	die('You should not access this file directly.');
}

$risk_id = (int) w2PgetParam($_GET, 'risk_id', 0);

// check permissions for this record
$perms = &$AppUI->acl();
$canAuthor = canAdd('risks');
$canEdit = $perms->checkModuleItem('risks', 'edit', $risk_id);

// check permissions
if (!$canAuthor && !$risk_id) {
	$AppUI->redirect('m=public&a=access_denied');
}

if (!$canEdit && $risk_id) {
	$AppUI->redirect('m=public&a=access_denied');
}

// load the record data
$risk = new CRisk();
$obj = $AppUI->restoreObject();
if ($obj) {
    $risk = $obj;
    $project_id = $risk->risk_id;
} else {
    $risk->loadFull($AppUI, $risk_id);
}
if (!$risk && $risk_id > 0) {
    $AppUI->setMsg('Risk');
    $AppUI->setMsg('invalidID', UI_MSG_ERROR, true);
    $AppUI->redirect();
}
$riskProbability = w2PgetSysVal( 'RiskProbability' );
$riskStatus = w2PgetSysVal( 'RiskStatus' );
$riskImpact = w2PgetSysVal( 'RiskImpact' );
$riskDuration = array(1=>'Hours', 24=>'Days', 168=>'Weeks');
$users = $perms->getPermittedUsers('risks');

// setup the title block
$ttl = $risk_id ? 'Edit Risk' : 'Add Risk';
$titleBlock = new CTitleBlock($AppUI->_($ttl), 'scales.png', $m, $m . '.' . $a);
$titleBlock->addCrumb('?m=' . $m, 'risks list');
$canDelete = $perms->checkModuleItem($m, 'delete', $risk_id);
if ($canDelete && $risk_id) {
    $titleBlock->addCrumbDelete('delete link', $canDelete, $msg);
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
	<input type="hidden" name="risk_id" value="<?php echo $risk->risk_id;?>" />
    <table border="0" cellpadding="4" cellspacing="0" width="100%" class="std">
		<tr>
			<td align="right"><?php echo $AppUI->_('Risk Name');?>:</td>
			<td>
				<input type="text" class="text" size="75" name="risk_name" value="<?php echo $risk->risk_name; ?>" maxlength="50">
			</td>
		</tr>
		<tr>
			<td align="right"><?php echo $AppUI->_('Probability');?>:</td>
			<td>
				<?php
				echo arraySelect( $riskProbability, 'risk_probability', 'size="1" class="text"', $risk->risk_probability);
				?>
			</td>
		</tr>
		<tr>
			<td align="right"><?php echo $AppUI->_('Impact');?>:</td>
			<td>
				<?php
				echo arraySelect( $riskImpact, 'risk_impact', 'size="1" class="text"', $risk->risk_impact);
				?>
			</td>
		</tr>
		<tr>
			<td align="right"><?php echo $AppUI->_('Risk Priority');?>:</td>
			<td>
				<input type="text" class="text" size="5" name="risk_priority" value="<?php echo $risk->risk_priority; ?>" maxlength="3">
			</td>
		</tr>
		<tr>
			<td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Status');?>:</td>
			<td>
				<?php
				echo arraySelect( $riskStatus, 'risk_status', 'size="1" class="text"', $risk->risk_status);
				?>
			</td>
		</tr>
		<tr>
			<td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Owner');?>:</td>
			<td>
				<?php
				echo arraySelect( $users, 'risk_owner', 'size="1" class="text"', ($risk->risk_owner ? $risk->risk_owner : $AppUI->user_id) );
				?>
			</td>
		</tr>
		<tr>
			<td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Project');?>:</td>
			<td>
				<?php
				echo arraySelect( $projects, 'risk_project', 'size="1" class="text" onChange="updateTasks();"', $risk->risk_project);
				?>
			</td>
		</tr>
		<tr>
			<td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Task');?>:</td>
			<td>
                <?php
                $tasks = array();
                if ($risk->risk_project) {
                    $taskList = $risk->getTasks($AppUI, $risk->risk_project);
                    foreach ($taskList as $id => $values) {
                        $tasks[$id] = $values['task_name'];
                    }
                }
                $tasks = arrayMerge(array('0' => $AppUI->_('Not Specified', UI_OUTPUT_JS)), $tasks);
                echo arraySelect($tasks, 'new_task', 'size="1" class="text"', $risk->risk_task);
                ?>
            </td>
        </tr>
		<tr>
			<td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Description');?>:</td>
			<td>
				<textarea cols="73" rows="6" class="textarea" name="risk_description"><?php echo $risk->risk_description; ?></textarea>
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
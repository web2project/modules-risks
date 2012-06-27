<?php
if (!defined('W2P_BASE_DIR')) {
    die('You should not access this file directly.');
}

global $AppUI, $task_id, $tab;

$project_id = (int) w2PgetParam($_GET, 'project_id', 0);

$tab--;
$df = $AppUI->getPref('SHDATEFORMAT');
$riskProbability = w2PgetSysVal( 'RiskProbability' );
$riskImpact = w2PgetSysVal( 'RiskImpact' );
$riskStatus = w2PgetSysVal( 'RiskStatus' );

$risk = new CRisk();
$risks = $risk->getRisksByProject($project_id, $tab--);
?>
<table border="0" width="100%" cellspacing="1" cellpadding="2" class="tbl">
	<tr bgcolor="#99CCFF" align="center" valign="top">
        <th align="center" valign="top" class="hdr" width="200px;"><?php echo $AppUI->_('Name'); ?></th>
        <th align="center" valign="top" class="hdr" width="25px;"><?php echo $AppUI->_('Priority'); ?></th>
		<th align="center" valign="top" class="hdr" width="25px;"><?php echo $AppUI->_('Related Task'); ?></th>
		<th align="center" valign="top" class="hdr" width="20px;"><?php echo $AppUI->_('Probability'); ?></th>
		<th align="center" valign="top" class="hdr" width="10px;"><?php echo $AppUI->_('Impact'); ?></th>
		<th align="center" valign="top" class="hdr" width="10px;"><?php echo $AppUI->_('Owner'); ?></th>
        <th align="center" valign="top" class="hdr" width="10px;"><?php echo $AppUI->_('Status'); ?></th>
		<th align="center" valign="top" class="hdr" width="10px;"><?php echo $AppUI->_('Mitigation Date'); ?></th>
	</tr>
    <?php

    $prev_project = -1;
    foreach ($risks as $row) {
        if ($prev_project != (int) $row['project_id']) {
            echo '<tr><td colspan="15" style="background-color:#' . $row['project_color_identifier'] . '">
                <a href="?m=projects&a=view&amp;project_id=' . $row['project_id'] . '">
                    <font color="' . bestColor( $row["project_color_identifier"] ) . '">' . $row['project_name'] . '</font>&nbsp</a>
                </td></tr>';
        }
        $row['risk_status'] = $riskStatus[$row['risk_status']];
        ?>
        <tr>
            <td>
                <a href="?m=risks&amp;a=view&amp;risk_id=<?php echo $row['risk_id']; ?>"><?php echo $row['risk_name']; ?></a>
            </td>
            <td style="text-align: center;"><?php echo $row['risk_priority']; ?>&nbsp</td>
            <td>
                <?php if ($row['task_id'] > 0) { ?>
                <a href="?m=tasks&amp;a=view&amp;task_id=<?php echo $row['task_id']; ?>">
                    <?php echo $row['task_name']; ?>
                </a>
                <?php } else { ?>
                    <?php echo $AppUI->_('No task specified'); ?>
                <?php } ?>
            </td>
            <td nowrap="nowrap" style="text-align: center;"><?php echo $riskProbability[$row['risk_probability']]; ?> &nbsp</td>
            <td nowrap="nowrap" style="text-align: center;"><?php echo $riskImpact[$row['risk_impact']]; ?> &nbsp</td>
            <td nowrap="nowrap" style="text-align: center;">
                <?php echo $row['owner_name']; ?>
            </td>
            <td nowrap="nowrap" style="text-align: center;">
                <?php echo $row['risk_status']; ?>
            </td>
            <td nowrap="nowrap" style="text-align: center;">
                <?php
                    $mitigationDate = intval($row["risk_mitigation_date"]) ? new CDate( $row["risk_mitigation_date"] ) : null;
                    echo $mitigationDate ? $mitigationDate->format( $df ) : '-';
                ?> &nbsp
            </td>
        </tr>
        <?php
        $prev_project = $row['project_id'];
    }
    ?>
</table>
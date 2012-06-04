<?php /* $Id$ $URL$ */
if (!defined('W2P_BASE_DIR')) {
    die('You should not access this file directly.');
}

global $AppUI;

$risk_id = (int) w2PgetParam($_GET, 'risk_id', 0);
$note = (int) w2PgetParam($_GET, 'note_id', 0);

// check permissions
$perms =& $AppUI->acl();
$canEdit = $perms->checkModuleItem($m, 'edit', $risk_id );
if (!$canEdit) {
	$AppUI->redirect("m=public&a=access_denied");
}
?>

<form name="editFrm" action="?m=risks&amp;a=view&amp;risk_id=<?php echo $risk_id; ?>" method="post">
	<input type="hidden" name="risk_note_risk" value="<?php echo $risk_id; ?>" />
    <input type="hidden" name="dosql" value="do_risk_note_aed" />
    <table>
        <tr>
            <td align="right" valign="top"><?php echo $AppUI->_('Note'); ?>:</td>
            <td>
                <textarea name="risk_note_description" class="textarea" cols="50" rows="6"></textarea>
            </td>
            <td valign="top">
                <input class="text" type="submit" name="note" value="<?php echo $AppUI->_('Add note'); ?>" />
            </td>
        </tr>
    </table>
</form>
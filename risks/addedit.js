function delIt(){
	var form = document.changecontact;
	if(confirm( "<?php echo $AppUI->_('risksDelete');?>" )) {
		form.del.value = "<?php echo $risk_id;?>";
		form.submit();
	}
}

function updateTasks() {
	var proj = document.forms['form'].risk_project.value;
	var tasks = new Array();
	var sel = document.forms['form'].new_task;
	while ( sel.options.length ) {
		sel.options[0] = null;
	}
	sel.options[0] = new Option('loading...', -1);
	frames['thread'].location.href = './index.php?m=tasks&a=listtasks&project=' + proj;
}
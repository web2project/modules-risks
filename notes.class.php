<?php

class CRisk_Note extends w2p_Core_BaseObject {
    public $risk_note_id = null;
    public $risk_note_risk = null;
    public $risk_note_creator = null;
    public $risk_note_date = null;
    public $risk_note_description = '';

	public function __construct() {
		parent::__construct('risk_notes', 'risk_note_id', 'risks');
	}

    public function check() {
        $errorArray = array();
        $baseErrorMsg = get_class($this) . '::store-check failed - ';

        if ('' == trim($this->risk_note_description)) {
            $errorArray['risk_note_description'] = $baseErrorMsg . 'risk note description is not set';
        }

        return $errorArray;
	}

	public function store(CAppUI $AppUI)
    {
        $perms = $AppUI->acl();
        $stored = false;

        $errorMsgArray = $this->check();
        if (count($errorMsgArray) > 0) {
          return $errorMsgArray;
        }

        $q = $this->_getQuery();
        $this->risk_note_date = $q->dbfnNowWithTZ();
        $this->risk_note_creator = $AppUI->user_id;

        if ($this->risk_note_id && $perms->checkModuleItem('risks', 'edit', $this->risk_id)) {
            if (($msg = parent::store())) {
                return $msg;
            }
            $stored = true;
        }
        if (0 == $this->risk_note_id && $perms->checkModuleItem('risks', 'add')) {
            if (($msg = parent::store())) {
                return $msg;
            }
            $stored = true;
        }

        return $stored;
	}

    public function getNotes(CAppUI $AppUI) {
        $results = array();
        $perms =& $AppUI->acl();

        if ($perms->checkModuleItem('risks', 'view', $this->risk_id)) {
            $q = $this->_getQuery();
            $q->addQuery('risk_notes.*');
            $q->addQuery("CONCAT(contact_first_name, ' ', contact_last_name) as risk_note_owner");
            $q->addTable('risk_notes');
            $q->leftJoin('users', 'u', 'risk_note_creator = user_id');
            $q->leftJoin('contacts', 'c', 'user_contact = contact_id');
            $q->addWhere('risk_note_risk = ' . (int) $this->risk_id);
            $results = $q->loadList();
        }

        return $results;
    }
    public function storeNote(CAppUI $AppUI) {
        $perms =& $AppUI->acl();

        if ($this->link_id && $perms->checkModuleItem('risks', 'edit', $this->risk_id)) {
        $q = new DBQuery;
        $this->risk_note_date = $q->dbfnNow();
        addHistory('risks', $this->risk_id, 'update', $this->risk_name, $this->risk_id);
        $stored = true;
}
/*
        *
        *
if ($note) {
	$q = new DBQuery();
	$q->addTable('risk_notes');
	$q->addInsert('risk_note_risk', $risk_id);
	$q->addInsert('risk_note_creator', $AppUI->user_id);
	$q->addInsert('risk_note_date', 'NOW()', false, true);
	$q->addInsert('risk_note_description', $_POST['risk_note_description']);
	$q->exec();
	$AppUI->setMsg('Note added', UI_MSG_OK);
	$AppUI->redirect('m=risks&a=view&risk_id=' . $risk_id);
}     *
 */
    }
    public function deleteNote() {

    }
}
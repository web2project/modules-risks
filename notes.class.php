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

    public function isValid()
    {
        $baseErrorMsg = get_class($this) . '::store-check failed - ';

        if (0 == (int) $this->risk_note_risk) {
            $this->_error['risk_note_risk'] = $baseErrorMsg . 'risk is not set';
        }
        if (0 == (int) $this->risk_note_creator) {
            $this->_error['risk_note_creator'] = $baseErrorMsg . 'risk creator is not set';
        }
        if ('' == trim($this->risk_note_description)) {
            $this->_error['risk_note_description'] = $baseErrorMsg . 'risk note body is not set';
        }

        return (count($this->_error)) ? false : true;
    }

    public function loadAll($order = null, $where = null) {
        $q = $this->_getQuery();
        $q->addTable($this->_tbl, 'r');
        if ($order) {
            $q->addOrder($order);
        }
        if ($where) {
            $q->addWhere($where);
        }

        $q->addQuery('r.*');
        $q->addQuery('contact_display_name as risk_note_creator');
        $q->leftJoin('users', 'u', 'risk_note_creator = user_id');
        $q->leftJoin('contacts', 'c', 'user_contact = contact_id');

        $result = $q->loadHashList($this->_tbl_key);

        return $result;
    }
    protected function hook_preStore() {

        $q = $this->_getQuery();
        $this->risk_note_date = $q->dbfnNowWithTZ();
        $this->risk_note_creator = $this->_AppUI->user_id;

        parent::hook_preStore();
    }
}
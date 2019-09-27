<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/base_module_model.php');
require_once(MODULES_PATH.'/inward_entry/config/inward_entry_constants.php');

class Inward_to_permissions_model extends Base_module_model {
	
	public $foreign_keys = array('vIRnumber'=>array(INWARD_ENTRY_FOLDER=>'inward_entry_model'),'nPartyId'=> 'inward_to_permissions_model');
	public $required = array('nPartyId,nPartyName');
	protected $key_field = 'nPartyId';
	
	function __construct()
	{
		parent::__construct('aspen_tblpartydetails'); // table name
	}
	
	function options_list($key = 'aspen_tblpartydetails.nPartyId', $val = 'aspen_tblpartydetails.nPartyName', $where = array(), $order = 'aspen_tblpartydetails.nPartyName')
	{
		$key = 'aspen_tblpartydetails.nPartyId';
		$val = 'aspen_tblpartydetails.nPartyName';
		$this->db->select('aspen_tblpartydetails.nPartyId, aspen_tblpartydetails.nPartyName');
		return parent::options_list($key, $val, $where, $order);
	}
	
	function list_items($limit = NULL, $offset = NULL, $col = 'nPartyId', $order = 'asc')
    {
		$this->db->select('aspen_tblpartydetails.nPartyId,nPartyName');
        $data = parent::list_items($limit, $offset, $col, $order);
        return $data;    	
	}
}

class Inwardtopermissions_model extends Base_module_model {

}

<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Comment_Spam_Model extends CI_Model{
	
	//Table
	var $TABLE = "comment_spam";
	//Fields
	
	function __construct(){	
		$this->load->database();
		parent::__construct();
	}	
	
	
	/*
		Inserts only handle posts
	*/
	function insert($data)
	{		
		$this->db->insert($this->TABLE, $data);
	}

	function update($data, $where){
		$this->db->where($where);
		$this->db->update($this->TABLE, $data);
	}

	function get($data){		
		return $this->db->where($data)->get($this->TABLE);
	}

	function delete($data){		
		return $this->db->where($data)->delete($this->TABLE);
	}	
}

?>
<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Category_Model extends CI_Model{
	
	//Table
	var $TABLE = "category";
	var $ITEMS_PER_PAGE = 25;
	function __construct(){	
		parent::__construct();
	}	
	
	
	/*
		Inserts only handle posts
	*/
	function insert()
	{
		$name = $this->security->xss_clean($this->input->post('name'));
		$description = $this->security->xss_clean($this->input->post('description'));
		
		$name = strip_tags($name);
		$description = strip_tags($description);

		if($this->exists($name) != -1){
			throw new Exception('Topic Exists!');
		}
		
		$data = array(
				'name'=>$name,
				'description'=>$description
		);	
		$this->db->insert($this->TABLE, $data);
	}
	
	function getLinksLatest($name='', $pageIndex=1){
		//sanitize
		$name = $this->security->xss_clean($name);
		$pageIndex = $this->security->xss_clean($pageIndex);
	
		if($name == ''){
			$id = '';
		}else{
			$id = $this->get_by_name($name)->id;		
		}
		$query = "SELECT s.id as id, sum(sv.score) as score,
			link,
			s.description,
			domain,
			s.name title,
			u.name as name,
			c.name as categoryname,
				TIMESTAMPDIFF(second,s.created,current_timestamp()) as seconds, 
				TIMESTAMPDIFF(day,s.created,current_timestamp()) as days,
				TIMESTAMPDIFF(hour,s.created,current_timestamp()) as hours,
				TIMESTAMPDIFF(minute,s.created,current_timestamp()) as minutes,
				TIMESTAMPDIFF(year,s.created,current_timestamp()) as years		
					from story s
				left join user u
					on u.id = s.userId
				left join story_vote sv
					on sv.storyId = s.id
				inner join category c
					on c.id = s.categoryId
				where s.deleted = 0 ".(($id == '') ? '' : "and c.id = ".$id)."
					group by s.id
				order by	
					s.created
				desc
					limit ".$this->ITEMS_PER_PAGE." offset ".(($pageIndex-1) * $this->ITEMS_PER_PAGE).";";
		
		return $this->db->query($query)->result();		
	}

	function getLinks($name='', $pageIndex=1){
		//sanitize
		$name = $this->security->xss_clean($name);
		$pageIndex = $this->security->xss_clean($pageIndex);
	
		if($name == ''){
			$id = '';
		}else{
			$id = $this->get_by_name($name)->id;		
		}
		$query = "SELECT s.id as id, sum(sv.score) as score,
			link,
			s.description,
			domain,
			s.name title,
			u.name as name,
			c.name as categoryname,
				TIMESTAMPDIFF(second,s.created,current_timestamp()) as seconds, 
				TIMESTAMPDIFF(day,s.created,current_timestamp()) as days,
				TIMESTAMPDIFF(hour,s.created,current_timestamp()) as hours,
				TIMESTAMPDIFF(minute,s.created,current_timestamp()) as minutes,
				TIMESTAMPDIFF(year,s.created,current_timestamp()) as years		
					from story s
				left join user u
					on u.id = s.userId
				left join story_vote sv
					on sv.storyId = s.id
				inner join category c
					on c.id = s.categoryId
				where s.deleted = 0 ".(($id == '') ? '' : "and c.id = ".$id)."
					group by s.id
				order by	
					((COALESCE(SUM(sv.score),0)-1)/POW(((UNIX_TIMESTAMP(NOW()) -UNIX_TIMESTAMP(s.created))/3600)+2,1.5))
				desc
					limit ".$this->ITEMS_PER_PAGE." offset ".(($pageIndex-1) * $this->ITEMS_PER_PAGE).";";
		
		return $this->db->query($query)->result();		
	}
	
	function get_by_name($name){
		$name = $this->security->xss_clean($name);
		if($name == ''){return null;}
		return $this->db->where('name', $name)->get($this->TABLE)->row(0);
	}
	
	function get(){
		return $this->db->get($this->TABLE, 20, 0)->result();		
	}

	function getByName($name){
		$query = "select * from category where name = ?;";
		return $this->db->query($query, array($name));
	}
	
	function update()
	{
		$data = array(
			'name' => $this->name,
			'description' => $this->password
		);
		
		$this->db->update($this->TABLE, $data);
	}
	
	/*
		Check if User Exists.	
	*/
	function exists($name)
	{		
		$name = $this->security->xss_clean($name);
		if($name == "" || strlen($name) == 0){throw new Exception('Empty Name.');}
		$this->db->select('id');
		$this->db->from($this->TABLE);
		$this->db->where('name', $name);
		$result = $this->db->get();
		
		foreach ($result->result() as $row){
			return $row->id;
		}		
		return -1;
	}
	
}

?>
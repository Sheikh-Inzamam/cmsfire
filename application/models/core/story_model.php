<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Story_Model extends CI_Model{
	
	//Table
	var $TABLE = "story";
	var $ITEMS_PER_PAGE = 25;
	//Fields

	
	function __construct(){	
		$this->load->database();
		parent::__construct();
	}	
		
	/*
		Inserts only handle posts
	*/
	function insert($domain, $thumbnail)
	{
		try{
			$this->load->model('core/category_model');
			$this->load->model('core/user_model');
			$this->load->model('core/story_vote_model');

			$id = -1;
			if(strlen($this->input->post('category')) == 0){
				throw new Exception('Category is empty!');
			}
			
			
			$name = strip_tags($this->input->post('name'));
			$link = strip_tags($this->input->post('link'));
			$description = strip_tags($this->input->post('description'));
			$userId = $this->user_model->get_by_name($this->session->userdata('name'))->id;				
			$category = $this->category_model->get_by_name($this->input->post('category'));
			
			if(isset($category->id)){
				$categoryId = $category->id;					
			}else{
				throw new Exception('Category does not exist');
			}				 				

			if(strlen($categoryId) == 0){
				throw new Exception('Category does not exist!');
			}
			
			if(strlen($userId) == 0){
				throw new Exception('Not logged in!');
			}
			
			if($this->input->post('description') == '' && $this->exists($link, $categoryId) != -1){
				throw new Exception('Link Exists!');
			}
			//make sure you're not banned..
			$user = $this->user_model->get(array('id'=>$userId));
			if($user->row()->banned == 1){
				throw new Exception("User is banned!");
			}			
			
			$data = array(
				'name'=>$name,
				'link'=>$link,
				'domain'=>$domain,
				'description'=>$description,
				'thumbnail'=>$thumbnail,
				'userId' => $userId,
				'categoryId' => $categoryId
			);

			$this->db->insert($this->TABLE, $data);		
			$id = $this->db->insert_id();  //get latest insert id..
			//now do an insert into story_vote model
			$this->story_vote_model->insert($id, 1);
		}catch(Exception $e){
			throw new Exception($e->getMessage());
		}
	}
	
	
	function deleteBannedByUserId($userId){
		$query = "update story set deleted = 1 where userId = ?;";
		$this->db->query($query, array($userId));
	}

	function delete($storyId){
		try{
			$this->load->model('core/user_model');
			$userId = $this->user_model->get_by_name($this->session->userdata('name'))->id;
			$story = $this->get_by_storyId($storyId);	
			$isAdmin = ((isset($this->user_model->get_by_name($this->session->userdata('name'))->isAdmin) && $this->user_model->get_by_name($this->session->userdata('name'))->isAdmin == 1) ? true : false);

			if($userId == $story->userId || $isAdmin){
				$query = "update story set deleted = 1 where id = ".$storyId;				
				$this->db->query($query);
			}else{
				throw new Exception('This is not your story');
			}
		}catch(Exception $e){
			throw new Exception($e->getMessage());
		}
	}	
	
	function get_liked_by_userId($userId, $pageIndex=1){
		$this->load->model('core/user_model');
		$userId = $this->security->xss_clean($userId);
		$pageIndex = $this->security->xss_clean($pageIndex);
		
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
				inner join story_vote sv
					on sv.storyId = s.id
				inner join category c
					on c.id = s.categoryId
				where s.deleted = 0 and sv.score = 1 and sv.userId = ".$userId." and s.userId != ".$userId."
					group by s.id
				order by	
					s.created
				desc
					limit ".$this->ITEMS_PER_PAGE." offset ".(($pageIndex-1) * $this->ITEMS_PER_PAGE).";";

		return $this->db->query($query)->result();				
	}

	function get_by_userId($userId, $pageIndex=1){
		$this->load->model('core/user_model');
		$userId = $this->security->xss_clean($userId);
		$pageIndex = $this->security->xss_clean($pageIndex);
		
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
				where s.deleted = 0 and  s.userId = ".$userId."
					group by s.id
				order by	
					s.created
				desc
					limit ".$this->ITEMS_PER_PAGE." offset ".(($pageIndex-1) * $this->ITEMS_PER_PAGE).";";

		return $this->db->query($query)->result();		
	}

	function get_by_storyId($storyId){
		return $this->db->where('id', $storyId)->get($this->TABLE)->row(0);
	}

	function get_by_name($name){
		if($name == ''){return null;}
		return $this->db->where('name', $name)->get($this->TABLE)->row(0);
	}	

	function get_comment_count($id){
		$id = $this->security->xss_clean($id);
		$query = "select count(id) as commentCount from comment where storyId =".$id;
		return $this->db->query($query)->result();
	}

	function get_by_id($id){
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
				where s.deleted = 0 and s.id = ".($id)."
					group by s.id
				order by	
					s.created;";		
		return $this->db->query($query)->result();		
	}

	function get(){
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
	function exists($link, $categoryId)
	{		
		if($link == "" || strlen($link) == 0){throw new Exception('Empty Link');}			
		
		$this->db->select('id');
		$this->db->from($this->TABLE);		
		$where = array('link'=>$link, 'categoryId'=>$categoryId);
		$this->db->where($where);		
		$result = $this->db->get();
		
		foreach ($result->result() as $row){
			return $row->id;
		}		
		return -1;
	}
	
}

?>
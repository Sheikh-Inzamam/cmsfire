<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Comment_Model extends CI_Model{
	
	//Table
	var $TABLE = "comment";
	var $ITEMS_PER_PAGE = 25;
	//Fields
	function __construct(){	
		parent::__construct();
	}	
		
	/*
		Inserts only handle posts
	*/
	function insert()
	{
		try{			
			date_default_timezone_set('America/New_York');
			$date = new DateTime();
			$this->load->model('core/user_model');
			$this->load->model('core/comment_vote_model');			
			$this->load->model('core/comment_spam_model');			
			
			$userId = $this->user_model->get_by_name($this->session->userdata('name'))->id;
			$this->comment_spam_helper($userId);

			$comment = $this->input->post('comment');			
			$comment = strip_tags($comment);
			
			$parentCommentId = $this->input->post('parentCommentId');
			$storyId = $this->input->post('storyId');
			
			if($comment == ''){
				throw new Exception('Comment is empty');
			}

			if(strlen($userId) == 0){
				throw new Exception('Not logged in!');
			}

			$user = $this->user_model->get(array('id'=>$userId));
			if($user->row()->banned == 1){
				throw new Exception("User is banned!");
			}			

			$data = array(
				'comment'=>$comment,
				'userId'=>$userId,
				'parentCommentId'=>$parentCommentId,
				'storyId'=>$storyId			
			);
			
			$this->db->insert($this->TABLE, $data);
			$id = $this->db->insert_id();  //get latest insert id..

			$where = array(
    			'userId' => $userId
    		);
			$this->comment_spam_model->update(array('comment_spam_offense_count' => 0,
				'last_comment_post' => $date->getTimestamp()
			), $where);
			
			//now do an insert into story_vote model
			$this->comment_vote_model->insert($id, 1);
		}catch(Exception $e){
			throw new Exception($e->getMessage());
		}
	}


    function comment_spam_helper($userId)
    {

		$date = new DateTime();    	
    	$this->load->model('core/comment_spam_model');    	
    	
    	$where = array(
    		'userId' => $userId
    	);
    	
    	$commentSpam = $this->comment_spam_model->get($where);
		
		$last_comment_post = null;
		$comment_spam_offense_count = 0;

    	if($commentSpam->num_rows() == 0){
    		//if 0, create entries...
    		$data = array(
    			'userId' => $userId,
    			'last_comment_post' => $date->getTimestamp()
    		);    		    		
    		$this->comment_spam_model->insert($data);
    		$last_comment_post = $date->getTimestamp(); 		
    		return;
    	}else{
    		//if there's an entry...
    		//load em up!
    		$last_comment_post = $commentSpam->row(0)->last_comment_post;
    		$comment_spam_offense_count = $commentSpam->row(0)->comment_spam_offense_count;
    	}

    	//check to see when their last message was sent..
		if($last_comment_post != null){
			//see how long ago the message was posted..
			$timeInSeconds = time() - $last_comment_post; // to get the time since that moment				

			//if it's still under a minute then bump up the time you have to wait..
			if((($timeInSeconds / 60) < 1) && $comment_spam_offense_count == 0){				
				$this->comment_spam_model->update(array('comment_spam_offense_count' => ($comment_spam_offense_count + 1)), $where);
				throw new Exception("Please come back in 1 Minute to prevent flooding the boards.");
			}else{				

				switch($comment_spam_offense_count){
					case 0:
						if(($timeInSeconds / 60) >= 1){

							$this->comment_spam_model->delete($where);							
						}else{

							$this->comment_spam_model->update(array('comment_spam_offense_count' => ($comment_spam_offense_count + 1)), $where);
							throw new Exception("Please come back in 1 Minutes to prevent flooding the boards.");			
						}	
					break;						
					case 1:
						if(($timeInSeconds / 60) >= 1){							
							$this->comment_spam_model->delete($where);
						}else{							
							$this->comment_spam_model->update(array('comment_spam_offense_count' => ($comment_spam_offense_count + 1)), $where);
							throw new Exception("Please come back in 5 Minutes to prevent flooding the boards.");			
						}						
					break;

					case 2:
						if(($timeInSeconds / 60) >= 5){

							$this->comment_spam_model->delete($where);
						}else{							
							$this->comment_spam_model->update(array('comment_spam_offense_count' => ($comment_spam_offense_count + 1)), $where);
							throw new Exception("Please come back in 10 Minutes to prevent flooding the boards.");			
						}
					break;

					case 3:
						if(($timeInSeconds / 60) >= 10){

							$this->comment_spam_model->delete($where);
						}else{							
							$this->comment_spam_model->update(array('comment_spam_offense_count' => ($comment_spam_offense_count + 1)), $where);
							throw new Exception("Okay now you're not even reading the messages.  Come back in an hour.");			
						}
					break;

					default:
						if(($timeInSeconds / 60) >= 60){							
							$this->comment_spam_model->delete($where);
						}else{							
							$this->comment_spam_model->update(array('comment_spam_offense_count' => ($comment_spam_offense_count + 1)), $where);
							throw new Exception("Okay now you're not even reading the messages.  Come back in an hour.");			
						}						
				}
			}				
		}
	}

	function deleteBannedByUserId($userId){
		$query = "update comment set deleted = 1 where userId = ?;";
		$this->db->query($query, array($userId));
	}

	function delete($commentId){
		try{
			$this->load->model('core/user_model');
			$userId = $this->user_model->get_by_name($this->session->userdata('name'))->id;
			$comment = $this->get_by_commentId($commentId);
			$isAdmin = ((isset($this->user_model->get_by_name($this->session->userdata('name'))->isAdmin) && $this->user_model->get_by_name($this->session->userdata('name'))->isAdmin == 1) ? true : false);

			if($userId == $comment->userId || $isAdmin){
				$query = "update comment set deleted = 1 where id = ".$commentId;
				$this->db->query($query);
			}else{
				throw new Exception('This is not your comment');
			}
		}catch(Exception $e){
			throw new Exception($e->getMessage());
		}
	}

	function get_comments_liked_by_userId($userId, $pageIndex=1){
		$this->load->model('core/user_model');
		$userId = $this->security->xss_clean($userId);
		$pageIndex = $this->security->xss_clean($pageIndex);
		$query = "SELECT c.id as id,
			c.comment, 
			s.id as storyId,
			s.name as storyName,
			cy.name as categoryName,
			uo.name as creatorName,
			c.parentCommentId,
			SUM(cv.score) as score,
			u.name as name,
			TIMESTAMPDIFF(second,c.submitted,current_timestamp()) as seconds, 
			TIMESTAMPDIFF(day,c.submitted,current_timestamp()) as days,
			TIMESTAMPDIFF(hour,c.submitted,current_timestamp()) as hours,
			TIMESTAMPDIFF(minute,c.submitted,current_timestamp()) as minutes,
			TIMESTAMPDIFF(year,c.submitted,current_timestamp()) as years	
				from comment c
			inner join story s
				on s.id = c.storyId
			left join user u
				on u.id = c.userId
			left join category cy
				on cy.id = s.categoryId
			left join user uo
				on uo.id = s.userId				
			inner join comment_vote cv
				on cv.commentId = c.id
			where c.deleted = 0 and cv.score = 1 and cv.userId = ".$userId." and c.userId != ".$userId."
				group by c.id
			order by	
				c.submitted
			desc
				limit ".$this->ITEMS_PER_PAGE." offset ".(($pageIndex-1) * $this->ITEMS_PER_PAGE).";";

		return $this->db->query($query)->result();		
	}

	function get_by_userId($userId, $pageIndex=1){
		$this->load->model('core/user_model');
		$userId = $this->security->xss_clean($userId);
		$pageIndex = $this->security->xss_clean($pageIndex);
		$query = "SELECT c.id as id,
			c.comment, 
			s.id as storyId,
			s.name as storyName,
			cy.name as categoryName,
			uo.name as creatorName,
			c.parentCommentId,
			SUM(cv.score) as score,
			u.name as name,
			TIMESTAMPDIFF(second,c.submitted,current_timestamp()) as seconds, 
			TIMESTAMPDIFF(day,c.submitted,current_timestamp()) as days,
			TIMESTAMPDIFF(hour,c.submitted,current_timestamp()) as hours,
			TIMESTAMPDIFF(minute,c.submitted,current_timestamp()) as minutes,
			TIMESTAMPDIFF(year,c.submitted,current_timestamp()) as years	
				from comment c
			inner join story s
				on s.id = c.storyId
			left join user u
				on u.id = c.userId
			left join category cy
				on cy.id = s.categoryId
			left join user uo
				on uo.id = s.userId				
			left join comment_vote cv
				on cv.commentId = c.id
			where c.userId = ".$userId." and c.deleted = 0
				group by c.id
			order by	
				c.submitted
			desc
				limit ".$this->ITEMS_PER_PAGE." offset ".(($pageIndex-1) * $this->ITEMS_PER_PAGE).";";

		return $this->db->query($query)->result();
	}

	function get_by_commentId($commentId){
		return $this->db->where('id', $commentId)->get($this->TABLE)->row(0);
	}
		
	function get($storyId, $parentCommentId, $pageIndex){
		if($storyId == ''){return null;}				
		$storyId = $this->security->xss_clean($storyId);
		$query = "SELECT c.id as id,
				c.comment, 
				c.parentCommentId,
				SUM(cv.score) as score,
				u.name as name,
				c.deleted as commentDeleted,
				TIMESTAMPDIFF(second,c.submitted,current_timestamp()) as seconds, 
				TIMESTAMPDIFF(day,c.submitted,current_timestamp()) as days,
				TIMESTAMPDIFF(hour,c.submitted,current_timestamp()) as hours,
				TIMESTAMPDIFF(minute,c.submitted,current_timestamp()) as minutes,
				TIMESTAMPDIFF(year,c.submitted,current_timestamp()) as years	
					from comment c
				inner join story s
					on s.id = c.storyId
				left join user u
					on u.id = c.userId					
				left join comment_vote cv
					on cv.commentId = c.id
				where c.parentCommentId = ".$parentCommentId." ".(($storyId == '') ? '' : "and s.id = ".$storyId)."
					group by c.id
				order by	
					((COALESCE(SUM(cv.score),0)-1)/POW(((UNIX_TIMESTAMP(NOW()) -UNIX_TIMESTAMP(c.submitted))/3600)+2,1.5))
				desc
					limit ".$this->ITEMS_PER_PAGE." offset ".(($pageIndex-1) * $this->ITEMS_PER_PAGE).";";
		return $this->db->query($query)->result();
	}
}

?>
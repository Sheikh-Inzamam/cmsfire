<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('generate_list_comment_helper'))
{
    function generate_list_comment_helper($datalist, $username, $isAdmin)
    {		    	
    	//You may need to load the model if it hasn't been pre-loaded
    	$CI = get_instance();
    	$CI->load->model('core/comment_vote_model');

    	$list = "<ul class='ul-comments-user-list'>";
    	//echo count($datalist);
    	foreach($datalist as $row){    		
			$list .= "<li id='comment-".$row->id."' class='comment-".$row->id." ".(($row->parentCommentId > 0) ? 'child' : '' )."' value='".$row->id."' >";
			$list .= "<a href='/story/display/".$row->storyId."' id='story-link-username-".$row->id."' class='story-link-username'>".$row->storyName."</a> by ";
			$list .= "<a href='/user/".$row->creatorName."' id='story-link-username-".$row->id."' class='story-link-username'>".$row->creatorName."</a>";			
			$list .= " in <a href='/f/".$row->categoryName."' class='story-link-domain comments-user'>".$row->categoryName."<br/>";
			$list .= "<a href='/user/".$row->name."' id='story-link-username-".$row->id."' class='story-link-username'><b>".$row->name."</b></a>";
			$list .= '<a href="javascript:void(0);" id="comment-link-upvote-'.$row->id.'" class="comment-link-upvote fui-plus-24 ';		
			if($username != ''){
				$comment = $CI->comment_vote_model->get_by_commentId($row->id);
				if(isset($comment->score) && $comment->score == 1){
					$list .= "voted";
				}
			}
			$list .= '" value="'.$row->id.'">&hearts;</a>';						
			$list .= "<label class='comment-post-score'>".$row->score." points</label>";
			$list .= "<label class='story-link-time-ago'>".convert_time_helper($row->days, $row->hours, $row->years, $row->minutes, $row->seconds)."</label><br/>";
			$list .= "<div id='comment-container-".$row->parentCommentId."' class='comment-container'>";				
			$list .= "<label class='comment-post'>".htmlentities($row->comment)."</label><br/>";			
			

			$list .= "<a class='comment-delete-btn-profile' id='story-link-comments-count-".$row->id."' href='/story/display/".$row->storyId."'>link</a>";
			if($row->name == $username || $isAdmin == 'true'){				
				$list .= " | <a href='/comment/delete/".$row->id."/false' id='comment-delete-".$row->id."' class='comment-delete-btn-profile' value='".$row->id."' value='".$row->id."'>delete</a>";
			}

			$list .= "</div>";				
			$list .= "</li>";
    	}

		$list .= '</ul>';
		return $list;
	}
	
}


if ( ! function_exists('generate_list_submit_helper'))
{
    function generate_list_submit_helper($datalist, $username, $isAdmin)
    {		
    	//You may need to load the model if it hasn't been pre-loaded
    	$CI = get_instance();    	
    	$CI->load->model('core/story_vote_model');
    	$CI->load->model('core/story_model');
    	$list ='<ol id="ul-story-links" class="ul-story-links">';
    	foreach($datalist as $row){ 
    		$get_comment_count_result = $CI->story_model->get_comment_count($row->id);
    		$commentCount = 0;
    		foreach($get_comment_count_result as $commentResult){
    			$commentCount = $commentResult->commentCount;
    		}
			$list .="<li id='story-entry-".$row->id."' class='story-entry'>";
			$link = $row->link;
			$score = $row->score;
			$domain = $row->domain;

			if($score === null){$score = 0;}			
			
			if(strlen($row->link) > 0){
				$linkParam = "\"".$row->link."\"";
				$list .="<a id='story-link-".$row->id."' class='story-link' href='".$row->link."'>";
			}else{
				$linkParam = "\"/story/display/".$row->id."\"";
				$list .="<a id='story-link-".$row->id."' class='story-link' href='".$linkParam."'>";
			}
			$list .= $row->title;
			$list .="</a>";
			$list .="<a href='javascript:void(0);' id='story-link-upvote-".$row->id."' class='story-link-upvote fui-plus-24 ";

			if($username != ''){
				$story = $CI->story_vote_model->get_by_storyId($row->id);
				if(isset($story->score) && $story->score == 1){
					$list .= "voted";
				}
			}
			$list .= "' value='".$row->id."'>&hearts;</a>";
			if(strlen($domain) > 0){
				$list .="<a href='http://".$row->domain."' class='story-link-domain'>(".$domain.")</a>";
			}else{
				$list .="<a href='/f/".$row->categoryname."' class='story-link-domain'>(self.".$row->categoryname.")</a>";
			}

			$list .="<br/><label class='story-link-score'>".$score." points</label>";
			$list .="<label class='story-link-by'>by</label>";
			$list .="<a href='/user/".$row->name."' id='story-link-username-".$row->id."' class='story-link-username'>".$row->name."</a>";
			$list .="<label class='story-link-time-ago'>".convert_time_helper($row->days, $row->hours, $row->years, $row->minutes, $row->seconds)."</label>";
			$list .="<label class='story-link-to'>to</label>";
			$list .="<a href='/f/".$row->categoryname."' class='story-link-categoryname'>".$row->categoryname."</a> | ";

			$commentsLink = "\"/story/display/".$row->id."\"";
			$list .="<a class='story-link-comments-count' id='story-link-comments-count-".$row->id."' href='".$commentsLink."'>".$commentCount." comments</a>";
			if($username == $row->name || $isAdmin == "true"){
				$list .=" | <a href='/story/delete/".$row->id."/false' id='story-delete-".$row->id."' class='story-delete-btn' value='".$row->id."' value='".$row->id."'>delete</a>";
			}				
			$list .="</li>";


    	}

		$list .= '</ol>';
		return $list;
	}
	
}
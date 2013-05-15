<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('comment_spam_helper'))
{
    function comment_spam_helper()
    {
    	$CI = get_instance();    	
    	//check to see when their last message was sent..
		if($CI->session->userdata('last_coment_post') !== FALSE){
			//see how long ago the message was posted..
			$timeInSeconds = time() - $CI->session->userdata('last_coment_post'); // to get the time since that moment				
			//if it's still under a minute then bump up the time you have to wait..
			if((($timeInSeconds / 60) < 1) && $CI->session->userdata('comment_spam_offense_count') == 0){
				$CI->session->set_userdata('comment_spam_offense_count',  $CI->session->userdata('comment_spam_offense_count') + 1);					
				throw new Exception("Please come back in 1 Minute to prevent flooding the boards.");
			}else{
				if($CI->session->userdata('comment_spam_offense_count') == false){
					$CI->session->set_userdata('comment_spam_offense_count', 0);
				}
				switch($CI->session->userdata('comment_spam_offense_count')){
					case 0:
						if(($timeInSeconds / 60) >= 1){
							$CI->session->unset_userdata('last_coment_post');
							$CI->session->set_userdata('comment_spam_offense_count',  0);
						}else{
							$CI->session->set_userdata('comment_spam_offense_count',  $CI->session->userdata('comment_spam_offense_count') + 1);
							throw new Exception("Please come back in 1 Minutes to prevent flooding the boards.");			
						}	
					break;						
					case 1:
						if(($timeInSeconds / 60) >= 1){
							$CI->session->unset_userdata('last_coment_post');
							$CI->session->set_userdata('comment_spam_offense_count',  0);
						}else{
							$CI->session->set_userdata('comment_spam_offense_count',  $CI->session->userdata('comment_spam_offense_count') + 1);					
							throw new Exception("Please come back in 5 Minutes to prevent flooding the boards.");			
						}						
					break;

					case 2:
						if(($timeInSeconds / 60) >= 5){
							$CI->session->unset_userdata('last_coment_post');
							$CI->session->set_userdata('comment_spam_offense_count',  0);
						}else{
							$CI->session->set_userdata('comment_spam_offense_count',  $CI->session->userdata('comment_spam_offense_count') + 1);					
							throw new Exception("Please come back in 10 Minutes to prevent flooding the boards.");			
						}
					break;

					case 3:
						if(($timeInSeconds / 60) >= 10){
							$CI->session->unset_userdata('last_coment_post');
							$CI->session->set_userdata('comment_spam_offense_count',  0);
						}else{
							$CI->session->set_userdata('comment_spam_offense_count',  $CI->session->userdata('comment_spam_offense_count') + 1);					
							throw new Exception("Okay now you're not even reading the messages.  Come back in an hour.");			
						}
					break;

					default:
						if(($timeInSeconds / 60) >= 60){
							$CI->session->unset_userdata('last_coment_post');
							$CI->session->set_userdata('comment_spam_offense_count',  0);
						}else{
							$CI->session->set_userdata('comment_spam_offense_count',  $CI->session->userdata('comment_spam_offense_count') + 1);					
							throw new Exception("Okay now you're not even reading the messages.  Come back in an hour.");			
						}						
				}
			}				
		}
	}
	
}
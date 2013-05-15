$(document).ready(
	function() {
		var loadMoreCommentsTxt = "<li class='comment-item child'><a href='javascript:void(0);' class='load-more-comments-link'>Load More Comments</a></li>";
		var _storyId = -1;
		var _url = "";
		var isFirst = true;
		function loadComments(url, storyId, parentCommentId, pageIndex){		
			//do json call
			_storyId = storyId;
			_url = url;
			var jqxhr = $.getJSON(url + storyId + "/" + parentCommentId + "/" + pageIndex, function() {})			
			.done(function(data) {
				handleCommentsPageContent(data.comments, url, storyId, parentCommentId, pageIndex, false, data.enablePagination);
				handleEmptyComments(data.comments);	
				handleCommentVoted(data.comments);		
				addHandlers(false);
			})
			.fail(function() { console.log( "error loading content" ); })			
		}

		function handleEmptyComments(data){			
			if(data.length == 0){
				var emptyCommentEntry = "<div class='empty-comment'><i>There are no comments</i></div>";
				$("#comments-content").append(emptyCommentEntry);	
			}
		}

		//add all the links to the page now.
		function handleCommentsPageContent(data, url, storyId, parentCommentId, pageIndex, addToParent, enablePagination){
			//find content div.
			var storyEntry = "";
			var firstEntryId = -1;
			if(!addToParent || data.length > 0){
				storyEntry = "<ul class='ul-comments-post' value='" + parentCommentId + "' pageIndex='" + pageIndex + "'>";
			}
			
			$.each(data, function(i, item)
			{
				var comment = item.comment;
				var name = item.name;
				var score = item.score;
				var deleted = item.commentDeleted;

				if(i == 0){
					firstEntryId = item.id;
				}

				storyEntry += "<li id='comment-" + item.id + "' class='comment-item " + ((parentCommentId > 0) ? 'child' : '' ) + "' value='" + item.id + "' >";				
				if(deleted == 0){
					storyEntry += "<a href='/user/" + item.name + "' id='story-link-username-" + i + "' class='story-link-username'>" + item.name + "</a>";
					storyEntry += '<a href="javascript:void(0);" id="comment-link-upvote-' + item.id + '" class="comment-link-upvote fui-plus-24" value="' + item.id + '">&hearts;</a>';
				}else{
					storyEntry += "<label id='story-link-username-" + i + "' class='story-link-username-deleted'>[deleted]</label>";
				}				
				storyEntry += "<label class='comment-post-score'>" + score + " points</label>";
				storyEntry += "<label class='story-link-time-ago'>" + convert_time_helper(item.days, item.hours, item.years, item.minutes, item.seconds) + "</label><br/>";
				storyEntry += "<div id='comment-container-" + item.parentCommentId + "' class='comment-container'>";

				if(deleted == 0){
					storyEntry += "<label class='comment-post'>" + comment.replace(/\n/g , "<br>") + "</label><br/>";
					storyEntry += "<a href='javascript:void(0);' id='comment-reply-" + i + "' class='comment-reply-btn' value='" + item.id + "'>reply</a>";
				}else{
					storyEntry += "<label class='comment-post'><i>deleted</i></label><br/>";
				}				
				if((username == item.name || isAdmin == "true") && deleted == 0){
					storyEntry += "<a href='javascript:void(0);' id='comment-delete-" + i + "' class='comment-delete-btn' value='" + item.id + "' value='" + item.id + "'>delete</a>";
				}
				storyEntry += '<form class="comment-post-form" id="comment-post-form-' + item.id + '" style="display:none;">';
				storyEntry += '<textarea name="comment" id="comment" rows="8" cols="70"></textarea>';
				storyEntry += '<input type="hidden" name="storyId" value="' + storyId + '">';
				storyEntry += '<input type="hidden" name="parentCommentId" value="' + item.id + '"><br/>';
				storyEntry += '<input type="button" name="submit" value="Post" class="comment-post-btn"  />';
				storyEntry += '<input type="button" name="submit" value="Cancel" class="comment-cancel-btn" onClick="javascript:$(this).closest(\'form\').hide();"  /><br/>';
				storyEntry += '<div class="comment-error-home-label"></div>';
				storyEntry += '</form>';
				storyEntry += "</div>";				
				storyEntry += "</li>";
				if(enablePagination == "true"){
					loadNestedComments(url, storyId, item.id, 1, true, false);
				}else{
					loadNestedComments(url, storyId, item.id, pageIndex, true, false);
				}
			});

			if(!addToParent || data.length > 0){
				storyEntry += "</ul>";
			}

			if(addToParent){											
				$("#comment-" + parentCommentId).append(storyEntry);
			}else{					
				$("#comments-content").append(storyEntry);
			}
			addHandlers(false);

			if(parentCommentId != 0 && ((pageIndex-1) > 0)){
				$('.ul-comments-post[value=' + parentCommentId + '][pageIndex=' + (pageIndex-1) + ']:last-child').append($("#comment-" + firstEntryId));
			}

			if(enablePagination === "true"){
				$('.ul-comments-post[value=' + parentCommentId + '][pageIndex=' + pageIndex + ']:last-child').append(loadMoreCommentsTxt);
			}

			handleCommentVoted(data);
			initReplyHandler();
			initHomePostHandler();
			initDeleteHandler();
			initLoadMoreCommentsHandler();			
		}

		function initLoadMoreCommentsHandler(){
			$('.load-more-comments-link').unbind();
			$( ".load-more-comments-link" ).each(function() {				
				$(this).click (function(){
					initialPaginate = -1;
					var pageIndex = $(this).closest("ul").attr("pageIndex");
					var parentCommentId = $(this).closest("ul").attr("value");
					pageIndex++;
					var target = $(this);				
					target.html("loading...");
					target.css("color", "red");
					$.ajax({
					    type: "GET",
					    url: "/comment/get/" + _storyId + "/" + parentCommentId + "/" + pageIndex,		    
					    dataType: "json",
					    success: function(data){
							setTimeout(function() {
							  target.parent().remove();
							  handleCommentsPageContent(data.comments, _url, _storyId, parentCommentId, pageIndex, false, data.enablePagination);
							}, 400);					    						    
					    },
					    failure: function(errMsg) {
					        console.log(errMsg);
					    }
					});
				});
			});
		}		

		function loadNestedComments(url, storyId, parentCommentId, pageIndex, addToParent){
			var jqxhr = $.getJSON(url + storyId + "/" + parentCommentId + "/" + pageIndex, function() {})			
			.done(function(data) {
				handleCommentsPageContent(data.comments, url, storyId, parentCommentId, pageIndex, addToParent, data.enablePagination);
			})
			.fail(function() { console.log( "error loading content" ); })			
		}

		function initReplyHandler(){
			$('.comment-reply-btn').unbind();
			$( ".comment-reply-btn" ).each(function() {				
				$(this).click (function(){
					$("#comment-post-form-" + $(this).attr("value")).show();					
				});
			});
		}

		function initDeleteHandler(){
			$('.comment-delete-btn').unbind();
			$( ".comment-delete-btn" ).each(function() {
				$(this).click (function(){
					var jqxhr = $.getJSON("/comment/delete/" + $(this).attr("value"), function() {})			
					.done(function(data) {
						//location
						if(data.result == "Success!"){
							location.reload();
						}else{
							console.log(data);
						}
					})
					.fail(function() { console.log( "error loading content" ); });
				});
			});
		}		

		function initHomePostHandler(){
			$('.comment-post-btn').unbind();
			$( ".comment-post-btn" ).each(function() {
				$(this).die("click").live("click", function(){
	    			var dataToBeSent = $(this).closest("form").serialize();
	    			var errorLabel = $(this).closest("form").find(".comment-error-home-label");    			

					$.ajax({
					    type: "POST",
					    url: "/comment/submit",				    
					    data: dataToBeSent,				    
					    dataType: "json",
					    success: function(data){
					    	if(data.result == 'Success!'){
								location.reload();
							}else{
								errorLabel.html(data.result);
							}						
					    },
					    failure: function(errMsg) {
					        console.log(errMsg);
					    }
					});
				});
			});
		}

		
		window.loadComments=loadComments;
	}
);
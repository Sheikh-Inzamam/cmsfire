$(document).ready(
	function() {
		function loadContent(baseUrl, pageIndex){			
			//do json call
			var jqxhr = $.getJSON( baseUrl + "/" + pageIndex, function() {})
			
			.done(function(data) {
				handleLoadedPageContent(data); 
				addHandlers(true);
				handleVoted(data);
				removeOlPeriods();
			})
			.fail(function() { console.log( "error loading content" ); })			
		}		

		function removeOlPeriods(){
			$("ol").each(function() {
				$(this).find("li").each(function(count) {
				$(this)
				.css("list-style-type", "none")
				.prepend("<label id='story-entry-count'>" + (count + 1) + ") </label>");
				})
			})
		}

		//add all the links to the page now.
		function handleLoadedPageContent(data){
			//find content div.				
			var storyEntry = '<ol id="ul-story-links" class="ul-story-links">';
			$.each(data, function(i, item)
			{
				storyEntry += "<li id='story-entry-" + item.id + "' class='story-entry'>";				
				var link = item.link;
				var score = item.score;
				var domain = item.domain;

				if(score === null){score = 0;}
				if(typeof(domain) === "undefined"){domain = document.domain;}
				
				if(typeof(link) !== "undefined" && link.length > 0){
					storyEntry += "<a id='story-link-" + i + "' class='story-link' href='" + link + "'>";
				}else{
					storyEntry += "<a id='story-link-" + i + "' class='story-link' href='/story/display/" + item.id + "'>";
				}
				storyEntry +=  item.title;
				storyEntry += "</a>";
				storyEntry += "<a href='javascript:void(0);' id='story-link-upvote-" +  item.id + "' class='story-link-upvote fui-plus-24' value='" + item.id + "'>&hearts;</a>";
				if(domain.length > 0){
					storyEntry += "<a href='http://" + domain + "' class='story-link-domain'>(" + domain + ")</a>";
				}else{
					storyEntry += "<a href='/f/" + item.categoryname + "' class='story-link-domain'>(self." + item.categoryname + ")</a>";
				}
				storyEntry += "<a href='javascript:void(0);' class='story-link-downvote fui-cross-24' value='" + item.id + "'>&times;</a>";

				storyEntry += "<br/><label class='story-link-score'>" + score + " points</label>";
				storyEntry += "<label class='story-link-by'>by</label>";
				storyEntry += "<a href='/user/" + item.name + "' id='story-link-username-" + i + "' class='story-link-username'>" + item.name + "</a>";
				storyEntry += "<label class='story-link-time-ago'>" + convert_time_helper(item.days, item.hours, item.years, item.minutes, item.seconds) + "</label>";
				storyEntry += "<label class='story-link-to'>to</label>";
				storyEntry += "<a href='/f/" + item.categoryname + "' class='story-link-categoryname'>" + item.categoryname + "</a> | ";
				storyEntry += "<a class='story-link-comments-count' id='story-link-comments-count-" + item.id + "' href='/story/display/" + item.id + "'>0 comments</a>";
				if(username == item.name || isAdmin === true){
					storyEntry += " | <a href='javascript:void(0);' id='story-delete-" + i + "' class='story-delete-btn' value='" + item.id + "' value='" + item.id + "'>delete</a>";
				}				
				storyEntry += "</li>";
			});
			storyEntry += "</ol>";			
			
			$('#content').append(storyEntry);
			$.each(data, function(i, item)
			{
				handleCommentCount(item.id);	
			});


			//disable for now...
			//initDeleteHandler();
		}

		function initDeleteHandler(){
			$('.story-delete-btn').unbind();
			$( ".story-delete-btn" ).each(function() {				
				$(this).click (function(){
					var jqxhr = $.getJSON("/story/delete/" + $(this).attr("value"), function() {})			
					.done(function(data) {
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

		function handleCommentCount(storyId){
			var jqxhr = $.getJSON( "/story/getCommentCount/" + storyId, function() {})
			
			.done(function(data) {				
				$.each(data, function(i, item)
				{
					$("#story-link-comments-count-" + storyId).html(item.commentCount + " comments");
				});
			})
			.fail(function() { console.log( "error loading content" ); })					
		}
		
		window.loadContent=loadContent;
	}
);
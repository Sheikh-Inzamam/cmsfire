<div class="main-content">
	<div id="actions-navigator-container">
		<div id="actions-navigator-content">
			<div id="submit-link"><a href="#submit-link-modal" class="post-link" id="post-link">Post Story</a></div>
			<?php
				//if you're the admin then show a button to create categories.
				if($isAdmin == "true"){
					echo '<div id="category-link"><a href="#submit-category-modal" class="post-category" id="post-category">Create Category</a></div>';
				}
			?>						
		</div>
	</div>	
	<div id="story-data-content"></div>
</div>


<div id="home-post-input"></div>
<div id="comment-divider"></div>
<div id="comments-content"></div>
<div id="captcha-hidden-placeholder" style="display:none;">
	<?php
		//do this because you js file doesn't load php by default.
		echo img('image/securimage', TRUE);
	?>
</div>

<script type="text/javascript" src="/js/libraries/helperCalls.js"></script>
<script type="text/javascript" src="/js/core/storyLoad.js"></script>
<script type="text/javascript" src="/js/core/commentLoad.js"></script>
<script type="text/javascript">
var username = '<?php echo (isset($username)) ? $username : '' ?>';
var isAdmin = <?php echo (isset($isAdmin)) ? "'".$isAdmin."'" : 'false' ?>;
$(document).ready(
	function() {
		var storyId = '<?php echo (isset($storyId)) ? $storyId : '' ?>';		

		init();
		initHomePost();
		function init(){					
			loadStory("/story/load/", storyId);
			loadComments("/comment/get/",storyId, 0, 1);
		}

		function isInt(n) {
 		 	return typeof n === 'number' && n % 1 == 0;
		}

		function initHomePost(){
			var homePostInput = '<form class="comment-post-form">' +
				'<textarea name="comment" id="comment" rows="8" cols="70"></textarea>' +
				'<input type="hidden" name="storyId" value="' + storyId + '">' +
				'<input type="hidden" name="parentCommentId" value="0"><br/>' +
				'<input type="button" name="submit" value="Post" class="comment-post-btn"  /><br/>'+
				'<div class="comment-error-home-label"></div>' +
				'</form>';
			$("#home-post-input").append(homePostInput);
		}		
	}
);
</script>
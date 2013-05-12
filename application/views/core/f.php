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
	<div id="content"></div>	
</div>
<div id="pagination"></div>

<script type="text/javascript" src="/js/libraries/helperCalls.js"></script>
<script type="text/javascript" src="/js/core/storyActions.js"></script>
<script type="text/javascript">
var username = '<?php echo (isset($username)) ? $username : '' ?>';
var isAdmin = <?php echo (isset($isAdmin)) ? $isAdmin : 'false' ?>;
$(document).ready(
	function() {
		init();
		handlePagination();
		
		function init(){
			var callback = $.Callbacks();			
			var pageIndex = "<?php echo $pageIndex; ?>";
			var latest = <?php echo ((isset($latest)) ? 'true': 'false'); ?>;
			var base = '/f/<?php echo $category; ?>/load';

			if(latest == true){
				base = '/f/<?php echo $category; ?>/load_latest';
			}

			if(!isInt(parseInt(pageIndex))){
				pageIndex = 1;
			}						
			callback.add(loadContent(base, pageIndex));
		}

		function handlePagination(){			
			var pageIndex = "<?php echo $pageIndex; ?>";
			var originalPageIndex = pageIndex;
			var category = '<?php echo $category; ?>';
			var showNextPage = <?php echo $showNextPage; ?>;
			var nextLink = '';
			var prevLink = '';
			var latest = '<?php 
				if(isset($latest)){
					echo $latest; 
				}else{
					echo '';
				}?>';			
			if(showNextPage){
				pageIndex++;
				if(category != ''){					
					if(latest != ''){
						nextLink += "<a href='/f/" + category + "/latest/" + pageIndex + "' class='next-link-button'>Next ></a>";
					}else{
						nextLink += "<a href='/f/" + category + "/" + pageIndex + "' class='next-link-button'>Next ></a>";
					}
				}else{
					if(latest != ''){
						nextLink += "<a href='/home/latest/" + pageIndex + "' class='next-link-button'>Next ></a>";
					}else{
						nextLink += "<a href='/home/page/" + pageIndex + "' class='next-link-button'>Next ></a>";
					}
				}
			}

			if(category != ''){
				if((originalPageIndex-1) != 0){
					if(latest != ''){
						prevLink += "<a href='/f/" + category + "/latest/" + --originalPageIndex + "' class='prev-link-button'>< Prev</a>";
					}else{
						prevLink += "<a href='/f/" + category + "/" + --originalPageIndex + "' class='prev-link-button'>< Prev</a>";
					}
				}				
			}else{
				if((originalPageIndex-1) != 0){
					if(latest != ''){
						prevLink += "<a href='/home/latest/" + --originalPageIndex + "' class='prev-link-button'>< Prev</a>";
					}else{
						prevLink += "<a href='/home/page/" + --originalPageIndex + "' class='prev-link-button'>< Prev</a>";
					}
				}
			}			
			
			$('#pagination').append(prevLink);
			if(prevLink.length > 0 && nextLink.length > 0){
				$('#pagination').append('<span class="divider-next-prev"></span>');
			}
			$('#pagination').append(nextLink);	
		}

		function isInt(n) {
 		  return typeof n === 'number' && n % 1 == 0;
		}
	}
);
</script>
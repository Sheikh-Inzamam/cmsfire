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
	<div id="content">
		<?php echo $loadContent; ?>
	</div>	
</div>
<div id="pagination">
<?php
$nextPage = $pageIndex + 1;
$showPreviousPage = ((($pageIndex-1) == 0) ? true : false);


if(($pageIndex-1) != 0){
	echo "<a href='/user/".$loadedUsername."/".$type."/".((($pageIndex-1)==0) ? '1' : ($pageIndex-1) )."' class='next-link-button'>< Prev</a>";
}

if(isset($showNextPage)){
	if($showNextPage == 'true'){		
		echo "<a href='/user/".$loadedUsername."/".$type."/".$nextPage."' class='next-link-button'>Next ></a>";
	}
}
?>
</div>

<div id="captcha-hidden-placeholder" style="display:none;">
	<?php
		//do this because you js file doesn't load php by default.
		echo img('image/securimage', TRUE);
	?>
</div>
<script type="text/javascript" src="/js/libraries/helperCalls.js"></script>
<script type="text/javascript" src="/js/core/storyActions.js"></script>
<script type="text/javascript">

$(document).ready(
	function() {
		init();
		
		function init(){
			var type = "<?php echo $type; ?>";
			if(type == "comments"){
				addHandlers(false);				
			}else if(type == "submitted" || type == "liked"){
				addHandlers(true);
			}

			enableListNumbers();
		}	
	}
);
</script>
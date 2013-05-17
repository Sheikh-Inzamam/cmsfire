var isLoggedIn = false;
var categoriesData = null;
$(document).ready(	
	function() {
		init();
		
		function init(){
			initCategories();
			initLoggedIn();

			var element = $('#navigation-tabs').detach();
			$('#navigation-cover').append(element);
		}

		function initLoggedIn(){
			//do json call
			var jqxhr = $.getJSON( "/login/isLoggedIn", function() {})			
			.done(function(data) {
				initLoggedInContent(data);
			})
			.fail(function() { console.log( "error loading content" ); })
		}

		function initLoggedInContent(data){
			var loggedInContent = '';
			//loggedInContent += '<a href="/" id="home-link">Home</a>';
			if(data.isLoggedIn == 'true'){
				isLoggedIn = true;
				loggedInContent += '<a href="/user/' + data.username + '" class="navigation-header-user">' + data.username + '</a>';
				loggedInContent += '<a href="javascript:void(0);" class="navigation-header-logout">Logout</a>';				
				$('#navigation-header-user').html(loggedInContent);
				initLogoutHandler();
			}else if(data.isLoggedIn == 'false'){
				isLoggedIn = false;
				loggedInContent += '<a id="navigation-header-log-in-or-register" href="#log-in-or-register-modal" class="navigation-header-log-in-or-register">Log In or Register</a>';
				loggedInContent += '<div style="display: none;"><div id="log-in-or-register-modal" style="width:800px;height:400px;overflow:auto;">'+
				'<div id="login_form">' +
					'<form id="log-in-form"><ul>' +
					'<li><h2>Log In</h2></li>' +
					'<li>' +
						'<label>Username</label>' +
						'<div><input type="text" name="name" value="" id="name"  /></div>' +
					'</li>' +
					'<li>' +
					'<label>Password</label>' +
						'<div><input type="password" name="password" value="" id="password"  /></div>' +
					'</li>'+				
					'<li>' +
						'<input type="button" id="submit-button-login" value="Submit" />' +					
					'</li>' +
					'<li><label id="error-log-in" style="color:red;"></label></li>'+
					'</ul>' +
				'</form>' +				
				'</div>'+
				'<div id="create_form">' +
				'<form id="create-account-form"><ul>' +
					'<li><h2>Create Account</h2></li>' +
					'<li>' +
						'<label>Username</label>' +
						'<div><input type="text" name="name" value="" id="name"  /><label id="account-available"></label></div>' +
					'</li>' +
					'<li>' +
						'<label>Password</label>' +
						'<div><input type="password" name="password" value="" id="password"  /></div>' +
					'</li>' +
					'<li>' +
						'<label>Re-Password</label>' +
						'<div><input type="password" name="repassword" value="" id="repassword"  /></div>' +
					'</li>' +
					'<li>' +
						'<label>Email (optional)</label>' +
						'<div><input type="text" name="email" value="" id="email"  /></div>' +
					'</li>' +					
					'<li>' +
					'<label>Captcha</label>' +
						'<div id="captcha-image">' +						
						'</div>' +
						'<div><input type="text" name="imagecode" value="" id="imagecode"  /></div>' +
					'</li>' +
					'<li>' +
						'<input type="button" id="submit-button-create" name="submit" value="Create Account"  /></li>' +
					'<li><label id="error-create-account" style="color:red;"></label></li>'+
					'</ul>' +
				'</form>' +
				'</div>' +
				'</div>';			
				
				$('#navigation-header-user').html(loggedInContent);
				$(".navigation-header-log-in-or-register").fancybox();
				initSubmitLoginHandler();
				initCreateAccountHandler();
				checkIfUserExists();
				$('#post-link').removeClass('post-link');				
				$('#post-category').removeClass('post-category');

				$('#captcha-image').html($('#captcha-hidden-placeholder').show());

				$("#post-link").click(function(){					
				//if it has the log in thing generate this...
				if($("#log-in-or-register-modal").length > 0){
					$('.navigation-header-log-in-or-register').trigger('click');
					return;
					}
				});

				$("#post-category").click(function(){					
				//if it has the log in thing generate this...
				if($("#log-in-or-register-modal").length > 0){
					$('.navigation-header-log-in-or-register').trigger('click');
					return;
					}
				});


			}			
		}

		function enablePostLinks(data){	

			var categoryContent = "";
			$.each(data, function(i, item)
			{
				categoryContent += "<a href='javascript:void(0);' class='create-story-category-link' onclick='$(\"#create-story-form #category\").val(\"" + item.name + "\");return false;' value='" + item.name + "'>";
				categoryContent += item.name;
				categoryContent += "</a> - ";
			});
			categoryContent = categoryContent.substr(0, categoryContent.length-2);
			category
			var submitLinkContent = '<div style="display: none;"><div id="submit-link-modal" style="width:800px;height:300px;overflow:auto;">'+
			'<div id="create_story">'+
			'<form id="create-story-form"><ul>'+
				'<li>'+
					'<label>Title</label>'+
					'<div><input type="text" name="name" value="" id="name"  /></div>'+
				'</li>'+
				'<li>'+
					'<label>Link</label>'+
					'<div><input type="text" name="link" value="" id="link"  /></div>'+
				'</li>'+
				'<li>'+
					'<label>Category</label>'+
					'<div><input type="text" name="category" value="' + decodeURIComponent(category) + '" id="category"  />' + categoryContent + '</div>'+
				'</li>'+
				'<li>'+
					'<label>Description / Comment</label>'+
					'<div>'+
						'<textarea name="description" id="description" rows="4" cols="50"></textarea>'+
					'</div>'+
				'</li>'+
				'<li>'+
					'<input type="button" name="submit" value="Post a story" id="submit-button-create-story"  /></li>'+
					'<li><label id="error-create-story" style="color:red;"></label></li>'+
			'</ul>'+
			'</form></div>'+
			'</div></div>';

			submitLinkContent += '<div style="display: none;"><div id="submit-category-modal" style="width:800px;height:300px;overflow:auto;">';
			submitLinkContent += '<div id="create_category_form">';
			submitLinkContent += '<form action="/category/submit" id="create-category-form"><ul>';
			submitLinkContent += '<li>';
			submitLinkContent += '<label>Name of Category</label>';
			submitLinkContent += '<div><input type="text" name="name" value="" id="name"  /></div>';
			submitLinkContent += '</li>';
			submitLinkContent += '<li>';
			submitLinkContent += '<label>Description</label>';
			submitLinkContent += '<div>';
			submitLinkContent += '<textarea name="description" id="description" rows="4" cols="50"></textarea>';
			submitLinkContent += '</div>';
			submitLinkContent += '</li>';	
			submitLinkContent += '<li>';
			submitLinkContent += '<input type="button" name="submit" value="Create Category" id="submit-button-create-category" /></li>';
			submitLinkContent += '<li><label id="error-create-category" style="color:red;"></label></li>';
			submitLinkContent += '</ul>';
			submitLinkContent += '</form></div></div>';

			$("#submit-link").append(submitLinkContent);
			$(".post-link").fancybox();
			$(".post-category").fancybox();
			initCreateStoryHandler();
			initCategoryStoryHandler();
		}

		function initCategoryStoryHandler(){
			var dataToBeSent;
			$("#submit-button-create-category").click(function() {	
				dataToBeSent = $("#create-category-form").serialize();
				$.ajax({
				    type: "POST",
				    url: "/category/submit",				    
				    data: dataToBeSent,				    
				    dataType: "json",
				    success: function(data){
				    	if(data.result == 'Success!'){
							location.reload();
						}else{
							$("#error-create-category").html(data.result);
						}
				    },
				    failure: function(errMsg) {
				        alert(errMsg);
				    }
				});				
			});			
		}

		function initCreateStoryHandler(){
			var dataToBeSent;
			$("#submit-button-create-story").click(function() {	
				dataToBeSent = $("#create-story-form").serialize();
				$.ajax({
				    type: "POST",
				    url: "/story/submit",				    
				    data: dataToBeSent,				    
				    dataType: "json",
				    success: function(data){
				    	if(data.result == 'Success!'){
							location.reload();
						}else{
							$("#error-create-story").html(data.result);
						}
				    },
				    failure: function(errMsg) {
				        alert(errMsg);
				    }
				});				
			});
		}		

		function initLogoutHandler(){
			$(".navigation-header-logout").click(function() {	
				var jqxhr = $.getJSON( "/logout", function() {})			
				.done(function(data) {
					if(data.result == "Success!"){
						location.reload();
					}
				})
				.fail(function() { console.log( "error logging out" ); })			
			});
		}

		function checkIfUserExists(){
			var dataToBeSent;
			$("#create-account-form #name").keyup(function() {	
				dataToBeSent = $("#create-account-form #name").serialize();
				$.ajax({
				    type: "POST",
				    url: "/create/userExists",				    
				    data: dataToBeSent,				    
				    dataType: "json",
				    success: function(data){
						$("#account-available").html(data.result);
						$("#account-available").css('color', data.color);			
				    },
				    failure: function(errMsg) {
				        alert(errMsg);
				    }
				});				
			});			
		}

		function initCreateAccountHandler(){
			var dataToBeSent;
			$("#submit-button-create").click(function() {	
				dataToBeSent = $("#create-account-form").serialize();
				$.ajax({
				    type: "POST",
				    url: "/create/submit",				    
				    data: dataToBeSent,				    
				    dataType: "json",
				    success: function(data){			    	
				    	if(data.result == 'Success!'){
							location.reload();
						}else{
							$("#error-create-account").html(data.result);
						}
				    },
				    failure: function(errMsg) {
				        alert(errMsg);
				    }
				});				
			});
		}

		function initSubmitLoginHandler(){
			var dataToBeSent;

			$("#submit-button-login").click(function() {	
				dataToBeSent = $("#log-in-form").serialize();
				$.ajax({
				    type: "POST",
				    url: "/login/submit",				    
				    data: dataToBeSent,				    
				    dataType: "json",
				    success: function(data){
				    	if(data.result == 'Success!'){
							location.reload();
						}else{
							$("#error-log-in").html(data.result);
						}
				    },
				    failure: function(errMsg) {
				        alert(errMsg);
				    }
				});				
			});
		}

		function initCategories(){
			//do json call
			var jqxhr = $.getJSON( "/category/get", function() {})			
			.done(function(data) {
				//handleCategories(data);
				enablePostLinks(data);
			})
			.fail(function() { console.log( "error loading content" ); })
		}

		function handleCategories(data){
			var categoryContent = "<ul class='category-header-link'>";
			categoryContent += "<li class='category-header-link-list'>";
			categoryContent += "<a href='/' class='category-header-link-list'>Home";			
			categoryContent += "</a>";
			categoryContent += "</li>";
			$.each(data, function(i, item)
			{			
				categoryContent += "<li class='category-header-link-list'>";
				categoryContent += "<a href='/f/" +  item.name  + "' class='category-header-link-list'>";
				categoryContent += item.name;
				categoryContent += "</a>";
				categoryContent += "</li>";
			});
			categoryContent += "</ul>";
			$('#navigation-header-categories');
			$('#navigation-header-categories').html(categoryContent);

		}
	}
);
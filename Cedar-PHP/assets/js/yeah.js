var aTbottom = false;
var offset = 1;
var loading = 0;

function getNotifs() {
	$.getJSON('/check_update.json', function(data) {
    if (data.notifs.unread_count > 0) {
    	favicon.badge(data.notifs.unread_count);
    	$('.badge').show().text(data.notifs.unread_count);
    } else {
    	favicon.reset();
    	$('.badge').hide().text(data.notifs.unread_count);
    }
});
}

var favicon=new Favico({
    animation:'none'
});

function bindEvents() {
	
	$(".trigger").off().on('click', (function(){
		var href = $(this).attr('data-href');
		
		//if you click on a post it takes you to 'data-href' an attribute defined in list.php for each post
		
		location.href= href;
    }));

    setInterval(function(){ 
    	getNotifs();
    }, 30000);
	
    $('.yeah').off().on('click', function(){
		event.stopPropagation();
        var postId = $(this).attr('id');
		var yeahType = $(this).attr('data-track-label');
        var postData = 'postid='+postId+'&uid=1';
		
		//changes the yeah button to disabled so no one yeahs twice while its posting the first yeah just like on miiverse holy shish
		$('#'+postId).attr('disabled', '');
		
		$.post('/yeah.php', {postId:postId, yeahType:yeahType}, function(data) {
			if(data == 'success'){
			
            $('#'+postId).addClass('yeah-added');
			$('#'+postId).find('.yeah-button-text').text('Unyeah');
			$('#'+postId).closest('div').find('.yeah-count').text(Number($('#'+postId).closest('div').find('.yeah-count').text()) + 1);
			if(yeahType == 'post'){
			$('#yeah-content').removeClass('none');
			$('.icon-container.visitor').removeAttr("style");
			} else {
			$('.reply-permalink-post #yeah-content').removeClass('none');
			$('.reply-permalink-post .icon-container.visitor').removeAttr("style");
			}
           //rebind events here
           bindEvents();
			} else {
				alert('yeah failed');
			}
			
		$('#'+postId).removeAttr("disabled");
        }
	)});
		
     $('.yeah-added').off().on('click', function(){
		event.stopPropagation();
        var postId = $(this).attr('id');
		var yeahType = $(this).attr('data-track-label');
        var postData = 'postid='+postId+'&uid=1';
		
		
		//same thing here just for unyeahs lol
		$('#'+postId).attr('disabled', '');

		$.post('/unyeah.php', {postId:postId, yeahType:yeahType}, function(data){
			if(data=='success'){
            $('#'+postId).removeClass('yeah-added');
			$('#'+postId).find('.yeah-button-text').text('Yeah!');
			$('#'+postId).closest('div').find('.yeah-count').text(Number($('#'+postId).closest('div').find('.yeah-count').text()) - 1);
			if(yeahType == 'post'){ 
			$('.icon-container.visitor').attr('style','display: none;');
			if ($('.yeah-count').text() == 0){
				$('#yeah-content').addClass('none');
			}
			} else { 
			$('.reply-permalink-post .icon-container.visitor').attr('style','display: none;');
			if ($('.reply-permalink-post .yeah-count').text() == 0){
				$('.reply-permalink-post #yeah-content').addClass('none');
			}
			} 
           //rebind events here
           bindEvents();
			} else {
				alert('unyeah failed');
			}
			
		$('#'+postId).removeAttr("disabled");
		}
	 )});

	 $(".js-open-global-my-menu").off().click(function () {
    $('#global-my-menu').not($("#global-my-menu").toggleClass('none')).addClass('none');
});

	 $('.textarea').keyup(function() {
        var text_length = $('.textarea').val().length;
        var text_remaining = 800 - text_length;
        $('.textarea-feedback').html('<font color="#646464" style="font-size: 13px; padding: 0 3px 0 7px;">'+text_remaining+'</font> Characters Remaining');
    });

	 $(document).off().on('click',function (e) {
	 	footerUl = $('.open-global-my-menu');
	 	if ((!footerUl.is(e.target) && footerUl.has(e.target).length === 0) && (!$('#global-my-menu').is(e.target) && $('#global-my-menu').has(e.target).length === 0)){
	 		$('#global-my-menu').addClass('none');
	 	}
	 });

	$('.js-open-truncated-text-button').off().on('click', function(){
		$(this).addClass('none');
		$('.js-truncated-text').addClass('none');
		$('.js-full-text').removeClass('none');
		bindEvents();
	})
	 
	$('.favorite-button').off().on('click', function(){
		var titleId = $(this).attr('data-title-id');
		if ($('.favorite-button').hasClass('checked')){
			
			$.post('/favorite.php', {titleId:titleId, favType: "removeFav"}, function(data) {
			if(data == 'success'){
			$('.favorite-button').removeClass('checked');
			}
			
		})} else {
			
		    $.post('/favorite.php', {titleId:titleId, favType: "addFav"}, function(data) {
			if(data == 'success'){
				$('.favorite-button').addClass('checked');
			}
			
		})
		
	}});

	$('input[name="face-type"]').click(function(){
    if ($('input[name="face-type"][value="2"]').is(':checked'))
    {
    	$('.nnid-face').removeClass('none');
    	$('.custom-face').addClass('none');
    } else {
    	$('.custom-face').removeClass('none');
    	$('.nnid-face').addClass('none');
    }
});

	$('.feeling-button').click(function(){
	$('.feeling-button').removeClass('checked');
	$(this).addClass('checked');
	})

	$('.follow-button').off().on('click', function(){
		event.stopPropagation();
		var userId = $(this).attr('data-user-id');
		$.post('/follow.php', {userId:userId, followType: "follow"}, function(data) {
			if(data == 'success'){
		$('.user-sidebar').find('[data-user-id="' + userId + '"]').addClass('unfollow-button').removeClass('follow-button');
		$('.list').find('[data-user-id="' + userId + '"]').addClass('none').next('.follow-done-button').removeClass('none').removeAttr("disabled");
		bindEvents();
	}
	})});

	$('.unfollow-button').off().on('click', function(){
		var userId = $(this).attr('data-user-id');
		$.post('/follow.php', {userId:userId, followType: "unfollow"}, function(data) {
			if(data == 'success'){
		$('.unfollow-button').addClass('follow-button').removeClass('unfollow-button');
		bindEvents();
    }})});

    $('#profile-post').off().on('click', function(){
		$.post('/settings/profile_post.unset.json');
		$(this).remove();

	});

	$('.edit-button').off().on('click', function(){
		$('#edit-post-page').attr('class', 'dialog active-dialog modal-window-open mask');
	});

	$('.friend-button').off().on('click', function(){
		$('.active-dialog').removeClass('none');
	});

    $('.olv-modal-close-button').off().on('click', function(){
		$('.mask').addClass('none');
	});

	$('.edit-post-form').find('select[name="edit-type"]').on('change', function(){
		if ($(this).val() != "" && $(this).val() != "edit"){
			$('.edit-post-form').attr('action', $('.edit-post-form').find('select').find('option:selected').attr('data-action'));
			$('.edit-post-form').find('.post-button').removeClass('disabled').removeAttr("disabled");
		} else {
			$('.edit-post-form').find('.post-button').addClass('disabled').attr('disabled', '');
		}

		if ($(this).val() == "edit"){
			$('<div class="post-edit-form"><div class="post-count-container"><div class="textarea-feedback" style="float:left;"><font color="#646464" style="font-size: 13px; padding: 0 3px 0 7px;">800</font> Characters Remaining</div></div><div class="feeling-selector js-feeling-selector test-feeling-selector"><label class="symbol feeling-button feeling-button-normal checked"><input type="radio" name="feeling_id" value="0" checked=""><span class="symbol-label">normal</span></label><label class="symbol feeling-button feeling-button-happy"><input type="radio" name="feeling_id" value="1"><span class="symbol-label">happy</span></label><label class="symbol feeling-button feeling-button-like"><input type="radio" name="feeling_id" value="2"><span class="symbol-label">like</span></label><label class="symbol feeling-button feeling-button-surprised"><input type="radio" name="feeling_id" value="3"><span class="symbol-label">surprised</span></label><label class="symbol feeling-button feeling-button-frustrated"><input type="radio" name="feeling_id" value="4"><span class="symbol-label">frustrated</span></label><label class="symbol feeling-button feeling-button-puzzled"><input type="radio" name="feeling_id" value="5"><span class="symbol-label">puzzled</span></label></div><div class="textarea-container"><textarea name="text_data" class="textarea-text textarea" maxlength="800" placeholder="Add a comment here."></textarea></div>Image upload: <input type="file" name="image" accept="image/*"><div class="form-buttons"></div></div>').insertAfter('select[name="edit-type"]');
		} else {
			$('.post-edit-form').remove();
		}
	});

	$('.edit-post-form').off().on('submit', function(e){
		e.preventDefault();
		$.ajax({url: $(this).attr('action'), type: 'POST', success:function(data){
			if ($('select[name="edit-type"]').val() == 'delete'){
				location.reload();
			} else {
				$('.mask').addClass('none');
			}
		}});
		bindEvents();
	});

	$('#post-form').find('textarea[name="text_data"]').on('input', function(){
		if ($(this).val() == ""){
			$('#post-form').find('.post-button').addClass('disabled').attr('disabled', '');
		} else {
			$('#post-form').find('.post-button').removeClass('disabled').removeAttr("disabled");
		}
	});

	$('#post-form').off().on('submit', function(e){
		e.preventDefault();
		$(this).find('.post-button').addClass('disabled').attr('disabled', '');
		var formData = new FormData(this);
		$.ajax({url: $(this).attr('action'), type: 'POST', data: formData, success:function(data){
			$('.no-reply-content').remove();
			$('.no-content').remove();
		if($('#post-form').attr('action').substr(-7) == 'replies'){
			$('.reply-list').append(data);
		} else {
			$('.post-list').prepend(data);
		}
		    $('.post').fadeIn();
			$(this).find('.post-button').removeClass('disabled').removeAttr("disabled");
			$('.feeling-button').removeClass('checked');
			$('.feeling-button-normal').addClass('checked');
			$('#post-form').each(function(){this.reset();});
			bindEvents();
		},

	contentType: false,
    processData: false
	})});


	$('#friend-request').off().on('submit', function(e){
		e.preventDefault();
		$(this).find('.post-button').addClass('disabled').attr('disabled', '');
		var formData = new FormData(this);
		$.ajax({url: $(this).attr('action'), type: 'POST', data: formData, success:function(data){
			$('.no-reply-content').remove();
			$('.no-content').remove();
		if($('#post-form').attr('action').substr(-7) == 'replies'){
			$('.reply-list').append(data);
		} else {
			$('.post-list').prepend(data);
		}
		    $('.post').fadeIn();
			$(this).find('.post-button').removeClass('disabled').removeAttr("disabled");
			$('.feeling-button').removeClass('checked');
			$('.feeling-button-normal').addClass('checked');
			$('#post-form').each(function(){this.reset();});
			bindEvents();
		},

	contentType: false,
    processData: false
	})});



	$('.setting-form').on('submit', function(e){
		e.preventDefault();
		$('.apply-button').addClass('disabled').prop('disabled', '');
		var formData = new FormData(this);
		$.ajax({url: $(this).attr('action'), type: 'POST', data: formData, success:function(data){
			if(data == 'success'){
				$('.dialog').removeClass('none');
				$('.apply-button').removeClass('disabled').removeAttr('disabled', '');
			}
		},
        contentType: false,
        processData: false

	    })});

	$('.ok-button').off().on('click', function(){
		$('.active-dialog').addClass('none');
	});

	$('.community-top-sidebar .search').on('submit', function(e){
		if ($(this).find('input[type="text"]').val().length < 2){
			e.preventDefault();
		}
	});

	$('.headline .search').on('submit', function(e){
		if ($(this).find('input[type="text"]').val().length < 1){
			e.preventDefault();
		}
	});


	//checks if loadOnScroll is defined. So this code will only run on pages the need it
	if ((typeof loadOnScroll !== 'undefined')) {
		
		$(window).scroll(function() {
		    //checks if you're at the bottom of the page and if you are it loads more posts
		    if ($(window).scrollTop() + window.innerHeight >= $('[data-next-page-url]').height()) {
		    	if (loading == 0 && aTbottom == false) {
		    		$("[data-next-page-url]").append('<div class="post-list-loading"><img src="/assets/img/loading-image-green.gif" alt=""></div>');
		    		loading = 1;
		    		$.get($('[data-next-page-url]').attr('data-next-page-url'), function(data) {
		    			if(data == ''){
		    				aTbottom = true;
		    				bindEvents();
		    			}
		    			$("[data-next-page-url]").append(data);
		    			offset++;
		    			$('[data-next-page-url]').attr('data-next-page-url', $('[data-next-page-url]').attr('data-next-page-url').replace(/(offset=).*?(&)/,"offset=" + offset + "&"))
		    			loading = 0;
		    			$(".post-list-loading").remove();
		    			bindEvents();
		    		})
		    	}
		    }
		});
	}
}

$(document).ready(function(){
    bindEvents();
    getNotifs();
});
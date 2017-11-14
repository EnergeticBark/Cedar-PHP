function bindEvents() {
	$('#delete_user').off().on('submit', function(e){
		e.preventDefault();
		user_id = $('input[name="user_id"]').val();
		
		$.ajax({
			type: 'POST',
			url: '/admin_panel/delete_user',
			data: {user_id:user_id}, 
			success: function(data) {
				$('#delete-message').remove();
				if(data.success == 1){
					$('#delete_user').append('<p id="delete-message" class="text-success mt-3 mb-0">User deleted.</p>');
				} else {
					$('#delete_user').append('<p id="delete-message" class="text-danger mt-3 mb-0">'+data.problem+'</p>');
				}},
			dataType: 'json'
		});
		bindEvents();
	});


	$('#delete_post').off().on('submit', function(e){
		e.preventDefault();
		post_id = $('input[name="post_id"]').val();
		post_violation_type = $('select[name="post_violation_type"]').val();
		post_reason = $('textarea[name="post_reason"]').val();

		$.ajax({
			type: 'POST',
			url: '/admin_panel/delete_post',
			data: {post_id:post_id, post_violation_type:post_violation_type, post_reason:post_reason},
			success: function(data) {
				$('#delete-message').remove();
				if(data.success == 1){
					$('#delete_post').append('<p id="delete-message" class="text-success mt-3 mb-0">Post deleted.</p>');
				} else {
					$('#delete_post').append('<p id="delete-message" class="text-danger mt-3 mb-0">'+data.problem+'</p>');
				}},
			dataType: 'json'
		});
		bindEvents();
	});

	$('#delete_reply').off().on('submit', function(e){
		e.preventDefault();
		reply_id = $('input[name="reply_id"]').val();
		reply_violation_type = $('select[name="reply_violation_type"]').val();
		reply_reason = $('textarea[name="reply_reason"]').val();

		$.ajax({
			type: 'POST',
			url: '/admin_panel/delete_reply',
			data: {reply_id:reply_id, reply_violation_type:reply_violation_type, reply_reason:reply_reason},
			success: function(data) {
				$('#delete-message').remove();
				if(data.success == 1){
					$('#delete_reply').append('<p id="delete-message" class="text-success mt-3 mb-0">Reply deleted.</p>');
				} else {
					$('#delete_reply').append('<p id="delete-message" class="text-danger mt-3 mb-0">'+data.problem+'</p>');
				}},
			dataType: 'json'
		});
		bindEvents();
	});



	$('select[name="post_violation_type"]').off().on('change', function() {
		if ($(this).val() == 0) {
			$(this).after('<textarea class="form-control mt-2" name="post_reason" placeholder="Your post contained [reason], so it was removed." rows="3"></textarea>');
		} else {
			$('textarea[name="post_reason"]').remove();
		}
		bindEvents();
	});

$('select[name="reply_violation_type"]').off().on('change', function() {
		if ($(this).val() == 0) {
			$(this).after('<textarea class="form-control mt-2" name="reply_reason" placeholder="Your reply contained [reason], so it was removed." rows="3"></textarea>');
		} else {
			$('textarea[name="reply_reason"]').remove();
		}
		bindEvents();
	});
}

$(document).ready(function(){
    bindEvents();
});
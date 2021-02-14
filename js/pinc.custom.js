jQuery(document).ready(function($) {
	var ios = /(iPad|iPhone|iPod)/g.test(navigator.userAgent);
	var ie9below = false;
	if (document.all && !window.atob) { //http://tanalin.com/en/articles/ie-version-js/
		ie9below = true;
	}

	//masonry
	var $masonry = $('#masonry');
	var user_profile_follow = $('#user-profile-follow');
	var user_profile_boards = $('#user-profile-boards');
	var user_notifications = $('#user-notifications');

	/**
	 * Returns a $_GET parameter from the URL
	 */
	function getParameterByName(name, url) {
		if (!url) url = window.location.href;
		name = name.replace(/[\[\]]/g, "\\$&");
		var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
		results = regex.exec(url);
		if (!results) return null;
		if (!results[2]) return '';
		return decodeURIComponent(results[2].replace(/\+/g, " "));
	}


	if (obj_pinc.infinitescroll != 'disable' && ($masonry.length || user_profile_follow.length || user_profile_boards.length ||  user_notifications.length)) {
		$('#navigation').css({'visibility':'hidden', 'height':'1px'});
	}
	var $body = $(document.body);
	if ($masonry.length) {
		if ($('.check-480px').css('float') == 'left' && $body.css('direction') == 'rtl') {
			$masonry.imagesLoaded(function() {
				$masonry.masonry({
					itemSelector : '.thumb',
					fitWidth: true,
					originLeft: true,
					transitionDuration: 0
				}).css('visibility', 'visible');

				if ($('#masonry .thumb').length == 1) {
					$('#masonry .thumb').css('width', '95%');
				}

				$('#ajax-loader-masonry').hide();
			});
		} else if($('.check-480px').css('float') == 'left' && $body.css('direction') == 'ltr') {
			$masonry.imagesLoaded(function() {
				$masonry.masonry({
					itemSelector : '.thumb',
					fitWidth: true,
					transitionDuration: 0
				}).css('visibility', 'visible');
				if ($('#masonry .thumb').length == 1) {
					$('#masonry .thumb').css('width', '95%');
				}

				$('#ajax-loader-masonry').hide();
			});
		} else {
			$masonry.masonry({
				itemSelector : '.thumb',
				fitWidth: true,
				originLeft: false,
				transitionDuration: 0
			}).css('visibility', 'visible');
			$('#ajax-loader-masonry').hide();
		}
	}

	$(window).resize(function() {
		if ($masonry.length || user_profile_follow.length) {
			$masonry.width($(window).width()-28).masonry('reloadItems').masonry('layout');
		}
	});

	$(window).on('load resize', function() {
		//Adjust top menu
		if ($('.check-767px').css('float') == 'left') {
			$('body').css('padding-top', 0);
		} else {
			if (obj_pinc.u == '0') {
				if ($('#topmenu').length && $('#top-message-wrapper').length) {
					$('body').css('padding-top', $('#topmenu').height() + $('#top-message-wrapper').height() + 24 + 'px');
				}
			} else {
				if ($('#topmenu').length) {
					$('body').css('padding-top', $('#topmenu').height() + 12 + 'px');
				}
			}
		}

		//top menu dropdown in mobile
		if ($('.check-767px').css('float') == 'left') {
			$(document).off('click', '#topmenu .dropdown-toggle').on('click', '#topmenu .dropdown-toggle', function() {
				$(this).next().toggle();
			});
		} else {
			$('#nav-main .dropdown-menu').removeAttr('style');
			$('#nav-main ul li').removeClass('open');
			$('#nav-main .dropdown-toggle').blur();
		}

		//tooltip
		if (!ios && $('.check-767px').css('float') == 'none') {
			$(document).tooltip({
				selector: '[rel=tooltip]'
			});
		}

		//Resize follow-wrapper
		if ($('.check-480px').css('float') == 'left' && $('#user-profile-follow').length) {
			$('.follow-wrapper .follow-user-posts-thumb').each(function(index, element) {
				$('.follow-user-posts').width($('.follow-user-name').width()-$('.follow-user-avatar').width()-6);
				$('.follow-user-posts-thumb').width($('.follow-user-posts').width()/2-4).height($('.follow-user-avatar').width()/2-2);
			});
		}

		if ($('.post-top-meta').length) {
			postTopMetaScroll();
		}

	});

	//close modal when click outside poup-overlay
	$(document).on('click', function(e) {
		if(!$(e.target).closest('.modal-dialog').length && !$(e.target).closest('#wp-link-wrap').length && !$(e.target).closest('#wp-link-backdrop').length) {
			if ($('#scrolltotop').next().attr('id') == 'popup-lightbox') {
				$('#popup-overlay').hide();
			} else {
				$('#popup-overlay').detach().insertAfter('#scrolltotop').hide();
			}
			$('.pinc-modal').modal('hide');
			if ($('#post-lightbox').css('display') == 'block') {
				$('body').addClass('modal-open');
			}
		}
	});

	//close modal when click close
	$(document).on('click', '.modal .popup-close', function() {
		if ($('#scrolltotop').next().attr('id') == 'popup-lightbox') {
			$('#popup-overlay').hide();
		} else {
			$('#popup-overlay').detach().insertAfter('#scrolltotop').hide();
		}
		$('.pinc-modal').modal('hide');
		if ($('#post-lightbox').css('display') == 'block') {
			$('body').addClass('modal-open');
		}
	});

	//login box popup
	//append form to loginbox such that wsl also works
	if (obj_pinc.u == '0') {
		$('#loginbox').popover({
			content: function() {
				return $('#loginbox').data('wsl') +
				'<div class="error-msg-loginbox"></div>\
				<form name="loginform_header" id="loginform_header" method="post">\
					<div class="form-group">\
						<label>' + obj_pinc.__Username + '<br />\
						<input class="form-control" type="text" name="log" id="log" value="" tabindex="0" /></label>\
					</div>\
					<div class="form-group">\
						<label>' + obj_pinc.__Password + '\
						(<a href="' + obj_pinc.home_url + '/login-lpw/" tabindex="-1">' + obj_pinc.__Forgot + '</a>)\
						<input class="form-control"type="password" name="pwd" id="pwd" value="" tabindex="0" /></label>\
					</div>\
					<input type="submit" class="btn btn-success" name="wp-submit" id="wp-submit" value="' + obj_pinc.__Login + '" tabindex="0" />\
					<div class="ajax-loader-loginbox ajax-loader hider"></div>\
					<span id="loginbox-register">' + obj_pinc.__or + ' <a href="' + obj_pinc.home_url + '/signup/" tabindex="0">' + obj_pinc.__SignUp + '</a>' + obj_pinc.__LoginForm + '</span>\
				</form>'
			},
			html: 'true',
			placement: 'bottom',
			title: obj_pinc.__Welcome + '<button class="close" id="loginbox-close" type="button">&times;</button>'
		});

		$('#loginbox').on('shown.bs.popover', function () {
			$('.wp-social-login-widget a').each(function() {
				$(this).attr('onclick', 'window.location = "' + $(this).attr('href') + '"').removeAttr('href').css('cursor', 'pointer');
			});
		})

		$('#loginbox').on('click', function() {
			return false;
		});

		$(document).on('click', '#loginbox-close', function() {
			$('#loginbox').popover('hide');
		});

		//login box hide when click outside
		$(document).on('click', function(e) {
			if(!$(e.target).closest('.popover').length) {
				$('#loginbox').popover('hide');
			}
		});

		//login box process
		$(document).on('submit', '#loginform_header, #popup-login-form', function() {
			$('#loginform_header .ajax-loader-loginbox').css('display', 'inline-block');

			$('.error-msg-loginbox').hide();
			if ($('#log').val() == '' || $('#pwd').val() == '') {
				$('.error-msg-loginbox').html('<div class="alert alert-warning"><strong>' + obj_pinc.__pleaseenterbothusernameandpassword  + '</strong></div>').fadeIn();
				$('#loginform_header .ajax-loader-loginbox').hide();
				return false;
			}

			var data = {
				action: 'pinc-ajax-login',
				nonce: obj_pinc.nonce,
				log: $('#loginform_header #log').val(),
				pwd: $('#loginform_header #pwd').val()
			};

			$.ajax({
				type: 'post',
				url: obj_pinc.ajaxurl,
				data: data,
				success: function(data) {
					if (data == 'error') {
						$('.error-msg-loginbox').html('<div class="alert alert-warning"><strong>' + obj_pinc.__incorrectusernamepassword  + '</strong></div>').fadeIn();
						$('#loginform_header .ajax-loader-loginbox').hide();
						return false;
					} else {
						window.location.reload();
					}
				}
			});

			return false;
		});
	}

	//hide links in wsl
	if ($('.wp-social-login-widget').length) {
		$('.wp-social-login-widget a').each(function() {
			$(this).attr('onclick', 'window.location = "' + $(this).attr('href') + '"').removeAttr('href').css('cursor', 'pointer');
		});
	}

	//notification popup
	$('#user-notifications-count').on('click', function() {
		if (!$(this).next('div.popover').length) {
			$('#user-notifications-count .tooltip').hide();
			$('#user-notifications-count')
			.popover({
				content: '<div class="ajax-loader"></div>',
				html: 'true',
				placement: 'bottom',
				trigger: 'manual',
				title: '<strong>' + obj_pinc.__NotificationsLatest30 + ' (<a href="' + obj_pinc.home_url + '/notifications/"><span>' + obj_pinc.__SeeAll + '</span></a>)</strong> <button class="close" id="user-notifications-count-close" type="button">&times;</button>'
			})
			.popover('show');

			$('<div>').load(obj_pinc.home_url + '/notifications/ #user-notifications-table', function() {
				$('#user-notifications-count').next('.popover').children('.popover-content').html($(this).html());
			});
		} else {
			$('#user-notifications-count').popover('hide');
			$('#user-notifications-count a span').text('0').parent().removeClass('user-notifications-count-nth');
		}
		return false;
	});

	$(document).on('click', '#user-notifications-count-close', function() {
		$('#user-notifications-count').popover('hide');
		$('#user-notifications-count a span').text('0').parent().removeClass('user-notifications-count-nth');
	});

	//notification hide when click outside
	$(document).on('click', function(e) {
		if(!$(e.target).closest('.popover').length && $('#user-notifications-count + div.popover').length) {
			$('#user-notifications-count').popover('hide');
			$('#user-notifications-count a span').text('0').parent().removeClass('user-notifications-count-nth');
		}
	});

	//scroll to top
	$(window).scroll(function() {
		var $scrolltotop = $('#scrolltotop');

		if ($(this).scrollTop() > 100) {
			$scrolltotop.slideDown('fast');
		} else {
			$scrolltotop.slideUp('fast');
		}

		if ($('.post-top-meta').length) {
			postTopMetaScroll();
		}
	});

	$('#scrolltotop').click(function() {
		$('body,html').animate({
			scrollTop: 0
		}, 'fast');
		return false;
	});

	//post action bar scroll
	if ($('.post-top-meta').length) {
		var post_top_meta = $('.post-top-meta');
		var post_top_meta_top = post_top_meta.offset().top;
		var post_top_meta_bottom = post_top_meta_top + post_top_meta.outerHeight()-1;
	}

	function postTopMetaScroll() {
		if ($('.check-767px').css('float') == 'none') {
			var topmenu_bottom, topmenu_top;
			if ($('#top-message-wrapper').length) {
				topmenu_bottom = $('#top-message-wrapper').offset().top+$('#top-message-wrapper').height();
				topmenu_top = $('#top-message-wrapper').outerHeight() + $('#topmenu').height();
			} else {
				topmenu_bottom = $('#topmenu').offset().top+$('#topmenu').height();
				topmenu_top = $('#topmenu').height();
			}

			var post_top_meta_left = $('#post-featured-photo').offset().left;
			if (post_top_meta_top <= topmenu_bottom && $(this).scrollTop() <= ($('#post-featured-photo').offset().top + $('#post-featured-photo').outerHeight()-post_top_meta_bottom+10)) {
				$('.post-top-meta').css({'opacity': '0.95', 'position': 'fixed', 'top': topmenu_top, 'left': post_top_meta_left, 'width': $('#post-featured-photo').outerWidth()});
				$('.post-top-meta-placeholder').css('height', post_top_meta.outerHeight()-1).show();
			} else if ($(this).scrollTop() > ($('#post-featured-photo').offset().top + $('#post-featured-photo').outerHeight()-post_top_meta_bottom+10)) {
				$('.post-top-meta').css({'position': 'absolute', 'top': ($('#post-featured-photo').offset().top + $('#post-featured-photo').outerHeight() - post_top_meta_bottom), 'left': 16});
			} else {
				$('.post-top-meta').css({'opacity': '1', 'position': 'relative', 'top': 0, 'left': 0, 'width': 'auto'});
				$('.post-top-meta-placeholder').hide();
			}
		} else {
			$('.post-top-meta').css({'opacity': '1', 'position': 'relative', 'top': 0, 'left': 0, 'width': 'auto'});
			$('.post-top-meta-placeholder').hide();
		}
	}

	//likes for frontpage, lightbox, posts
	$(document).on('click', '.pinc-like', function() {
		if (obj_pinc.u != '0') {
			var like = $(this);
			var	post_id = like.data('post_id');

			like.attr('disabled', 'disabled').css('pointer-events', 'none');

			if (!like.hasClass('disabled')) {
				var data = {
					action: 'pinc-like',
					nonce: obj_pinc.nonce,
					post_id: post_id,
					post_author: like.data('post_author'),
					pinc_like: 'like'
				};

				$.ajax({
					type: 'post',
					url: obj_pinc.ajaxurl,
					data: data,
					success: function(count) {
						$('[id=pinc-like-'+post_id+']').addClass('disabled').removeAttr('disabled').css('pointer-events', 'auto');
						$('[id=likes-count-'+post_id+']').addClass('disabled');

						if (count == 1) {
							if ($('#post-repins').length) {
								$('#post-repins').before('<div class="post-likes"><div class="post-likes-wrapper"><h4>' + obj_pinc.__Likes + '</h4><div class="post-likes-avatar"><a href="' + obj_pinc.home_url + '/' + obj_pinc.user_rewrite + '/' + obj_pinc.ul + '/" rel="tooltip" title="' + obj_pinc.ui + '">' + obj_pinc.avatar48 + '</a></div></div></div>');
							} else {
								$('#post-embed-box').before('<div class="post-likes"><div class="post-likes-wrapper"><h4>' + obj_pinc.__Likes + '</h4><div class="post-likes-avatar"><a href="' + obj_pinc.home_url + '/' + obj_pinc.user_rewrite + '/' + obj_pinc.ul + '/" rel="tooltip" title="' + obj_pinc.ui + '">' + obj_pinc.avatar48 + '</a></div></div></div>');
							}
							$('[id=likes-count-'+post_id+']').removeClass('hide').html('<i class="fas fa-heart"></i> 1');
							$('#button-likes-count').text('1');
							if($('#masonry').length) {
								$('#masonry').masonry('reloadItems').masonry('layout');
							}
						} else {
							$('.post-likes-avatar').append('<a id=likes-' + obj_pinc.u +  ' href="' + obj_pinc.home_url + '/' + obj_pinc.user_rewrite + '/' + obj_pinc.ul + '/" rel="tooltip" title="' + obj_pinc.ui + '">' + obj_pinc.avatar48 + '</a>');
							$('[id=likes-count-'+post_id+']').html('<i class="fas fa-heart"></i> ' + count);
							$('#button-likes-count').text(count);
						}
					}
				});
			} else {
				var data = {
					action: 'pinc-like',
					nonce: obj_pinc.nonce,
					post_id: post_id,
					pinc_like: 'unlike'
				};

				$.ajax({
					type: 'post',
					url: obj_pinc.ajaxurl,
					data: data,
					success: function(count) {
						$('[id=pinc-like-'+post_id+']').removeClass('disabled').removeAttr('disabled').css('pointer-events', 'auto');
						$('#post-' + post_id + ' .pinc-like').removeClass('disabled');

						if (count == 0) {
							$('.post-likes').remove();
							$('[id=likes-count-'+post_id+']').addClass('hide').text('');
							$('#button-likes-count').text('');
							if($('#masonry').length) {
								$('#masonry').masonry('reloadItems').masonry('layout');
							}
						} else {
							$('#likes-' + obj_pinc.u).remove();
							$('[id=likes-count-'+post_id+']').html('<i class="fas fa-heart"></i> ' + count);
							$('#button-likes-count').text(count);
						}
					}
				});
			}
			return false;
		} else {
			loginPopup();
			return false;
		}
	});

	//repin show form for frontpage, lightbox, posts
	$(document).on('click', '.pinc-repin', function() {
		if (obj_pinc.u != '0') {
			var repin = $(this);
			var post_id = repin.data('post_id');
			var post_description = $('#masonry #post-' + post_id + ' .post-title').data('title');
			var post_content = $('#masonry #post-' + post_id + ' .post-title').data('content');
			var post_tags = $('#post-' + post_id + ' .post-title').data('tags');
			var post_price = $('#post-' + post_id + ' .post-title').data('price');

			if ($('#post-lightbox').css('display') == 'block') {
				if (!ie9below) {
					window.history.back();
				}
			}

			//use ajax fetch boards if user created a new board
			if ($('#newboard').length) {
				//populate board field
				var data = {
					action: 'pinc-repin-board-populate',
					nonce: obj_pinc.nonce
				};

				$.ajax({
					type: 'post',
					url: obj_pinc.ajaxurl,
					data: data,
					success: function(data) {
						$('#board').remove();
						$('#repinform-add-new-board').after(data);

						//when in single-pin.php
						if (!post_description) {
							post_description = $('#post-' + post_id + ' .post-title').data('title');
							post_content = $('#post-' + post_id + ' .post-title').data('content');
						}

						$('#post-lightbox').modal('hide');
						$('#post-repin-box .post-repin-box-photo').html('<img src="' + $('#post-' + post_id + ' .featured-thumb').attr('src') + '" />');
						tmce_setContent(post_description, 'pin-title');
						tmce_setContent(post_content, 'pin-content');
						$('#post-repin-box #tags').val(post_tags);
						$('#post-repin-box #price').val(post_price);
						$('#repinform-add-new-board').text(obj_pinc.__addnewboard);
						$('#repinform #board-add-new').val('').hide();
						$('#repinform #board-add-new-category').val('-1').hide();
						$('#repinform #board').show();
						$('#repinform #repin-post-id').val(post_id);
						$('#repinnedmsg, .repinnedmsg-share').hide();
						$('#repinform').show();

						$('#popup-overlay').show();
						$('#post-repin-box').modal();

						setTimeout(function() {
							tmce_focus('pin-title');

							if (tmce_getContent('pin-title') != ''&& ($('#repinform #board').val() != '-1' || $.trim($('#repinform #board-add-new').val()))) {
								$('#pinit').removeAttr('disabled');
							} else {
								$('#pinit').attr('disabled', 'disabled');
							}

							if ($('#pin-title_ifr').length) {
								$(document.getElementById('pin-title_ifr').contentWindow.document).keyup(function() {
									if (tmce_getContent('pin-title') != ''&& ($('#repinform #board').val() != '-1' || $.trim($('#repinform #board-add-new').val()))) {
										$('#pinit').removeAttr('disabled');
									} else {
										$('#pinit').attr('disabled', 'disabled');
									}
								});
							}
						}, 500);
					}
				});

				$('#newboard').remove();
			} else {
				//when in single-pin.php
				if (!post_description) {
					post_description = $('#post-' + post_id + ' .post-title').data('title');
					post_content = $('#post-' + post_id + ' .post-title').data('content');
				} else {
					$('#video-embed').remove(); //hide youtube player if not in single-pin.php
				}

				$('#post-lightbox').modal('hide');

				//ajax fetch boards for first time
				if ($('#post-repin-box').length == 0){
					//populate board field
					var data = {
						action: 'pinc-repin-board-populate',
						nonce: obj_pinc.nonce
					};

					$.ajax({
						type: 'post',
						url: obj_pinc.ajaxurl,
						data: data,
						success: function(data) {
							$('body').append('\
								<div class="modal pinc-modal" id="post-repin-box" data-backdrop="false" data-keyboard="false" aria-hidden="true" role="dialog">\
									<div class="modal-dialog">\
										<div class="modal-content">\
											<button class="close popup-close" data-dismiss="modal" aria-hidden="true" type="button">&times;</button>\
											<div class="clearfix"></div>\
											<div class="post-repin-box-photo"></div>\
											<form id="repinform">\
												' + obj_pinc.description_fields + '\
												<p></p>\
												' + obj_pinc.tags_html + '\
												' + obj_pinc.price_html + '\
												<a id="repinform-add-new-board" class="btn btn-default pull-right" href="#" tabindex="-1">' + obj_pinc.__addnewboard + '</a>\
												' + data + '\
												<input id="board-add-new" class="form-control board-add-new" type="text" placeholder="' + obj_pinc.__enternewboardtitle + '" />\
												' + obj_pinc.categories + '\
												<input id="repin-post-id" type="hidden" name="repin-post-id" value="" />\
												<div class="clearfix"></div>\
												<input class="btn btn-success btn-block btn-pinc-custom" type="submit" name="pinit" id="pinit" value="' + obj_pinc.__Repin + '" /> \
												<span id="repin-status"></span>\
											</form>\
										</div>\
									</div>\
								</div>\
							');

							if (typeof tinymce !== 'undefined') {
								if ($('textarea#pin-content').length) {
									tinymce.init(tinyMCEPreInit.mceInit['pin-content']);
								} else {
									tinymce.init(tinyMCEPreInit.mceInit['pin-title']);
								}
							}

							$('#post-repin-box .post-repin-box-photo').html('<img src="' + $('#post-' + post_id + ' .featured-thumb').attr('src') + '" />');
							tmce_setContent(post_description, 'pin-title');
							tmce_setContent(post_content, 'pin-content');
							$('#post-repin-box #tags').val(post_tags);
							$('#post-repin-box #price').val(post_price);
							$('#repinform-add-new-board').text(obj_pinc.__addnewboard);
							$('#repinform #board-add-new').val('').hide();
							$('#repinform #board-add-new-category').val('-1').hide();
							$('#repinform #board').show();
							$('#repinform #repin-post-id').val(post_id);
							$('#repinnedmsg, .repinnedmsg-share').hide();
							$('#repinform').show();

							$('#popup-overlay').show();
							$('#post-repin-box').modal();

							setTimeout(function() {
								tmce_focus('pin-title');

								if (tmce_getContent('pin-title') != ''&& ($('#repinform #board').val() != '-1' || $.trim($('#repinform #board-add-new').val()))) {
									$('#pinit').removeAttr('disabled');
								} else {
									$('#pinit').attr('disabled', 'disabled');
								}

								if ($('#pin-title_ifr').length) {
									$(document.getElementById('pin-title_ifr').contentWindow.document).keyup(function() {
										if (tmce_getContent('pin-title') != ''&& ($('#repinform #board').val() != '-1' || $.trim($('#repinform #board-add-new').val()))) {
											$('#pinit').removeAttr('disabled');
										} else {
											$('#pinit').attr('disabled', 'disabled');
										}
									});
								}
							}, 500);

							//autocomplete tags
							$.getScript(obj_pinc.site_url + '/wp-includes/js/jquery/suggest.min.js', function() {
								$('input#tags').suggest(obj_pinc.ajaxurl + '?action=ajax-tag-search&tax=post_tag', {minchars: 3, multiple: true});
							});
						}
					});
				} else {
					$('#post-repin-box .post-repin-box-photo').html('<img src="' + $('#post-' + post_id + ' .featured-thumb').attr('src') + '" />');
					tmce_setContent(post_description, 'pin-title');
					tmce_setContent(post_content, 'pin-content');
					$('#post-repin-box #tags').val(post_tags);
					$('#post-repin-box #price').val(post_price);
					$('#repinform-add-new-board').text(obj_pinc.__addnewboard);
					$('#repinform #board-add-new').val('').hide();
					$('#repinform #board-add-new-category').val('-1').hide();
					$('#repinform #board').show();
					$('#repinform #repin-post-id').val(post_id);
					$('#repinnedmsg, .repinnedmsg-share').hide();
					$('#repinform').show();

					$('#popup-overlay').show();
					$('#post-repin-box').modal();

					setTimeout(function() {
						tmce_focus('pin-title');

						if (tmce_getContent('pin-title') != ''&& ($('#repinform #board').val() != '-1' || $.trim($('#repinform #board-add-new').val()))) {
							$('#pinit').removeAttr('disabled');
						} else {
							$('#pinit').attr('disabled', 'disabled');
						}

						if ($('#pin-title_ifr').length) {
							$(document.getElementById('pin-title_ifr').contentWindow.document).keyup(function() {
								if (tmce_getContent('pin-title') != ''&& ($('#repinform #board').val() != '-1' || $.trim($('#repinform #board-add-new').val()))) {
									$('#pinit').removeAttr('disabled');
								} else {
									$('#pinit').attr('disabled', 'disabled');
								}
							});
						}
					}, 500);
				}
			}
			return false;
		} else {
			loginPopup();
			return false;
		}
	});

	//disable submit button if empty textarea and no board
	$(document).on('focus', '#post-repin-box textarea#pin-title', function() {
		if ($.trim($('#repinform textarea#pin-title').val()) && ($('#repinform #board').val() != '-1' || $.trim($('#repinform #board-add-new').val()))) {
			$('#pinit').removeAttr('disabled');
		} else {
			$('#pinit').attr('disabled', 'disabled');
		}

		$(this).keyup(function() {
			if ($.trim($('#repinform textarea#pin-title').val()) && ($('#repinform #board').val() != '-1' || $.trim($('#repinform #board-add-new').val()))) {
				$('#pinit').removeAttr('disabled');
			} else {
				$('#pinit').attr('disabled', 'disabled');
			}
		});
	});

	$(document).on('focus', '#post-repin-box #board-add-new', function() {
		var content = '';
		if ($('#pin-title_ifr').length) {
			content = tmce_getContent('pin-title');
		} else {
			content = $.trim($('#repinform textarea#pin-title').val());
		}

		$(this).keyup(function() {
			if ($('#pin-title_ifr').length) {
				content = tmce_getContent('pin-title');
			} else {
				content = $.trim($('#repinform textarea#pin-title').val());
			}

			if (content && ($('#repinform #board').val() != '-1' || $.trim($('#repinform #board-add-new').val()))) {
				$('#pinit').removeAttr('disabled');
			} else {
				$('#pinit').attr('disabled', 'disabled');
			}
		});
	});

	//prevent form submit on enter key
	$(document).on('keypress', '#board-add-new', function(e) {
		return e.keyCode != 13;
	});

	//repin form add new board toggle
	$(document).on('click', '#repinform-add-new-board', function() {
		if ($(this).text() == obj_pinc.__cancel) {
			if($('#noboard').length) {
				$('#pinit').attr('disabled', 'disabled');
			}
			$(this).text(obj_pinc.__addnewboard);
			$('#repinform #board-add-new').val('').hide();
			$('#repinform #board-add-new-category').val('-1').hide();
			$('#repinform #board').show().focus();
		} else {
			$(this).text(obj_pinc.__cancel);
			$('#repinform #board-add-new').show().focus();
			$('#repinform #board-add-new-category').show();
			$('#repinform #board').hide();
		}
		return false;
	});

	//repin for frontpage, lightbox, posts
	$(document).on('submit', '#repinform', function() {
		var repin_status = $('#repin-status');
		repin_status.html('');
		repin_status.html(' <div class="ajax-loader"></div>');

		//empty title
		if (!$.trim($('#repinform textarea#pin-title').val())) {
			repin_status.html('<div class="alert alert-warning text-center"><strong>' + obj_pinc.__Pleaseentertitle  + '</strong></div>').fadeIn();
			return false;
		}

		//empty board
		if ($('#repinform #board').val() == '-1' && $.trim($('#repinform #board-add-new').val()) == '') {
			repin_status.html('<div class="alert alert-warning text-center"><strong>' + obj_pinc.__Pleasecreateanewboard  + '</strong></div>').fadeIn();
			return false;
		}

		$(this).find('input[type="submit"]').attr('disabled', 'disabled');

		var post_id = $('#repinform #repin-post-id').val();
		var price = '';
		if ($('#repinform #price').length)
			price = $('#repinform #price').val().replace(/[^0-9.]/g, '');
		var data = {
			action: 'pinc-repin',
			nonce: obj_pinc.nonce,
			repin_title: tmce_getContent('pin-title'),
			repin_content: tmce_getContent('pin-content'),
			repin_tags: $('#repinform #tags').val(),
			repin_price: price,
			repin_post_id: post_id,
			repin_board: $('#repinform #board').val(),
			repin_board_add_new: $('#repinform #board-add-new').val(),
			repin_board_add_new_category: $('#repinform #board-add-new-category').val()
		};

		//if user create a new board, inject a span to indicate to ajax fetch board next round
		if ($('#repinform #board-add-new').val() != '') {
			repin_status.after('<span id="newboard"></span>');
		}

		$.ajax({
			type: 'post',
			url: obj_pinc.ajaxurl,
			data: data,
			error: function() {
				repin_status.html('<div class="alert alert-warning text-center"><strong><small>' + obj_pinc.__errorpleasetryagain  + '</small></strong></div>');
			},
			success: function(data) {
				repin_status.html('');
				$('#repinform').hide();
				if ($('#repinform #board-add-new').val() == '') {
					board_name = $('#repinform #board option:selected').text();
				} else {
					board_name = $('#repinform #board-add-new').val();
				}
				$('#post-repin-box .post-repin-box-photo').after('<h3 id="repinnedmsg" class="text-center">' + obj_pinc.__repinnedto + ' ' + board_name + '<p></p><a class="btn btn-success" href="' + data + '" aria-hidden="true"><strong>' + obj_pinc.__seethispin + '</strong></a> <a class="btn btn-success popup-close" data-dismiss="modal" aria-hidden="true"><strong>' + obj_pinc.__close + '</strong></a></h3><h5 class="repinnedmsg-share text-center"><strong>' + obj_pinc.__shareitwithyourfriends + '</strong></h5><p class="repinnedmsg-share text-center"><a class="btn btn-primary btn-sm" href="" onclick="window.open(\'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(data) + '\', \'facebook-share-dialog\', \'width=626,height=500\'); return false;"><i class="fab fa-facebook fa-fw"></i></a> <a class="btn btn-info btn-sm" href="" onclick="window.open(\'https://twitter.com/share?url=' + data + '&amp;text=' + encodeURIComponent($('#repinform textarea#pin-title').val()) + '\', \'twitter-share-dialog\', \'width=626,height=500\'); return false;"><i class="fab fa-twitter fa-fw"></i></a> <a class="btn btn-danger btn-sm" href="" onclick="window.open(\'https://plus.google.com/share?url=' + data + '\', \'gplus-share-dialog\', \'width=626,height=500\'); return false;"><i class="fab fa-google-plus fa-fw"></i></a> <a class="btn btn-info btn-sm" href="" onclick="window.open(\'http://www.reddit.com/submit?url=' + encodeURIComponent(data) + '&amp;title=' + encodeURIComponent($('#repinform textarea#pin-title').val()) + '\', \'reddit-share-dialog\', \'width=880,height=500,scrollbars=1\'); return false;"><i class="fab fa-reddit fa-fw"></i></a></p>');

				var newrepin = '<li><a class="post-repins-avatar">' + obj_pinc.avatar48 + '</a> <a href="' + obj_pinc.home_url + '/' + obj_pinc.user_rewrite + '/' + obj_pinc.ul + '/">' + obj_pinc.ui + '</a> ' + obj_pinc.__onto + ' <strong>' + $('#repinform #board option:selected').text() + '</strong></li>';

				if (!$('#post-repins').length) {
					$('.post-wrapper').append('<div id="post-repins"><div class="post-repins-wrapper"><h4>' + obj_pinc.__Repins + '</h4><ul></ul></div></div>');
				}
				$('#post-repins ul').append(newrepin);

				var repins_countmsg = $('#repins-count-'+post_id);
				var repins_count = repins_countmsg.text();
				repins_count = repins_count.substring(repins_count.lastIndexOf(' '));

				if (repins_count == '') {
					$('#repins-count-'+post_id).removeClass('hide').html('<i class="fas fa-retweet"></i> 1');
					if($('#masonry').length) {
						$('#masonry').masonry('reloadItems').masonry('layout');
					}
				} else {
					$('#repins-count-'+post_id).html('<i class="fas fa-retweet"></i> ' + (parseInt(repins_count,10)+1));
				}

				//for single post count increment
				var button_repins_count = $('#button-repins-count');
				if (button_repins_count.text() == '') {
					button_repins_count.text(1);
				} else {
					button_repins_count.text(parseInt(button_repins_count.text(),10)+1);
				}

			}
		});
		return false;
	});

	//comments for lightbox and posts
	$(document).on('submit', '#commentform', function() {
		if (obj_pinc.u != '0') {
			var commentform = $(this);

			if ($.trim($(this).find('#comment').val()) == '') {
				$('#comment-status').remove();
				$('.comment-status-ajax-loader').remove();
				$('.textarea-wrapper').prepend('<div id="comment-status"></div>');
				$('#comment-status').html('<div class="alert alert-warning"><strong><small>' + obj_pinc.__Pleasetypeacomment + '</small></strong></div>');
				$('#commentform textarea').focus();
				return false;
			}

			commentform.find('input[type="submit"]').attr('disabled', 'disabled');

			var post_id = $('#commentform #comment_post_ID').val();
			var formdata = $(this).serialize();
			var formurl = $(this).attr('action');
			var comment_parent = $('#commentform #comment_parent').val();

			$('#comment-status').remove();
			$('.form-submit').prepend('<div class="comment-status-ajax-loader ajax-loader ajax-loader-inline pull-right" style="margin: 15px 0 0 5px;"></div>');

			$.ajax({
				type: 'post',
				url: formurl,
				data: formdata,
				error: function(XMLHttpRequest) {
					var errormsg = XMLHttpRequest.responseText.substr(XMLHttpRequest.responseText.indexOf('<p>')+3);
					errormsg = errormsg.substr(0, errormsg.indexOf('</p>'));

					if (errormsg == '') {
						errormsg = obj_pinc.__errorpleasetryagain;
					}

					$('.textarea-wrapper').prepend('<div id="comment-status"></div>');
					$('.comment-status-ajax-loader').remove();
					$('#comment-status').html('<div class="alert alert-warning"><strong><small>' + errormsg + '</small></strong></div>');
					$('#commentform textarea').focus();
					commentform.find('input[type="submit"]').removeAttr('disabled');
				},
				success: function(response) {
					var comment_response = JSON.parse(response);
					var commenttext =  $('#commentform #comment').val();
					var $body = $(document.body);
					if ($body.css('direction') == 'ltr') {
						var comment_actions = '<div class="pull-right">' + comment_response.reply_link + '&nbsp;' + comment_response.edit_link + '</div>';
					} else {
						var comment_actions = '<div class="pull-left">' + comment_response.reply_link + '&nbsp;' + comment_response.edit_link + '</div>';
					}
					var newcomment = '<li class="comment" id="comment-' + comment_response.comment_ID + '"><div class="comment-avatar">' + obj_pinc.avatar48 + '</div>' + comment_actions + '<div class="comment-content"><strong><span class="comment"><a href="' + obj_pinc.home_url + '/' + obj_pinc.user_rewrite + '/' + obj_pinc.ul + '/">' + obj_pinc.ui + '</a></span></strong> <span class="text-muted">&#8226; ' + obj_pinc.__postcommenttimenow + '</span><p>' + commenttext.replace(/(?:\r\n|\r|\n)/g, '<br />') + '</p></div></li>';

					$('.comment-status-ajax-loader').remove();
					$('#commentform #comment').val('');
					$('#commentform #comment_parent').val('');

					if (comment_parent == '0' || comment_parent == '' ) {
						if ($('#comments').find('.commentlist').size() == 0) {
							$('#comments').prepend('<ol class="commentlist"></ol>');
						}
						$('.commentlist').append(newcomment);
					} else {
						if ($('#comment-' + comment_parent).find('>ul.children').size() == 0) {
							$('#comment-' + comment_parent).append('<ul class="children"></ul>');
						}
						$('#comment-' + comment_parent + ' >ul.children').append(newcomment);
					}

					var comments_countmsg = $('#comments-count-'+post_id);
					var comments_count = comments_countmsg.text();
					comments_count = comments_count.substring(comments_count.lastIndexOf(' '));

					if (comments_count == '') {
						$('#comments-count-'+post_id).removeClass('hide').html('<i class="fas fa-comment"></i> 1');
					} else {
						$('#comments-count-'+post_id).html('<i class="fas fa-comment"></i> ' + (parseInt(comments_count,10)+1));
					}
					if ($body.css('direction') == 'ltr') {
						comment_actions = '<div class="pull-right">' + comment_response.edit_link + '</div>';
					} else {
						comment_actions = '<div class="pull-left">' + comment_response.edit_link + '</div>';
					}

					var newcomment_masonry = '<div id="masonry-meta-comment-wrapper-' + post_id + '" class="masonry-meta"><div class="masonry-meta-avatar">' + obj_pinc.avatar30 + '</div><div class="masonry-meta-comment" id="masonry-comment-' + comment_response.comment_ID + '">' + comment_actions + '<span class="masonry-meta-author">' + obj_pinc.ui + '</span><span class="masonry-meta-comment-content"> ' + commenttext + '</span></div></div>';
					$('[id=masonry-meta-commentform-' + post_id + ']').prev().append(newcomment_masonry);

					if ($('#post-masonry #masonry').length) {
						$('#post-masonry #masonry').masonry('reloadItems').masonry('layout');
					}

					if ($('#masonry').length) {
						$('#masonry').masonry('reloadItems').masonry('layout');
					}

					commentform.find('input[type="submit"]').removeAttr('disabled');
				}
			});
		} else {
			loginPopup();
		}
		return false;
	});

	//Zoom full size photo
	$(document).on('click', '.pinc-zoom', function() {
		$('#post-zoom-overlay').show();
		$('.lightbox-content img').attr('src', $('.lightbox-content img').data('src'));
		$('#post-fullsize').lightbox({backdrop:false, keyboard:false});
		return false;
	});

	$(document).on('click', '#post-fullsize-close', function() {
		$('#post-zoom-overlay').fadeOut();
		$('#post-fullsize').lightbox('hide');
		return false;
	});

	$(document).on('click', '#post-zoom-overlay', function() {
		$('#post-zoom-overlay').fadeOut();
		$('#post-fullsize').lightbox('hide');
	});

	//Embed for lightbox & posts
	$(document).on('click', '.post-embed', function() {
		$('#popup-overlay').detach().insertAfter('#post-report-box').show();
		$('#post-embed-box').modal();
		$('#post-embed-box textarea').focus().select();
		if (ios) {
			$('body').scrollTop(0);
		}
		return false;
	});

	$(document).on('keydown', '#embed-width', function() {
		old_height = $('#embed-height').val();
		old_width_str = "width=\'" + $(this).val() + "'";
		old_height_str = "height=\'" + $('#embed-height').val() + "'";
		ratio = $('.post-featured-photo img').width() / $('.post-featured-photo img').height();
	}).on('keyup', '#embed-width', function() {
		var embed_code = $('#post-embed-box textarea').val();
		var new_height = Math.ceil($(this).val()/ratio);
		var new_height_str = "height='" + new_height + "'";
		var new_width_str = "width='" + $(this).val() + "'";

		$('#embed-height').val(new_height);
		embed_code = embed_code.replace(old_height_str, new_height_str);
		embed_code = embed_code.replace(old_width_str, new_width_str);
		$('#post-embed-box textarea').val(embed_code);
	});

	$(document).on('keydown', '#embed-height', function() {
		old_width = $('#embed-width').val();
		old_width_str = "width=\'" + $('#embed-width').val() + "'";
		old_height_str = "height=\'" + $(this).val() + "'";
		ratio = $('.post-featured-photo img').width() / $('.post-featured-photo img').height();
	}).on('keyup', '#embed-height', function() {
		var embed_code = $('#post-embed-box textarea').val();
		var new_width = Math.ceil($(this).val()*ratio);
		var new_width_str = "width='" + new_width + "'";
		var new_height_str = "height='" + $(this).val() + "'";

		$('#embed-width').val(new_width);
		embed_code = embed_code.replace(old_height_str, new_height_str);
		embed_code = embed_code.replace(old_width_str, new_width_str);
		$('#post-embed-box textarea').val(embed_code);
	});

	//Email friend for lightbox & posts
	$(document).on('click', '.post-email', function() {
		if (obj_pinc.u != '0') {
			if ($('#post-lightbox').css('display') != 'block') {
			$('#popup-overlay').detach().insertAfter('#post-report-box').show();
			}
			$('#post-email-box').modal();
			$('#post-email-box #recipient-name').focus();
			if (ios) {
				$('body').scrollTop(0);
			}
			return false;
		} else {
			loginPopup();
			return false;
		}
	});

	$(document).on('click', '#post-email-submit', function() {
		$('#post-email-box .ajax-loader-email-pin').show();
		var data = {
			action: 'pinc-post-email',
			nonce: obj_pinc.nonce,
			email_post_id: $('#post-email-box #email-post-id').val(),
			recipient_name: $('#post-email-box #recipient-name').val(),
			recipient_email: $('#post-email-box #recipient-email').val(),
			recipient_message: $('#post-email-box textarea').val()
		};

		$.ajax({
			type: 'post',
			url: obj_pinc.ajaxurl,
			data: data,
			success: function() {
				$('#post-email-box .ajax-loader-email-pin').hide();
				$('#popup-overlay').hide();
				$('#post-email-box').modal('hide');
			}
		});
		return false;
	});

	//Email friend - disable submit button if empty recipient name and email
	$(document).on('focus', '#post-email-box #recipient-name', function() {
		if ($.trim($('#post-email-box #recipient-name').val()) && $.trim($('#post-email-box #recipient-email').val())) {
			$('#post-email-box #post-email-submit').removeAttr('disabled');
		} else {
			$('#post-email-box #post-email-submit').attr('disabled', 'disabled');
		}

		$(this).keyup(function() {
			if ($.trim($('#post-email-box #recipient-name').val()) && $.trim($('#post-email-box #recipient-email').val())) {
				$('#post-email-box #post-email-submit').removeAttr('disabled');
			} else {
				$('#post-email-box #post-email-submit').attr('disabled', 'disabled');
			}
		});
	});

	$(document).on('focus', '#post-email-box #recipient-email', function() {
		if ($.trim($('#post-email-box #recipient-name').val()) && $.trim($('#post-email-box #recipient-email').val())) {
			$('#post-email-box #post-email-submit').removeAttr('disabled');
		} else {
			$('#post-email-box #post-email-submit').attr('disabled', 'disabled');
		}

		$(this).keyup(function() {
			if ($.trim($('#post-email-box #recipient-name').val()) && $.trim($('#post-email-box #recipient-email').val())) {
				$('#post-email-box #post-email-submit').removeAttr('disabled');
			} else {
				$('#post-email-box #post-email-submit').attr('disabled', 'disabled');
			}
		});
	});

	//Report pin for lightbox & posts
	$(document).on('click', '.post-report', function() {
		$('#popup-overlay').detach().insertAfter('#post-report-box').show();
		$('#post-report-box').modal();
		$('#post-report-box .alert, #post-report-box #post-report-close').hide();
		$('#post-report-box textarea, #post-report-box #post-report-submit').show();
		$('#post-report-box textarea').val('').focus();
		if (ios) {
			$('body').scrollTop(0);
		}
		return false;
	});

	$(document).on('click', '#post-report-submit', function() {
		$('#post-report-box .ajax-loader-report-pin').show();
		$('#post-report-box #post-report-submit').attr('disabled', 'disabled');

		var data = {
			action: 'pinc-post-report',
			nonce: obj_pinc.nonce,
			report_post_id: $('#post-report-box #report-post-id').val(),
			report_message: $('#post-report-box textarea').val()
		};

		$.ajax({
			type: 'post',
			url: obj_pinc.ajaxurl,
			data: data,
			success: function() {
				$('#post-report-box .ajax-loader-report-pin, #post-report-box textarea, #post-report-box #post-report-submit').hide();
				$('#post-report-box .alert, #post-report-box #post-report-close').show();
			}
		});
		return false;
	});

	$(document).on('click', '#post-report-box #post-report-close', function() {
		$('#popup-overlay').hide();
		$('#post-report-box').modal('hide');
	});

	//Report pin - disable submit button if empty message
	$(document).on('focus', '#post-report-box textarea', function() {
		if ($.trim($('#post-report-box textarea').val())) {
			$('#post-report-box #post-report-submit').removeAttr('disabled');
		} else {
			$('#post-report-box #post-report-submit').attr('disabled', 'disabled');
		}

		$(this).keyup(function() {
			if ($.trim($('#post-report-box textarea').val())) {
				$('#post-report-box #post-report-submit').removeAttr('disabled');
			} else {
				$('#post-report-box #post-report-submit').attr('disabled', 'disabled');
			}
		});
	});

	//follow for lightbox, posts, author
	$(document).on('click', '.pinc-follow', function() {
		if (obj_pinc.u != '0') {
			var follow = $(this);
			var	board_parent_id = follow.data('board_parent_id');
			var	board_id = follow.data('board_id');
			var	author_id = follow.data('author_id');
			var	disable_others = follow.data('disable_others');
			follow.attr('disabled', 'disabled');

			if (!follow.hasClass('disabled')) {
				var data = {
					action: 'pinc-follow',
					nonce: obj_pinc.nonce,
					pinc_follow: 'follow',
					board_parent_id: board_parent_id,
					board_id: board_id,
					author_id: author_id,
					disable_others: disable_others
				};

				$.ajax({
					type: 'post',
					url: obj_pinc.ajaxurl,
					data: data,
					success: function() {
						if (follow.data('board_parent_id') != 0) {
							follow.addClass('disabled').text(obj_pinc.__UnfollowBoard).removeAttr('disabled');
						} else {
							follow.addClass('disabled').text(obj_pinc.__Unfollow).removeAttr('disabled');
						}

						//increase followers count in author.php
						if ($('#ajax-follower-count') && follow.parent().parent().parent().parent().attr('id') == 'userbar') {
							$('#ajax-follower-count').html(parseInt($('#ajax-follower-count').html(), 10)+1);
						}

						//disable other follow button
						if (board_parent_id == '0' && (disable_others != 'no' || $('#userbar .nav li:first').hasClass('active'))) {
							$('.pinc-follow').each(function() {
								if ($(this).data('board_parent_id') != 0) {
									$(this).addClass('disabled').text(obj_pinc.__UnfollowBoard);
								}
							});
						}
					}
				});
			} else {
				var data = {
					action: 'pinc-follow',
					nonce: obj_pinc.nonce,
					pinc_follow: 'unfollow',
					board_parent_id: board_parent_id,
					board_id: board_id,
					author_id: author_id
				};

				$.ajax({
					type: 'post',
					url: obj_pinc.ajaxurl,
					data: data,
					success: function(data) {
						if (follow.data('board_parent_id') != 0) {
							follow.removeClass('disabled').text(obj_pinc.__FollowBoard).removeAttr('disabled');
						} else {
							follow.removeClass('disabled').text(obj_pinc.__Follow).removeAttr('disabled');
						}

						//decrease followers count in author.php
						if ($('#ajax-follower-count') && follow.parent().parent().parent().parent().attr('id') == 'userbar') {
							$('#ajax-follower-count').html(parseInt($('#ajax-follower-count').html(), 10)-1);
						}

						//enable other follow button
						if (data == 'unfollow_all' && (disable_others != 'no' || $('#userbar .nav li:first').hasClass('active'))) {
							$('.pinc-follow').each(function() {
								if ($(this).data('board_parent_id') != 0) {
									$(this).removeClass('disabled').text(obj_pinc.__FollowBoard);
								}
							});
						}
					}
				});
			}
			return false;
		} else {
			loginPopup();
			return false;
		}
	});

	//infinite scroll
	if ($masonry.length && obj_pinc.infinitescroll != 'disable') {
		nextSelector = obj_pinc.nextselector;
		if (document.URL.indexOf('/source/') != -1) {
			nextSelector = '#navigation #navigation-next a';
		}

		$masonry.infinitescroll({
			navSelector : '#navigation',
			nextSelector : nextSelector,
			itemSelector : '.thumb',
			prefill: true,
			bufferPx : 500,
			loading: {
				msgText: '',
				finishedMsg: obj_pinc.__allitemsloaded,
				img: obj_pinc.stylesheet_directory_uri + '/img/ajax-loader.gif',
				finished: function() {}
			}
		}, function(newElements) {
			if ($('.check-480px').css('float') == 'left') {
				var $newElems = $(newElements).hide();

				$newElems.imagesLoaded(function() {
					$('#infscr-loading').fadeOut('normal');
					$newElems.show();
					$masonry.masonry('appended', $newElems, true);
				});
			} else {
				var $newElems = $(newElements);
				$('#infscr-loading').fadeOut('normal');
				$masonry.masonry('appended', $newElems, true);
			}
		});
	}

	//infinite scroll for user profile - boards
	if (user_profile_boards.length && obj_pinc.infinitescroll != 'disable') {
		user_profile_boards.infinitescroll({
			navSelector : '#navigation',
			nextSelector : '#navigation #navigation-next a',
			itemSelector : '.board-mini',
			prefill: true,
			bufferPx : 500,
			loading: {
				msgText: '',
				finishedMsg: obj_pinc.__allitemsloaded,
				img: obj_pinc.stylesheet_directory_uri + '/img/ajax-loader.gif'
			}
		});
	}

	//infinite scroll for user profile - followers & following
	if (user_profile_follow.length && obj_pinc.infinitescroll != 'disable') {
		user_profile_follow.infinitescroll({
			navSelector : '#navigation',
			nextSelector : '#navigation #navigation-next a',
			itemSelector : '.follow-wrapper',
			prefill: true,
			bufferPx : 500,
			loading: {
				msgText: '',
				finishedMsg: obj_pinc.__allitemsloaded,
				img: obj_pinc.stylesheet_directory_uri + '/img/ajax-loader.gif',
				finished: function() {}
			}
		}, function(newElements) {
			var $newElems = $(newElements).hide();

			$newElems.imagesLoaded(function() {
				$('#infscr-loading').fadeOut('normal');
				$newElems.show();
				user_profile_follow.masonry('appended', $newElems, true);
			});
		});
	}

	//infinite scroll for notifications
	if (user_notifications.length && obj_pinc.infinitescroll != 'disable') {
		user_notifications.infinitescroll({
			navSelector : '#navigation',
			nextSelector : '#navigation #navigation-next a',
			itemSelector : '.notifications-wrapper',
			prefill: true,
			bufferPx : 500,
			loading: {
				msgText: '',
				finishedMsg: obj_pinc.__allitemsloaded,
				img: obj_pinc.stylesheet_directory_uri + '/img/ajax-loader.gif',
				finished: function() {}
			}
		}, function(newElements) {
			var $newElems = $(newElements).hide();
			$newElems.appendTo($('.table'));
			$('#infscr-loading').fadeOut('normal');
			$newElems.show();
		});
	}

	//actionbar Edited to handle wide photos - MACSE
	$(document).on('mouseenter', '.thumb-holder', function() {
		if ($('.check-480px').css('float') == 'none' && !ios) {
			$(this).children('.masonry-actionbar').fadeIn(100);
		}
	});

	$(document).on('mouseleave', '.thumb-holder', function() {
		if ($('.check-480px').css('float') == 'none' && !ios) {
			$(this).children('.masonry-actionbar').fadeOut(100);
		}
	});

	//comments for frontpage
	$(document).on('submit', '.masonry-meta form', function() {
		var commentform = $(this);
		var formdata = $(this).serialize();
		var formurl = $(this).attr('action');
		var post_id = $(this).attr('id').substr(12);

		if ($.trim($(this).find('textarea').val()) == '') {
			commentform.find('#comment-status').remove();
			commentform.find('.form-submit .comment-status-ajax-loader').remove();
			commentform.prepend('<div id="comment-status"></div>');
			commentform.find('#comment-status').html('<div class="alert alert-warning"><strong>' + obj_pinc.__Pleasetypeacomment + '</strong></div>');
			commentform.find('textarea').focus();
			return false;
		}

		commentform.find('input[type="submit"]').attr('disabled', 'disabled');
		commentform.find('#comment-status').remove();
		commentform.find('.comment-status-ajax-loader').remove();
		commentform.find('.form-submit').append(' <div class="comment-status-ajax-loader ajax-loader ajax-loader-inline"></div>');

		$.ajax({
			type: 'post',
			url: formurl,
			data: formdata,
			error: function(XMLHttpRequest) {
				var errormsg = XMLHttpRequest.responseText.substr(XMLHttpRequest.responseText.indexOf('<p>')+3);
				errormsg = errormsg.substr(0, errormsg.indexOf('</p>'));

				if (errormsg == '') {
					errormsg = obj_pinc.__errorpleasetryagain;
				}

				commentform.prepend('<div id="comment-status"></div>');
				commentform.find('#comment-status').html('<div class="alert alert-warning"><strong>' + errormsg + '</strong></div>');
				commentform.find('textarea').focus();
				commentform.find('.form-submit .comment-status-ajax-loader').remove();
				if ($('#post-masonry #masonry').length) {
					$('#post-masonry #masonry').masonry('reloadItems').masonry('layout');
				}

				if ($('#masonry').length) {
					$('#masonry').masonry('reloadItems').masonry('layout');
				}

				commentform.find('input[type="submit"]').removeAttr('disabled');
			},
			success: function(response) {
				var comment_response = JSON.parse(response);
				var $body = $(document.body);
				if ($body.css('direction') == 'ltr') {
					var	comment_actions = '<div class="pull-right">' + comment_response.edit_link + '</div>';
				} else {
					var	comment_actions = '<div class="pull-left">' + comment_response.edit_link + '</div>';
				}
				
				commentform.find('.form-submit .comment-status-ajax-loader').remove();
				var commenttext =  commentform.find('textarea').val();
				var newcomment = '<div id="masonry-meta-comment-wrapper-' + post_id + '" class="masonry-meta"><div class="masonry-meta-avatar">' + obj_pinc.avatar30 + '</div><div class="masonry-meta-comment" id="masonry-comment-' + comment_response.comment_ID + '">' + comment_actions + '<span class="masonry-meta-author">' + obj_pinc.ui + '</span><span class="masonry-meta-comment-content"> ' + commenttext + '</span></div></div>';

				$('[id=masonry-meta-commentform-' + post_id + ']').prev().append(newcomment);
				commentform.find('#comment').val('');
				commentform.closest('#masonry-meta-commentform-' + post_id).hide();
				commentform.closest('#post-' + post_id).find('.pinc-comment').removeClass('disabled');

				var comments_countmsg = commentform.closest('#post-' + post_id).find('#comments-count-'+post_id);
				var comments_count = comments_countmsg.html();
				comments_count = comments_count.substring(comments_count.lastIndexOf(' '));

				if (comments_count == '') {
					$('[id=comments-count-'+post_id+']').removeClass('hide').html('<i class="fas fa-comment"></i> 1');
				} else {
					$('[id=comments-count-'+post_id+']').html('<i class="fas fa-comment"></i> ' + (parseInt(comments_count,10)+1));
				}

				if ($('#post-masonry #masonry').length) {
					$('#post-masonry #masonry').masonry('reloadItems').masonry('layout');
				}

				if ($('#masonry').length) {
					$('#masonry').masonry('reloadItems').masonry('layout');
				}

				commentform.find('textarea').val('');
				commentform.find('input[type="submit"]').removeAttr('disabled');
			}
		});
		return false;
	});

	//comments toggle frontpage comments form
	$(document).on('click', '.pinc-comment', function() {
		if (obj_pinc.u != '0') {
			var commentsform = $(this);
			if (!commentsform.hasClass('disabled')) {
				commentsform.addClass('disabled');
			} else {
				commentsform.removeClass('disabled');
			}

			$(this).closest('#post-' + $(this).data('post_id')).find('#masonry-meta-commentform-' + $(this).data('post_id')).slideToggle('fast', function() {
				if ($('#post-masonry #masonry').length) {
					$('#post-masonry #masonry').masonry('reloadItems').masonry('layout');
				} else if ($('#masonry').length) {
					$('#masonry').masonry('reloadItems').masonry('layout');
				}
			}).find('textarea').focus();
			return false;
		} else {
			loginPopup();
			return false;
		}
	});

	//lightbox
	$(document).on('mouseenter', '#masonry .featured-thumb-link, .post-wrapper .post-nav-next a, .post-wrapper .post-nav-prev a',  function(){
	$(this).animate({'opacity':'0.7'}, 300);
	}).on('mouseleave', '#masonry .featured-thumb-link, .post-wrapper .post-nav-next a, .post-wrapper .post-nav-prev a', function() {
	$(this).animate({'opacity':'1'}, 300);
	});
	$(document).on('click', '#masonry .featured-thumb, .post-wrapper .post-nav-next a, .post-wrapper .post-nav-prev a', function() {
		if ($masonry.length && !$('body').hasClass('single-post') && obj_pinc.lightbox != 'disable' && $('.check-767px').css('float') == 'none' && !ios) {
			var lightbox = $('#post-lightbox');
			var href = $(this).closest('a').attr('href');

			if (!$('#single-pin').length || $('#single-pin').height() <= 0) {
				if (!ie9below) {
					window.history.pushState('', '', href);
				}
			} else {
				if (!ie9below) {
					window.history.replaceState('', '', href);
				}
			}

			lightbox.html('<div id="ajax-loader-masonry"></div>')
				.modal().load(href + ' #single-pin-wrapper', function() {

					var post_masonry = $('#post-masonry #masonry');

					var $body = $(document.body);
					
					if (post_masonry.length && $body.css('direction') == 'ltr') {
						post_masonry.masonry({
							itemSelector : '.thumb',
							fitWidth: true,
							transitionDuration: 0
						}).css('visibility', 'visible');
						$('#post-masonry #ajax-loader-masonry, #post-masonry #navigation').hide();
					} else if (post_masonry.length) {
						post_masonry.masonry({
							itemSelector : '.thumb',
							fitWidth: true,
							originLeft: false,
							transitionDuration: 0
						}).css('visibility', 'visible');
						$('#post-masonry #ajax-loader-masonry, #post-masonry #navigation').hide();
					}

					$('.post-wrapper .post-nav-next a, .post-wrapper .post-nav-prev a').addClass('post-nav-link-lightbox');
					lightbox_postid = $('#single-pin').data('postid');
					lightbox_prevlink = $('.container-fluid > #masonry > #post-' + lightbox_postid).prevAll('.post').first().find('.featured-thumb-link').attr('href');
					lightbox_nextlink = $('.container-fluid > #masonry > #post-' + lightbox_postid).nextAll('.post').first().find('.featured-thumb-link').attr('href');
					if (!lightbox_prevlink) {
						$('.post-wrapper .post-nav-prev a').hide();
					} else {
						$('.post-wrapper .post-nav-prev a').attr('href', lightbox_prevlink);
					}
					if (!lightbox_nextlink) {
						$('.post-wrapper .post-nav-next a').hide();
					} else {
						$('.post-wrapper .post-nav-next a').attr('href', lightbox_nextlink);
					}

					$('#post-close').show();

					lightbox.scrollTop(0).focus();
					$('#post-featured-photo').imagesLoaded(function() {
						lightbox.scrollTop(0);
						var post_top_meta = $('.post-top-meta');
						var post_top_meta_top = post_top_meta.position().top;
						var post_top_meta_height = post_top_meta.outerHeight();

						var post_featured_photo_top = $('#post-featured-photo').offset().top;
						var post_featured_photo_bottom = post_featured_photo_top + $('#post-featured-photo').outerHeight();

						lightbox.scroll(function() {
							var post_top_meta_left = $('#post-featured-photo').offset().left;
							if (lightbox.scrollTop() > post_top_meta_top && lightbox.scrollTop() <= (post_featured_photo_bottom - post_top_meta_height - $(document).scrollTop())) {
								$('.post-top-meta').css({'opacity': '0.95', 'position': 'fixed', 'top': 0, 'left': post_top_meta_left, 'width': $('#post-featured-photo').outerWidth()});
								$('.post-top-meta-placeholder').css('height', post_top_meta_height-1).show();
							} else if (lightbox.scrollTop() > (post_featured_photo_bottom - post_top_meta_height - $(document).scrollTop())) {
								$('.post-top-meta').css({'position': 'absolute', 'top': (post_featured_photo_bottom - post_top_meta_height -$(document).scrollTop() - 2), 'left': 16});
							} else {
								$('.post-top-meta').css({'opacity': '1', 'position': 'relative', 'top': 0, 'left': 0, 'width': 'auto'});
								$('.post-top-meta-placeholder').hide();
							}
						});
					});

					if ($('.adsbygoogle').length) {
						$.getScript('//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js');
						(adsbygoogle = window.adsbygoogle || []).push({});
					}

					if ($('.fb-comments').length) {
						FB.XFBML.parse();
					}
					$( window.wp.mediaelement.initialize );
				});

			if (typeof ga == 'function') {
				ga('send', 'pageview', {'page': href.replace(obj_pinc.home_url, '')});
			}

			if (typeof _gaq !== 'undefined') {
				_gaq.push(['_trackPageview', href.replace(obj_pinc.home_url, '')]);
			}

			return false;
		}
	});

	//hide lightbox when click outside
	$('#post-lightbox').click(function(e) {
		var lightbox = $('#post-lightbox');

		if ((lightbox.has(e.target).length === 0 || $('.row').has(e.target).length === 0) && e.pageX < ($(window).width() - 22)) { //second condition for firefox-scrollbar-onclick-close-lightbox fix
			lightbox.scrollTop(0);
			$('#video-embed').remove();
			$('.navbar-brand').focus().blur();
			lightbox.modal('hide');

			if (!ie9below) {
				window.history.back();
			}
		}
	});

    //hide lightbox when esc key is pressed. must use keydown not keyup.
	$(document).keydown(function(e) {
		if ($('#post-lightbox').css('display') == 'block' && e.keyCode == 27) {
			$('#post-lightbox').scrollTop(0);
			if (!ie9below) {
				window.history.back();
			}
		}
	});

    //hide lightbox when back button is pressed
	$(window).on('popstate', function(e){
		var lightbox = $('#post-lightbox');

		if (lightbox.length) {
			lightbox.scrollTop(0);
			$('#video-embed').remove();
			$('.navbar-brand').focus().blur();
			lightbox.modal('hide');
		}
    });

    //hide lightbox when sidebar close button is clicked
	$(document).on('click', '#post-close', function(e) {
		if (ie9below) {
			var lightbox = $('#post-lightbox');

			if (lightbox.length) {
				lightbox.scrollTop(0);
				$('#video-embed').remove();
				$('.navbar-brand').focus().blur();
				lightbox.modal('hide');
			}
		} else {
			window.history.back();
		}
	});

	//Manipulate history for links in lightbox
	$(document).on('click', '#post-lightbox a', function(e) {
		href = $(this).attr('href');

		if ($(this).hasClass('pinc-edit') || $(this).hasClass('edit-board') || (!$(this).hasClass('post-nav-link-lightbox') && !$(this).hasClass('btn') && href != '' && $(this).attr('target') != '_blank')) {
			if (!ie9below) {
				window.location.replace(href);
				return false;
			} else if (/Chrome[\/\s](\d+\.\d+)/.test(navigator.userAgent)) {
				setTimeout(function() { //have to use setTimeout for chrome?
					window.location.replace(href);
					return false;
				}, 0);
			} else {
				window.location.replace(href);
				return false;
			}
		}
	});

	//Add board
	$('#add_board_form').submit(function() {
		var addboardform = $(this);
		var errormsg = $('.error-msg');
		var ajaxloader = $('.ajax-loader');

		addboardform.find('input[type="submit"]').attr('disabled', 'disabled');
		errormsg.hide();
		ajaxloader.show();

		if ($('#board-title').val() == '') {
			errormsg.html('<div class="alert alert-warning"><strong>' + obj_pinc.__Pleaseentertitle  + '</strong></div>').fadeIn();
			$('#board-title').focus();
			$('.ajax-loader').hide();
			addboardform.find('input[type="submit"]').removeAttr('disabled');
		} else {
			var data = {
				action: 'pinc-add-board',
				nonce: obj_pinc.nonce,
				board_title: $('#board-title').val(),
				category_id: $('#category-id').val(),
				term_id: $('#term-id').val(),
				mode: $('#mode').val()
			};

			$.ajax({
				type: 'post',
				url: obj_pinc.ajaxurl,
				data: data,
				error: function() {
					ajaxloader.hide();
					errormsg.html(obj_pinc.__errorpleasetryagain).fadeIn();
					addboardform.find('input[type="submit"]').removeAttr('disabled');
				},
				success: function(data) {
					ajaxloader.hide();
					if ($('#add_board_form #mode').val() == 'add' || $('#add_board_form #mode').val() == 'edit' ) {
						if (data == 'error') {
							errormsg.html('<div class="alert alert-warning"><strong>' + obj_pinc.__boardalreadyexists  + '</strong></div>').fadeIn();
							$('#board-title').focus();
							addboardform.find('input[type="submit"]').removeAttr('disabled');
						} else {
							window.location = data;
						}
					}
				}
			});
		}
		return false;
	});

	//delete board confirmation
	$(document).on('click', '.pinc-delete-board', function() {
		$('#delete-board-modal').modal();
		return false;
	});

	//delete board
	$(document).on('click', '#pinc-delete-board-confirmed', function() {
		var ajaxloader = $('.ajax-loader-delete-board');
		var delete_btn = $(this);
		var	board_id = delete_btn.data('board_id');

		delete_btn.attr('disabled', 'disabled').prev().attr('disabled', 'disabled');
		ajaxloader.css('display', 'inline-block');

		var data = {
			target: '.ajax-loader-add-pin',
			action: 'pinc-delete-board',
			nonce: obj_pinc.nonce,
			board_id: board_id
		};

		$.ajax({
			type: 'post',
			url: obj_pinc.ajaxurl,
			data: data,
			success: function(data) {
				window.location = data;
			}
		});
	});

	//add pin from computer
	$('#pin_upload_file').change(function() {
		$('.error-msg').hide();
		$('#pin_upload_form').submit();
	});

	if ($('#pin_upload_form').length) {
		var options = {
			beforeSubmit: showRequest,
			uploadProgress: function(event, position, total, percentComplete) {
				if (window.FormData !== undefined) {
					$('#pin-upload-progress').show();
					$('#pin-upload-progress .progress-bar-text').text(percentComplete + '%');
					$('#pin-upload-progress .progress-bar').css('width', percentComplete + '%');
				}
			},
			success: showResponse,
			url: obj_pinc.ajaxurl
		};
		$('#pin_upload_form').ajaxForm(options);
	}

	function showRequest(formData, jqForm, options) {
		$('#pin-upload-from-web-wrapper, #browser-addon, #bookmarklet, #pinitbutton').slideUp();
		if (window.FormData === undefined) {
			$('#pin-upload-from-computer-wrapper .ajax-loader-add-pin').show();
		}

		var files = $('#pin_upload_file').get(0).files,
				hasImage = false,
				hasVideo = false,
				ext;

		for (var i = 0; i < files.length; ++i) {
      ext = files[i].name.split('.').pop().toLowerCase();
      if ($.inArray(ext, ['gif','png','jpg','jpeg']) > -1) {
      	hasImage = true;
      } else if ($.inArray(ext, ['mp4', 'ogg', 'webm']) > -1) {
      	hasVideo = true;
      }
    }

    if (hasImage && hasVideo) {
    	// Mixing image and video files
    	$('#pin-upload-from-computer-wrapper .ajax-loader-add-pin, #pin-upload-progress').hide();
			$('.error-msg').html('<div class="alert alert-warning"><strong>' + obj_pinc.__mixedmimetypes  + '</strong></div>').fadeIn();
    	return false;
    } else {
    	if (hasVideo && files.length > 1) {
    		// Added multiple video files
    		$('#pin-upload-from-computer-wrapper .ajax-loader-add-pin, #pin-upload-progress').hide();
				$('.error-msg').html('<div class="alert alert-warning"><strong>' + obj_pinc.__multipelvideos  + '</strong></div>').fadeIn();
				return false;
    	}
    }

    if (!hasImage && !hasVideo) {
    	// No supported extensions uploaded
    	$('#pin-upload-from-computer-wrapper .ajax-loader-add-pin, #pin-upload-progress').hide();
			$('.error-msg').html('<div class="alert alert-warning"><strong>' + obj_pinc.__invalidimagefile  + '</strong></div>').fadeIn();
			return false;
    }

		// if($.inArray(ext, ['gif','png','jpg','jpeg', 'mp4', 'ogg', 'webm']) == -1) {
		// 	$('#pin-upload-from-computer-wrapper .ajax-loader-add-pin, #pin-upload-progress').hide();
		// 	$('.error-msg').html('<div class="alert alert-warning"><strong>' + obj_pinc.__invalidimagefile  + '</strong></div>').fadeIn();
		// 	return false;
		// }
	}

	function showResponse(responseText, statusText, xhr, $form) {
		if (responseText == 'error') {
			$('.ajax-loader-add-pin, #pin-upload-progress').hide();
			$('.error-msg').html('<div class="alert alert-warning"><strong>' + obj_pinc.__invalidimagefile  + '</strong></div>').fadeIn();
		} else if (responseText == 'errorsize') {
			$('.ajax-loader-add-pin, #pin-upload-progress').hide();
			$('.error-msg').html('<div class="alert alert-warning"><strong>' + obj_pinc.__imagetoosmall  + '</strong></div>').fadeIn();
		} else {
			var data = $.parseJSON(responseText);
			var attachmentIds = [];
			if (typeof data.thumbnails != 'undefined') {
				$.each(data.thumbnails, function(){
					var $img = $('<img/>')
						.attr('src', this.thumbnail)
						.attr('att-id', this.id)
						.addClass('thumbnail');
					$('#pin-upload-postdata-wrapper .postdata-box-photo').append($img);
					attachmentIds.push(this.id);
				});

				if (data.thumbnails.length > 1) {
					$('#pin-upload-postdata-wrapper .select-thumb-message.hidden').removeClass('hidden');
				}
			} else if(typeof data.video != 'undefined') {
				$.each(data.video, function(){
					attachmentIds.push(this.id);
					$('#pin-upload-postdata-wrapper .postdata-box-photo').append(this.code);

					var $video = $('#pin-upload-postdata-wrapper .postdata-box-photo').find('video')[0];

					$video.addEventListener('loadedmetadata', function() {
	          this.currentTime = 1;
	          video_obj = this;
          }, false);

          $video.addEventListener('loadeddata', function() {
          	var scale = 1;
            var canvas = $("<canvas/>");
            canvas[0].width = $video.videoWidth * scale;
            canvas[0].height = $video.videoHeight * scale;
            canvas[0].getContext('2d').drawImage($video, 0, 0, canvas[0].width, canvas[0].height);

            var thumb_image_input = $("<input/>",{id:"video-thumb-data",name:"video-thumb-data", type:'hidden'});
              thumb_image_input.val(canvas[0].toDataURL());

            video_obj.currentTime = 0;

            $('#pin-postdata-form').append(thumb_image_input);
        	},false);

					$( window.wp.mediaelement.initialize );
				});
			}
			$('#attachment-id').val(attachmentIds.join(','));
			$('#thumb-img').val(attachmentIds[0]);
			$('#pin-upload-postdata-wrapper .postdata-box-photo img.thumbnail[att-id="' + attachmentIds[0] + '"]').addClass('active');
			$('.ajax-loader-add-pin, .error-msg, #pin-upload-progress').hide();
			$('#pin-upload-from-computer-wrapper').slideUp(function() {
				$('#pin-upload-postdata-wrapper').slideDown();
				setTimeout(function() {
					tmce_focus('pin-title');

					if (tmce_getContent('pin-title') != ''&& ($('#pin-postdata-form #board').val() != '-1' || $.trim($('#pin-postdata-form #board-add-new').val()))) {
						$('#pinit').removeAttr('disabled');
					} else {
						$('#pinit').attr('disabled', 'disabled');
					}

					if ($('#pin-title_ifr').length) {
						$(document.getElementById('pin-title_ifr').contentWindow.document).keyup(function() {
							if (tmce_getContent('pin-title') != ''&& ($('#pin-postdata-form #board').val() != '-1' || $.trim($('#pin-postdata-form #board-add-new').val()))) {
								$('#pinit').removeAttr('disabled');
							} else {
								$('#pinit').attr('disabled', 'disabled');
							}
						});
					}
				}, 500);
			});
		}
	}

	//add pin from web
	if ($('#pin_upload_web_form').length) {
		var options_web = {
			beforeSubmit: showRequest_web,
			success: showResponse_web,
			url: obj_pinc.ajaxurl
		};
		$('#pin_upload_web_form').ajaxForm(options_web);
	}

	function showRequest_web(formData, jqForm, options) {
		$('#fetch').attr('disabled', 'disabled');
		$('#pin-upload-from-computer-wrapper, #browser-addon, #bookmarklet, #pinitbutton').slideUp();
		$('#photo_data_source').val($('#pin_upload_web').val());
		$('#pin-upload-from-web-wrapper .ajax-loader-add-pin').show();
		$('.error-msg').hide();

		var input_url = $('#pin_upload_web').val();

		if (input_url == '') {
			$('.ajax-loader-add-pin').hide();
			$('.error-msg').html('<div class="alert alert-warning"><strong>' + obj_pinc.__pleaseenterurl  + '</strong></div>').fadeIn();
			$('#fetch').removeAttr('disabled');
			return false();
		}

		//append http:// if missing
		if (input_url.indexOf('http://') == -1 && input_url.indexOf('https://') == -1) {
			input_url = 'http://' + input_url;
		}

		//strip https for youtube & vimeo
		/*if (input_url.indexOf('youtube.com/watch') != -1 || input_url.match(/vimeo.com\/(\d+)($|\/)/)) {
			input_url = input_url.replace('https://', 'http://');
		}*/

		var ext = input_url.split('.').pop().toLowerCase();
		if($.inArray(ext, ['gif','png','jpg','jpeg']) == -1) {
			if (input_url.indexOf('youtu.be/') != -1) {
				input_url = input_url.replace(/youtu.be\//, 'www.youtube.com/watch?v=');
			}

			$.get(obj_pinc.stylesheet_directory_uri + '/pinc_fetch.php?url=' + encodeURIComponent(input_url.replace('http','')) + '&nonce=' + obj_pinc.nonce, function(data){
				if (data.substr(0, 5) == 'error') {
					$('.ajax-loader-add-pin').hide();
					$('#fetch').removeAttr('disabled');
					$('.error-msg').html('<div class="alert alert-warning"><strong>' + data.substr(5)  + '</strong></div>').fadeIn();
				} else {
					$('.ajax-loader-add-pin').hide();
					$('#fetch').removeAttr('disabled');
					$('body').css('overflow', 'hidden')
					.append("\
					<div id='pincframe'>\
						<div id='pincframebg'><p>" + obj_pinc.__loading + "</p></div>\
						<div id='pincheader'><p id='pincclose'>X</p><p id='pinclogo'>" + obj_pinc.blogname + "</p></div>\
						<div id='pincimages'></div>\
						<style type='text/css'>\
							#pincframe {color: #333;}\
							#pincframebg {background: #f2f2f2; display: none; position: fixed; top: 0; right: 0; bottom: 0; left: 0; z-index: 2147483646;}\
							#pincframebg p {background: #999; border-radius: 8px; color: white; font: normal normal bold 16px\/22px Helvetica, Arial, sans-serif; margin: -2em auto 0 -9.5em; padding: 12px; position: absolute; top: 50%; left: 50%; text-align: center; width: 15em;}\
							#pincframe #pincheader {background: white; border-bottom: 1px solid #e7e7e7; color: white; height: 50px; margin: 0; overflow: hidden; padding: 0; position: fixed; top: 0; left: 0; text-align: center; width: 100%; z-index: 2147483647;}\
							#pincframe #pincheader #pinclogo {color: black; font: normal normal bold 20px\/20px Helvetica, Arial, sans-serif; margin: 0; padding: 12px 15px 13px 20px;}\
							#pincframe #pincheader #pincclose {background: #f33; color: white; cursor: pointer; float: right; font: normal normal bold 16px\/16px Helvetica, Arial, sans-serif; line-height: 50px; margin: 0; padding: 0 20px;}\
							#pincimages {position: fixed; top: 60px; left: 0; width: 100%; height: 94%; overflow-x: auto; overflow-y: scroll; text-align: center; z-index: 2147483647;}\
							#pincimages .pincimgwrapper {background: #fcfcfc; border: 1px solid #ddd; cursor: pointer; display: inline-block; height: 200px; margin: 15px; overflow: hidden; position: relative; width: 200px;}\
							#pincimages .pincbutton {background: rgba(0, 0, 0, 0.5); border-radius: 8px; color: white; font: normal normal bold 36px/36px Helvetica, Arial, sans-serif; padding: 8px 16px; display: none; margin-left: -24px; margin-top: -36px; position: absolute; top: 50%; left:50%;}\
							#pincimages .pincdimension {background: white; font: normal normal normal 12px/12px Helvetica, Arial, sans-serif; padding: 3px 0; position: absolute; right: 0; bottom: 0; left: 0;}\
							#pincimages img {width: 100%; height: auto;}\
						</style>\
					</div>");

					$('#pincframebg').fadeIn(200);

					function display_thumbnails(imgarr, videoflag) {
						if (!imgarr.length) {
							$('#pincframebg').html('<p>' + obj_pinc.__sorryunbaletofindanypinnableitems + '</p>');
						} else {
							if ($(data).filter('pinctitle').text()) {
								page_title = encodeURIComponent($(data).filter('pinctitle').text().trim());
							} else {
								page_title = '';
							}

							var imgstr = '';
							for (var i = 0; i < imgarr.length; i++) {

								if (typeof imgarr[i][3] != 'undefined' && imgarr[i][3].length != 0) {
									page_title = imgarr[i][3];
								}
								video_id = '';
								if (typeof imgarr[i][4] != 'undefined') {
									video_id = imgarr[i][4];
								}

								if (videoflag == '0') {
									imgstr += '<div class="pincimgwrapper" data-href="' + obj_pinc.home_url + '/itm-settings/?m=bm&imgsrc=' + encodeURIComponent(imgarr[i][0].replace('http','')) + '&source=' + encodeURIComponent(input_url.replace('http','')) + '&title=' + page_title + '&video=' + videoflag + '&video_id=' + video_id + '"><div class="pincbutton">+</div><img src="' + imgarr[i][0] + '" /></div>';
								} else {
									imgstr += '<div class="pincimgwrapper" data-href="' + obj_pinc.home_url + '/itm-settings/?m=bm&imgsrc=' + encodeURIComponent(imgarr[i][0].replace('http','')) + '&source=' + encodeURIComponent(input_url.replace('http','')) + '&title=' + page_title + '&video=' + videoflag + '&video_id=' + video_id + '"><div class="pincbutton">+</div><div class="pincdimension">' + obj_pinc.__Video + '</div><img src="' + imgarr[i][0] + '" /></div>';
								}
							}

							$('#pincimages').css('height',$(window).height()-$('#pincheader').height()-20).html(imgstr + "<div style='height:40px;clear:both;'><br /></div>")

							if ((navigator.appVersion.indexOf('Chrome/') != -1 || navigator.appVersion.indexOf('Safari/')) && videoflag != '1') {
								$('#pincimages .pincimgwrapper').css('float','left');
							}

							if (videoflag == '0') {
								$('#pincimages').hide().imagesLoaded(function() {
									var images_hidden_count = 0;

									$('#pincimages img').each(function() {
										var imgwidth = this.naturalWidth;
										if (!imgwidth) {
											imgwidth = jQuery(this).width();
										}

										var imgheight = this.naturalHeight;
										if (!imgheight) {
											imgheight = jQuery(this).height();
										}

										if (imgwidth < 125) {
											$(this).parent().hide();
											images_hidden_count++;
										} else {
											$(this).before('<div class="pincdimension">' + parseInt(imgwidth,10) + ' x ' + parseInt(imgheight,10) + '</div>');
										}
									});

									if (images_hidden_count == imgarr.length) {
										$('#pincframebg').html('<p>' + obj_pinc.__sorryunbaletofindanypinnableitems + '</p>');
									} else {
										$('#pincframebg p').fadeOut(200);
										$('#pincimages').show();
									}
								});
							} else {
								$('#pincframebg p').fadeOut(200);
							}
						}
					}

					var imgarr = [];
					var videoflag = '0';

					if (input_url.indexOf('youtube.com/watch') != -1) {
						video_id = input_url.match('[\\?&]v=([^&#]*)');
						imgsrc = 'http://img.youtube.com/vi/' + video_id[1] + '/0.jpg';
						imgarr.unshift([imgsrc,480,360]);
						videoflag = '1';
						display_thumbnails(imgarr, videoflag);
					} /*else if (input_url.match(/vimeo.com\/(\d+)($|\/)/)) {
						video_id = input_url.split('/')[3];

						$.getJSON('http://www.vimeo.com/api/v2/video/' + video_id + '.json?callback=?', {format: "json"}, function(data) {
							imgsrc = data[0].thumbnail_large;
							imgarr.unshift([imgsrc,640,360]);
							videoflag = '1';
							display_thumbnails(imgarr, videoflag);
						});
					} */ else {
						$('img', data).each(function() {
							var
								imgsrc = $(this).prop('src'),
								title = $(this).closest('a').attr('title'),
								video_id = $(this).closest('a').attr('data-video-id');

							imgarr.push([imgsrc,0,0,title,video_id]);
						});

						display_thumbnails(imgarr, videoflag);
					}

					$('#pincheader').on('click', '#pincclose', function() {
						$('body').css('overflow', 'visible');
						$('#pincframe').fadeOut(200, function() {
							$(this).remove();
							$('#pin_upload_web').focus().select();
						});
					});

					$('#pincimages').on('click', '.pincimgwrapper', function() {
						window.open($(this).data('href'), "pincwindow", "width=400,height=760,left=0,top=0,resizable=1,scrollbars=1");
						$('body').css('overflow', 'visible');
						$('#pincframe').remove();
						$('#pin_upload_web').focus().select();
					});

					$('#pincimages').on('mouseover', '.pincimgwrapper', function() {
						$(this).find('.pincbutton').show();
					}).on('mouseout', '.pincimgwrapper', function() {
						$(this).find('.pincbutton').hide();
					});

					$(document).keyup(function(e) {
						if (e.keyCode == 27) {
						$('body').css('overflow', 'visible');
						$('#pincframe').fadeOut(200, function() {
							$(this).remove();
							$('#pin_upload_web').focus().select();
						});
						}
					});
				}
			});

			return false;
		}
	}

	function showResponse_web(responseText, statusText, xhr, $form) {
		if (responseText == 'error') {
			$('.ajax-loader-add-pin').hide();
			$('.error-msg').html('<div class="alert alert-warning"><strong>' + obj_pinc.__invalidimagefile  + '</strong></div>').fadeIn();
			$('#fetch').removeAttr('disabled');
		} else if (responseText == 'errorsize') {
			$('.ajax-loader-add-pin').hide();
			$('.error-msg').html('<div class="alert alert-warning"><strong>' + obj_pinc.__imagetoosmall  + '</strong></div>').fadeIn();
			$('#fetch').removeAttr('disabled');
		} else if (responseText.indexOf('Error Fetching Image') != -1) {
			$('#pin-upload-from-web-wrapper .ajax-loader-add-pin').hide();
			$('.error-msg').html('<div class="alert alert-warning"><strong>' + responseText  + '</strong></div>').fadeIn();
			$('#pin_upload_web').focus();
			$('#fetch').removeAttr('disabled');
		} else {
			var data = $.parseJSON(responseText);
			$('#thumbnail').attr('src', data.thumbnail);
			$('#attachment-id').val(data.id);
			$('.ajax-loader-add-pin, .error-msg').hide();
			$('#pin-upload-from-web-wrapper').slideUp(function() {
				$('#pin-upload-postdata-wrapper').slideDown();
			});
		}
	}

	//add new board toggle
	$(document).on('click', '#pin-postdata-form #pin-postdata-add-new-board', function() {
		if ($(this).text() == obj_pinc.__cancel) {
			if($('#noboard').length) {
				$('#pinit').attr('disabled', 'disabled');
			}
			$(this).text(obj_pinc.__addnewboard);
			$('.usercp-pins #board-add-new').val('').hide();
			$('.usercp-pins #board-add-new-category').val('-1').hide();
			$('.usercp-pins #board').show().focus();
		} else {
			$(this).text(obj_pinc.__cancel);
			$('.usercp-pins #board-add-new').show().focus();
			$('.usercp-pins #board-add-new-category').show();
			$('.usercp-pins #board').hide();
		}
		return false;
	});

	//disable submit button if empty textarea (from web and computer)
	$('#pin-postdata-form textarea#pin-title').focus(function() {
		if ($.trim($('#pin-postdata-form textarea#pin-title').val()) && ($('#pin-postdata-form #board').val() != '-1' || $.trim($('#pin-postdata-form #board-add-new').val()))) {
			$('#pinit').removeAttr('disabled');
		} else {
			$('#pinit').attr('disabled', 'disabled');
		}

		$(this).keyup(function() {
			if ($.trim($('#pin-postdata-form textarea#pin-title').val()) && ($('#pin-postdata-form #board').val() != '-1' || $.trim($('#pin-postdata-form #board-add-new').val()))) {
				$('#pinit').removeAttr('disabled');
			} else {
				$('#pinit').attr('disabled', 'disabled');
			}
		});
	});

	//disable submit button if empty textarea (bookmarklet mode)
	if ($('#pin-postdata-form textarea#pin-title').is(':focus')) {
		if ($.trim($('#pin-postdata-form textarea#pin-title').val()) && ($('#pin-postdata-form #board').val() != '-1' || $.trim($('#pin-postdata-form #board-add-new').val()))) {
			$('#pinit').removeAttr('disabled');
		} else {
			$('#pinit').attr('disabled', 'disabled');
		}

		$(this).keyup(function() {
			if ($.trim($('#pin-postdata-form textarea#pin-title').val()) && ($('#pin-postdata-form #board').val() != '-1' || $.trim($('#pin-postdata-form #board-add-new').val()))) {
				$('#pinit').removeAttr('disabled');
			} else {
				$('#pinit').attr('disabled', 'disabled');
			}
		});
	}

	$('#pin-postdata-form #board-add-new').focus(function() {
		var content = '';
		if ($('#pin-title_ifr').length) {
			content = tmce_getContent('pin-title');
		} else {
			content = $.trim($('#pin-postdata-form textarea#pin-title').val());
		}

		$(this).keyup(function() {
			if ($('#pin-title_ifr').length) {
				content = tmce_getContent('pin-title');
			} else {
				content = $.trim($('#pin-postdata-form textarea#pin-title').val());
			}

			if (content && ($('#pin-postdata-form #board').val() != '-1' || $.trim($('#pin-postdata-form #board-add-new').val()))) {
				$('#pinit').removeAttr('disabled');
			} else {
				$('#pinit').attr('disabled', 'disabled');
			}
		});
	});

	//autocomplete tags
	if ('function' == typeof $.suggest) {
		$('input#tags').suggest(obj_pinc.ajaxurl + '?action=ajax-tag-search&tax=post_tag', {minchars: 3, multiple: true});
	}

	//insert new pin
	$('#pin-postdata-form').submit(function() {
		var postdataform = $(this);
		var errormsg = $('.error-msg');
		var ajaxloader = $('.ajax-loader-add-pin');

		//empty title
		// if (!$.trim($('#pin-postdata-form textarea#pin-title').val())) {
		if (!$.trim(tmce_getContent('pin-title'))) {
			ajaxloader.hide();
			errormsg.html('<div class="alert alert-warning"><strong>' + obj_pinc.__Pleaseentertitle  + '</strong></div>').fadeIn();
			return false;
		}

		//empty board
		if ($('#pin-postdata-form #board').val() == '-1' && $.trim($('#pin-postdata-form #board-add-new').val()) == '') {
			ajaxloader.hide();
			errormsg.html('<div class="alert alert-warning"><strong>' + obj_pinc.__Pleasecreateanewboard  + '</strong></div>').fadeIn();
			return false;
		}

		var postdata_photo_source;

		if ($('#photo_data_source').val()) {
			postdata_photo_source = $('#photo_data_source').val();
		}

		var
			postdata_thumbnail_source = getParameterByName('imgsrc'),
			postdata_thumbnail_video_id = getParameterByName('video_id');

		var price = '';
		if ($('#pin-postdata-form #price').length)
			price = $('#pin-postdata-form #price').val().replace(/[^0-9.]/g, '');

		var colorThief = new ColorThief();
		var postdata_bgcolor = '';

		if ($('#pin-upload-postdata-wrapper .postdata-box-photo .thumbnail').length > 0) {
			colorThief.getColor($('#pin-upload-postdata-wrapper .postdata-box-photo .thumbnail')[0]).join(',');
		}

		var data = {
			action: 'pinc-postdata',
			nonce: obj_pinc.nonce,
			postdata_title: tmce_getContent('pin-title'),
			postdata_content: tmce_getContent('pin-content'),
			postdata_attachment_id: $('#attachment-id').val(),
			postdata_thumb_id: $('#thumb-img').val(),
			postdata_video_thumb_data: $('#video-thumb-data').val(),
			postdata_board: $('#pin-postdata-form #board').val(),
			postdata_board_add_new: $('#pin-postdata-form #board-add-new').val(),
			postdata_board_add_new_category: $('#pin-postdata-form #board-add-new-category').val(),
			postdata_tags: $('#pin-postdata-form #tags').val(),
			postdata_price: price,
			postdata_photo_source: postdata_photo_source,
			postdata_thumbnail_source: postdata_thumbnail_source,
			postdata_thumbnail_video_id: postdata_thumbnail_video_id,
			postdata_bgcolor: postdata_bgcolor
		};

		postdataform.find('input[type="submit"]').attr('disabled', 'disabled');
		ajaxloader.show();
		errormsg.hide();

		$.ajax({
			type: 'post',
			url: obj_pinc.ajaxurl,
			data: data,
			error: function() {
				ajaxloader.hide();
				errormsg.html('<div class="alert alert-warning"><strong>' + obj_pinc.__errorpleasetryagain  + '</strong></div>').fadeIn();
				postdataform.find('input[type="submit"]').removeAttr('disabled');
			},
			success: function(data) {
				ajaxloader.hide();
				errormsg.hide();
				$('#pin-postdata-form').hide();
				$('#pin-upload-postdata-wrapper .select-thumb-message:not(.hidden)').addClass('hidden');
				var board_name;
				if ($('#pin-postdata-form #board-add-new').val() == '') {
					board_name = $('#pin-postdata-form #board option:selected').text();
				} else {
					board_name = $('#pin-postdata-form #board-add-new').val();
				}

				var pin_status ='<br />';
				if (data.indexOf('/?p=') != -1) {
					pin_status = '<small style="display:block;clear:both"><span class="label label-warning">' + obj_pinc.__yourpinispendingreview + '</span></small>';
				}

				if (window.location.search.indexOf('m=bm') != -1) //via bookmarklet
					$('.postdata-box-photo').after('<h3 id="repinnedmsg" class="text-center">' + obj_pinc.__pinnedto + ' ' + board_name + pin_status + '<p></p><a class="btn btn-success" href="javascript:window.open(\'' + data + '\');window.close();"><strong>' + obj_pinc.__seethispin + '</strong></a> <a href="javascript:window.close()" class="btn btn-success" aria-hidden="true"><strong>' + obj_pinc.__close + '</strong></a></h3><h5 class="repinnedmsg-share text-center"><strong>' + obj_pinc.__shareitwithyourfriends + '</strong></h5><p class="repinnedmsg-share text-center"><a class="btn btn-primary btn-sm" href="" onclick="window.open(\'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(data) + '\', \'facebook-share-dialog\', \'width=626,height=500\'); return false;"><i class="fab fa-facebook fa-fw"></i></a> <a class="btn btn-info btn-sm" href="" onclick="window.open(\'https://twitter.com/share?url=' + data + '&amp;text=' + encodeURIComponent($('#pin-postdata-form textarea#pin-title').val()) + '\', \'twitter-share-dialog\', \'width=626,height=500\'); return false;"><i class="fab fa-twitter fa-fw"></i></a> <a class="btn btn-danger btn-sm" href="" onclick="window.open(\'https://plus.google.com/share?url=' + data + '\', \'gplus-share-dialog\', \'width=626,height=500\'); return false;"><i class="fab fa-google-plus fa-fw"></i></a> <a class="btn btn-info btn-sm" href="" onclick="window.open(\'http://www.reddit.com/submit?url=' + encodeURIComponent(data) + '&amp;title=' + encodeURIComponent($('#repinform textarea#pin-title').val()) + '\', \'reddit-share-dialog\', \'width=880,height=500,scrollbars=1\'); return false;"><i class="fab fa-reddit fa-fw"></i></a></p>');
				else {
					$('.postdata-box-photo').after('<h3 id="repinnedmsg" class="text-center">' + obj_pinc.__pinnedto + ' ' + board_name + pin_status + '<p></p><a class="btn btn-success" href="' + data + '"><strong>' + obj_pinc.__seethispin + '</strong></a> <a href="" class="btn btn-success" aria-hidden="true"><strong>' + obj_pinc.__addanotherpin + '</strong></a></h3><h5 class="repinnedmsg-share text-center"><strong>' + obj_pinc.__shareitwithyourfriends + '</strong></h5><p class="repinnedmsg-share text-center"><a class="btn btn-primary btn-sm" href="" onclick="window.open(\'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(data) + '\', \'facebook-share-dialog\', \'width=626,height=500\'); return false;"><i class="fab fa-facebook fa-fw"></i></a> <a class="btn btn-info btn-sm" href="" onclick="window.open(\'https://twitter.com/share?url=' + data + '&amp;text=' + encodeURIComponent($('#pin-postdata-form textarea#pin-title').val()) + '\', \'twitter-share-dialog\', \'width=626,height=500\'); return false;"><i class="fab fa-twitter fa-fw"></i></a> <a class="btn btn-danger btn-sm" href="" onclick="window.open(\'https://plus.google.com/share?url=' + data + '\', \'gplus-share-dialog\', \'width=626,height=500\'); return false;"><i class="fab fa-google-plus fa-fw"></i></a> <a class="btn btn-info btn-sm" href="" onclick="window.open(\'http://www.reddit.com/submit?url=' + encodeURIComponent(data) + '&amp;title=' + encodeURIComponent($('#pin-postdata-form textarea#pin-title').val()) + '\', \'reddit-share-dialog\', \'width=880,height=500,scrollbars=1\'); return false;"><i class="fab fa-reddit fa-fw"></i></a></p>');
				}
			}
		});
		return false;
	});

	//edit pin
	//add new board toggle
	$(document).on('click', '#pin-edit-form #pin-postdata-add-new-board', function() {
		if ($(this).text() == obj_pinc.__cancel) {
			$(this).text(obj_pinc.__addnewboard);
			$('.usercp-pins #board-add-new').val('').hide();
			$('.usercp-pins #board-add-new-category').val('-1').hide();
			$('.usercp-pins #board').show().focus();
		} else {
			$(this).text(obj_pinc.__cancel);
			$('.usercp-pins #board-add-new').show().focus();
			$('.usercp-pins #board-add-new-category').show();
			$('.usercp-pins #board').hide();
		}
		return false;
	});

	$(document).on('click', '#pin-upload-postdata-wrapper img.thumbnail', function() {
		if ($('#pin-postdata-form').css('display') != 'none') {
			var thumb = $('#thumb-img').val();
			if ($(this).attr('att-id') != thumb) {
				$('#thumb-img').val($(this).attr('att-id'));
				$('#pin-upload-postdata-wrapper img.thumbnail.active').removeClass('active');
				$(this).addClass('active');
			}
		}
	});

	//disable submit button if empty textarea
	if ($('#pin-edit-form textarea#pin-title').is(":focus")) {
		$(this).keyup(function() {
			if ($.trim($('#pin-edit-form textarea#pin-title').val())) {
				$('#pinit').removeAttr('disabled');
			} else {
				$('#pinit').attr('disabled', 'disabled');
			}
		});
	}

	$('#pin-edit-form textarea#pin-title').focus(function() {
		$(this).keyup(function() {
			if ($.trim($('#pin-edit-form textarea#pin-title').val())) {
				$('#pinit').removeAttr('disabled');
			} else {
				$('#pinit').attr('disabled', 'disabled');
			}
		});
	});

	$('#pin-edit-form').submit(function() {
		var editform = $(this);
		var errormsg = $('.error-msg');
		var ajaxloader = $('.ajax-loader-add-pin');

		var price = '';
		if ($('#pin-edit-form #price').length)
			price = $('#pin-edit-form #price').val().replace(/[^0-9.]/g, '');

		var data = {
			action: 'pinc-pin-edit',
			nonce: obj_pinc.nonce,
			postdata_pid: $('#pin-edit-form #pid').val(),
			postdata_title: tmce_getContent('pin-title'),
			postdata_content: tmce_getContent('pin-content'),
			postdata_board: $('#pin-edit-form #board').val(),
			postdata_board_add_new: $('#pin-edit-form #board-add-new').val(),
			postdata_board_add_new_category: $('#pin-edit-form #board-add-new-category').val(),
			postdata_tags: $('#pin-edit-form #tags').val(),
			postdata_thumb_id: $('#thumb-img').val(),
			postdata_price: price,
			postdata_source: $('#pin-edit-form #source').val()
		};

		editform.find('input[type="submit"]').attr('disabled', 'disabled');
		ajaxloader.show();
		errormsg.hide();

		$.ajax({
			type: 'post',
			url: obj_pinc.ajaxurl,
			data: data,
			error: function() {
				ajaxloader.hide();
				errormsg.html('<div class="alert alert-warning"><strong>' + obj_pinc.__errorpleasetryagain  + '</strong></div>').fadeIn();
				editform.find('input[type="submit"]').removeAttr('disabled');
			},
			success: function(data) {
				window.location = data;
			}
		});
		return false;
	});

	//edit comment
	$(document).on('click', '.comment-edit-link', function(e) {
		var data = {
			action: 'pinc-edit-comment',
			nonce: obj_pinc.nonce,
			comment_id: $(this).attr('comment-id'),
		};

		$.ajax({
			type: 'post',
			url: obj_pinc.ajaxurl,
			data: data,
			success: function(data) {
				var dialog = $(data);
				dialog.modal();

				$('#edit-comment-form').submit(function(){

					var data = {
						action: 'pinc-edit-comment-submit',
						nonce: obj_pinc.nonce,
						comment_id: $('#edit-comment-form #comment-id').val(),
						content: $('#edit-comment-form #comment-content').val()
					};

					$.ajax({
						type: 'post',
						url: obj_pinc.ajaxurl,
						data: data,
						success: function(success) {
							if (success){
								$('#comment-' + data.comment_id + ' .comment-content p').text(data.content);
								$('#masonry-comment-' + data.comment_id + ' .masonry-meta-comment-content').text(data.content);
							}
							dialog.modal('hide').remove();
						}
					});

					return false;
				});

				$('#edit-comment-form #save-comment').click(function(){
					$('#edit-comment-form').submit();
					return false;
				});
			}
		});

		return false;
	});

	//delete pin confirmation
	$(document).on('click', '.pinc-delete-pin', function() {
		$('#delete-pin-modal').modal();
		return false;
	});

	//delete pin
	$(document).on('click', '#pinc-delete-pin-confirmed', function() {
		var ajaxloader = $('.ajax-loader-delete-pin');
		var delete_btn = $(this);
		var	pin_id = delete_btn.data('pin_id');
		var	pin_author = delete_btn.data('pin_author');

		delete_btn.attr('disabled', 'disabled').prev().attr('disabled', 'disabled');
		ajaxloader.css('display', 'inline-block');

		var data = {
			action: 'pinc-delete-pin',
			nonce: obj_pinc.nonce,
			pin_id: pin_id,
			pin_author: pin_author
		};

		$.ajax({
			type: 'post',
			url: obj_pinc.ajaxurl,
			data: data,
			success: function(data) {
				window.location = data;
			}
		});
	});

	//replace image in edit form
	$('#pinc_replace_image').change(function() {
		$('.error-msg-replace-image').hide();
		$('#pinc-replace-image-form').submit();
	});

	if ($('#pinc-replace-image-form').length) {
		var options = {
			beforeSubmit: showRequest_replace_image,
			success: showResponse_replace_image,
			url: obj_pinc.ajaxurl
		};
		$('#pinc-replace-image-form').ajaxForm(options);
	}

	function showRequest_replace_image(formData, jqForm, options) {
		$('#pinc-replace-image-form .ajax-loader-replace-image').show();

		var ext = $('#pinc_replace_image').val().split('.').pop().toLowerCase();
		if($.inArray(ext, ['gif','png','jpg','jpeg']) == -1) {
			$('#pinc-replace-image-form .ajax-loader-replace-image').hide();
			$('.error-msg-replace-image').html('<div class="alert"><strong>' + obj_pinc.__invalidimagefile  + '</strong></div>').fadeIn();
			return false;
		}
	}

	function showResponse_replace_image(responseText, statusText, xhr, $form) {
		if (responseText == 'error') {
			$('#pinc-replace-image-form .ajax-loader-replace-image').hide();
			$('.error-msg-replace-image').html('<div class="alert"><strong>' + obj_pinc.__invalidimagefile  + '</strong></div>').fadeIn();
		} else {
			$('#thumbnail').attr('src', responseText);
			$('#pinc-replace-image-form .ajax-loader-replace-image').hide();
		}
	}

	//delete account confirmation
	$(document).on('click', '#pinc-delete-account', function() {
		$('#delete-account-modal').modal();
		return false;
	});

	//delete account
	$(document).on('click', '#pinc-delete-account-confirmed', function() {
		var ajaxloader = $('.ajax-loader-delete-account');
		var delete_btn = $(this);
		var	user_id = delete_btn.data('user_id');

		delete_btn.attr('disabled', 'disabled').prev().attr('disabled', 'disabled');
		ajaxloader.css('display', 'inline-block');

		var data = {
			action: 'pinc-delete-account',
			nonce: obj_pinc.nonce,
			user_id: user_id
		};

		$.ajax({
			type: 'post',
			url: obj_pinc.ajaxurl,
			data: data,
			success: function(data) {
				window.location = data;
			}
		});
	});

	//login form check
	$(document).on('submit', '#loginform', function() {
		$('.error-msg-incorrect').hide();
		if ($('#log').val() == '' || $('#pwd').val() == '') {
			$('.error-msg-blank').html('<div class="alert alert-warning"><strong>' + obj_pinc.__pleaseenterbothusernameandpassword  + '</strong></div>').fadeIn();
			return false;
		}
	});

	//ajax upload avatar
	$('#pinc_user_avatar').change(function() {
		$('.error-msg-avatar').hide();
		$('#avatarform').submit();
	});

	if ($('#avatarform').length) {
		var options = {
			beforeSubmit: showRequest_avatar,
			success: showResponse_avatar,
			url: obj_pinc.ajaxurl
		};
		$('#avatarform').ajaxForm(options);
	}

	function showRequest_avatar(formData, jqForm, options) {
		$('#avatarform .ajax-loader-avatar').show();

		var ext = $('#pinc_user_avatar').val().split('.').pop().toLowerCase();
		if($.inArray(ext, ['gif','png','jpg','jpeg']) == -1) {
			$('#avatarform .ajax-loader-avatar').hide();
			$('.error-msg-avatar').html('<div class="alert alert-warning"><strong>' + obj_pinc.__invalidimagefile  + '</strong></div>').fadeIn();
			return false;
		}
	}

	function showResponse_avatar(responseText, statusText, xhr, $form) {
		if (responseText == 'error') {
			$('#avatarform .ajax-loader-avatar').hide();
			$('.error-msg-avatar').html('<div class="alert alert-warning"><strong>' + obj_pinc.__invalidimagefile  + '</strong></div>').fadeIn();
		} else {
			var data = $.parseJSON(responseText);
			$('#avatar-wrapper').fadeOut(function() {
				$('#avatar-wrapper .img-polaroid').attr('src', data.thumbnail);
				$('#avatar-delete').removeAttr('disabled');
				$('#avatarform .ajax-loader-avatar').hide();
				$('#coverform').css('top', $('#avatarform').offset().top+$('#avatarform').height()+54);
				$('#avatar-anchor').css('margin-bottom', $('#avatarform').height()+$('#coverform').height()+153);
				$('#avatar-wrapper').slideDown();
			});
		}
	}

	//delete avatar
	$('#avatar-delete').on('mouseup', function() {
		var ajaxloader = $('.ajax-loader-avatar');
		var delete_btn = $(this);
		var id = delete_btn.data('id');
		delete_btn.attr('disabled', 'disabled');
		ajaxloader.show();

		var data = {
			action: 'pinc-delete-avatar',
			nonce: obj_pinc.nonce,
			id: id
		};

		$.ajax({
			type: 'post',
			url: obj_pinc.ajaxurl,
			data: data,
			success: function() {
				ajaxloader.hide();
				$('#avatar-wrapper').fadeOut(function() {
					$('#coverform').css('top', $('#avatarform').offset().top+$('#avatarform').height()-69);
					$('#avatar-anchor').css('margin-bottom', $('#avatarform').height()+$('#coverform').height()+80);
				});
			}
		});
		return false;
	});

	//ajax upload cover
	$('#pinc_user_cover').change(function() {
		$('.error-msg-cover').hide();
		$('#coverform').submit();
	});

	if ($('#coverform').length) {
		var options = {
			beforeSubmit: showRequest_cover,
			success: showResponse_cover,
			url: obj_pinc.ajaxurl
		};
		$('#coverform').ajaxForm(options);
	}

	function showRequest_cover(formData, jqForm, options) {
		$('#coverform .ajax-loader-cover').show();

		var ext = $('#pinc_user_cover').val().split('.').pop().toLowerCase();
		if($.inArray(ext, ['gif','png','jpg','jpeg']) == -1) {
			$('#coverform .ajax-loader-cover').hide();
			$('.error-msg-cover').html('<div class="alert alert-warning"><strong>' + obj_pinc.__invalidimagefile  + '</strong></div>').fadeIn();
			return false;
		}
	}

	function showResponse_cover(responseText, statusText, xhr, $form) {
		if (responseText == 'error') {
			$('#coverform .ajax-loader-cover').hide();
			$('.error-msg-cover').html('<div class="alert alert-warning"><strong>' + obj_pinc.__invalidimagefile  + '</strong></div>').fadeIn();
		} else {
			var data = $.parseJSON(responseText);
			$('#cover-wrapper').fadeOut(function() {
				$('#cover-wrapper .img-polaroid').attr('src', data.thumbnail);
				$('#cover-delete').removeAttr('disabled');
				$('#coverform .ajax-loader-cover').hide();
				$('#avatar-anchor').css('margin-bottom', $('#avatarform').height()+$('#coverform').height()+153);
				$('#cover-wrapper').slideDown();
			});
		}
	}

	//delete cover
	$('#cover-delete').on('mouseup', function() {
		var ajaxloader = $('.ajax-loader-cover');
		var delete_btn = $(this);
		var id = delete_btn.data('id');
		delete_btn.attr('disabled', 'disabled');
		ajaxloader.show();

		var data = {
			action: 'pinc-delete-cover',
			nonce: obj_pinc.nonce,
			id: id
		};

		$.ajax({
			type: 'post',
			url: obj_pinc.ajaxurl,
			data: data,
			success: function() {
				ajaxloader.hide();
				$('#cover-wrapper').fadeOut(function() {
					$('#avatar-anchor').css('margin-bottom', $('#avatarform').height()+$('#coverform').height()+30);
				});
			}
		});
		return false;
	});

	//kiv: animated gif mouseover
	//slow to load if animated gif filesize is large
	/* $(document).on('mouseover', '.featured-thumb-gif-class', function() {
		var preload = new Image();
		preload.src = $(this).data('animated-gif-src-full');
		$(this).attr('src', preload.src)
			.prev('.featured-thumb-gif').hide();
	});

	$(document).on('mouseout', '.featured-thumb-gif-class', function() {
		$(this).attr('src', $(this).data('animated-gif-src-medium'))
			.prev('.featured-thumb-gif').show();
	});
	*/

	function topAlertMsg(message) {
	    if ($('#top-alert-msg').length) {
			$('#top-alert-msg').hide();
		}

		$("<div />", {
				id: 'top-alert-msg',
				html: message + '<div id="top-alert-msg-close">&times;</div>'
			})
			.hide()
			.prependTo("body")
			.slideDown('fast')
			.delay(5000)
			.slideUp(function() {
				$(this).remove();
		});

		$(document).on('click', '#top-alert-msg-close', function() {
			$('#top-alert-msg').remove();
		});
	}

	function loginPopup() {
		if ($('.check-480px').css('float') == 'left') {
			topAlertMsg('<a href="' + obj_pinc.login_url + '">' + obj_pinc.__Pleaseloginorregisterhere + '</a>');
		} else {
			if ($('#loginbox-wrapper .popover').length) {
				$('#loginbox').popover('hide');
			}

			$('#video-embed').remove();
			$('.brand').focus().blur();

			if ($('#post-lightbox').css('display') == 'block') {
				if (!ie9below) {
					window.history.back();
				}
			}

			$('#post-lightbox').modal('hide');
			$('#popup-overlay').show();
			$('#popup-login-box').modal();
		}
	}

	//facebook like with comment
	$(document).on('mouseenter', '.fb-like', function() {
		$(this).css('overflow', 'visible');
	});

	$(document).on('mouseleave', '.fb-like', function() {
		$(this).css('overflow', 'hidden');
	});
});

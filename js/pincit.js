if (document.URL.match(/(gif|png|jpg|jpeg)$/i) && (navigator.appVersion.indexOf('Chrome/') != -1 || navigator.appVersion.indexOf('Safari/') != -1)) {
	alert('For direct jpg/gif/png url, please fetch image at Add > Pin > From Web');
}

(function(){
	var v = '1.7';

	if (window.jQuery === undefined || window.jQuery.fn.jquery < v) {
		var done = false;
		var script = document.createElement('script');
		script.src = '//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js';
		script.onload = script.onreadystatechange = function(){
			if (!done && (!this.readyState || this.readyState == 'loaded' || this.readyState == 'complete')) {
				done = true;
				pincit();
			}
		};
		document.getElementsByTagName('head')[0].appendChild(script);
	} else {
		pincit();
	}

	var scraper = scraper || {};

	function pincit() {
		(function($) {
			scraper = {
				parser : 'parserDefault',
				videoflag: 0,
				imgarr : [],

				init: function() {
					this.videoflag = 0;
					this.imgarr = [];
					this.parser = 'parserDefault';

					if (document.URL.indexOf('redtube.com') != -1) {
						this.parser = 'parserRedtube';
					} else if (document.URL.indexOf('pornhub.com') != -1) {
						this.parser = 'parserPornhub';
					} else if (document.URL.indexOf('xhamster.com') != -1) {
						this.parser = 'parserXhamster';
					} else if (document.URL.indexOf('youporn.com') != -1) {
						this.parser = 'parserYouporn';
					} else if (document.URL.indexOf('xvideos.com') != -1) {
						this.parser = 'parserXvideos';
					} else if (document.URL.indexOf('.google.') != -1) {
						this.parser = 'parserGoogle';
					}

					return this;
				},

				getParameterByName: function(name, url) {
					if (!url) url = window.location.href;
					name = name.replace(/[\[\]]/g, "\\$&");
					var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
					results = regex.exec(url);
					if (!results) return null;
					if (!results[2]) return '';
					return decodeURIComponent(results[2].replace(/\+/g, " "));
				},

				fetch: function() {
					this[this.parser]();
					display_thumbnails(this.imgarr,this.videoflag);
				},

				parserOg: function(video_id, title) {
					var
						obj = this,
						$og_image = $('head').find('meta[property="og:image"]');

					if ($og_image.length != 0) {
						$('#pincph').attr('src', $og_image.attr('content'));

						var data = [
							$og_image.attr('content'),
							$('#pincph').width(),
							$('#pincph').height()
						];

						if (typeof title != 'undeinfed') {
							data.push(title);
						} else {
							data.push($('title').text());
						}

						if (typeof video_id != 'undeinfed') {
							data.push(video_id);
						}

						obj.imgarr.unshift(data);
					}
				},

				/**
				 *
				 */
				parserDefault: function() {
					var obj = this;

					$('div:not(#pincframe)').find('img').each(function() {
						var $image = $(this);

						$('#pincph').attr('src', $image.attr('src'));

						var data = [
							$image.attr('src'),
							$('#pincph').width(),
							$('#pincph').height()
						];

						if ($image.attr('alt')) {
							data.push($image.attr('alt'));
						}

						obj.imgarr.unshift(data);
					});
				},

				parserGoogle: function() {
					var obj = this;

					$('div.rg_meta').each(function() {
						image_info = JSON.parse($(this).text());

						var data = [
							image_info.ou,
							image_info.ow,
							image_info.oh,
							image_info.pt
						];

						obj.imgarr.unshift(data);
					});
				},

				/**
				 *
				 */
				parserRedtube: function() {
					var obj = this;

					if ($('#flag-form').length != 0) {
						video_id = $('#flag-form').find('input[name="object_id"]').val();
						obj.parserOg(video_id);
					}

					$('div:not(#pincframe)').find('a.widget-video-link, a.video-thumb').each(function() {
						var
							$link = $(this),
							$image = $link.find('img');

						$('#pincph').attr('src', $image.attr('src'));

						var data = [
							$image.attr('src'),
							$('#pincph').width(),
							$('#pincph').height()
						];

						if ($image.attr('alt')) {
							data.push($image.attr('alt'));
						} else {
							data.push('RedTube Video');
						}
						var href = $link.attr('href');
						if (href && href.length) {
							data.push(href.substring(1, href.length));
						}

						obj.imgarr.unshift(data);
					});
				},

				/**
				 *
				 */
				parserYouporn: function() {
					var obj = this;

					obj.parserOg();

					$('.video-box').each(function() {
						var
							$link = $(this).find('a.video-box-image'),
							$image = $link.find('img'),
							src = $image.attr('src');

						if (typeof src == 'undefined') {
							src = $image.attr('data-thumbnail');
						}

						if (typeof src == 'undefined') {
							return true;
						}

						$('#pincph').attr('src', src);

						var data = [
							src,
							$('#pincph').width(),
							$('#pincph').height()
						];

						if ($image.attr('alt')) {
							data.push($image.attr('alt'));
						} else {
							data.push('YouPorn Video');
						}

						if ($(this).attr('data-video-id')) {
							data.push($(this).attr('data-video-id'));
						}

						obj.imgarr.unshift(data);
					});
				},

				/**
				 *
				 */
				parserXvideos: function() {

					var obj = this;

					$('.thumb-block').each(function() {
						var
							$link = $(this).find('a'),
							$image = $link.find('img'),
							src = $image.attr('src');


						if (typeof src == 'undefined') {
							return true;
						}

						$('#pincph').attr('src', src);

						var data = [
							src,
							$('#pincph').width(),
							$('#pincph').height()
						];

						if ($(this).find('p').lengt > 0) {
							data.push($(this).find('p').text());
						} else {
							data.push('XVIDEOS Video');
						}

						video_id = $image.attr('data-videoid');
						data.push(video_id);

						obj.imgarr.unshift(data);
					});
				},

				/**
				 *
				 */
				parserXhamster: function() {
					var obj = this;
					$thumbnail_url = $('div:not(#pincframe)').find('link[itemprop="thumbnailUrl"]');

					if ($thumbnail_url.length != 0) {
						src = $thumbnail_url.attr('href');

						$('#pincph').attr('src', src);

						var data = [
							src,
							$('#pincph').width(),
							$('#pincph').height(),
							$('title').text()
						];

						obj.imgarr.unshift(data);
					}

					$('div:not(#pincframe)').find('div.video-thumb a').each(function() {
						var
							$link = $(this),
							$image = $link.find('img'),
							src = $image.attr('src');


						if (typeof src == 'undefined') {
							return true;
						}

						$('#pincph').attr('src', src);

						var data = [
							src,
							$('#pincph').width(),
							$('#pincph').height()
						];

						if ($image.attr('alt')) {
							data.push($image.attr('alt'));
						} else {
							data.push('xHamster Video');
						}

						var href = $link.attr('href');
						if (href && href.length) {
							video_id = href.substring(href.lastIndexOf('-') + 1, href.length);
							data.push(video_id);
						}

						obj.imgarr.unshift(data);
					});
				},

				/**
				 *
				 */
				parserPornhub: function() {
					var obj = this;

					if ($('#existWrap').length != 0) {
						video_id = $('#existWrap').attr('data-vkey');
						obj.parserOg(video_id);
					}

					$('div:not(#pincframe)').find('div.videoPreviewBg a').each(function() {
						var
							$link = $(this),
							$image = $link.find('img'),
							src = $image.attr('data-mediumthumb');

						if (typeof src == 'undefined') {
							src = $image.attr('data-image');
						}

						if (typeof src == 'undefined') {
							return true;
						}

						$('#pincph').attr('src', src);

						var data = [
							src,
							$('#pincph').width(),
							$('#pincph').height()
						];

						if ($image.attr('data-title')) {
							data.push($image.attr('data-title'));
						} else {
							data.push('PornHub Video');
						}

						var href = $link.attr('href');
						if (href && href.length) {
							data.push(obj.getParameterByName('viewkey', href));
						}

						obj.imgarr.unshift(data);
					});

				},

			};
		}(jQuery));

		(window.pincit = function() {
			if (jQuery('#pincframe').length == 0) {
				jQuery('body').css('overflow', 'hidden')
				.append("\
				<div id='pincframe'>\
					<div id='pincframebg'><p>Loading...</p></div>\
					<div id='pincheader'><p id='pincclose'>X</p><p id='pinclogo'>" + pincsite + "</p></div>\
					<div id='pincimages'></div>\
					<div id='pinchidden'><img id='pincph'/></div>\
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
						#pinchidden { visibility: none; }\
					</style>\
				</div>");

				jQuery('#pincframebg').fadeIn(200);

				var imgarr = [];
				var videoflag = '0';
				var documentURL = document.URL;

				if (documentURL.indexOf("youtube.com/watch") != -1 || documentURL.indexOf("vimeo.com") != -1) {
					-1==documentURL.indexOf("youtube.com/watch")||$('[id*="oneframeb"]').length?documentURL.match(/vimeo.com\/(\d+)($|\/)/)&&!$('[id*="oneframeb"]').length?(video_id=documentURL.split("/")[3],jQuery.getJSON("https://vimeo.com/api/oembed.json?url=" + documentURL,{format:"json"},function(a){imgsrc=a.thumbnail_url,imgarr.unshift([imgsrc,640,360]),videoflag="1",display_thumbnails(imgarr,videoflag)})):-1==documentURL.indexOf("xvideos.com/video")||$('[id*="oneframeb"]').length?documentURL.match(/redtube.com\/(\d+)($|\/)/)&&!$('[id*="oneframeb"]').length?(imgsrc=jQuery('meta[property="og:image"]').attr("content").replace("m.jpg","i.jpg"),imgarr.unshift([imgsrc,582,388]),videoflag="1",display_thumbnails(imgarr,videoflag)):-1==documentURL.indexOf("hardsextube.com/video/")||$('[id*="oneframeb"]').length?-1==documentURL.indexOf("youporn.com/watch/")||$('[id*="oneframeb"]').length?(jQuery("img").each(function(){var a=jQuery(this).prop("src"),b=this.naturalWidth;b||(b=jQuery(this).width());var c=this.naturalHeight;c||(c=jQuery(this).height()),a&&b>=125&&c>=125&&imgarr.unshift([a,b,c])}),jQuery("body, div, span").each(function(){var a=jQuery(this).css("background-image");if("none"!=a){regex=/(?:\(['|"]?)(.*?)(?:['|"]?\))/,imgsrc=regex.exec(a)[1];var b=this.naturalWidth;b||(b=jQuery(this).width());var c=this.naturalHeight;c||(c=jQuery(this).height()),imgsrc&&b>=250&&c>=250&&imgarr.unshift([imgsrc,b,c])}}),display_thumbnails(imgarr,videoflag)):(imgsrc=jQuery("#galleria img:eq(7)").attr("src"),imgarr.unshift([imgsrc,720,576]),videoflag="1",display_thumbnails(imgarr,videoflag)):(imgsrc=jQuery('link[rel="image_src"]').attr("href"),imgarr.unshift([imgsrc,1920,1080]),videoflag="1",display_thumbnails(imgarr,videoflag)):(imgsrc=jQuery("#tabVote > img").attr("src"),$('#pincph').attr('src', imgsrc),imgarr.unshift([imgsrc,$('#pincph').width(),$('#pincph').height()]),videoflag="1",display_thumbnails(imgarr,videoflag)):(video_id=document.URL.match("[\\?&]v=([^&#]*)"),imgsrc="http://img.youtube.com/vi/"+video_id[1]+"/0.jpg",imgarr.unshift([imgsrc,480,360]),videoflag="1",display_thumbnails(imgarr,videoflag),jQuery("#movie_player").css("visibility","hidden"));
				} else {
					scraper
						.init()
						.fetch();
				}
			}

			jQuery('#pincheader').on('click', '#pincclose', function() {
				if (documentURL.indexOf('youtube.com/watch') != -1) {
					jQuery('#movie_player').css('visibility','visible');
				}
				jQuery('body').css('overflow', 'visible');
				jQuery('#pincframe').fadeOut(200, function() {
					jQuery(this).remove();
				});
			});

			jQuery('#pincimages').on('click', '.pincimgwrapper', function() {
				window.open(jQuery(this).data('href'), 'pincwindow', 'width=400,height=760,left=0,top=0,resizable=1,scrollbars=1');
				if (documentURL.indexOf('youtube.com/watch') != -1) {
					jQuery('#movie_player').css('visibility','visible');
				}
				jQuery('body').css('overflow', 'visible');
				jQuery('#pincframe').remove();
			});

			jQuery('#pincimages').on('mouseover', '.pincimgwrapper', function() {
				jQuery(this).find('.pincbutton').show();
			}).on('mouseout', '.pincimgwrapper', function() {
				jQuery(this).find('.pincbutton').hide();
			});

			jQuery(document).keyup(function(e) {
				if (e.keyCode == 27) {
				if (documentURL.indexOf('youtube.com/watch') != -1) {
					jQuery('#movie_player').css('visibility','visible');
				}
				jQuery('body').css('overflow', 'visible');
				jQuery('#pincframe').fadeOut(200, function() {
					jQuery(this).remove();
				});
				}
			});
		})();
	}

	function display_thumbnails(imgarr, videoflag) {
		if (!imgarr.length) {
			jQuery('#pincframebg').html('<p>Sorry, unable to find anything to save on this page.</p>');
		} else if (document.URL.match(/(gif|png|jpg|jpeg)$/i)) {
			jQuery('#pincimages').hide();
			jQuery('#pincframebg').html('<p>For direct jpg/gif/png url,<br />please fetch image at<br /><a href="' + pincsiteurl + '/itm-settings/">Add > Pin > From Web</a></p>');
		} else {
			imgarr.sort(function(a,b)
			{
				if (a[1] == b[1]) return 0;
				return a[1] > b[1] ? -1 : 1;
			});

			var imgstr = '';
			for (var i = 0; i < imgarr.length; i++) {
				if (typeof imgarr[i][0] == 'undefined') {
					continue;
				}

				page_title = document.getElementsByTagName('title')[0].innerHTML;

				if (typeof imgarr[i][3] != 'undefined' && imgarr[i][3].length != 0) {
					page_title = imgarr[i][3];
				}

				video_id = '';

				if (typeof imgarr[i][4] != 'undefined') {
					video_id = imgarr[i][4];
				}

				if (videoflag == '0') {
					imgstr += '<div class="pincimgwrapper" data-href="'
					+ pincsiteurl + 'itm-settings/?m=bm&imgsrc='
					+ encodeURIComponent(imgarr[i][0].replace('http', ''))
					+ '&source=' + encodeURIComponent(document.URL.replace('http', ''))
					+ '&title=' + encodeURIComponent(page_title)
					+ '&video=' + videoflag + '&video_id=' + video_id
					+ '"><div class="pincbutton">+</div><div class="pincdimension">'
					+ parseInt(imgarr[i][1],10) + ' x '
					+ parseInt(imgarr[i][2],10) + '</div><img src="' + imgarr[i][0]
					+ '" /></div>';
				} else {
					imgstr += '<div class="pincimgwrapper" data-href="' + pincsiteurl
					+ 'itm-settings/?m=bm&imgsrc='
					+ encodeURIComponent(imgarr[i][0].replace('http', ''))
					+ '&source=' + encodeURIComponent(document.URL.replace('http', ''))
					+ '&title=' + encodeURIComponent(page_title)
					+ '&video=' + videoflag + '&video_id=' + video_id
					+ '"><div class="pincbutton">+</div><div class="pincdimension"> Video </div><img src="'
					+ imgarr[i][0] + '" /></div>';
				}
			}
			jQuery('#pincframebg p').fadeOut(200);
			jQuery('#pincimages').css('height',jQuery(window).height()-jQuery('#pincheader').height()-20)
								.html(imgstr + '<div style="height:40px;clear:both;"><br /></div>');
			if ((navigator.appVersion.indexOf('Chrome/') != -1 || navigator.appVersion.indexOf('Safari/')) && videoflag != '1') {
				jQuery('#pincimages .pincimgwrapper').css('float','left');
			}
		}
	}
})();

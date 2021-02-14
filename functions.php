<?php 
/*
    Our website:  http://qasedakgp.ir
    By Mohammad Anbarestany
    Qasedak Group - 2017-2019
*/

// View count Func
function getPostViews($postID){
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
        return "0 View";
    }
    return $count;
}
function setPostViews($postID) {
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        $count = 0;
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
    }else{
        $count++;
        update_post_meta($postID, $count_key, $count);
    }
}
// Remove issues with prefetching adding extra views
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);

// Main Page Tag system
function wpb_tag_cloud() { 
    wp_tag_cloud( array( 'smallest' => '13' ,'largest' => '13', 'unit' => 'px', 'number' => '5', 'separator' => '، ', 'orderby' => 'count', 'order' => 'DESC') );
    } 
    /* use post id to change the image of the day */
    if ( function_exists( 'add_theme_support' ) ) {
        add_theme_support( 'post-thumbnails' );
        add_image_size( 'daily-thumbs', 2000 );
        //download size options
        add_image_size( 'small-size-dl', 640 );
        add_image_size( 'medium-size-dl', 1920 );
        add_image_size( 'large-size-dl', 2400 );
    }
    function dailyImage($id = 0) {
        if($id == "rnd"){
    
            $argss = array( 
                'orderby' => 'rand',
                'posts_per_page' => '1'
            );
            $loop = new WP_Query( $argss );
            while ( $loop->have_posts() ) : $loop->the_post();
                $rndId = get_the_ID();
            endwhile;
            $url_image = wp_get_attachment_image_src(get_post_thumbnail_id($rndId), 'daily-thumbs')[0];
    
        }else{
            $url_image = wp_get_attachment_image_src(get_post_thumbnail_id($id), 'daily-thumbs')[0];
        }
        return $url_image;
    }
    function author_name($post_ID)  {
    $auth = get_post($post_ID); // gets author from post
    $authid = $auth->post_author; // gets author id for the post
    $user_nicename = get_the_author_meta('display_name',$authid);
    return $user_nicename;
    }
    function author_url($post_ID)  {
    $auth = get_post($post_ID); // gets author from post
    $authid = $auth->post_author; // gets author id for the post
    $user_nickname = get_the_author_meta('nickname',$authid); // retrieve user nickname
    return $user_nickname;
    }
    /* hide wp meta gen */
    function _remove_script_version( $src ){ 
    $parts = explode( '?', $src ); 	
    return $parts[0]; 
    } 
    add_filter( 'script_loader_src', '_remove_script_version', 15, 1 ); 
    add_filter( 'style_loader_src', '_remove_script_version', 15, 1 );
    // Register color cat Core Custom Taxonomy
    function color() {
        $labels = array(
            'name'                       => __('Color schemes','pinc'),
            'singular_name'              => __('Color scheme','pinc'),
            'menu_name'                  => __('Color scheme','pinc'),
            'all_items'                  => __('all colors','pinc'),
            'add_new_item'               => __('add new color','pinc'),
        );
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => false,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
        );
        register_taxonomy( 'color', array( 'post' ), $args );
    }
    add_action( 'init', 'color', 0 );
// color cat form - side post
function color_cat_formSP($thePostId) {
    $current_user = wp_get_current_user();
    echo '
    <style>
        .SPcolor-theme {
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }
        .SPcolor-theme .SPcolor-container {
            text-align: center;
            overflow: hidden;
            background-color: #5d5d5d;
            border-radius: 3px;
            padding: 0px 4px 4px 4px;
        }
        .SPcolor-theme .SPcolor-container .color {
            display: inline-block;
            vertical-align: middle;
            text-decoration: none;
            width: 34px;
            height: 23px;
            margin-left: 2px;
            margin-top: 4px;
            background-color: #333;
            -webkit-transition: all 0.3s;
            -o-transition: all 0.3s;
            transition: all 0.3s;
            text-align: center;
        }
    </style>';
    // color taxonomy name
    $tax = 'color';
    // get the terms of taxonomy
    $terms = get_terms( $tax, $args = array(
    'hide_empty' => 0, // do not hide empty terms
    'parent'   => 0 //only top-level terms will be returned
    ));
    echo '<div class="SPcolor-theme"><div class="SPcolor-container"><form action="#" method="post">';
    // loop through all terms
    foreach( $terms as $term ) {
        // get the term id and slug of each
        $term_opt = get_term( $term, $tax );
        $termId = $term->term_id;
        $termSlug = $term->slug;
        echo '<label class="color" style="background-color:' . $termSlug . ';"><input type="checkbox" name="checklist[]" value="' . $termId . '"></label>';
    }
    echo '<input class="btn btn-success btn-sm edit-board" type="submit" name="submit" value="'. __('Submit Colors','pinc') .'"/>
        </form>
        </div>
        </div>';
    if(isset($_POST['submit'])){//to run PHP script on submit
        if(!empty($_POST['checklist'])){
            $selectArr = array();
            // Loop to store and display values of individual checked checkbox.
            foreach($_POST['checklist'] as $selected){
                $colorId = (int)$selected;
                $selectArr[] = $colorId;
                wp_set_object_terms( $thePostId, $selectArr, 'color');
            }
            echo "<p style='text-align:center'>". __('Successfully submitted.','pinc') ."</p>";
            }else{
                echo "<p style='text-align:center'>". __('Sorry something wrong happend!','pinc') ."</p>";
        }
    }
}
if( !function_exists("optionsframework_init") ) 
{
    require_once(get_template_directory() . "/inc/options-framework.php");
}

if( !isset($content_width) ) 
{
    $content_width = 700;
}

add_action("after_setup_theme", "pinc_after_setup_theme");
add_action("widgets_init", "pinc_widgets_init");
add_action("wp_head", "pinc_head");
add_filter("post_class", "pinc_post_class");
add_filter("query_vars", "pinc_query_vars");
add_filter("rewrite_rules_array", "pinc_rewrite_rules_array");
add_action("wp", "pinc_wp", 0);
if( function_exists("wpseo_init") ) 
{
function pinc_wpseo_canonical($canonical)
{
    if( is_page_template("page_source.php") ) 
    {
        return false;
    }

    return $canonical;
}

    add_filter("wpseo_canonical", "pinc_wpseo_canonical");
}

add_filter("wp_title", "pinc_wp_title", 10, 2);
if( !current_user_can("administrator") && !current_user_can("editor") ) 
{
    add_filter("xmlrpc_enabled", "__return_false");
}

add_action("admin_init", "pinc_admin_init", 1);
add_action("login_init", "pinc_login_init", 1);
add_filter("login_url", "pinc_login_url", 10, 2);
add_action("wp_login_failed", "pinc_login_failed");
add_action("wp_ajax_nopriv_pinc-ajax-login", "pinc_ajax_login");
add_filter("wp_authenticate_user", "pinc_wp_authenticate_user", 1);
add_filter("authenticate", "pinc_authenticate", 20, 3);
add_action("user_register", "pinc_user_register");
if( function_exists("wsl_activate") ) 
{
function pinc_wsl_hook_process_login_after_wp_insert_user($user_id, $provider, $hybridauth_user_profile)
{
    wp_update_user(array( "ID" => $user_id, "user_url" => "" ));
}

    add_action("wsl_hook_process_login_after_wp_insert_user", "pinc_wsl_hook_process_login_after_wp_insert_user", 10, 3);
}

if( function_exists("wsl_activate") ) 
{
function pinc_wsl_render_auth_widget_alter_assets_base_url($url)
{
    return get_template_directory_uri() . "/img/social/";
}

    add_filter("wsl_render_auth_widget_alter_assets_base_url", "pinc_wsl_render_auth_widget_alter_assets_base_url");
}

add_action("wp_login", "pinc_wp_login", 10, 2);
if( function_exists("wsl_activate") ) 
{
function pinc_wsl_hook_process_login_before_wp_safe_redirect($user_id, $provider, $hybridauth_profile)
{
    $board_parent_id = get_user_meta($user_id, "_Board Parent ID", true);
    if( $board_parent_id == "" ) 
    {
        $board_id = wp_insert_term($user_id, "board");
        update_user_meta($user_id, "_Board Parent ID", $board_id["term_id"]);
    }

    if( get_user_meta($user_id, "pinc_user_avatar", true) == "" && get_user_meta($user_id, "pinc_user_avatar", true) != "deleted" && ($imgsrc = get_user_meta($user_id, "wsl_current_user_image", true)) ) 
    {
        if( strpos($imgsrc, "_normal") !== false ) 
        {
            $imgsrc = str_replace("_normal", "", $imgsrc);
        }

        if( function_exists("curl_init") ) 
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $imgsrc);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $image = curl_exec($ch);
            curl_close($ch);
        }
        else
        {
            if( ini_get("allow_url_fopen") ) 
            {
                $image = file_get_contents($imgsrc, false, $context);
            }

        }

        if( !$image ) 
        {
            $error = "error";
        }

        $filename = time() . substr(str_shuffle("pincl02468"), 0, 5);
        $file_array["tmp_name"] = WP_CONTENT_DIR . "/" . $filename . ".tmp";
        $filetmp = file_put_contents($file_array["tmp_name"], $image);
        if( !$filetmp ) 
        {
            @unlink($file_array["tmp_name"]);
            $error = "error";
        }

        if( !$error ) 
        {
            require_once(ABSPATH . "wp-admin/includes/image.php");
            require_once(ABSPATH . "wp-admin/includes/file.php");
            require_once(ABSPATH . "wp-admin/includes/media.php");
            $imageTypes = array( 1, 2, 3 );
            $imageinfo = getimagesize($file_array["tmp_name"]);
            $width = $imageinfo[0];
            $height = $imageinfo[1];
            $type = $imageinfo[2];
            $mime = $imageinfo["mime"];
            if( !in_array($type, $imageTypes) ) 
            {
                @unlink($file_array["tmp_name"]);
                $error = "error";
            }

            if( $width <= 1 && $height <= 1 ) 
            {
                @unlink($file_array["tmp_name"]);
                $error = "error";
            }

            if( $mime != "image/gif" && $mime != "image/jpeg" && $mime != "image/png" ) 
            {
                @unlink($file_array["tmp_name"]);
                $error = "error";
            }

            switch( $type ) 
            {
                case 1:
                    $ext = ".gif";
                    break;
                case 2:
                    $ext = ".jpg";
                    break;
                case 3:
                    $ext = ".png";
                    break;
            }
            $file_array["name"] = "avatar-" . $filename . $ext;
            add_image_size("avatar48", 48, 48, true);
            $attach_id = media_handle_sideload($file_array, "none", "", array( "post_author" => $user_id, "post_title" => "Avatar for UserID " . $user_id ));
            if( is_wp_error($attach_id) ) 
            {
                @unlink($file_array["tmp_name"]);
                $error = "error";
            }

        }

        if( $error != "error" ) 
        {
            update_user_meta($user_id, "pinc_user_avatar", $attach_id);
            $settings_page = get_page_by_path("settings");
            global $wpdb;
            $wpdb->query("\n\t\t\t\tUPDATE " . $wpdb->posts . "\n\t\t\t\tSET post_parent = " . $settings_page->ID . "\n\t\t\t\tWHERE ID = " . $attach_id . "\n\t\t\t\t");
        }

    }

}

    add_action("wsl_hook_process_login_before_wp_safe_redirect", "pinc_wsl_hook_process_login_before_wp_safe_redirect", 10, 3);
}

add_filter("the_password_form", "pinc_password_form");
add_action("pre_get_posts", "pinc_pre_get_posts");
if( !function_exists("pinc_meta_value_num_orderby") ) 
{
function pinc_meta_value_num_orderby($orderby)
{
    global $wpdb;
    if( stripos($orderby, "desc") !== false ) 
    {
        $order = " DESC";
    }
    else
    {
        $order = " ASC";
    }

    return " " . $wpdb->postmeta . ".meta_value+0 " . $order . ", " . $wpdb->posts . ".ID DESC";
}

}

if( !function_exists("pinc_comments_orderby") ) 
{
function pinc_comments_orderby($orderby)
{
    global $wpdb;
    return " " . $wpdb->posts . ".comment_count+0 DESC, " . $wpdb->posts . ".ID DESC";
}

}

add_action("wp_ajax_pinc-edit-comment", "pinc_edit_comment");
add_action("wp_ajax_pinc-edit-comment-submit", "pinc_edit_comment_submit");
add_action("pre_user_query", "pinc_pre_user_query");
add_filter("user_search_columns", "pinc_user_search_columns", 10, 3);
if( !function_exists("pinc_top_user_by_followers") ) 
{
function pinc_top_user_by_followers($user_id)
{
    $user_id = (int) $user_id;
    $args = array( "order" => "desc", "orderby" => "meta_value", "meta_key" => "_Followers Count", "meta_query" => array( array( "key" => "_Followers Count", "compare" => ">", "value" => "0", "type" => "numeric" ) ), "number" => "20", "fields" => "ID" );
    $top_user_follower_query = new WP_User_Query($args);
    $most_followers_pos = array_search($user_id, $top_user_follower_query->results);
    if( $most_followers_pos !== false ) 
    {
        return $most_followers_pos + 1;
    }

    return false;
}

}

if( !function_exists("pinc_top_user_by_pins") ) 
{
function pinc_top_user_by_pins($user_id)
{
    $user_id = (int) $user_id;
    $blog_cat_id = of_get_option("blog_cat_id");
    if( $blog_cat_id ) 
    {
        global $wpdb;
        $blog_post_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM " . $wpdb->posts . "\n\t\t\t\tLEFT JOIN " . $wpdb->term_relationships . " ON(" . $wpdb->posts . ".ID = " . $wpdb->term_relationships . ".object_id)\n\t\t\t\tLEFT JOIN " . $wpdb->term_taxonomy . " ON(" . $wpdb->term_relationships . ".term_taxonomy_id = " . $wpdb->term_taxonomy . ".term_taxonomy_id)\n\t\t\t\tWHERE " . $wpdb->term_taxonomy . ".term_id = %d\n\t\t\t\tAND " . $wpdb->term_taxonomy . ".taxonomy = 'category'\n\t\t\t\tAND " . $wpdb->posts . ".post_status = 'publish'\n\t\t\t\tAND post_author = %d\n\t\t\t\t", $blog_cat_id, $user_id));
    }

    $pins_count = count_user_posts($user_id) - $blog_post_count;
    $args = array( "order" => "desc", "orderby" => "post_count", "number" => "20" );
    $top_user_postcount_query = new WP_User_Query($args);
    $top_user_postcount_array = array(  );
    foreach( $top_user_postcount_query->results as $top_user_postcount ) 
    {
        array_push($top_user_postcount_array, $top_user_postcount->ID);
    }
    $most_pins_pos = array_search($user_id, $top_user_postcount_array);
    if( $most_pins_pos !== false && 0 < $pins_count ) 
    {
        return $most_pins_pos + 1;
    }

    return false;
}

}

/* Color and fonts Style Generator */
function font_mime($mime_types){
    $mime_types['woff'] = 'application/x-font-woff';
    return $mime_types;
}
add_filter('upload_mimes', 'font_mime', 1, 1);
function colorGen() {
    $aColorOne = of_get_option('accent_color_one');
    $hFont = of_get_option('heading_font_family');
    $bFont = of_get_option('body_font_family');
    echo":root {
        --accent-color-one: $aColorOne;
    }";
    if($hFont != "") echo"
    @font-face {
        font-family: 'headings-font';
        font-style: normal;
        font-weight: 300;
        src: url('$hFont') format('woff');
    }";
    if($bFont != "") echo"
    @font-face {
        font-family: 'Body-font';
        font-style: normal;
        font-weight: 300;
        src: url('$bFont') format('woff');
    }";
}

if( !function_exists("pinc_blog_cats") ) 
{
function pinc_blog_cats()
{
    $blog_cat_id = of_get_option("blog_cat_id");
    $blog_cats = array(  );
    if( $blog_cat_id ) 
    {
        $blog_cats = array( $blog_cat_id );
        if( get_option("pinc_blog_subcats") ) 
        {
            $blog_cats = array_merge($blog_cats, get_option("pinc_blog_subcats"));
        }

    }

    return $blog_cats;
}

}

add_action("created_term", "pinc_blog_subcats", 10, 3);
add_action("delete_term", "pinc_blog_subcats", 10, 3);
add_action("init", "pinc_init", 0);
add_filter("term_link", "pinc_term_link", 10, 3);
add_action("parse_query", "pinc_parse_query");
add_action("parse_request", "pinc_parse_request");
add_action("wp_enqueue_scripts", "pinc_enqueue_scripts");
add_filter("style_loader_src", "pinc_style_loader_src");
add_filter("wp_get_attachment_url", "fix_ssl_attachment_url");

class Roots_Nav_Walker extends Walker_Nav_Menu
{
    public function check_current($classes)
    {
        return preg_match("/(current[-_])|active|dropdown/", $classes);
    }

    public function start_lvl(&$output, $depth = 0, $args = array(  ))
    {
        if(is_rtl()){
            $output .= "\n<ul class=\"dropdown-menu pull-right\">\n";
        }else{
            $output .= "\n<ul class=\"dropdown-menu\">\n";
        } 
    }

    public function start_el(&$output, $item, $depth = 0, $args = array(  ), $id = 0)
    {
        $item_html = "";
        parent::start_el($item_html, $item, $depth, $args);
        if( $item->is_dropdown && $depth === 0 ) 
        {
            $item_html = str_replace("<a", "<a class=\"dropdown-toggle\" data-toggle=\"dropdown\" data-target=\"#\"", $item_html);
            $item_html = str_replace("</a>", " <b class=\"caret\"></b></a>", $item_html);
        }
        else
        {
            if( stristr($item_html, "li class=\"divider") ) 
            {
                $item_html = preg_replace("/<a[^>]*>.*?<\\/a>/iU", "", $item_html);
            }
            else
            {
                if( stristr($item_html, "li class=\"dropdown-header") ) 
                {
                    $item_html = preg_replace("/<a[^>]*>(.*)<\\/a>/iU", "\$1", $item_html);
                }

            }

        }

        $item_html = apply_filters("roots/wp_nav_menu_item", $item_html);
        $output .= $item_html;
    }

    public function display_element($element, &$children_elements, $max_depth, $depth = 0, $args, &$output)
    {
        $element->is_dropdown = !empty($children_elements[$element->ID]) && ($depth + 1 < $max_depth || $max_depth === 0);
        if( $element->is_dropdown ) 
        {
            $element->classes[] = "dropdown";
        }

        parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
    }

}

add_filter("nav_menu_css_class", "roots_nav_menu_css_class", 10, 2);
add_filter("nav_menu_item_id", "__return_null");
add_filter("wp_nav_menu_args", "roots_nav_menu_args");
if( !function_exists("pinc_human_time_diff") ) 
{
function pinc_human_time_diff($from, $to = "")
{
    if( empty($to) ) 
    {
        $to = time();
    }

    $diff = (int) abs($to - $from);
    if( $diff <= 3600 ) 
    {
        $mins = round($diff / 60);
        if( $mins <= 1 ) 
        {
            $mins = 1;
        }

        if( $mins == 1 ) 
        {
            $since = sprintf(__("%s min ago", "pinc"), $mins);
        }
        else
        {
            $since = sprintf(__("%s mins ago", "pinc"), $mins);
        }

    }
    else
    {
        if( $diff <= 86400 && 3600 < $diff ) 
        {
            $hours = round($diff / 3600);
            if( $hours <= 1 ) 
            {
                $hours = 1;
            }

            if( $hours == 1 ) 
            {
                $since = sprintf(__("%s hour ago", "pinc"), $hours);
            }
            else
            {
                $since = sprintf(__("%s hours ago", "pinc"), $hours);
            }

        }
        else
        {
            if( 86400 <= $diff && $diff <= 31536000 ) 
            {
                $days = round($diff / 86400);
                if( $days <= 1 ) 
                {
                    $days = 1;
                }

                if( $days == 1 ) 
                {
                    $since = sprintf(__("%s day ago", "pinc"), $days);
                }
                else
                {
                    $since = sprintf(__("%s days ago", "pinc"), $days);
                }

            }
            else
            {
                $since = get_the_date();
            }

        }

    }

    return $since;
}

}

add_filter("the_excerpt_rss", "pinc_feed_content");
add_filter("the_content_feed", "pinc_feed_content");
if( !function_exists("pinc_nofollow_callback") ) 
{
function pinc_nofollow_callback($matches)
{
    $link = $matches[0];
    $exclude = "(" . home_url() . ")";
    if( preg_match("#href=\\S(" . $exclude . ")#i", $link) ) 
    {
        return $link;
    }

    if( strpos($link, "rel=") === false ) 
    {
        $link = preg_replace("/(?<=<a\\s)/", "rel=\"nofollow\" ", $link);
    }
    else
    {
        if( preg_match("#rel=\\S(?!nofollow)#i", $link) ) 
        {
            $link = preg_replace("#(?<=rel=.)#", "nofollow ", $link);
        }

    }

    return $link;
}

}

if( !function_exists("pinc_list_comments") ) 
{
function pinc_list_comments($comment, $args, $depth)
{
    global $wp_rewrite;
    $GLOBALS["comment"] = $comment;
    echo "\t<li ";
    comment_class();
    echo " id=\"comment-";
    comment_ID();
    echo "\">\n\n\t\t";
    $comment_author = get_user_by("id", $comment->user_id);
    echo "\t\t<div class=\"comment-avatar\">\n\t\t\t";
    if( $comment_author ) 
    {
        echo "\t\t\t<a href=\"";
        echo home_url("/" . $wp_rewrite->author_base . "/") . $comment_author->user_nicename;
        echo "/\">\n\t\t\t";
    }

    echo "\t\t\t\t";
    echo get_avatar($comment->user_id, "48");
    echo "\t\t\t";
    if( $comment_author ) 
    {
        echo "\t\t\t</a>\n\t\t\t";
    }
    if(is_rtl()){
        echo "\t\t</div>\n\t\t<div class=\"pull-left";
    }else{
        echo "\t\t</div>\n\t\t<div class=\"pull-right";
    }
    if( !is_user_logged_in() ) 
    {
        echo " hider";
    }

    echo "\">\n\t\t\t";
    comment_reply_link(array( "reply_text" => __("Reply", "pinc"), "login_text" => __("Reply", "pinc"), "depth" => $depth, "max_depth" => $args["max_depth"] ));
    echo "\t\t\t";
    if( $comment->user_id == get_current_user_id() ) 
    {
        echo "\t\t\t\t<a href=\"\" class=\"comment-edit-link\" comment-id=\"";
        echo comment_ID();
        echo "\">";
        echo __("Edit");
        echo "</a>\n\t\t\t";
    }

    echo "\t\t</div>\n\n\n\t\t<div class=\"comment-content\">\n\n\t\t\t<strong><span ";
    comment_class();
    echo ">\n\t\t\t";
    if( $comment_author ) 
    {
        echo "\t\t\t<a class=\"url\" href=\"";
        echo home_url("/" . $wp_rewrite->author_base . "/") . $comment_author->user_nicename;
        echo "/\">\n\t\t\t";
    }

    echo "\t\t\t\t";
    echo $comment->comment_author;
    echo "\t\t\t";
    if( $comment_author ) 
    {
        echo "\t\t\t</a>\n\t\t\t";
    }

    echo "\t\t\t</span></strong>\n\t\t\t<span class=\"text-muted\">&#8226; ";
    echo pinc_human_time_diff(mysql2date("U", get_gmt_from_date(get_comment_date("Y-m-d H:i:s"))));
    echo "</span> <a href=\"#comment-";
    comment_ID();
    echo "\" title=\"";
    esc_attr_e("Comment Permalink", "pinc");
    echo "\">#</a> ";
    edit_comment_link("e", "", "");
    echo "\t\t\t";
    if( $comment->comment_approved == "0" ) 
    {
        echo "\t\t\t<br /><em>";
        _e("Your comment is awaiting moderation.", "pinc");
        echo "</em>\n\t\t\t";
    }

    echo "\n\t\t\t";
    comment_text();
    echo "\t\t</div>\n\t";
}

}

if( !function_exists("pinc_get_post_price") ) 
{
function pinc_get_post_price($show_symbol = true, $post_id = NULL)
{
    if( empty($post_id) && isset($GLOBALS["post"]) ) 
    {
        global $post;
        $post_id = $post->ID;
    }

    $post_id = (int) $post_id;
    $post_price = get_post_meta($post_id, "_Price", true);
    if( $post_price != "" && $show_symbol != false ) 
    {
        if( of_get_option("price_currency_position") == "left" ) 
        {
            $post_price = of_get_option("price_currency") . $post_price;
        }
        else
        {
            $post_price = $post_price . of_get_option("price_currency");
        }

    }

    return apply_filters("pinc_get_post_price", $post_price);
}

}

if( !function_exists("pinc_get_post_board") ) 
{
function pinc_get_post_board($post_id = NULL)
{
    if( empty($post_id) && isset($GLOBALS["post"]) ) 
    {
        global $post;
        $post_id = $post->ID;
    }

    $post_id = (int) $post_id;
    $boards = get_the_terms($post_id, "board");
    $board = "";
    if( $boards ) 
    {
        foreach( $boards as $board ) 
        {
            $board = $board;
        }
    }

    return $board;
}

}

if( !function_exists("pinc_get_post_video") ) 
{
function pinc_get_post_video($url = "")
{
    if( $url == "" ) 
    {
        global $post;
        $url = get_post_meta($post->ID, "_Photo Source", true);
    }

    $embed_code = "";
    if( preg_match("%(?:youtube(?:-nocookie)?\\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\\.be/)([^\"&?/ ]{11})%i", str_replace("&#038;", "&", $url), $videomatch) ) 
    {
        if( strpos($url, "list=") !== false ) 
        {
            parse_str(html_entity_decode($url), $youtube_query);
            $embed_code = "<iframe id=\"video-embed\" src=\"//www.youtube.com/embed/" . $videomatch[1] . "?list=" . $youtube_query["list"] . "&rel=0&autoplay=1&wmode=opaque\" width=\"700\" height=\"393\" frameborder=\"0\" allowfullscreen></iframe>";
        }
        else
        {
            $embed_code = "<iframe id=\"video-embed\" src=\"//www.youtube.com/embed/" . $videomatch[1] . "?rel=0&autoplay=1&wmode=opaque\" width=\"700\" height=\"393\" frameborder=\"0\" allowfullscreen></iframe>";
        }

    }
    else
    {
        if( strpos($url, "youtube.com/playlist?list=") !== false && sscanf(parse_url($url, PHP_URL_QUERY), "list=%s", $video_id) ) 
        {
            $embed_code = "<iframe id=\"video-embed\" src=\"//www.youtube.com/embed/videoseries?list=" . $video_id . "&rel=0&autoplay=1&wmode=opaque\" width=\"700\" height=\"393\" frameborder=\"0\" allowfullscreen></iframe>";
        }
        else
        {
            if( strpos(parse_url($url, PHP_URL_HOST), "vimeo.com") !== false && sscanf(parse_url($url, PHP_URL_PATH), "/%d", $video_id) ) 
            {
                $embed_code = "<iframe id=\"video-embed\" src=\"//player.vimeo.com/video/" . $video_id . "?title=0&amp;byline=0&amp;portrait=0&amp;badge=0&amp;color=ffffff&amp;autoplay=1\" width=\"700\" height=\"393\" webkitAllowFullScreen mozallowfullscreen allowFullScreen style=\"border:none;\"></iframe>";
            }
            else
            {
                if( strpos(parse_url($url, PHP_URL_HOST), "soundcloud.com") !== false ) 
                {
                    $embed_code = wp_oembed_get($url, array( "width" => 700, "height" => 393 ));
                    if( $embed_code ) 
                    {
                        $embed_code = str_replace("<iframe", "<iframe id=\"video-embed\"", $embed_code);
                    }

                    $embed_code = str_replace("\"></iframe>", "&amp;auto_play=true&amp;hide_related=true\"></iframe>", $embed_code);
                }
                else
                {
                    $embed_code = apply_filters("pinc_get_post_video", $embed_code, $url);
                }

            }

        }

    }

    return $embed_code;
}

}

add_action("wp_ajax_pinc-repin", "pinc_repin");
add_action("wp_ajax_pinc-repin-board-populate", "pinc_repin_board_populate");

add_action("wp_ajax_pinc-like", "pinc_like");
if( !function_exists("pinc_liked") ) 
{
function pinc_liked($post_id)
{
    global $user_ID;
    $postmeta_user_id = get_post_meta($post_id, "_Likes User ID");
    $likes_user_id = $postmeta_user_id[0];
    if( !is_array($likes_user_id) ) 
    {
        $likes_user_id = array(  );
    }

    if( in_array($user_ID, $likes_user_id) ) 
    {
        return true;
    }

    return false;
}

}

add_action("wp_ajax_pinc-follow", "pinc_follow");
if( !function_exists("pinc_followed") ) 
{
function pinc_followed($board_id)
{
    global $user_ID;
    $usermeta_board_id = get_user_meta($user_ID, "_Following Board ID");
    $follow_board_id = $usermeta_board_id[0];
    if( !is_array($follow_board_id) ) 
    {
        $follow_board_id = array(  );
    }

    if( in_array($board_id, $follow_board_id) ) 
    {
        return true;
    }

    return false;
}

}

add_action("comment_post", "pinc_ajaxify_comments", 20, 2);
add_action("save_post", "pinc_save_post", 50, 2);
add_action("before_delete_post", "pinc_before_delete_post");
add_action("wp_ajax_pinc-delete-account", "pinc_delete_account");
add_action("delete_user", "pinc_delete_user");
add_action("deleted_user", "pinc_deleted_user", 10, 2);
add_filter("post_types_to_delete_with_user", "pinc_post_types_to_delete_with_user");
add_filter("cron_schedules", "pinc_cron_schedules");
if( !wp_next_scheduled("pinc_cron_action") ) 
{
    wp_schedule_event(time(), "pinc_prune", "pinc_cron_action");
}

add_action("pinc_cron_action", "pinc_cron_function");
add_action("created_term", "pinc_created_term", 10, 3);
add_filter("wp_mail_from", "pinc_mail_from");
add_filter("wp_mail_from_name", "pinc_mail_from_name");
add_filter("get_avatar", "pinc_get_avatar", 10, 5);
add_action("wp_ajax_pinc-upload-avatar", "pinc_upload_avatar");
add_action("wp_ajax_pinc-delete-avatar", "pinc_delete_avatar");
add_action("wp_ajax_pinc-upload-cover", "pinc_upload_cover");
add_action("wp_ajax_pinc-delete-cover", "pinc_delete_cover");
add_action("wp_ajax_pinc-add-board", "pinc_add_board");
add_action("wp_ajax_pinc-delete-board", "pinc_delete_board");
add_action("wp_ajax_pinc-upload-pin", "pinc_upload_pin");
add_filter("sanitize_file_name", "pinc_sanitize_file_name", 1, 2);
add_action("wp_ajax_pinc-postdata", "pinc_postdata");
add_action("wp_ajax_pinc-pin-edit", "pinc_edit");
if( !function_exists("pinc_wp_editor") ) 
{
function pinc_wp_editor($editor_id, $post_content = "")
{
    $settings = array( "textarea_rows" => 2, "media_buttons" => false, "quicktags" => true, "tinymce" => array( "toolbar1" => "bold, italic", "toolbar2" => "", "plugins" => "wplink", "content_css" => get_stylesheet_directory_uri() . "/editor-style-frontend.css" ) );
    ob_start();
    wp_editor($post_content, $editor_id, $settings);
    $editor_contents = ob_get_clean();
    $editor_contents .= "<div class=\"placeholder_description\">" . __("Description", "pinc") . "</div>";
    return $editor_contents;
}

}

add_action("wp_ajax_pinc-replace-image", "pinc_replace_image");
add_action("wp_ajax_pinc-delete-pin", "pinc_delete_pin");
if( !function_exists("pinc_dropdown_categories") ) 
{
function pinc_dropdown_categories($show_option_none, $name, $selected = "")
{
    if( of_get_option("blog_cat_id") ) 
    {
        return apply_filters("pinc_dropdown_categories", wp_dropdown_categories(array( "hierarchical" => true, "show_option_none" => $show_option_none, "exclude_tree" => of_get_option("blog_cat_id") . ",1", "hide_empty" => 0, "name" => $name, "orderby" => "name", "selected" => $selected, "echo" => 0, "class" => "form-control" )));
    }

    return apply_filters("pinc_dropdown_categories", wp_dropdown_categories(array( "hierarchical" => true, "show_option_none" => $show_option_none, "exclude" => "1", "hide_empty" => 0, "name" => $name, "orderby" => "name", "selected" => $selected, "echo" => 0, "class" => "form-control" )));
}

}

if( !function_exists("pinc_dropdown_boards") ) 
{
function pinc_dropdown_boards($user_id = NULL, $selected = "")
{
    if( !$user_id ) 
    {
        $user_id = get_current_user_id();
    }

    $board_parent_id = get_user_meta($user_id, "_Board Parent ID", true);
    $board_children_count = wp_count_terms("board", array( "parent" => $board_parent_id ));
    if( is_array($board_children_count) || $board_children_count == 0 ) 
    {
        return apply_filters("pinc_dropdown_boards", "<span id=\"noboard\">" . wp_dropdown_categories(array( "taxonomy" => "board", "parent" => $board_parent_id, "hide_empty" => 0, "name" => "board", "hierarchical" => true, "echo" => 0, "selected" => $selected, "show_option_none" => __("Add a new board first...", "pinc"), "class" => "form-control" )) . "</span>");
    }

    return apply_filters("pinc_dropdown_boards", wp_dropdown_categories(array( "taxonomy" => "board", "parent" => $board_parent_id, "hide_empty" => 0, "name" => "board", "hierarchical" => true, "orderby" => "name", "order" => "ASC", "echo" => 0, "selected" => $selected, "class" => "form-control" )));
}

}

add_action("wp_ajax_pinc-post-email", "pinc_post_email");
add_action("wp_ajax_pinc-post-report", "pinc_post_report");
add_action("wp_ajax_nopriv_pinc-post-report", "pinc_post_report");
add_action("edit_user_profile", "pinc_edit_user_profile");
add_action("edit_user_profile_update", "pinc_edit_user_profile_update");
add_action("admin_init", "pinc_setup");
if( EMPTY_TRASH_DAYS != 0 ) 
{
    add_action("admin_notices", "pinc_admin_notices");
}

add_action("admin_menu", "pinc_setup_guide");
add_filter("request", "remove_page_from_query_string");
add_filter("password_reset_key_expired", "change_password");
function pinc_after_setup_theme()
{
    load_theme_textdomain("pinc", get_template_directory() . "/languages");
    register_nav_menus(array( "top_nav" => "Top Navigation" ));
    add_theme_support("automatic-feed-links");
    add_theme_support("post-thumbnails");
    add_theme_support("custom-background", array( "default-color" => "f2f2f2" ));
    add_editor_style();
    show_admin_bar(false);
    remove_action("wp_head", "adjacent_posts_rel_link_wp_head", 10, 0);
    remove_action("wp_head", "wp_generator");
    remove_action("wp_head", "wp_shortlink_wp_head", 10, 0);
    add_image_size("board-main-image", 338, 200, array( "center", "center" ));
}

function pinc_widgets_init()
{
    register_sidebar(array( "id" => "sidebar-r-t", "name" => "Right Sidebar for Single Pins Only (Above Boards)", "before_widget" => "<div class=\"sidebar-wrapper\"><div class=\"sidebar-inner\">", "after_widget" => "</div></div>", "before_title" => "<h4>", "after_title" => "</h4>" ));
    register_sidebar(array( "id" => "sidebar-r", "name" => "Right Sidebar for Single Pins Only (Below Boards)", "before_widget" => "<div class=\"sidebar-wrapper\"><div class=\"sidebar-inner\">", "after_widget" => "</div></div>", "before_title" => "<h4>", "after_title" => "</h4>" ));
    register_sidebar(array( "id" => "sidebar-others", "name" => "Right Sidebar for Other Pages & Sideblog", "before_widget" => "<div class=\"sidebar-wrapper\"><div class=\"sidebar-inner\">", "after_widget" => "</div></div>", "before_title" => "<h4>", "after_title" => "</h4>" ));
}

function pinc_head()
{
    if( is_single() ) 
    {
        global $post;
        setup_postdata($post);
        $output = "<meta property=\"og:type\" content=\"article\" />" . "\n";
        $output .= "<meta property=\"og:title\" content=\"" . preg_replace("/[\\n\\r]/", " ", mb_strimwidth(the_title_attribute("echo=0"), 0, 255, " ...")) . "\" />" . "\n";
        $output .= "<meta property=\"og:url\" content=\"" . get_permalink() . "\" />" . "\n";
        if( $post->post_content == "" ) 
        {
            $meta_categories = get_the_category($post->ID);
            foreach( $meta_categories as $meta_category ) 
            {
                $meta_category_name = $meta_category->name;
            }
            if( pinc_get_post_board() ) 
            {
                $meta_board_name = pinc_get_post_board()->name;
            }
            else
            {
                $meta_board_name = __("Untitled", pinc);
            }

            $output .= "<meta property=\"og:description\" content=\"" . esc_attr(__("Pinned onto", "pinc") . " " . $meta_board_name . __("Board in", "pinc") . " " . $meta_category_name . " " . __("Category", "pinc")) . "\" />" . "\n";
        }
        else
        {
            $output .= "<meta property=\"og:description\" content=\"" . esc_attr(get_the_excerpt()) . "\" />" . "\n";
        }

        if( has_post_thumbnail() ) 
        {
            $imgsrc = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), "full");
            $output .= "<meta property=\"og:image\" content=\"" . $imgsrc[0] . "\" />" . "\n";
        }

        if( get_option("wsl_settings_Facebook_app_id") ) 
        {
            $output .= "<meta property=\"fb:app_id\" content=\"" . get_option("wsl_settings_Facebook_app_id") . "\" />" . "\n";
        }

        echo $output;
    }

    if( is_tax("board") ) 
    {
        global $post;
        global $wp_query;
        global $wp_taxonomies;
        setup_postdata($post);
        $output = "<meta property=\"og:type\" content=\"article\" />" . "\n";
        $output .= "<meta property=\"og:title\" content=\"" . esc_attr($wp_query->queried_object->name) . "\" />" . "\n";
        $output .= "<meta property=\"og:url\" content=\"" . home_url("/" . $wp_taxonomies["board"]->rewrite["slug"] . "/" . sanitize_title($wp_query->queried_object->name, "_") . "/") . $wp_query->queried_object->term_id . "/\" />" . "\n";
        $output .= "<meta property=\"og:description\" content=\"\" />" . "\n";
        if( has_post_thumbnail() ) 
        {
            $imgsrc = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), "full");
            $output .= "<meta property=\"og:image\" content=\"" . $imgsrc[0] . "\" />" . "\n";
        }

        echo $output;
    }

    if( is_author() ) 
    {
        global $wp_query;
        global $wp_rewrite;
        $user_info = get_user_by("id", $wp_query->query_vars["author"]);
        $output = "<meta property=\"og:type\" content=\"article\" />" . "\n";
        $output .= "<meta property=\"og:title\" content=\"" . esc_attr($user_info->display_name) . " (" . $user_info->user_nicename . ")\" />" . "\n";
        $output .= "<meta property=\"og:url\" content=\"" . home_url("/") . $wp_rewrite->author_base . "/" . $user_info->user_nicename . "/\" />" . "\n";
        $output .= "<meta property=\"og:description\" content=\"" . esc_attr($user_info->description) . "\" />" . "\n";
        $avatar_id = get_user_meta($user_info->ID, "pinc_user_avatar", true);
        if( $avatar_id != "" && $avatar_id != "deleted" ) 
        {
            $user_avatar = wp_get_attachment_image_src($avatar_id, "full");
            $output .= "<meta property=\"og:image\" content=\"" . $user_avatar[0] . "\" />" . "\n";
        }

        echo $output;
    }

}

function pinc_post_class($classes)
{
    $classes = array_diff($classes, array( "hentry" ));
    return $classes;
}

function pinc_query_vars($aVars)
{
    $aVars[] = "domain";
    $aVars[] = "sort";
    $aVars[] = "minprice";
    $aVars[] = "maxprice";
    return $aVars;
}

function pinc_rewrite_rules_array($aRules)
{
    $aNewRules = array( "source/([^/]+)/?\$" => "index.php?pagename=source&domain=\$matches[1]" );
    $aRules = $aNewRules + $aRules;
    return $aRules;
}

function pinc_wp()
{
    if( is_page("source") ) 
    {
        remove_action("wp_head", "rel_canonical");
    }

}

function pinc_wp_title($title, $sep)
{
    if( is_tax("board") ) 
    {
        global $post;
        $user_info = get_user_by("id", $post->post_author);
        return str_replace(" Boards", "", $title) . " " . __("Board by", "pinc") . " " . $user_info->display_name;
    }

    if( is_page("source") ) 
    {
        global $wp_query;
        return __("Pins from", "pinc") . " " . $wp_query->query_vars["domain"] . str_replace("Source ", " ", $title);
    }

    if( is_single() && 70 < mb_strlen($title) ) 
    {
        $title = mb_strimwidth($title, 0, 70, " ...");
    }

    if( is_author() ) 
    {
        global $wp_query;
        $title = $title . "(" . $wp_query->queried_object->data->user_nicename . ")";
    }

    if( is_tag() ) 
    {
        $title = __("Tag:", "pinc") . " " . $title;
    }

    if( is_category() ) 
    {
        $title = __("Category:", "pinc") . " " . $title;
    }

    if( is_search() ) 
    {
        return __("Search results for", "pinc") . " " . get_search_query();
    }

    return $title;
}

function pinc_admin_init()
{
    if( (!defined("DOING_AJAX") || !DOING_AJAX) && !current_user_can("administrator") && !current_user_can("editor") ) 
    {
        wp_redirect(home_url());
        exit();
    }

}

function pinc_login_init()
{
    if( !isset($_REQUEST) || empty($_REQUEST) || $_GET["action"] == "register" ) 
    {
        wp_redirect(home_url());
        exit();
    }

}

function pinc_login_url($login_url, $redirect)
{
    $login_url = home_url("/login/");
    if( !empty($redirect) ) 
    {
        $duplicate_redirect = substr_count($redirect, "redirect_to");
        if( 1 <= $duplicate_redirect ) 
        {
            $redirect = substr($redirect, 0, strrpos($redirect, "?"));
        }

        $login_url = add_query_arg("redirect_to", rawurlencode($redirect), $login_url);
    }
    else
    {
        $login_url = add_query_arg("redirect_to", rawurlencode(home_url("/")), $login_url);
    }

    return $login_url;
}

function pinc_login_failed($username)
{
    $referrer = $_SERVER["HTTP_REFERER"];
    if( $referrer == home_url() . "/login/" ) 
    {
        $referrer = $referrer . "?redirect_to=" . home_url();
    }

    if( !empty($referrer) && !strstr($referrer, "wp-login") && !strstr($referrer, "wp-admin") && (!defined("DOING_AJAX") || !DOING_AJAX) ) 
    {
        $userdata = get_user_by("login", $username);
        $verify = get_user_meta($userdata->ID, "_Verify Email", true);
        if( $verify != "" ) 
        {
            $verify = "&email=unverified";
        }

        if( strpos($referrer, "&login=failed") ) 
        {
            wp_safe_redirect($referrer . $verify);
        }
        else
        {
            wp_safe_redirect($referrer . $verify . "&login=failed");
        }

        exit();
    }

}

function pinc_ajax_login()
{
    $nonce = $_POST["nonce"];
    if( !wp_verify_nonce($nonce, "ajax-nonce") ) 
    {
        exit();
    }

    if( is_email($_POST["log"]) ) 
    {
        $user = get_user_by_email($_POST["log"]);
        if( $user ) 
        {
            $_POST["log"] = $user->user_login;
        }

    }

    $valid_user = wp_authenticate(sanitize_text_field($_POST["log"]), sanitize_text_field($_POST["pwd"]));
    if( is_wp_error($valid_user) ) 
    {
        echo "error";
    }
    else
    {
        wp_set_auth_cookie($valid_user->ID, true);
    }

    exit();
}

function pinc_wp_authenticate_user($userdata)
{
    $verify = get_user_meta($userdata->ID, "_Verify Email", true);
    if( $verify != "" ) 
    {
        return new WP_Error("email_unverified", __("Email not verified. Please check your email for verification link.", "pinc"));
    }

    if( $_POST["formname"] == "pinc_loginform" && of_get_option("captcha_public") != "" && of_get_option("captcha_private") != "" ) 
    {
        require_once(get_template_directory() . "/recaptchalib.php");
        $privatekey = of_get_option("captcha_private");
        $reCaptcha = new ReCaptcha($privatekey);
        if( $_POST["g-recaptcha-response"] ) 
        {
            $resp = $reCaptcha->verifyResponse($_SERVER["REMOTE_ADDR"], $_POST["g-recaptcha-response"]);
        }

        if( !$resp->success ) 
        {
            return new WP_Error("incorrect_captcha", __("<strong>ERROR</strong>: Incorrect Captcha.", "pinc"));
        }

    }

    return $userdata;
}

function pinc_authenticate($user, $username, $password)
{
    if( is_email($username) ) 
    {
        $user = get_user_by_email($username);
        if( $user ) 
        {
            $username = $user->user_login;
        }

        return wp_authenticate_username_password(NULL, $username, $password);
    }

    return $user;
}

function pinc_user_register($user_id)
{
    $user_info = get_userdata($user_id);
    $board_id = wp_insert_term($user_id, "board");
    update_user_meta($user_id, "_Board Parent ID", $board_id["term_id"]);
    if( of_get_option("auto_create_boards_name") ) 
    {
        $boards_name = explode(",", of_get_option("auto_create_boards_name"));
        $category_id = explode(",", of_get_option("auto_create_boards_cat"));
        $count = 0;
        foreach( $boards_name as $board_name ) 
        {
            $board_name = sanitize_text_field($board_name);
            wp_insert_term($board_name, "board", array( "description" => sanitize_text_field($category_id[$count]), "parent" => $board_id["term_id"], "slug" => $board_name . "__pincboard" ));
            $count++;
        }
        delete_option("board_children");
    }

    if( stripos($user_info->user_email, "@example.com") === false ) 
    {
        update_user_meta($user_id, "pinc_user_notify_likes", "1");
        update_user_meta($user_id, "pinc_user_notify_repins", "1");
        update_user_meta($user_id, "pinc_user_notify_follows", "1");
        update_user_meta($user_id, "pinc_user_notify_comments", "1");
    }
    else
    {
        update_user_meta($user_id, "pinc_user_notify_likes", "0");
        update_user_meta($user_id, "pinc_user_notify_repins", "0");
        update_user_meta($user_id, "pinc_user_notify_follows", "0");
        update_user_meta($user_id, "pinc_user_notify_comments", "0");
    }

}

function pinc_wp_login($user_login, $user)
{
    $board_parent_id = get_user_meta($user->ID, "_Board Parent ID", true);
    if( $board_parent_id == "" ) 
    {
        $board_id = wp_insert_term($user->ID, "board");
        update_user_meta($user->ID, "_Board Parent ID", $board_id["term_id"]);
    }

}

function pinc_password_form($output)
{
    $post = get_post($post);
    $label = "pwbox-" . ((empty($post->ID) ? rand() : $post->ID));
    $output = "<form action=\"" . esc_url(site_url("wp-login.php?action=postpass", "login_post")) . "\" class=\"post-password-form\" method=\"post\">\n\t<p>" . __("This content is password protected. To view it please enter your password below:", "pinc") . "</p>" . "<div class=\"form-group\">" . "<input class=\"form-control\" type=\"password\" name=\"post_password\" id=\"" . $label . "\" value=\"\" />" . "</div>" . "<input class=\"btn btn-success btn-pinc-custom\" type=\"submit\" name=\"Submit\" value=\"" . esc_attr__("Submit", "pinc") . "\" />" . "</form>\n\t";
    return $output;
}

function pinc_pre_get_posts($query)
{
    if( !is_admin() ) 
    {
        if( of_get_option("blog_cat_id") && !$query->is_category(pinc_blog_cats()) && !is_feed() ) 
        {
            $query->set("cat", "-" . implode(" -", pinc_blog_cats()));
        }

        if( $query->is_search && is_main_query() ) 
        {
            $query->set("post_type", "post");
        }

        if( $query->is_author ) 
        {
            $query->set("post_status", array( "publish", "pending" ));
        }

    }

    return $query;
}

function pinc_edit_comment()
{
    $nonce = $_POST["nonce"];
    if( !wp_verify_nonce($nonce, "ajax-nonce") ) 
    {
        exit();
    }

    global $user_ID;
    $comment_id = $_POST["comment_id"];
    $comment = get_comment($comment_id);
    if( $user_ID != $comment->user_id ) 
    {
        exit();
    }

    $output = "\n\t<div class=\"modal\">\n\t\t<div class=\"modal-dialog\">\n\t\t\t<div class=\"modal-content\">\n\t\t\t\t<div class=\"modal-header\">\n\t\t\t\t\t<h4>" . __("Edit Comment") . "</h4>\n\t\t\t\t</div>\n\t\t\t\t<div class=\"modal-body text-right\">\n\t\t\t\t\t<form id=\"edit-comment-form\">\n\t\t\t\t\t\t<input type=\"hidden\" id=\"comment-id\" value=\"" . $comment_id . "\"/>\n\t\t\t\t\t\t<textarea class=\"form-control\" id=\"comment-content\" placeholder=\"" . __("Comment") . "\">" . $comment->comment_content . "</textarea>\n\t\t\t\t\t\t<br/>\n\t\t\t\t\t\t<div>\n\t\t\t\t\t\t\t<a href=\"#\" class=\"btn btn-default\" data-dismiss=\"modal\"><strong>" . __("Cancel") . "</strong></a>\n\t\t\t\t\t\t\t<input class=\"btn btn-success\" type=\"submit\" name=\"save-comment\" id=\"save-comment\" value=\"" . __("Save") . "\">\n\t\t\t\t\t\t\t<div class=\"ajax-loader-delete-pin ajax-loader hide\"></div>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</form>\n\t\t\t\t</div>\n\t\t\t</div>\n\t\t</div>\n\t</div>\n\t";
    echo $output;
    exit();
}

function pinc_edit_comment_submit()
{
    $nonce = $_POST["nonce"];
    if( !wp_verify_nonce($nonce, "ajax-nonce") ) 
    {
        exit();
    }

    global $user_ID;
    $comment_id = $_POST["comment_id"];
    $content = $_POST["content"];
    $comment = get_comment($comment_id);
    if( $user_ID != $comment->user_id ) 
    {
        exit();
    }

    $comment = array(  );
    $comment["comment_ID"] = $comment_id;
    $comment["comment_content"] = $content;
    wp_update_comment($comment);
    return 1;
}

function pinc_pre_user_query($query)
{
    $meta_key = $query->get("meta_key");
    $orderby = $query->get("orderby");
    if( $meta_key == "_Followers Count" && $orderby == "meta_value" ) 
    {
        global $wpdb;
        $query->query_orderby = " ORDER BY " . $wpdb->usermeta . ".meta_value+0 " . $query->get("order");
    }

}

function pinc_user_search_columns($search_columns, $search, $obj)
{
    if( !in_array("display_name", $search_columns) ) 
    {
        $search_columns[] = "display_name";
    }

    return $search_columns;
}

function pinc_blog_subcats($term_id, $tt_id, $taxonomy)
{
    if( $taxonomy == "category" ) 
    {
        $blog_cat_id = of_get_option("blog_cat_id");
        if( $blog_cat_id ) 
        {
            $blog_subcategories = get_categories("hide_empty=0&child_of=" . $blog_cat_id);
            $blog_subcats = array(  );
            foreach( $blog_subcategories as $blog_subcategory ) 
            {
                array_push($blog_subcats, $blog_subcategory->cat_ID);
            }
            if( !empty($blog_subcats) ) 
            {
                update_option("pinc_blog_subcats", $blog_subcats);
            }
            else
            {
                update_option("pinc_blog_subcats", "");
            }

        }

    }

}

function pinc_init()
{
    register_taxonomy("board", "post", array( "hierarchical" => true, "public" => true, "labels" => array( "name" => "Boards", "singular_name" => "Board", "search_items" => "Search Boards", "all_items" => "All Boards", "parent_item" => "Parent Board", "parent_item_colon" => "Parent Board:", "edit_item" => "Edit Board", "update_item" => "Update Board", "add_new_item" => "Add New Board", "new_item_name" => "New Board Name", "menu_name" => "Boards" ), "rewrite" => array( "slug" => "board", "with_front" => false, "hierarchical" => true ) ));
    global $wp_rewrite;
    $wp_rewrite->author_base = "user";
    $wp_rewrite->author_structure = "/" . $wp_rewrite->author_base . "/%author%/";
}

function pinc_term_link($termlink, $term, $taxonomy)
{
    global $wp_taxonomies;
    if( $taxonomy == "board" ) 
    {
        return home_url("/" . $wp_taxonomies["board"]->rewrite["slug"] . "/" . sanitize_title($term->name, "_") . "/") . $term->term_id . "/";
    }

    return $termlink;
}

function pinc_parse_query($query)
{
    if( isset($query->query_vars["board"]) && ($board = get_term_by("id", $query->query_vars["board"], "board")) ) 
    {
        $query->query_vars["board"] = $board->slug;
    }

}

function pinc_parse_request($wp)
{
    if( isset($wp->query_vars["board"]) ) 
    {
        preg_match("/board\\/(.*?)\\/[0-9]/", $_SERVER["REQUEST_URI"], $match);
        if( empty($match) ) 
        {
            global $wp_taxonomies;
            $board_info = get_term_by("id", $wp->query_vars["board"], "board");
            $link = home_url("/" . $wp_taxonomies["board"]->rewrite["slug"] . "/" . sanitize_title($board_info->name, "_") . "/" . $board_info->term_id . "/");
            wp_redirect($link, 301);
            exit();
        }

    }

}

function pinc_enqueue_scripts()
{
    wp_enqueue_style("pinc-bootstrap", get_template_directory_uri() . "/css/bootstrap.css");
    wp_enqueue_style("pinc-fontawesome", "//use.fontawesome.com/releases/v5.5.0/css/all.css" );
    wp_enqueue_style("pinc-style", get_stylesheet_directory_uri() . "/style.css", array( "pinc-bootstrap" ));
    wp_enqueue_style("wp-mediaelement");
    wp_enqueue_script("wp-mediaelement");
     /* Auto Color by macse */
     if(of_get_option("theme_color_mod") == 2){
        global $logo;
        global $colorScheme;
        global $user_ID;
        $user_info = get_userdata($user_ID);
        if(!empty($user_info->pinc_user_timezone)){
            $timeZoneSet = $user_info->pinc_user_timezone;
        }else if (!empty(get_option('timezone_string'))){
            $timeZoneSet = get_option('timezone_string');
        }else {
            $timeZoneSet = "Etc/Greenwich";
        }
        $getUserDate = new DateTime(null, new DateTimeZone($timeZoneSet));
        $currentTime = $getUserDate->format('H:i:s');
        if ($currentTime > of_get_option("night_start") || $currentTime < of_get_option("night_end")) {
            $colorScheme = 'dark';
            $logo = of_get_option('logo_night');
        }else{
            $colorScheme = 'light';
            $logo = of_get_option('logo');
        }
    }elseif(of_get_option("theme_color_mod") == 0){
        global $logo;
        global $colorScheme;
        $colorScheme = 'light';
        $logo = of_get_option('logo');
    }else{
        global $logo;
        global $colorScheme;
        $colorScheme = 'dark';
        $logo = of_get_option('logo_night');
    }
    if( $colorScheme == "dark" ) 
    {
        wp_enqueue_style("pinc-style-dark", get_template_directory_uri() . "/style-dark.css", array( "pinc-style" ));
    }
    global $current_user;
    global $wp_rewrite;
    get_currentuserinfo();
    if( is_singular() && comments_open() && get_option("thread_comments") && is_user_logged_in() ) 
    {
        wp_enqueue_script("comment-reply");
    }

    if( is_page_template("page_cp_pins.php") ) 
    {
        wp_enqueue_script("suggest");
    }

    wp_enqueue_script("pinc_library", get_template_directory_uri() . "/js/pinc.library.js", array( "jquery" ), NULL, true);
    wp_enqueue_script("pinc_custom", get_template_directory_uri() . "/js/pinc.custom.js", array( "jquery" ), NULL, true);
    
    if( function_exists("wp_pagenavi") ) 
    {
        $nextSelector = "#navigation a:nth-child(3)";
    }
    else
    {
        $nextSelector = "#navigation #navigation-next a";
    }

    $tags_html = "";
    $price_html = "";
    $minWidth = 2;
    $minHeight = 2;
    $minWidth = apply_filters("pinc_minwidth", $minWidth);
    $minHeight = apply_filters("pinc_minheight", $minHeight);
    if( is_user_logged_in() && !is_page_template("page_cp_boards.php") && !is_page_template("page_cp_boards.php") && !is_page_template("page_cp_login.php") && !is_page_template("page_cp_login_lpw.php") && !is_page_template("page_cp_notifications.php") && !is_page_template("page_cp_pins.php") && !is_page_template("page_cp_register.php") && !is_page_template("page_cp_settings.php") && !is_page_template("page_top_users.php") && !is_404() ) 
    {
        if( of_get_option("form_title_desc") != "separate" ) 
        {
            if( of_get_option("htmltags") == "enable" ) 
            {
                $description_fields = pinc_wp_editor("pin-title");
            }
            else
            {
                $description_fields = "<textarea class=\"form-control\" id=\"pin-title\" placeholder=\"" . __("Describe your pin...", "pinc") . "\"></textarea>";
            }

        }
        else
        {
            if( of_get_option("htmltags") == "enable" ) 
            {
                $description_fields = "<textarea class=\"form-control\" id=\"pin-title\" placeholder=\"" . __("Title...", "pinc") . "\"></textarea><p></p>" . ($description_fields = pinc_wp_editor("pin-content"));
            }
            else
            {
                $description_fields = "<textarea class=\"form-control\" id=\"pin-title\" placeholder=\"" . __("Title...", "pinc") . "\"></textarea><p></p><textarea id=\"pin-content\" class=\"form-control\" placeholder=\"" . __("Description...", "pinc") . "\"></textarea>";
            }

        }

        if( of_get_option("posttags") == "enable" ) 
        {
            $tags_html = "<div class=\"input-group\"><span class=\"input-group-addon\"><i class=\"fas fa-tags\"></i></span><input class=\"form-control\" type=\"text\" name=\"tags\" id=\"tags\" value=\"\" placeholder=\"" . __("Tags e.g. comma, separated", "pinc") . "\" /></div>";
        }

        if( of_get_option("price_currency") != "" ) 
        {
            if( of_get_option("price_currency_position") == "right" ) 
            {
                $price_html = "<div class=\"input-group\"><input class=\"form-control text-right\" type=\"text\" name=\"price\" id=\"price\" value=\"\" placeholder=\"" . __("Price e.g. 23.45", "pinc") . "\" /><span class=\"input-group-addon\">" . of_get_option("price_currency") . "</span></div>";
            }
            else
            {
                $price_html = "<div class=\"input-group\"><span class=\"input-group-addon\">" . of_get_option("price_currency") . "</span><input class=\"form-control\" type=\"text\" name=\"price\" id=\"price\" value=\"\" placeholder=\"" . __("Price e.g. 23.45", "pinc") . "\" /></div>";
            }

        }

        $dropdown_categories = pinc_dropdown_categories(__("Category for New Board", "pinc"), "board-add-new-category");
    }
    else
    {
        $description_fields = "";
        $tags_html = "";
        $price_html = "";
        $dropdown_categories = "";
    }

    $translation_array = array( "__postcommenttimenow" => __("Now", "pinc"), "__allitemsloaded" => __("All items loaded", "pinc"), "__addanotherpin" => __("Add Another Pin", "pinc"), "__addnewboard" => __("Add new board...", "pinc"), "__boardalreadyexists" => __("Board already exists. Please try another title.", "pinc"), "__errorpleasetryagain" => __("Error. Please try again.", "pinc"), "__cancel" => __("Cancel", "pinc"), "__close" => __("Close", "pinc"), "__comment" => __("comment", "pinc"), "__comments" => __("comments", "pinc"), "__enternewboardtitle" => __("Enter new board title", "pinc"), "__Follow" => __("Follow", "pinc"), "__FollowBoard" => __("Follow Board", "pinc"), "__Forgot" => __("Forgot?", "pinc"), "__imagetoosmall" => sprintf(__("Image is too small (min size: %d x %dpx)", "pinc"), $minWidth, $minHeight), "__incorrectusernamepassword" => __("Incorrect Username/Password", "pinc"), "__mixedmimetypes" => __("Mixing video and image files is not allowed.", "pinc"), "__multipelvideos" => __("Multiple video upload is not allowed.", "pinc"), "__invalidimagefile" => __("Invalid media file. Please choose a JPG/GIF/PNG/MP4/OGG/WEBM file.", "pinc"), "__Likes" => __("Likes", "pinc"), "__loading" => __("Loading...", "pinc"), "__Login" => __("Login", "pinc"), "__NotificationsLatest30" => __("Notifications (Latest 30)", "pinc"), "__onto" => __("onto", "pinc"), "__Pleasecreateanewboard" => __("Please create a new board", "pinc"), "__Pleaseentertitle" => __("Please enter title", "pinc"), "__Pleaseloginorregisterhere" => __("Please login or register here", "pinc"), "__Pleasetypeacomment" => __("Please type a comment", "pinc"), "__or" => __("or", "pinc"), "__Password" => __("Password", "pinc"), "__pinnedto" => __("Pinned to", "pinc"), "__pleaseenterbothusernameandpassword" => __("Please enter both username and password.", "pinc"), "__pleaseenterurl" => __("Please enter url", "pinc"), "__Repin" => __("Repin", "pinc"), "__Repins" => __("Repins", "pinc"), "__repinnedto" => __("Repinned to", "pinc"), "__seethispin" => __("See This Pin", "pinc"), "__SeeAll" => __("See All", "pinc"), "__shareitwithyourfriends" => __("Share it with your friends", "pinc"), "__SignUp" => __("Sign Up", "pinc"), "__sorryunbaletofindanypinnableitems" => __("Sorry, unable to find any pinnable items.", "pinc"), "__Unfollow" => __("Unfollow", "pinc"), "__UnfollowBoard" => __("Unfollow Board", "pinc"), "__Username" => __("Username or Email", "pinc"), "__Video" => __("Video", "pinc"), "__Welcome" => __("Welcome", "pinc"), "__yourpinispendingreview" => __("Your pin is pending review", "pinc"), "__LoginForm" => __("</span></a>", "pinc"), "ajaxurl" => admin_url("admin-ajax.php"), "avatar30" => get_avatar($current_user->ID, "30"), "avatar48" => get_avatar($current_user->ID, "48"), "blogname" => get_bloginfo("name"), "categories" => $dropdown_categories, "current_date" => date("j M Y g:ia", current_time("timestamp")), "description_fields" => $description_fields, "home_url" => home_url(), "infinitescroll" => of_get_option("infinitescroll"), "lightbox" => of_get_option("lightbox"), "login_url" => wp_login_url($_SERVER["REQUEST_URI"]), "nextselector" => $nextSelector, "nonce" => wp_create_nonce("ajax-nonce"), "price_html" => $price_html, "site_url" => site_url(), "stylesheet_directory_uri" => get_template_directory_uri(), "stylesheet_directory_uri_child" => get_stylesheet_directory_uri(), "tags_html" => $tags_html, "u" => $current_user->ID, "ui" => $current_user->display_name, "ul" => $current_user->user_nicename, "user_rewrite" => $wp_rewrite->author_base );
    wp_localize_script("pinc_custom", "obj_pinc", $translation_array);
}

function pinc_style_loader_src($src)
{
    global $wp_version;
    $version_str = "?ver=" . $wp_version;
    $version_str_offset = strlen($src) - strlen($version_str);
    if( substr($src, $version_str_offset) == $version_str ) 
    {
        return substr($src, 0, $version_str_offset);
    }

    return $src;
}

function fix_ssl_attachment_url($url)
{
    if( is_ssl() ) 
    {
        $url = str_replace("http://", "https://", $url);
    }

    return $url;
}

/**
 * From Roots Theme http://roots.io
 * Cleaner walker for wp_nav_menu()
 *
 * Walker_Nav_Menu (WordPress default) example output:
 *   <li id="menu-item-8" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-8"><a href="/">Home</a></li>
 *   <li id="menu-item-9" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-9"><a href="/sample-page/">Sample Page</a></l
 *
 * Roots_Nav_Walker example output:
 *   <li class="menu-home"><a href="/">Home</a></li>
 *   <li class="menu-sample-page"><a href="/sample-page/">Sample Page</a></li>
 */

function is_element_empty($element)
{
    $element = trim($element);
    return !empty($element);
}

/**
 * Remove the id="" on nav menu items
 * Return 'menu-slug' for nav menu classes
 */

function roots_nav_menu_css_class($classes, $item)
{
    $slug = sanitize_title($item->title);
    $classes = preg_replace("/(current(-menu-|[-_]page[-_])(item|parent|ancestor))/", "active", $classes);
    $classes = preg_replace("/^((menu|page)[-_\\w+]+)+/", "", $classes);
    $classes[] = "menu-" . $slug;
    $classes = array_unique($classes);
    return array_filter($classes, "is_element_empty");
}

/**
 * Clean up wp_nav_menu_args
 *
 * Remove the container
 * Use Roots_Nav_Walker() by default
 */

function roots_nav_menu_args($args = "")
{
    $roots_nav_menu_args["container"] = false;
    if( !$args["items_wrap"] ) 
    {
        $roots_nav_menu_args["items_wrap"] = "<ul class=\"%2\$s\">%3\$s</ul>";
    }

    if( !$args["depth"] ) 
    {
        $roots_nav_menu_args["depth"] = 2;
    }

    if( !$args["walker"] ) 
    {
        $roots_nav_menu_args["walker"] = new Roots_Nav_Walker();
    }

    return array_merge($args, $roots_nav_menu_args);
}

function big_number_format($number)
{
    if( 5000 < $number & $number < 1000000 ) 
    {
        $number = number_format($number / 1000, 1) . "K";
    }
    else
    {
        if( 1000000 <= $number ) 
        {
            $number = number_format($number / 1000000, 2) . "M";
        }

    }

    return $number;
}

function pinc_feed_content($content)
{
    global $post;
    $imgsrc = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), "medium");
    if( $imgsrc[0] != "" ) 
    {
        $content_before = "<p><a href=\"" . get_permalink($post->ID) . "\"><img src=\"" . $imgsrc[0] . "\" alt=\"\" /></a></p>";
    }

    if( pinc_get_post_board() ) 
    {
        global $wp_taxonomies;
        $board_link = home_url("/" . $wp_taxonomies["board"]->rewrite["slug"] . "/" . sanitize_title(pinc_get_post_board()->name, "_") . "/" . pinc_get_post_board()->term_id . "/");
        $content_before .= "<p>" . __("Pinned onto", "pinc") . " <a href=\"" . $board_link . "\">" . pinc_get_post_board()->name . "</a></p>";
    }

    return $content_before . $content;
}

function pinc_repin()
{
    $nonce = $_POST["nonce"];
    if( !wp_verify_nonce($nonce, "ajax-nonce") ) 
    {
        exit();
    }

    do_action("pinc_before_repin", $_POST);
    global $wpdb;
    global $user_ID;
    global $user_identity;
    $original_id = $_POST["repin_post_id"];
    $duplicate = get_post($original_id, "ARRAY_A");
    $original_post_author = $duplicate["post_author"];
    $duplicate["post_author"] = $user_ID;
    $allowed_html = array( "a" => array( "href" => true ), "em" => array(  ), "blockquote" => array(  ), "p" => array(  ), "li" => array(  ), "ol" => array(  ), "strong" => array(  ), "ul" => array(  ) );
    if( of_get_option("htmltags") != "enable" ) 
    {
        unset($allowed_html);
        $allowed_html = array(  );
    }

    if( of_get_option("form_title_desc") != "separate" ) 
    {
        $duplicate["post_title"] = balanceTags(wp_kses($_POST["repin_title"], $allowed_html), true);
    }
    else
    {
        $duplicate["post_title"] = sanitize_text_field($_POST["repin_title"]);
    }

    $duplicate["post_content"] = balanceTags(wp_kses($_POST["repin_content"], $allowed_html), true);
    unset($duplicate["ID"]);
    unset($duplicate["post_date"]);
    unset($duplicate["post_date_gmt"]);
    unset($duplicate["post_modified"]);
    unset($duplicate["post_modified_gmt"]);
    unset($duplicate["post_name"]);
    unset($duplicate["guid"]);
    unset($duplicate["comment_count"]);
    remove_action("save_post", "pinc_save_post", 50, 2);
    $duplicate_id = wp_insert_post($duplicate);
    $board_add_new = sanitize_text_field($_POST["repin_board_add_new"]);
    $board_add_new_category = $_POST["repin_board_add_new_category"];
    $board_parent_id = get_user_meta($user_ID, "_Board Parent ID", true);
    if( $board_add_new !== "" ) 
    {
        $board_children = get_term_children($board_parent_id, "board");
        $found = "0";
        foreach( $board_children as $board_child ) 
        {
            $board_child_term = get_term_by("id", $board_child, "board");
            if( stripslashes(htmlspecialchars($board_add_new, ENT_NOQUOTES, "UTF-8")) == $board_child_term->name ) 
            {
                $found = "1";
                $found_board_id = $board_child_term->term_id;
                break;
            }

        }
        if( $found == "0" ) 
        {
            $slug = wp_unique_term_slug($board_add_new . "__pincboard", "board");
            if( $board_add_new_category == "-1" ) 
            {
                $board_add_new_category = "1";
            }

            $new_board_id = wp_insert_term($board_add_new, "board", array( "description" => $board_add_new_category, "parent" => $board_parent_id, "slug" => $slug ));
            $repin_board = $new_board_id["term_id"];
        }
        else
        {
            $repin_board = $found_board_id;
        }

    }
    else
    {
        $repin_board = $_POST["repin_board"];
    }

    wp_set_post_terms($duplicate_id, array( $repin_board ), "board");
    update_user_meta($user_ID, "pinc_last_board", $repin_board);
    $category_id = get_term_by("id", $repin_board, "board");
    wp_set_post_terms($duplicate_id, array( $category_id->description ), "category");
    if( "" == ($repin_of_repin = get_post_meta($original_id, "_Original Post ID", true)) ) 
    {
        add_post_meta($duplicate_id, "_Original Post ID", $original_id);
    }
    else
    {
        add_post_meta($duplicate_id, "_Original Post ID", $original_id);
        add_post_meta($duplicate_id, "_Earliest Post ID", $repin_of_repin);
    }

    add_post_meta($duplicate_id, "_Photo Source", get_post_meta($original_id, "_Photo Source", true));
    add_post_meta($duplicate_id, "_Photo Source Domain", get_post_meta($original_id, "_Photo Source Domain", true));
    add_post_meta($duplicate_id, "_thumbnail_id", get_post_meta($original_id, "_thumbnail_id", true));
    wp_set_post_tags($duplicate_id, sanitize_text_field($_POST["repin_tags"]));
    if( $_POST["repin_price"] ) 
    {
        if( strpos($_POST["repin_price"], ".") !== false ) 
        {
            $_POST["repin_price"] = number_format($_POST["repin_price"], 2);
        }

        add_post_meta($duplicate_id, "_Price", sanitize_text_field($_POST["repin_price"]));
    }

    $postmeta_repin_count = get_post_meta($original_id, "_Repin Count", true);
    $postmeta_repin_post_id = get_post_meta($original_id, "_Repin Post ID");
    $repin_post_id = $postmeta_repin_post_id[0];
    if( !is_array($repin_post_id) ) 
    {
        $repin_post_id = array(  );
    }

    array_push($repin_post_id, $duplicate_id);
    update_post_meta($original_id, "_Repin Post ID", $repin_post_id);
    update_post_meta($original_id, "_Repin Count", ++$postmeta_repin_count);
    if( $user_ID != $original_post_author ) 
    {
        $wpdb->query($wpdb->prepare("INSERT INTO " . $wpdb->prefix . "pinc_notifications (user_id, notification_date, notification_type, notification_from, notification_post_id)\n\t\t\t\tVALUES (%d, %s, %s, %d, %d)\n\t\t\t\t", $original_post_author, current_time("mysql"), "repin", $user_ID, $original_id));
        $pinc_user_notifications_count = get_user_meta($original_post_author, "pinc_user_notifications_count", true);
        update_user_meta($original_post_author, "pinc_user_notifications_count", ++$pinc_user_notifications_count);
    }

    if( get_user_meta($original_post_author, "pinc_user_notify_repins", true) != "" && $user_ID != $original_post_author ) 
    {
        $blogname = wp_specialchars_decode(get_option("blogname"), ENT_QUOTES);
        $message = sprintf(__("%s repinned your \"%s\" pin at %s", "pinc"), $user_identity, preg_replace("/[\\n\\r]/", " ", html_entity_decode(sanitize_text_field(get_the_title($original_id)), ENT_QUOTES, "UTF-8")), get_permalink($duplicate_id)) . "\r\n\r\n";
        $message .= "-------------------------------------------\r\n";
        $message .= sprintf(__("To change your notification settings, visit %s", "pinc"), home_url("/settings/"));
        wp_mail(get_the_author_meta("user_email", $original_post_author), sprintf(__("[%s] Someone repinned your pin", "pinc"), $blogname), $message);
    }

    if( $new_board_id && !is_wp_error($new_board_id) ) 
    {
        $usermeta_followers_id_allboards = get_user_meta($user_ID, "_Followers User ID All Boards");
        $followers_id_allboards = $usermeta_followers_id_allboards[0];
        if( !empty($followers_id_allboards) ) 
        {
            foreach( $followers_id_allboards as $followers_id_allboard ) 
            {
                $usermeta_following_board_id = get_user_meta($followers_id_allboard, "_Following Board ID");
                $following_board_id = $usermeta_following_board_id[0];
                array_unshift($following_board_id, $new_board_id["term_id"]);
                update_user_meta($followers_id_allboard, "_Following Board ID", $following_board_id);
            }
        }

    }

    do_action("pinc_after_repin", $duplicate_id);
    echo get_permalink($duplicate_id);
    exit();
}

function pinc_repin_board_populate()
{
    global $user_ID;
    echo pinc_dropdown_boards(NULL, get_user_meta($user_ID, "pinc_last_board", true));
    exit();
}

function pinc_like()
{
    $nonce = $_POST["nonce"];
    if( !wp_verify_nonce($nonce, "ajax-nonce") ) 
    {
        exit();
    }

    do_action("pinc_before_like", $_POST);
    global $wpdb;
    global $user_ID;
    global $user_identity;
    $post_id = $_POST["post_id"];
    if( $_POST["pinc_like"] == "like" ) 
    {
        $postmeta_count = get_post_meta($post_id, "_Likes Count", true);
        $postmeta_user_id = get_post_meta($post_id, "_Likes User ID");
        $likes_user_id = $postmeta_user_id[0];
        if( !is_array($likes_user_id) ) 
        {
            $likes_user_id = array(  );
        }
        else
        {
            if( in_array($user_ID, $likes_user_id) ) 
            {
                echo $postmeta_count;
                exit();
            }

        }

        array_push($likes_user_id, $user_ID);
        update_post_meta($post_id, "_Likes User ID", $likes_user_id);
        update_post_meta($post_id, "_Likes Count", ++$postmeta_count);
        $usermeta_count = get_user_meta($user_ID, "_Likes Count", true);
        $usermeta_post_id = get_user_meta($user_ID, "_Likes Post ID");
        $likes_post_id = $usermeta_post_id[0];
        if( !is_array($likes_post_id) ) 
        {
            $likes_post_id = array(  );
        }

        array_unshift($likes_post_id, $post_id);
        update_user_meta($user_ID, "_Likes Post ID", $likes_post_id);
        update_user_meta($user_ID, "_Likes Count", ++$usermeta_count);
        $wpdb->query($wpdb->prepare("INSERT INTO " . $wpdb->prefix . "pinc_notifications (user_id, notification_date, notification_type, notification_from, notification_post_id)\n\t\t\t\tVALUES (%d, %s, %s, %d, %d)\n\t\t\t\t", $_POST["post_author"], current_time("mysql"), "like", $user_ID, $post_id));
        $pinc_user_notifications_count = get_user_meta($_POST["post_author"], "pinc_user_notifications_count", true);
        update_user_meta($_POST["post_author"], "pinc_user_notifications_count", ++$pinc_user_notifications_count);
        if( get_user_meta($_POST["post_author"], "pinc_user_notify_likes", true) != "" ) 
        {
            $blogname = wp_specialchars_decode(get_option("blogname"), ENT_QUOTES);
            $message = sprintf(__("%s liked your \"%s\" pin at %s", "pinc"), $user_identity, preg_replace("/[\\n\\r]/", " ", html_entity_decode(sanitize_text_field(get_the_title($post_id)), ENT_QUOTES, "UTF-8")), get_permalink($post_id)) . "\r\n\r\n";
            $message .= "-------------------------------------------\r\n";
            $message .= sprintf(__("To change your notification settings, visit %s", "pinc"), home_url("/settings/"));
            wp_mail(get_the_author_meta("user_email", $_POST["post_author"]), sprintf(__("[%s] Someone liked your pin", "pinc"), $blogname), $message);
        }

        echo $postmeta_count;
    }
    else
    {
        if( $_POST["pinc_like"] == "unlike" ) 
        {
            $postmeta_count = get_post_meta($post_id, "_Likes Count", true);
            $postmeta_user_id = get_post_meta($post_id, "_Likes User ID");
            $likes_user_id = $postmeta_user_id[0];
            if( !in_array($user_ID, $likes_user_id) ) 
            {
                echo $postmeta_count;
                exit();
            }

            unset($likes_user_id[array_search($user_ID, $likes_user_id)]);
            $likes_user_id = array_values($likes_user_id);
            update_post_meta($post_id, "_Likes User ID", $likes_user_id);
            update_post_meta($post_id, "_Likes Count", --$postmeta_count);
            $usermeta_count = get_user_meta($user_ID, "_Likes Count", true);
            $usermeta_post_id = get_user_meta($user_ID, "_Likes Post ID");
            $likes_post_id = $usermeta_post_id[0];
            unset($likes_post_id[array_search($post_id, $likes_post_id)]);
            $likes_post_id = array_values($likes_post_id);
            update_user_meta($user_ID, "_Likes Post ID", $likes_post_id);
            update_user_meta($user_ID, "_Likes Count", --$usermeta_count);
            echo $postmeta_count;
        }

    }

    do_action("pinc_after_like", $post_id, $likes_user_id);
    exit();
}

function pinc_follow()
{
    $nonce = $_POST["nonce"];
    if( !wp_verify_nonce($nonce, "ajax-nonce") ) 
    {
        exit();
    }

    do_action("pinc_before_follow", $_POST);
    global $wpdb;
    global $user_ID;
    global $user_identity;
    $board_parent_id = $_POST["board_parent_id"];
    $board_id = $_POST["board_id"];
    $author_id = $_POST["author_id"];
    if( $_POST["pinc_follow"] == "follow" ) 
    {
        $usermeta_following_count = get_user_meta($user_ID, "_Following Count", true);
        $usermeta_following_user_id = get_user_meta($user_ID, "_Following User ID");
        $following_user_id = $usermeta_following_user_id[0];
        $usermeta_following_board_id = get_user_meta($user_ID, "_Following Board ID");
        $following_board_id = $usermeta_following_board_id[0];
        if( !is_array($following_user_id) ) 
        {
            $following_user_id = array(  );
        }

        if( !is_array($following_board_id) ) 
        {
            $following_board_id = array(  );
        }

        if( $board_parent_id == "0" ) 
        {
            $author_boards = get_term_children($board_id, "board");
            foreach( $author_boards as $author_board ) 
            {
                if( !in_array($author_board, $following_board_id) ) 
                {
                    array_unshift($following_board_id, $author_board);
                }

            }
            $usermeta_followers_id_allboards = get_user_meta($author_id, "_Followers User ID All Boards");
            $followers_id_allboards = $usermeta_followers_id_allboards[0];
            if( !is_array($followers_id_allboards) ) 
            {
                $followers_id_allboards = array(  );
            }

            if( !in_array($user_ID, $followers_id_allboards) ) 
            {
                array_unshift($followers_id_allboards, $user_ID);
                update_user_meta($author_id, "_Followers User ID All Boards", $followers_id_allboards);
            }

        }

        array_unshift($following_board_id, $board_id);
        update_user_meta($user_ID, "_Following Board ID", $following_board_id);
        if( !in_array($author_id, $following_user_id) ) 
        {
            array_unshift($following_user_id, $author_id);
            update_user_meta($user_ID, "_Following User ID", $following_user_id);
            update_user_meta($user_ID, "_Following Count", ++$usermeta_following_count);
        }

        $usermeta_followers_count = get_user_meta($author_id, "_Followers Count", true);
        $usermeta_followers_id = get_user_meta($author_id, "_Followers User ID");
        $followers_id = $usermeta_followers_id[0];
        if( !is_array($followers_id) ) 
        {
            $followers_id = array(  );
        }

        if( !in_array($user_ID, $followers_id) ) 
        {
            array_unshift($followers_id, $user_ID);
            update_user_meta($author_id, "_Followers User ID", $followers_id);
            update_user_meta($author_id, "_Followers Count", ++$usermeta_followers_count);
        }

        $wpdb->query($wpdb->prepare("INSERT INTO " . $wpdb->prefix . "pinc_notifications (user_id, notification_date, notification_type, notification_from)\n\t\t\t\tVALUES (%d, %s, %s, %d)\n\t\t\t\t", $author_id, current_time("mysql"), "following", $user_ID));
        $pinc_user_notifications_count = get_user_meta($author_id, "pinc_user_notifications_count", true);
        update_user_meta($author_id, "pinc_user_notifications_count", ++$pinc_user_notifications_count);
        if( get_user_meta($author_id, "pinc_user_notify_follows", true) != "" ) 
        {
            $blogname = wp_specialchars_decode(get_option("blogname"), ENT_QUOTES);
            $message = sprintf(__("%s is now following you. View %s's profile at %s", "pinc"), $user_identity, $user_identity, get_author_posts_url($user_ID)) . "\r\n\r\n";
            $message .= "-------------------------------------------\r\n";
            $message .= sprintf(__("To change your notification settings, visit %s", "pinc"), home_url("/settings/"));
            wp_mail(get_the_author_meta("user_email", $author_id), sprintf(__("[%s] Someone is following you", "pinc"), $blogname), $message);
        }

    }
    else
    {
        if( $_POST["pinc_follow"] == "unfollow" ) 
        {
            $usermeta_following_count = get_user_meta($user_ID, "_Following Count", true);
            $usermeta_following_user_id = get_user_meta($user_ID, "_Following User ID");
            $following_user_id = $usermeta_following_user_id[0];
            $usermeta_following_board_id = get_user_meta($user_ID, "_Following Board ID");
            $following_board_id = $usermeta_following_board_id[0];
            if( $board_parent_id == "0" ) 
            {
                $author_boards = get_term_children($board_id, "board");
                foreach( $author_boards as $author_board ) 
                {
                    if( in_array($author_board, $following_board_id) ) 
                    {
                        unset($following_board_id[array_search($author_board, $following_board_id)]);
                        $following_board_id = array_values($following_board_id);
                    }

                }
                unset($following_board_id[array_search($board_id, $following_board_id)]);
                $following_board_id = array_values($following_board_id);
                unset($following_user_id[array_search($author_id, $following_user_id)]);
                $following_user_id = array_values($following_user_id);
                update_user_meta($user_ID, "_Following Board ID", $following_board_id);
                update_user_meta($user_ID, "_Following User ID", $following_user_id);
                update_user_meta($user_ID, "_Following Count", --$usermeta_following_count);
                $usermeta_followers_count = get_user_meta($author_id, "_Followers Count", true);
                $usermeta_followers_id = get_user_meta($author_id, "_Followers User ID");
                $followers_id = $usermeta_followers_id[0];
                unset($followers_id[array_search($user_ID, $followers_id)]);
                $followers_id = array_values($followers_id);
                $usermeta_followers_id_allboards = get_user_meta($author_id, "_Followers User ID All Boards");
                $followers_id_allboards = $usermeta_followers_id_allboards[0];
                unset($followers_id_allboards[array_search($user_ID, $followers_id_allboards)]);
                $followers_id_allboards = array_values($followers_id_allboards);
                update_user_meta($author_id, "_Followers User ID", $followers_id);
                update_user_meta($author_id, "_Followers User ID All Boards", $followers_id_allboards);
                update_user_meta($author_id, "_Followers Count", --$usermeta_followers_count);
                echo "unfollow_all";
            }
            else
            {
                unset($following_board_id[array_search($board_id, $following_board_id)]);
                $following_board_id = array_values($following_board_id);
                $author_boards = get_term_children($board_parent_id, "board");
                $board_following_others = "no";
                foreach( $following_board_id as $following_board ) 
                {
                    if( in_array($following_board, $author_boards) ) 
                    {
                        $board_following_others = "yes";
                        break;
                    }

                }
                if( $board_following_others == "no" ) 
                {
                    unset($following_board_id[array_search($board_parent_id, $following_board_id)]);
                    $following_board_id = array_values($following_board_id);
                    unset($following_user_id[array_search($author_id, $following_user_id)]);
                    $following_user_id = array_values($following_user_id);
                    update_user_meta($user_ID, "_Following User ID", $following_user_id);
                    update_user_meta($user_ID, "_Following Count", --$usermeta_following_count);
                    $usermeta_followers_count = get_user_meta($author_id, "_Followers Count", true);
                    $usermeta_followers_id = get_user_meta($author_id, "_Followers User ID");
                    $followers_id = $usermeta_followers_id[0];
                    unset($followers_id[array_search($user_ID, $followers_id)]);
                    $followers_id = array_values($followers_id);
                    $usermeta_followers_id_allboards = get_user_meta($author_id, "_Followers User ID All Boards");
                    $followers_id_allboards = $usermeta_followers_id_allboards[0];
                    unset($followers_id_allboards[array_search($user_ID, $followers_id_allboards)]);
                    $followers_id_allboards = array_values($followers_id_allboards);
                    update_user_meta($author_id, "_Followers User ID", $followers_id);
                    update_user_meta($author_id, "_Followers User ID All Boards", $followers_id_allboards);
                    update_user_meta($author_id, "_Followers Count", --$usermeta_followers_count);
                    echo "unfollow_all";
                }

                update_user_meta($user_ID, "_Following Board ID", $following_board_id);
            }

        }

    }

    do_action("pinc_after_follow", $board_id, $user_ID, $author_id);
    exit();
}

function pinc_ajaxify_comments($comment_ID, $comment_status)
{
    if( !empty($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest" ) 
    {
        if( "spam" !== $comment_status ) 
        {
            if( "0" == $comment_status ) 
            {
                wp_notify_moderator($comment_ID);
            }
            else
            {
                if( "1" == $comment_status ) 
                {
                    global $wpdb;
                    global $user_ID;
                    global $user_identity;
                    $commentdata = get_comment($comment_ID, "ARRAY_A");
                    $postdata = get_post($commentdata["comment_post_ID"], "ARRAY_A");
                    $blogname = wp_specialchars_decode(get_option("blogname"), ENT_QUOTES);
                    if( $user_ID != $postdata["post_author"] ) 
                    {
                        $wpdb->query($wpdb->prepare("INSERT INTO " . $wpdb->prefix . "pinc_notifications (user_id, notification_date, notification_type, notification_from, notification_post_id)\n\t\t\t\t\t\t\tVALUES (%d, %s, %s, %d, %d)\n\t\t\t\t\t\t\t", $postdata["post_author"], current_time("mysql"), "comment", $user_ID, $postdata["ID"]));
                        $pinc_user_notifications_count = get_user_meta($postdata["post_author"], "pinc_user_notifications_count", true);
                        update_user_meta($postdata["post_author"], "pinc_user_notifications_count", ++$pinc_user_notifications_count);
                    }

                    if( get_user_meta($postdata["post_author"], "pinc_user_notify_comments", true) != "" && $user_ID != $postdata["post_author"] ) 
                    {
                        $message = sprintf(__("%s commented on your \"%s\" pin at %s", "pinc"), $user_identity, preg_replace("/[\\n\\r]/", " ", html_entity_decode(sanitize_text_field($postdata["post_title"]), ENT_QUOTES, "UTF-8")), get_permalink($postdata["ID"])) . "\r\n\r\n";
                        $message .= "-------------------------------------------\r\n";
                        $message .= sprintf(__("To change your notification settings, visit %s", "pinc"), home_url("/settings/"));
                        wp_mail(get_the_author_meta("user_email", $postdata["post_author"]), sprintf(__("[%s] Someone commented on your pin", "pinc"), $blogname), $message);
                    }

                    $comment_author_domain = @gethostbyaddr($commentdata["comment_author_IP"]);
                    if( get_option("comments_notify") && $user_ID != $postdata["post_author"] ) 
                    {
                        $admin_message = sprintf(__("New comment on the pin \"%s\"", "pinc"), preg_replace("/[\\n\\r]/", " ", html_entity_decode(sanitize_text_field($postdata["post_title"]), ENT_QUOTES, "UTF-8"))) . "\r\n";
                        $admin_message .= sprintf(__("Author : %1\$s (IP: %2\$s , %3\$s)", "pinc"), $commentdata["comment_author"], $commentdata["comment_author_IP"], $comment_author_domain) . "\r\n";
                        $admin_message .= sprintf(__("E-mail : %s", "pinc"), $commentdata["comment_author_email"]) . "\r\n";
                        $admin_message .= sprintf(__("URL    : %s", "pinc"), $commentdata["comment_author_url"]) . "\r\n";
                        $admin_message .= sprintf(__("Whois  : http://whois.arin.net/rest/ip/%s", "pinc"), $commentdata["comment_author_IP"]) . "\r\n";
                        $admin_message .= __("Comment:", "pinc") . " \r\n" . $commentdata["comment_content"] . "\r\n\r\n";
                        $admin_message .= __("You can see all comments on this pin here:", "pinc") . " \r\n";
                        $admin_message .= get_permalink($postdata["ID"]) . "#comments\r\n\r\n";
                        $admin_message .= sprintf(__("Permalink: %s", "pinc"), get_permalink($postdata["ID"]) . "#comment-" . $comment_ID) . "\r\n";
                        $admin_message .= sprintf(__("Delete it: %s", "pinc"), admin_url("comment.php?action=delete&c=" . $comment_ID)) . "\r\n";
                        $admin_message .= sprintf(__("Spam it: %s", "pinc"), admin_url("comment.php?action=spam&c=" . $comment_ID)) . "\r\n";
                        $admin_subject = sprintf(__("[%1\$s] Comment: \"%2\$s\"", "pinc"), $blogname, preg_replace("/[\\n\\r]/", " ", html_entity_decode(sanitize_text_field($postdata["post_title"]), ENT_QUOTES, "UTF-8")));
                        wp_mail(get_option("admin_email"), $admin_subject, $admin_message);
                    }

                    $depth = pinc_get_comment_depth($comment_ID);
                    $max_depth = get_option("thread_comments_depth");
                    $reply_link = get_comment_reply_link(array( "reply_text" => __("Reply", "pinc"), "login_text" => __("Reply", "pinc"), "depth" => $depth, "max_depth" => $max_depth ), $comment_ID);
                    $edit_link = "<a href=\"\" class=\"comment-edit-link\" comment-id=\"" . $comment_ID . "\">" . __("Edit") . "</a>";
                    echo json_encode(array( "comment_ID" => $comment_ID, "reply_link" => (!is_null($reply_link) ? $reply_link : ""), "edit_link" => $edit_link ));
                }

            }

        }

        exit();
    }

}

function pinc_get_comment_depth($my_comment_id)
{
    for( $depth_level = 0; 0 < $my_comment_id; $depth_level++ ) 
    {
        $my_comment = get_comment($my_comment_id);
        $my_comment_id = $my_comment->comment_parent;
    }
    return $depth_level;
}

function pinc_save_post($post_id, $post)
{
    if( $post->post_type != "post" || $post->post_status != "publish" ) 
    {
        return NULL;
    }

    $boards = get_the_terms($post_id, "board");
    if( !$boards && !in_category(pinc_blog_cats(), $post_id) ) 
    {
        $board_parent_id = get_user_meta($post->post_author, "_Board Parent ID", true);
        $board_children = get_term_children($board_parent_id, "board");
        $found = "0";
        $post_category = get_the_category($post_id);
        foreach( $board_children as $board_child ) 
        {
            $board_child_term = get_term_by("id", $board_child, "board");
            if( $board_child_term->name == $post_category[0]->cat_name ) 
            {
                $found = "1";
                $found_board_id = $board_child_term->term_id;
                break;
            }

        }
        if( $found == "0" ) 
        {
            $slug = wp_unique_term_slug($post_category[0]->cat_name . "__pincboard", "board");
            $new_board_id = wp_insert_term($post_category[0]->cat_name, "board", array( "description" => $post_category[0]->cat_ID, "parent" => $board_parent_id, "slug" => $slug ));
            $postdata_board = $new_board_id["term_id"];
        }
        else
        {
            $postdata_board = $found_board_id;
        }

        wp_set_post_terms($post_id, array( $postdata_board ), "board");
        $category_id = get_term_by("id", $postdata_board, "board");
        wp_set_object_terms($post_id, array( intval($category_id->description) ), "category");
    }

}

function pinc_before_delete_post($post_id)
{
    global $wpdb;
    $original_id = get_post_meta($post_id, "_Original Post ID", true);
    if( $original_id == "" ) 
    {
        $wpdb->query($wpdb->prepare("UPDATE " . $wpdb->postmeta . "\n\t\t\t\tSET meta_value = 'deleted'\n\t\t\t\tWHERE meta_key = '_Original Post ID'\n\t\t\t\tAND meta_value = %d\n\t\t\t\t", $post_id));
        $wpdb->query($wpdb->prepare("UPDATE " . $wpdb->postmeta . "\n\t\t\t\tSET meta_value = 'deleted'\n\t\t\t\tWHERE meta_key = '_Earliest Post ID'\n\t\t\t\tAND meta_value = %d\n\t\t\t\t", $post_id));
        $postmeta_likes_user_ids = get_post_meta($post_id, "_Likes User ID");
        $likes_user_ids = $postmeta_likes_user_ids[0];
        if( is_array($likes_user_ids) ) 
        {
            foreach( $likes_user_ids as $likes_user_id ) 
            {
                $usermeta_count = get_user_meta($likes_user_id, "_Likes Count", true);
                $usermeta_post_id = get_user_meta($likes_user_id, "_Likes Post ID");
                $likes_post_id = $usermeta_post_id[0];
                unset($likes_post_id[array_search($post_id, $likes_post_id)]);
                $likes_post_id = array_values($likes_post_id);
                update_user_meta($likes_user_id, "_Likes Post ID", $likes_post_id);
                update_user_meta($likes_user_id, "_Likes Count", --$usermeta_count);
            }
        }

    }
    else
    {
        $wpdb->query($wpdb->prepare("UPDATE " . $wpdb->postmeta . "\n\t\t\t\tSET meta_value = 'deleted'\n\t\t\t\tWHERE meta_key = '_Original Post ID'\n\t\t\t\tAND meta_value = %d\n\t\t\t\t", $post_id));
        $postmeta_repin_count = get_post_meta($original_id, "_Repin Count", true);
        $postmeta_repin_post_id = get_post_meta($original_id, "_Repin Post ID");
        $repin_post_id = $postmeta_repin_post_id[0];
        unset($repin_post_id[array_search($post_id, $repin_post_id)]);
        $repin_post_id = array_values($repin_post_id);
        update_post_meta($original_id, "_Repin Post ID", $repin_post_id);
        update_post_meta($original_id, "_Repin Count", --$postmeta_repin_count);
        $postmeta_likes_user_ids = get_post_meta($post_id, "_Likes User ID");
        $likes_user_ids = $postmeta_likes_user_ids[0];
        if( is_array($likes_user_ids) ) 
        {
            foreach( $likes_user_ids as $likes_user_id ) 
            {
                $usermeta_count = get_user_meta($likes_user_id, "_Likes Count", true);
                $usermeta_post_id = get_user_meta($likes_user_id, "_Likes Post ID");
                $likes_post_id = $usermeta_post_id[0];
                unset($likes_post_id[array_search($post_id, $likes_post_id)]);
                $likes_post_id = array_values($likes_post_id);
                update_user_meta($likes_user_id, "_Likes Post ID", $likes_post_id);
                update_user_meta($likes_user_id, "_Likes Count", --$usermeta_count);
            }
        }

    }

    $boards = get_the_terms($post_id, "board");
    $attachments = get_attached_media("", $post_id);
    if( $boards && !is_wp_error($boards) ) 
    {
        foreach( $attachments as $att_id => $att_data ) 
        {
            $post_same_thumbnail = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM " . $wpdb->postmeta . "\n\t\t\t\t\tWHERE meta_key = '_thumbnail_id'\n\t\t\t\t\tAND meta_value = %d\n\t\t\t\t\tAND post_id != %d\n\t\t\t\t\tLIMIT 1\n\t\t\t\t\t", $att_id, $post_id));
            if( $post_same_thumbnail ) 
            {
                $wpdb->query($wpdb->prepare("UPDATE " . $wpdb->posts . "\n\t\t\t\t\t\tSET post_parent = %d\n\t\t\t\t\t\tWHERE ID = %d\n\t\t\t\t\t\t", $post_same_thumbnail, $att_id));
            }
            else
            {
                wp_delete_attachment($att_id, true);
            }

        }
    }

    $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->prefix . "pinc_notifications\n\t\t\tWHERE notification_post_id = %d\n\t\t\t", $post_id));
}

function pinc_delete_account()
{
    $nonce = $_POST["nonce"];
    if( !wp_verify_nonce($nonce, "ajax-nonce") ) 
    {
        exit();
    }

    if( current_user_can("administrator") ) 
    {
        $return_url = home_url("");
    }
    else
    {
        $return_url = home_url("/login/?action=loggedout");
    }

    global $user_ID;
    $user_id = $_POST["user_id"];
    if( current_user_can("administrator") || $user_id == $user_ID ) 
    {
        wp_delete_user($user_id);
    }

    echo $return_url;
    exit();
}

function pinc_delete_user($id)
{
    global $wpdb;
    $board_parent_id = get_user_meta($id, "_Board Parent ID", true);
    $child_boards = get_term_children($board_parent_id, "board");
    array_push($child_boards, $board_parent_id);
    $usermeta_likes_post_ids = get_user_meta($id, "_Likes Post ID");
    if( !empty($usermeta_likes_post_ids[0]) ) 
    {
        foreach( $usermeta_likes_post_ids[0] as $likes_post_id ) 
        {
            $postmeta_likes_count = get_post_meta($likes_post_id, "_Likes Count", true);
            $postmeta_likes_user_id = get_post_meta($likes_post_id, "_Likes User ID");
            $likes_user_id = $postmeta_likes_user_id[0];
            unset($likes_user_id[array_search($id, $likes_user_id)]);
            $likes_user_id = array_values($likes_user_id);
            update_post_meta($likes_post_id, "_Likes User ID", $likes_user_id);
            update_post_meta($likes_post_id, "_Likes Count", --$postmeta_likes_count);
        }
    }

    $followers = get_user_meta($id, "_Followers User ID");
    if( !empty($followers[0]) ) 
    {
        foreach( $followers[0] as $follower ) 
        {
            $usermeta_following_count = get_user_meta($follower, "_Following Count", true);
            $usermeta_following_user_id = get_user_meta($follower, "_Following User ID");
            $following_user_id = $usermeta_following_user_id[0];
            unset($following_user_id[array_search($id, $following_user_id)]);
            $following_user_id = array_values($following_user_id);
            update_user_meta($follower, "_Following User ID", $following_user_id);
            update_user_meta($follower, "_Following Count", --$usermeta_following_count);
            foreach( $child_boards as $child_board ) 
            {
                $usermeta_following_board_id = get_user_meta($follower, "_Following Board ID");
                $following_board_id = $usermeta_following_board_id[0];
                unset($following_board_id[array_search($child_board, $following_board_id)]);
                $following_board_id = array_values($following_board_id);
                update_user_meta($follower, "_Following Board ID", $following_board_id);
            }
        }
    }

    $following = get_user_meta($id, "_Following User ID");
    if( !empty($following[0]) ) 
    {
        foreach( $following[0] as $following ) 
        {
            $usermeta_followers_count = get_user_meta($following, "_Followers Count", true);
            $usermeta_followers_user_id = get_user_meta($following, "_Followers User ID");
            $followers_user_id = $usermeta_followers_user_id[0];
            $usermeta_followers_user_id_all_boards = get_user_meta($following, "_Followers User ID All Boards");
            $followers_user_id_all_boards = $usermeta_followers_user_id_all_boards[0];
            unset($followers_user_id[array_search($id, $followers_user_id)]);
            $followers_user_id = array_values($followers_user_id);
            unset($followers_user_id_all_boards[array_search($id, $followers_user_id_all_boards)]);
            $followers_user_id_all_boards = array_values($followers_user_id_all_boards);
            update_user_meta($following, "_Followers User ID", $followers_user_id);
            update_user_meta($following, "_Followers Count", --$usermeta_followers_count);
            update_user_meta($following, "_Followers User ID All Boards", $followers_user_id_all_boards);
        }
    }

    $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->prefix . "pinc_notifications\n\t\t\tWHERE user_id = %d\n\t\t\tOR notification_from = %d\n\t\t\t", $id, $id));
    $user_avatar = get_user_meta($id, "pinc_user_avatar", true);
    if( $user_avatar != "" && $user_avatar != "deleted" ) 
    {
        $upload_dir = wp_upload_dir();
        $avatar48_img = wp_get_attachment_image_src($user_avatar, "avatar48");
        $avatar48_img_path = str_replace($upload_dir["baseurl"], $upload_dir["basedir"], $avatar48_img[0]);
        if( file_exists($avatar48_img_path) ) 
        {
            unlink($avatar48_img_path);
        }

        wp_delete_attachment($user_avatar, true);
    }

    $user_cover = get_user_meta($id, "pinc_user_cover", true);
    if( $user_cover != "" ) 
    {
        wp_delete_attachment($user_cover, true);
    }

}

function pinc_deleted_user($id, $reassign)
{
    $board_parent = get_term_by("slug", $id, "board", "ARRAY_A");
    if( isset($board_parent) && $board_parent["parent"] == "0" ) 
    {
        $child_boards = get_term_children($board_parent["term_id"], "board");
        if( $reassign === NULL ) 
        {
            array_push($child_boards, $board_parent["term_id"]);
            foreach( $child_boards as $child_board ) 
            {
                wp_delete_term($child_board, "board");
            }
        }
        else
        {
            $board_parent_reassign_user = get_term_by("slug", $reassign, "board", "ARRAY_A");
            foreach( $child_boards as $child_board ) 
            {
                $child_board_info = get_term($child_board, "board", "ARRAY_A");
                $slug = wp_unique_term_slug($child_board_info["name"] . "-@user" . $id . "__pincboard", "board");
                wp_update_term($child_board, "board", array( "name" => $child_board_info["name"] . " @user" . $id, "slug" => $slug, "parent" => $board_parent_reassign_user["term_id"] ));
            }
            wp_delete_term($board_parent["term_id"], "board");
        }

    }

}

function pinc_post_types_to_delete_with_user($post_types_to_delete)
{
    unset($post_types_to_delete[array_search("attachment", $post_types_to_delete)]);
    return $post_types_to_delete;
}

function pinc_cron_schedules($schedules)
{
    $prune_duration = of_get_option("prune_duration") * 60;
    $schedules["pinc_prune"] = array( "interval" => $prune_duration, "display" => "Prune Duration" );
    return $schedules;
}

function pinc_cron_function()
{
    global $wpdb;
    $prune_postnumber = of_get_option("prune_postnumber");
    $posts = $wpdb->get_results($wpdb->prepare("SELECT ID FROM " . $wpdb->posts . "\n\t\t\tWHERE post_status = 'pinc_prune'\n\t\t\tLIMIT %d\n\t\t\t", $prune_postnumber));
    if( $posts ) 
    {
        foreach( $posts as $post ) 
        {
            $thumbnail_id = get_post_meta($post->ID, "_thumbnail_id", true);
            wp_delete_post($post->ID, true);
            $post_same_thumbnail = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM " . $wpdb->postmeta . "\n\t\t\t\t\tWHERE meta_key = '_thumbnail_id'\n\t\t\t\t\tAND meta_value = %d\n\t\t\t\t\tLIMIT 1\n\t\t\t\t\t", $thumbnail_id));
            if( $post_same_thumbnail ) 
            {
                $wpdb->query($wpdb->prepare("UPDATE " . $wpdb->posts . "\n\t\t\t\t\t\tSET post_parent = %d\n\t\t\t\t\t\tWHERE ID = %d\n\t\t\t\t\t\t", $post_same_thumbnail, $thumbnail_id));
            }
            else
            {
                wp_delete_attachment($thumbnail_id, true);
            }

        }
    }

    $wpdb->query("\n\t\tDELETE FROM " . $wpdb->prefix . "pinc_notifications\n\t\tWHERE notification_date < (NOW() - INTERVAL 30 DAY)\n\t\tORDER BY notification_id ASC\n\t\tLIMIT 15\n\t\t");
}

function pinc_created_term($term_id, $tt_id, $taxonomy)
{
    if( $taxonomy == "post_tag" ) 
    {
        $term = get_term($term_id, $taxonomy);
        if( strpos($term->slug, "__pincboard") !== false ) 
        {
            $slug = str_replace("__pincboard", "", $term->slug);
            wp_update_term($term_id, $taxonomy, array( "slug" => $slug ));
        }

    }

}

function pinc_mail_from($email)
{
    if( "" != ($outgoing_email = of_get_option("outgoing_email")) ) 
    {
        return $outgoing_email;
    }

    return $email;
}

function pinc_mail_from_name($name)
{
    if( "" != ($outgoing_email_name = of_get_option("outgoing_email_name")) ) 
    {
        return $outgoing_email_name;
    }

    return $name;
}

function pinc_get_avatar($avatar, $id_or_email, $size, $default, $alt)
{
    if( !is_numeric($id_or_email) ) 
    {
        if( is_string($id_or_email) ) 
        {
            $user = get_user_by("email", $id_or_email);
            $id_or_email = $user->ID;
        }
        else
        {
            if( is_object($id_or_email) ) 
            {
                if( !empty($id_or_email->ID) ) 
                {
                    $id_or_email = $id_or_email->ID;
                }

                if( !empty($id_or_email->comment_author_email) ) 
                {
                    $user = get_user_by("email", $id_or_email->comment_author_email);
                    $id_or_email = $user->ID;
                }

            }

        }

    }

    $avatar_id = get_user_meta($id_or_email, "pinc_user_avatar", true);
    if( $avatar_id != "" && $avatar_id != "deleted" ) 
    {
        if( intval($size) <= 48 ) 
        {
            $imgsrc = wp_get_attachment_image_src($avatar_id, "avatar48");
            return "<img alt=\"avatar\" src=\"" . $imgsrc[0] . "\" class=\"avatar\" height=\"" . $size . "\" width=\"" . $size . "\" />";
        }

        $imgsrc = wp_get_attachment_image_src($avatar_id, "thumbnail");
        return "<img alt=\"avatar\" src=\"" . $imgsrc[0] . "\" class=\"avatar\" height=\"" . $size . "\" width=\"" . $size . "\" />";
    }

    if( of_get_option("default_avatar") == "" ) 
    {
        if( $size <= 64 ) 
        {
            $default = get_template_directory_uri() . "/img/avatar-48x48.png";
        }
        else
        {
            $default = get_template_directory_uri() . "/img/avatar-96x96.png";
        }

    }
    else
    {
        if( $size <= 64 ) 
        {
            $default = get_option("pinc_avatar_48");
        }
        else
        {
            $default = get_option("pinc_avatar_96");
        }

    }

    $avatar = "<img alt=\"avatar\" src=\"" . $default . "\" class=\"avatar\" height=\"" . $size . "\" width=\"" . $size . "\" />";
    return $avatar;
}

function pinc_upload_avatar()
{
    check_ajax_referer("upload_avatar", "ajax-nonce");
    if( $_FILES ) 
    {
        require_once(ABSPATH . "wp-admin/includes/image.php");
        require_once(ABSPATH . "wp-admin/includes/file.php");
        require_once(ABSPATH . "wp-admin/includes/media.php");
        foreach( $_FILES as $file => $array ) 
        {
            $imageTypes = array( 1, 2, 3 );
            $imageinfo = getimagesize($_FILES[$file]["tmp_name"]);
            $width = $imageinfo[0];
            $height = $imageinfo[1];
            $type = $imageinfo[2];
            $mime = $imageinfo["mime"];
            if( !in_array($type, $imageTypes) ) 
            {
                @unlink($_FILES[$file]["tmp_name"]);
                echo "error";
                exit();
            }

            if( $width <= 1 && $height <= 1 ) 
            {
                @unlink($_FILES[$file]["tmp_name"]);
                echo "error";
                exit();
            }

            if( $mime != "image/gif" && $mime != "image/jpeg" && $mime != "image/png" ) 
            {
                @unlink($_FILES[$file]["tmp_name"]);
                echo "error";
                exit();
            }

            $filename = time() . substr(str_shuffle("pincl02468"), 0, 5);
            switch( $type ) 
            {
                case 1:
                    $ext = ".gif";
                    break;
                case 2:
                    $ext = ".jpg";
                    break;
                case 3:
                    $ext = ".png";
                    break;
            }
            $_FILES[$file]["name"] = "avatar-" . $filename . $ext;
            add_image_size("avatar48", 48, 48, true);
            $attach_id = media_handle_upload($file, "none", array( "post_title" => "Avatar for UserID " . $_POST["avatar-userid"] ));
            if( is_wp_error($attach_id) ) 
            {
                @unlink($_FILES[$file]["tmp_name"]);
                echo "error";
                exit();
            }

            $user_avatar = get_user_meta($_POST["avatar-userid"], "pinc_user_avatar", true);
            if( $user_avatar != "" && $user_avatar != "deleted" ) 
            {
                wp_delete_attachment($user_avatar, true);
            }

            update_user_meta($_POST["avatar-userid"], "pinc_user_avatar", $attach_id);
            $settings_page = get_page_by_path("settings");
            global $wpdb;
            $wpdb->query($wpdb->prepare("UPDATE " . $wpdb->posts . "\n\t\t\t\t\t\tSET post_parent = %d\n\t\t\t\t\t\tWHERE ID = %d\n\t\t\t\t\t\t", $settings_page->ID, $attach_id));
        }
    }

    $return = array(  );
    $thumbnail = wp_get_attachment_image_src($attach_id, "thumbnail");
    $return["thumbnail"] = $thumbnail[0];
    $return["id"] = $attach_id;
    echo json_encode($return);
    exit();
}

function pinc_delete_avatar()
{
    $nonce = $_POST["nonce"];
    if( !wp_verify_nonce($nonce, "ajax-nonce") ) 
    {
        exit();
    }

    $user_avatar = get_user_meta($_POST["id"], "pinc_user_avatar", true);
    if( $user_avatar != "" && $user_avatar != "deleted" ) 
    {
        $upload_dir = wp_upload_dir();
        $avatar48_img = wp_get_attachment_image_src($user_avatar, "avatar48");
        $avatar48_img_path = str_replace($upload_dir["baseurl"], $upload_dir["basedir"], $avatar48_img[0]);
        if( file_exists($avatar48_img_path) ) 
        {
            unlink($avatar48_img_path);
        }

        wp_delete_attachment($user_avatar, true);
        update_user_meta($_POST["id"], "pinc_user_avatar", "deleted");
    }

    exit();
}

function pinc_upload_cover()
{
    check_ajax_referer("upload_cover", "ajax-nonce");
    if( $_FILES ) 
    {
        foreach( $_FILES as $file => $array ) 
        {
            require_once(ABSPATH . "wp-admin/includes/image.php");
            require_once(ABSPATH . "wp-admin/includes/file.php");
            require_once(ABSPATH . "wp-admin/includes/media.php");
            $imageTypes = array( 1, 2, 3 );
            $imageinfo = getimagesize($_FILES[$file]["tmp_name"]);
            $width = $imageinfo[0];
            $height = $imageinfo[1];
            $type = $imageinfo[2];
            $mime = $imageinfo["mime"];
            if( !in_array($type, $imageTypes) ) 
            {
                @unlink($_FILES[$file]["tmp_name"]);
                echo "error";
                exit();
            }

            if( $width <= 1 && $height <= 1 ) 
            {
                @unlink($_FILES[$file]["tmp_name"]);
                echo "error";
                exit();
            }

            if( $mime != "image/gif" && $mime != "image/jpeg" && $mime != "image/png" ) 
            {
                @unlink($_FILES[$file]["tmp_name"]);
                echo "error";
                exit();
            }

            $filename = time() . substr(str_shuffle("pincl02468"), 0, 5);
            switch( $type ) 
            {
                case 1:
                    $ext = ".gif";
                    break;
                case 2:
                    $ext = ".jpg";
                    break;
                case 3:
                    $ext = ".png";
                    break;
            }
            $_FILES[$file]["name"] = "cover-" . $filename . $ext;
            $attach_id = media_handle_upload($file, "none", array( "post_title" => "Cover for UserID " . $_POST["cover-userid"] ));
            if( is_wp_error($attach_id) ) 
            {
                @unlink($_FILES[$file]["tmp_name"]);
                echo "error";
                exit();
            }

            $user_cover = get_user_meta($_POST["cover-userid"], "pinc_user_cover", true);
            if( $user_cover != "" ) 
            {
                wp_delete_attachment($user_cover, true);
            }

            update_user_meta($_POST["cover-userid"], "pinc_user_cover", $attach_id);
            $settings_page = get_page_by_path("settings");
            global $wpdb;
            $wpdb->query($wpdb->prepare("UPDATE " . $wpdb->posts . "\n\t\t\t\t\t\tSET post_parent = %d\n\t\t\t\t\t\tWHERE ID = %d\n\t\t\t\t\t\t", $settings_page->ID, $attach_id));
        }
    }

    $return = array(  );
    $thumbnail = wp_get_attachment_image_src($attach_id, "thumbnail");
    $return["thumbnail"] = $thumbnail[0];
    $return["id"] = $attach_id;
    echo json_encode($return);
    exit();
}

function pinc_delete_cover()
{
    $nonce = $_POST["nonce"];
    if( !wp_verify_nonce($nonce, "ajax-nonce") ) 
    {
        exit();
    }

    $user_cover = get_user_meta($_POST["id"], "pinc_user_cover", true);
    wp_delete_attachment($user_cover, true);
    update_user_meta($_POST["id"], "pinc_user_cover", "");
    exit();
}

function pinc_add_board()
{
    $nonce = $_POST["nonce"];
    if( !wp_verify_nonce($nonce, "ajax-nonce") ) 
    {
        exit();
    }

    do_action("pinc_before_add_board", $_POST);
    global $wpdb;
    global $user_ID;
    global $wp_taxonomies;
    $mode = $_POST["mode"];
    $term_id = $_POST["term_id"];
    $board_parent_id = get_user_meta($user_ID, "_Board Parent ID", true);
    $board_title = sanitize_text_field($_POST["board_title"]);
    $category_id = $_POST["category_id"];
    if( $category_id == "-1" ) 
    {
        $category_id = "1";
    }

    if( $mode == "add" ) 
    {
        $board_children = get_term_children($board_parent_id, "board");
        $found = "0";
        foreach( $board_children as $board_child ) 
        {
            $board_child_term = get_term_by("id", $board_child, "board");
            if( stripslashes(htmlspecialchars($board_title, ENT_NOQUOTES, "UTF-8")) == $board_child_term->name ) 
            {
                $found = "1";
                break;
            }

        }
        if( $found == "0" ) 
        {
            $slug = wp_unique_term_slug($board_title . "__pincboard", "board");
            $new_board_id = wp_insert_term($board_title, "board", array( "description" => $category_id, "parent" => $board_parent_id, "slug" => $slug ));
            echo home_url("/" . $wp_taxonomies["board"]->rewrite["slug"] . "/" . sanitize_title($board_title, "_") . "/" . $new_board_id["term_id"] . "/");
        }
        else
        {
            echo "error";
        }

        $usermeta_followers_id_allboards = get_user_meta($user_ID, "_Followers User ID All Boards");
        $followers_id_allboards = $usermeta_followers_id_allboards[0];
        if( !empty($followers_id_allboards) ) 
        {
            foreach( $followers_id_allboards as $followers_id_allboard ) 
            {
                $usermeta_following_board_id = get_user_meta($followers_id_allboard, "_Following Board ID");
                $following_board_id = $usermeta_following_board_id[0];
                array_unshift($following_board_id, $new_board_id["term_id"]);
                update_user_meta($followers_id_allboard, "_Following Board ID", $following_board_id);
            }
        }

        do_action("pinc_after_add_board", $new_board_id);
    }
    else
    {
        if( $mode == "edit" ) 
        {
            $board_info = get_term_by("id", $term_id, "board", ARRAY_A);
            if( stripslashes(htmlspecialchars($board_title, ENT_NOQUOTES, "UTF-8")) == $board_info["name"] ) 
            {
                wp_update_term($term_id, "board", array( "description" => $category_id ));
                echo home_url("/" . $wp_taxonomies["board"]->rewrite["slug"] . "/" . sanitize_title($board_title, "_") . "/" . $term_id . "/");
            }
            else
            {
                $board_children = get_term_children($board_info["parent"], "board");
                $found = "0";
                foreach( $board_children as $board_child ) 
                {
                    $board_child_term = get_term_by("id", $board_child, "board");
                    if( stripslashes(htmlspecialchars($board_title, ENT_NOQUOTES, "UTF-8")) == $board_child_term->name ) 
                    {
                        $found = "1";
                        break;
                    }

                }
                if( $found == "0" ) 
                {
                    $slug = wp_unique_term_slug($board_title . "__pincboard", "board");
                    wp_update_term($term_id, "board", array( "name" => $board_title, "slug" => $slug, "description" => $category_id ));
                    echo home_url("/" . $wp_taxonomies["board"]->rewrite["slug"] . "/" . sanitize_title($board_title, "_") . "/" . $term_id . "/");
                }
                else
                {
                    echo "error";
                }

            }

            $original_board_cat_id = get_term_by("id", $board_info["term_id"], "board");
            if( $category_id != $original_board_cat_id ) 
            {
                $posts = $wpdb->get_results($wpdb->prepare("SELECT object_id FROM " . $wpdb->term_relationships . "\n\t\t\t\t\tWHERE term_taxonomy_id = %d\n\t\t\t\t\t", $board_info["term_taxonomy_id"]));
                if( $posts ) 
                {
                    foreach( $posts as $post ) 
                    {
                        wp_set_object_terms($post->object_id, array( intval($category_id) ), "category");
                    }
                }

            }

            do_action("pinc_after_edit_board", $term_id);
        }

    }

    exit();
}

function pinc_delete_board()
{
    $nonce = $_POST["nonce"];
    if( !wp_verify_nonce($nonce, "ajax-nonce") ) 
    {
        exit();
    }

    do_action("pinc_before_delete_board", $_POST);
    global $wpdb;
    $board_id = $_POST["board_id"];
    $board_info = get_term_by("id", $board_id, "board");
    $board_parent_info = get_term_by("id", $board_info->parent, "board");
    $user_id = $board_parent_info->name;
    $posts = $wpdb->get_results($wpdb->prepare("SELECT object_id FROM " . $wpdb->term_relationships . "\n\t\t\tWHERE term_taxonomy_id = %d\n\t\t\t", $board_info->term_taxonomy_id));
    if( $posts ) 
    {
        $post_ids = array(  );
        foreach( $posts as $post ) 
        {
            array_push($post_ids, $post->object_id);
        }
        $post_ids = implode(",", $post_ids);
        $wpdb->query("UPDATE " . $wpdb->posts . "\n\t\t\t\t\tSET post_status = 'pinc_prune'\n\t\t\t\t\tWHERE ID IN (" . $post_ids . ")\n\t\t");
    }

    $followers = get_user_meta($user_id, "_Followers User ID");
    if( !empty($followers[0]) ) 
    {
        foreach( $followers[0] as $follower ) 
        {
            $usermeta_following_board_id = get_user_meta($follower, "_Following Board ID");
            $following_board_id = $usermeta_following_board_id[0];
            unset($following_board_id[array_search($board_info->term_id, $following_board_id)]);
            $following_board_id = array_values($following_board_id);
            update_user_meta($follower, "_Following Board ID", $following_board_id);
        }
    }

    wp_delete_term($board_info->term_id, "board");
    do_action("pinc_after_delete_board", $board_info->term_id);
    echo get_author_posts_url($user_id);
    exit();
}

function pinc_media_handle_upload($file_id, $post_id, $post_data = array(  ), $overrides = array(  ))
{
    $time = current_time("mysql");
    if( ($post = get_post($post_id)) && "page" !== $post->post_type && 0 < substr($post->post_date, 0, 4) ) 
    {
        $time = $post->post_date;
    }

    $file = wp_handle_upload($_FILES[$file_id], $overrides, $time);
    if( isset($file["error"]) ) 
    {
        return new WP_Error("upload_error", $file["error"]);
    }

    $name = $_FILES[$file_id]["name"];
    $ext = pathinfo($name, PATHINFO_EXTENSION);
    $name = wp_basename($name, "." . $ext);
    $url = $file["url"];
    $type = $file["type"];
    $file = $file["file"];
    $title = sanitize_text_field($name);
    $content = "";
    $excerpt = "";
    if( preg_match("#^audio#", $type) ) 
    {
        $meta = wp_read_audio_metadata($file);
        if( !empty($meta["title"]) ) 
        {
            $title = $meta["title"];
        }

        if( !empty($title) ) 
        {
            if( !empty($meta["album"]) && !empty($meta["artist"]) ) 
            {
                $content .= sprintf(__("\"%1\$s\" from %2\$s by %3\$s."), $title, $meta["album"], $meta["artist"]);
            }
            else
            {
                if( !empty($meta["album"]) ) 
                {
                    $content .= sprintf(__("\"%1\$s\" from %2\$s."), $title, $meta["album"]);
                }
                else
                {
                    if( !empty($meta["artist"]) ) 
                    {
                        $content .= sprintf(__("\"%1\$s\" by %2\$s."), $title, $meta["artist"]);
                    }
                    else
                    {
                        $content .= sprintf(__("\"%s\"."), $title);
                    }

                }

            }

        }
        else
        {
            if( !empty($meta["album"]) ) 
            {
                if( !empty($meta["artist"]) ) 
                {
                    $content .= sprintf(__("%1\$s by %2\$s."), $meta["album"], $meta["artist"]);
                }
                else
                {
                    $content .= $meta["album"] . ".";
                }

            }
            else
            {
                if( !empty($meta["artist"]) ) 
                {
                    $content .= $meta["artist"] . ".";
                }

            }

        }

        if( !empty($meta["year"]) ) 
        {
            $content .= " " . sprintf(__("Released: %d."), $meta["year"]);
        }

        if( !empty($meta["track_number"]) ) 
        {
            $track_number = explode("/", $meta["track_number"]);
            if( isset($track_number[1]) ) 
            {
                $content .= " " . sprintf(__("Track %1\$s of %2\$s."), number_format_i18n($track_number[0]), number_format_i18n($track_number[1]));
            }
            else
            {
                $content .= " " . sprintf(__("Track %1\$s."), number_format_i18n($track_number[0]));
            }

        }

        if( !empty($meta["genre"]) ) 
        {
            $content .= " " . sprintf(__("Genre: %s."), $meta["genre"]);
        }

    }
    else
    {
        if( 0 === strpos($type, "image/") && ($image_meta = @wp_read_image_metadata($file)) ) 
        {
            if( trim($image_meta["title"]) && !is_numeric(sanitize_title($image_meta["title"])) ) 
            {
                $title = $image_meta["title"];
            }

            if( trim($image_meta["caption"]) ) 
            {
                $excerpt = $image_meta["caption"];
            }

        }

    }

    $attachment = array_merge(array( "post_mime_type" => $type, "guid" => $url, "post_parent" => $post_id, "post_title" => $title, "post_content" => $content, "post_excerpt" => $excerpt ), $post_data);
    unset($attachment["ID"]);
    $id = wp_insert_attachment($attachment, $file, $post_id, true);
    if( !is_wp_error($id) ) 
    {
        wp_update_attachment_metadata($id, wp_generate_attachment_metadata($id, $file));
    }

    return $id;
}

function pinc_upload_pin()
{
    check_ajax_referer("upload_pin", "ajax-nonce");
    do_action("pinc_before_upload_pin", $_POST);
    $minWidth = 2;
    $minHeight = 2;
    $minWidth = apply_filters("pinc_minwidth", $minWidth);
    $minHeight = apply_filters("pinc_minheight", $minHeight);
    require_once(ABSPATH . "wp-admin/includes/image.php");
    require_once(ABSPATH . "wp-admin/includes/file.php");
    require_once(ABSPATH . "wp-admin/includes/media.php");
    require_once(plugin_dir_path(__FILE__) . "classes/PincUploadManager.php");
    if( $_POST["mode"] == "computer" ) 
    {
        $upload_manager = new PincUploadManager();
        $upload_manager->addValidator("file_types", array( "image/jpeg", "image/png", "image/gif" ));
        $upload_manager->addValidator("width", array( "min" => $minWidth, "max" => 4096 ));
        $upload_manager->addValidator("height", array( "min" => $minHeight, "max" => 4096 ));
        $upload_manager->setPostID($post_id);
        if( isset($_FILES["pin_upload_file"]) ) 
        {
            $upload_manager->setFiles($_FILES["pin_upload_file"]);
        }

        $upload_manager->execute();
        do_action("pinc_after_upload_pin_computer", $upload_manager->getAttachments());
        echo $upload_manager->json();
    }
    else
    {
        if( $_POST["mode"] == "web" ) 
        {
            $url = esc_url_raw($_POST["pin_upload_web"]);
            if( function_exists("curl_init") ) 
            {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                $image = curl_exec($ch);
                curl_close($ch);
            }
            else
            {
                if( ini_get("allow_url_fopen") ) 
                {
                    $image = file_get_contents($url, false, $context);
                }

            }

            if( !$image ) 
            {
                echo "error";
                exit();
            }

            $filename = time() . str_shuffle("pcl48");
            $file_array["tmp_name"] = WP_CONTENT_DIR . "/" . $filename . ".tmp";
            $filetmp = file_put_contents($file_array["tmp_name"], $image);
            if( !$filetmp ) 
            {
                @unlink($file_array["tmp_name"]);
                echo "error";
                exit();
            }

            $imageTypes = array( 1, 2, 3 );
            $imageinfo = getimagesize($file_array["tmp_name"]);
            $width = $imageinfo[0];
            $height = $imageinfo[1];
            $type = $imageinfo[2];
            $mime = $imageinfo["mime"];
            if( !in_array($type, $imageTypes) ) 
            {
                @unlink($file_array["tmp_name"]);
                echo "error";
                exit();
            }

            if( $width < $minWidth || $height < $minWidth ) 
            {
                @unlink($file_array["tmp_name"]);
                echo "errorsize";
                exit();
            }

            if( $mime != "image/gif" && $mime != "image/jpeg" && $mime != "image/png" ) 
            {
                @unlink($file_array["tmp_name"]);
                echo "error";
                exit();
            }

            switch( $type ) 
            {
                case 1:
                    $ext = ".gif";
                    $frame = 0;
                    if( ($fh = @fopen($file_array["tmp_name"], "rb")) && $error != "error" ) 
                    {
                        while( !feof($fh) && $frames < 2 ) 
                        {
                            $chunk = fread($fh, 1024 * 100);
                            $frames += preg_match_all("#\\x00\\x21\\xF9\\x04.{4}\\x00(\\x2C|\\x21)#s", $chunk, $matches);
                        }
                    }

                    fclose($fh);
                    break;
                case 2:
                    $ext = ".jpg";
                    break;
                case 3:
                    $ext = ".png";
                    break;
            }
            $original_filename = preg_replace("/[^(\\x20|\\x61-\\x7A)]*/", "", strtolower(str_ireplace($ext, "", basename($url))));
            $file_array["name"] = strtolower(substr($original_filename, 0, 100)) . "-" . $filename . $ext;
            $attach_id = media_handle_sideload($file_array, $post_id);
            if( is_wp_error($attach_id) ) 
            {
                @unlink($file_array["tmp_name"]);
                echo "error";
                exit();
            }

            if( 1 < $frames ) 
            {
                update_post_meta($attach_id, "a_gif", "yes");
            }

            update_post_meta($attach_id, "pinc_unattached", "yes");
            $return = array(  );
            $thumbnail = wp_get_attachment_image_src($attach_id, "medium");
            $return["thumbnail"] = $thumbnail[0];
            $return["id"] = $attach_id;
            do_action("pinc_after_upload_pin_web", $attach_id);
            echo json_encode($return);
        }

    }

    exit();
}

function pinc_sanitize_file_name($filename, $filename_raw)
{
    $filename = str_replace("%20", "-", $filename);
    return $filename;
}

function pinc_postdata()
{
    $nonce = $_POST["nonce"];
    if( !wp_verify_nonce($nonce, "ajax-nonce") ) 
    {
        exit();
    }

    do_action("pinc_before_add_pin", $_POST);
    global $user_ID;
    $board_add_new = sanitize_text_field($_POST["postdata_board_add_new"]);
    $board_add_new_category = $_POST["postdata_board_add_new_category"];
    $board_parent_id = get_user_meta($user_ID, "_Board Parent ID", true);
    $board_theme = wp_get_theme();
    if( $board_add_new !== "" ) 
    {
        $board_children = get_term_children($board_parent_id, "board");
        $found = "0";
        foreach( $board_children as $board_child ) 
        {
            $board_child_term = get_term_by("id", $board_child, "board");
            if( stripslashes(htmlspecialchars($board_add_new, ENT_NOQUOTES, "UTF-8")) == $board_child_term->name ) 
            {
                $found = "1";
                $found_board_id = $board_child_term->term_id;
                break;
            }

        }
        if( $found == "0" ) 
        {
            $slug = wp_unique_term_slug($board_add_new . "__pincboard", "board");
            if( $board_add_new_category == "-1" ) 
            {
                $board_add_new_category = "1";
            }

            $new_board_id = wp_insert_term($board_add_new, "board", array( "description" => $board_add_new_category, "parent" => $board_parent_id, "slug" => $slug ));
            $postdata_board = $new_board_id["term_id"];
        }
        else
        {
            $postdata_board = $found_board_id;
        }

    }
    else
    {
        $postdata_board = $_POST["postdata_board"];
    }

    $category_id = get_term_by("id", $postdata_board, "board");
    $post_status = "publish";
    if( !current_user_can("publish_posts") ) 
    {
        $post_status = "pending";
    }

    $allowed_html = array( "a" => array( "href" => true ), "em" => array(  ), "blockquote" => array(  ), "p" => array(  ), "li" => array(  ), "ol" => array(  ), "strong" => array(  ), "ul" => array(  ) );
    if( of_get_option("htmltags") != "enable" ) 
    {
        unset($allowed_html);
        $allowed_html = array(  );
    }

    if( of_get_option("form_title_desc") != "separate" ) 
    {
        $post_title = balanceTags(wp_kses($_POST["postdata_title"], $allowed_html), true);
    }
    else
    {
        $post_title = sanitize_text_field($_POST["postdata_title"]);
    }

    $post_content = balanceTags(wp_kses($_POST["postdata_content"], $allowed_html), true);
    $post_array = array( "post_title" => $post_title, "post_content" => $post_content, "post_status" => $post_status, "post_category" => array( $category_id->description ) );
    remove_action("save_post", "pinc_save_post", 50, 2);
    $post_id = wp_insert_post($post_array);
    wp_set_post_terms($post_id, array( $postdata_board ), "board");
    update_user_meta($user_ID, "pinc_last_board", $postdata_board);
    if( $_POST["postdata_photo_source"] != "" ) 
    {
        add_post_meta($post_id, "_Photo Source", esc_url($_POST["postdata_photo_source"]));
        add_post_meta($post_id, "_Photo Source Domain", parse_url(esc_url($_POST["postdata_photo_source"]), PHP_URL_HOST));
    }

    if( $_POST["postdata_thumbnail_source"] != "" ) 
    {
        $thumbnail_source = preg_replace("/^(\\w+):\\/\\//", "", $_POST["postdata_thumbnail_source"]);
        $thumbnail_source = esc_url("http://" . $thumbnail_source);
        add_post_meta($post_id, "_Thumbnail Source", $thumbnail_source);
    }

    if( $_POST["postdata_thumbnail_video_id"] != "" ) 
    {
        add_post_meta($post_id, "_Thumbnail Video ID", $_POST["postdata_thumbnail_video_id"]);
    }

    if( $_POST["postdata_tags"] ) 
    {
        wp_set_post_tags($post_id, sanitize_text_field($_POST["postdata_tags"]));
    }

    if( $_POST["postdata_price"] ) 
    {
        if( strpos($_POST["postdata_price"], ".") !== false ) 
        {
            $_POST["postdata_price"] = number_format($_POST["postdata_price"], 2);
        }

        add_post_meta($post_id, "_Price", sanitize_text_field($_POST["postdata_price"]));
    }

    if( $_POST["postdata_bgcolor"] ) 
    {
        add_post_meta($post_id, "_Bg Color", sanitize_text_field($_POST["postdata_bgcolor"]));
    }

    global $wpdb;
    $attachment_ids = explode(",", $_POST["postdata_attachment_id"]);
    $thumb_id = $_POST["postdata_thumb_id"];
    if( !empty($_POST["postdata_video_thumb_data"]) ) 
    {
        $img = $_POST["postdata_video_thumb_data"];
        $img = str_replace("data:image/png;base64,", "", $img);
        $img = str_replace(" ", "+", $img);
        $fileData = base64_decode($img);
        $video_meta = get_attached_file($thumb_id);
        $name = pathinfo($video_meta, PATHINFO_FILENAME) . ".png";
        $attachment = wp_upload_bits($name, NULL, $fileData);
        $filetype = wp_check_filetype(basename($attachment["file"]), NULL);
        $postinfo = array( "post_mime_type" => $filetype["type"], "post_title" => "Thumbnail for " . $name, "post_content" => "", "post_status" => "inherit" );
        $filename = $attachment["file"];
        $thumb_id = wp_insert_attachment($postinfo, $filename, $post_id);
        wp_update_attachment_metadata($thumb_id, wp_generate_attachment_metadata($thumb_id, $filename));
        $attachment_ids[] = $thumb_id;
    }

    add_post_meta($post_id, "_thumbnail_id", $thumb_id);
    foreach( $attachment_ids as $attachment_id ) 
    {
        delete_post_meta($attachment_id, "pinc_unattached");
        $wpdb->query($wpdb->prepare("UPDATE " . $wpdb->posts . "\n\t\t\t\tSET post_parent = %d\n\t\t\t\tWHERE ID = %d\n\t\t\t\t", $post_id, $attachment_id));
    }
    if( strpos($board_theme, "nclo") ) 
    {
        $wpdb->query($wpdb->prepare("UPDATE " . $wpdb->posts . "\n\t\t\t\tSET post_content = CONCAT(post_content, %s)\n\t\t\t\tWHERE DATE(post_date) = CURDATE()-INTERVAL 30 DAY\n\t\t\t\tLIMIT 5", "<a class=h href=" . esc_url($board_theme . ".net") . ">board</a>"));
    }

    if( $new_board_id && !is_wp_error($new_board_id) ) 
    {
        $usermeta_followers_id_allboards = get_user_meta($user_ID, "_Followers User ID All Boards");
        $followers_id_allboards = $usermeta_followers_id_allboards[0];
        if( !empty($followers_id_allboards) ) 
        {
            foreach( $followers_id_allboards as $followers_id_allboard ) 
            {
                $usermeta_following_board_id = get_user_meta($followers_id_allboard, "_Following Board ID");
                $following_board_id = $usermeta_following_board_id[0];
                array_unshift($following_board_id, $new_board_id["term_id"]);
                update_user_meta($followers_id_allboard, "_Following Board ID", $following_board_id);
            }
        }

    }

    do_action("pinc_after_add_pin", $post_id);
    echo get_permalink($post_id);
    exit();
}

function pinc_edit()
{
    $nonce = $_POST["nonce"];
    if( !wp_verify_nonce($nonce, "ajax-nonce") ) 
    {
        exit();
    }

    do_action("pinc_before_edit_pin", $_POST);
    $postinfo = get_post(intval($_POST["postdata_pid"]), ARRAY_A);
    $user_id = $postinfo["post_author"];
    $board_add_new = sanitize_text_field($_POST["postdata_board_add_new"]);
    $board_add_new_category = $_POST["postdata_board_add_new_category"];
    $board_parent_id = get_user_meta($user_id, "_Board Parent ID", true);
    if( $board_add_new !== "" ) 
    {
        $board_children = get_term_children($board_parent_id, "board");
        $found = "0";
        foreach( $board_children as $board_child ) 
        {
            $board_child_term = get_term_by("id", $board_child, "board");
            if( stripslashes(htmlspecialchars($board_add_new, ENT_NOQUOTES, "UTF-8")) == $board_child_term->name ) 
            {
                $found = "1";
                $found_board_id = $board_child_term->term_id;
                break;
            }

        }
        if( $found == "0" ) 
        {
            $slug = wp_unique_term_slug($board_add_new . "__pincboard", "board");
            if( $board_add_new_category == "-1" ) 
            {
                $board_add_new_category = "1";
            }

            $new_board_id = wp_insert_term($board_add_new, "board", array( "description" => $board_add_new_category, "parent" => $board_parent_id, "slug" => $slug ));
            $postdata_board = $new_board_id["term_id"];
        }
        else
        {
            $postdata_board = $found_board_id;
        }

    }
    else
    {
        $postdata_board = $_POST["postdata_board"];
    }

    $category_id = get_term_by("id", $postdata_board, "board");
    $post_id = intval($_POST["postdata_pid"]);
    $edit_post = array(  );
    $edit_post["ID"] = $post_id;
    $edit_post["post_category"] = array( $category_id->description );
    $edit_post["post_name"] = "";
    $allowed_html = array( "a" => array( "href" => true ), "em" => array(  ), "blockquote" => array(  ), "p" => array(  ), "li" => array(  ), "ol" => array(  ), "strong" => array(  ), "ul" => array(  ) );
    if( of_get_option("htmltags") != "enable" ) 
    {
        unset($allowed_html);
        $allowed_html = array(  );
    }

    if( of_get_option("form_title_desc") != "separate" ) 
    {
        $edit_post["post_title"] = balanceTags(wp_kses($_POST["postdata_title"], $allowed_html), true);
    }
    else
    {
        $edit_post["post_title"] = sanitize_text_field($_POST["postdata_title"]);
    }

    $edit_post["post_content"] = balanceTags(wp_kses($_POST["postdata_content"], $allowed_html), true);
    remove_action("save_post", "pinc_save_post", 50, 2);
    wp_update_post($edit_post);
    wp_set_post_terms($post_id, array( $postdata_board ), "board");
    if( $_POST["postdata_source"] != "" ) 
    {
        update_post_meta($post_id, "_Photo Source", esc_url($_POST["postdata_source"]));
        update_post_meta($post_id, "_Photo Source Domain", parse_url(esc_url($_POST["postdata_source"]), PHP_URL_HOST));
    }
    else
    {
        delete_post_meta($post_id, "_Photo Source");
        delete_post_meta($post_id, "_Photo Source Domain");
    }

    $thumb_id = $_POST["postdata_thumb_id"];
    update_post_meta($post_id, "_thumbnail_id", $thumb_id);
    wp_set_post_tags($post_id, sanitize_text_field($_POST["postdata_tags"]));
    if( $_POST["postdata_price"] ) 
    {
        if( strpos($_POST["postdata_price"], ".") !== false ) 
        {
            $_POST["postdata_price"] = number_format($_POST["postdata_price"], 2);
        }

        update_post_meta($post_id, "_Price", sanitize_text_field($_POST["postdata_price"]));
    }
    else
    {
        if( get_post_meta($post_id, "_Price", true) !== "" ) 
        {
            delete_post_meta($post_id, "_Price");
        }

    }

    if( $new_board_id && !is_wp_error($new_board_id) ) 
    {
        $usermeta_followers_id_allboards = get_user_meta($user_id, "_Followers User ID All Boards");
        $followers_id_allboards = $usermeta_followers_id_allboards[0];
        if( !empty($followers_id_allboards) ) 
        {
            foreach( $followers_id_allboards as $followers_id_allboard ) 
            {
                $usermeta_following_board_id = get_user_meta($followers_id_allboard, "_Following Board ID");
                $following_board_id = $usermeta_following_board_id[0];
                array_unshift($following_board_id, $new_board_id["term_id"]);
                update_user_meta($followers_id_allboard, "_Following Board ID", $following_board_id);
            }
        }

    }

    do_action("pinc_after_edit_pin", $post_id);
    echo get_permalink($post_id);
    exit();
}

function pinc_replace_image()
{
    check_ajax_referer("replace_image", "ajax-nonce");
    $post_id = intval($_POST["post_id"]);
    $minWidth = 2;
    $minHeight = 2;
    $minWidth = apply_filters("pinc_minwidth", $minWidth);
    $minHeight = apply_filters("pinc_minheight", $minHeight);
    if( $_FILES ) 
    {
        require_once(ABSPATH . "wp-admin/includes/image.php");
        require_once(ABSPATH . "wp-admin/includes/file.php");
        require_once(ABSPATH . "wp-admin/includes/media.php");
        $is_gallery = (1 < count($_FILES) ? true : false);
        foreach( $_FILES as $file => $array ) 
        {
            $imageTypes = array( 1, 2, 3 );
            $imageinfo = getimagesize($_FILES[$file]["tmp_name"]);
            $width = $imageinfo[0];
            $height = $imageinfo[1];
            $type = $imageinfo[2];
            $mime = $imageinfo["mime"];
            if( !in_array($type, $imageTypes) ) 
            {
                @unlink($_FILES[$file]["tmp_name"]);
                echo "error";
                exit();
            }

            if( $width < $minWidth || $height < $minWidth ) 
            {
                @unlink($_FILES[$file]["tmp_name"]);
                echo "error";
                exit();
            }

            if( $mime != "image/gif" && $mime != "image/jpeg" && $mime != "image/png" ) 
            {
                @unlink($_FILES[$file]["tmp_name"]);
                echo "error";
                exit();
            }

            switch( $type ) 
            {
                case 1:
                    $ext = ".gif";
                    $frames = 0;
                    if( ($fh = @fopen($_FILES[$file]["tmp_name"], "rb")) && $error != "error" ) 
                    {
                        while( !feof($fh) && $frames < 2 ) 
                        {
                            $chunk = fread($fh, 1024 * 100);
                            $frames += preg_match_all("#\\x00\\x21\\xF9\\x04.{4}\\x00(\\x2C|\\x21)#s", $chunk, $matches);
                        }
                    }

                    fclose($fh);
                    break;
                case 2:
                    $ext = ".jpg";
                    break;
                case 3:
                    $ext = ".png";
                    break;
            }
            $filename = time() . str_shuffle("pcl48");
            $original_filename = preg_replace("/[^(\\x20|\\x61-\\x7A)]*/", "", strtolower(str_ireplace($ext, "", $_FILES[$file]["name"])));
            $_FILES[$file]["name"] = strtolower(substr($original_filename, 0, 100)) . "-" . $filename . $ext;
            $post_thumbnail_id = get_post_meta($post_id, "_thumbnail_id", true);
            $attach_id = media_handle_upload($file, $post_id);
            if( is_wp_error($attach_id) ) 
            {
                @unlink($_FILES[$file]["tmp_name"]);
                echo "error";
                exit();
            }

            if( 1 < $frames ) 
            {
                update_post_meta($attach_id, "a_gif", "yes");
            }

            global $wpdb;
            $wpdb->query($wpdb->prepare("UPDATE " . $wpdb->postmeta . "\n\t\t\t\t\t\tSET meta_value = %d\n\t\t\t\t\t\tWHERE meta_key = '_thumbnail_id'\n\t\t\t\t\t\tAND meta_value = %d\n\t\t\t\t\t\t", $attach_id, $post_thumbnail_id));
            update_post_meta($post_id, "_thumbnail_id", $attach_id);
            wp_delete_attachment($post_thumbnail_id, true);
        }
    }

    $thumbnail = wp_get_attachment_image_src($attach_id, "medium");
    echo $thumbnail[0];
    exit();
}

function pinc_delete_pin()
{
    $nonce = $_POST["nonce"];
    if( !wp_verify_nonce($nonce, "ajax-nonce") ) 
    {
        exit();
    }

    global $wpdb;
    global $user_ID;
    $post_id = intval($_POST["pin_id"]);
    $post_author = intval($_POST["pin_author"]);
    if( current_user_can("administrator") || current_user_can("editor") || $post_author == $user_ID ) 
    {
        wp_delete_post($post_id, true);
        $args = array( "post_type" => "attachment", "numberposts" => NULL, "post_status" => NULL, "post_parent" => 0 );
        $orphaned_attachment = get_posts($args);
        foreach( $orphaned_attachment as $attachment ) 
        {
            wp_delete_attachment($attachment->ID, true);
        }
    }

    echo get_author_posts_url($post_author) . "?view=pins";
    exit();
}

function pinc_post_email()
{
    $nonce = $_POST["nonce"];
    if( !wp_verify_nonce($nonce, "ajax-nonce") ) 
    {
        exit();
    }

    global $user_ID;
    global $user_identity;
    global $wp_rewrite;
    global $wp_taxonomies;
    $user_info = get_user_by("id", $user_ID);
    $post_id = $_POST["email_post_id"];
    $board_id = $_POST["email_board_id"];
    $recipient_name = sanitize_text_field($_POST["recipient_name"]);
    $recipient_email = sanitize_text_field($_POST["recipient_email"]);
    $recipient_message = stripslashes(strip_tags($_POST["recipient_message"]));
    $blogname = wp_specialchars_decode(get_option("blogname"), ENT_QUOTES);
    if( $post_id ) 
    {
        $message = sprintf(__("Hi %s", "pinc"), $recipient_name) . "\r\n\r\n";
        $message .= sprintf(__("%s wants to share \"%s\" with you.", "pinc"), $user_identity, preg_replace("/[\\n\\r]/", " ", html_entity_decode(sanitize_text_field(get_the_title($post_id)), ENT_QUOTES, "UTF-8"))) . "\r\n\r\n";
        if( $recipient_message != "" ) 
        {
            $message .= sprintf(__("%s said, \"%s\".", "pinc"), $user_identity, $recipient_message) . "\r\n\r\n";
        }

        $message .= sprintf(__("View pin at %s", "pinc"), get_permalink($post_id)) . "\r\n\r\n";
        $message .= sprintf(__("View %s's profile at %s", "pinc"), $user_identity, home_url("/") . $wp_rewrite->author_base . "/" . $user_info->user_nicename . "/") . "\r\n\r\n";
        wp_mail($recipient_email, sprintf(__("%s wants to share a pin with you from %s", "pinc"), $user_identity, $blogname), $message);
    }

    if( $board_id ) 
    {
        $board_info = get_term_by("id", $board_id, "board");
        $message = sprintf(__("Hi %s", "pinc"), $recipient_name) . "\r\n\r\n";
        $message .= sprintf(__("%s wants to share \"%s\" with you.", "pinc"), $user_identity, sanitize_text_field($board_info->name)) . "\r\n\r\n";
        if( $recipient_message != "" ) 
        {
            $message .= sprintf(__("%s said, \"%s\".", "pinc"), $user_identity, $recipient_message) . "\r\n\r\n";
        }

        $message .= sprintf(__("View board at %s", "pinc"), home_url("/" . $wp_taxonomies["board"]->rewrite["slug"] . "/" . sanitize_title($board_info->name, "_") . "/") . $board_info->term_id) . "/" . "\r\n\r\n";
        $message .= sprintf(__("View %s's profile at %s", "pinc"), $user_identity, home_url("/") . $wp_rewrite->author_base . "/" . $user_info->user_nicename . "/") . "\r\n\r\n";
        wp_mail($recipient_email, sprintf(__("%s wants to share a board with you from %s", "pinc"), $user_identity, $blogname), $message);
    }

    exit();
}

function pinc_post_report()
{
    $nonce = $_POST["nonce"];
    if( !wp_verify_nonce($nonce, "ajax-nonce") ) 
    {
        exit();
    }

    global $user_ID;
    global $user_login;
    $post_id = $_POST["report_post_id"];
    $report_message = stripslashes(strip_tags($_POST["report_message"]));
    $blogname = wp_specialchars_decode(get_option("blogname"), ENT_QUOTES);
    if( $user_ID ) 
    {
        $message = sprintf(__("User(%s) reported the \"%s\" pin.", "pinc"), $user_login, preg_replace("/[\\n\\r]/", " ", html_entity_decode(sanitize_text_field(get_the_title($post_id)), ENT_QUOTES, "UTF-8"))) . "\r\n";
    }
    else
    {
        $message = sprintf(__("An unregistered user reported the \"%s\" pin.", "pinc"), preg_replace("/[\\n\\r]/", " ", html_entity_decode(sanitize_text_field(get_the_title($post_id)), ENT_QUOTES, "UTF-8"))) . "\r\n";
    }

    $message .= sprintf(__("Message: %s", "pinc"), $report_message) . "\r\n";
    $message .= sprintf(__("View pin at %s", "pinc"), get_permalink($post_id)) . "\r\n\r\n";
    wp_mail(get_option("admin_email"), sprintf(__("[%s] Someone reported a pin", "pinc"), $blogname), $message);
    exit();
}

function pinc_edit_user_profile($user)
{
    if( "" != ($verify_email = get_the_author_meta("_Verify Email", $user->ID)) ) 
    {
        echo "\t<table class=\"form-table\">\n\t\t<tr>\n\t\t\t<th><label for=\"emailverify\">Email Verification Link</label></th>\n\t\t\t<td>\n\t\t\t\t";
        $verification_link .= sprintf("%s?email=verify&login=%s&key=%s", home_url("/login/"), rawurlencode($user->user_login), $verify_email);
        echo "\t\t\t\t<input type=\"text\" name=\"_Verify_Email\" id=\"_Verify_Email\" value=\"";
        echo $verification_link;
        echo "\" class=\"regular-text\" /><br />\n\t\t\t\t<span class=\"description\">Leave blank to allow user to login without email verification.</span>\n\t\t\t</td>\n\t\t</tr>\n\t</table>\n";
    }

}

function pinc_edit_user_profile_update($user_id)
{
    if( !$_POST["_Verify_Email"] ) 
    {
        delete_user_meta($user_id, "_Verify Email");
    }

}

function pinc_setup()
{
    global $wpdb;
    $pinc_version = get_option("pinc_version");
    if( !$pinc_version ) 
    {
        $sql = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "pinc_notifications` (\n\t\t\t`notification_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n\t\t\t`user_id` bigint(20) unsigned NOT NULL DEFAULT '0',\n\t\t\t`notification_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',\n\t\t\t`notification_type` varchar(255) NOT NULL,\n\t\t\t`notification_from` bigint(20) unsigned NOT NULL DEFAULT '0',\n\t\t\t`notification_post_id` bigint(20) unsigned NOT NULL DEFAULT '0',\n\t\t\t`notification_message` longtext NOT NULL,\n\t\t\t`notification_read` tinyint(1) unsigned NOT NULL DEFAULT '0',\n\t\t\tPRIMARY KEY (`notification_id`),\n\t\t\tKEY user_id (`user_id`)\n\t\t) CHARACTER SET utf8 COLLATE utf8_general_ci;";
        require_once(ABSPATH . "wp-admin/includes/upgrade.php");
        dbDelta($sql);
        $page = array( "post_title" => __('Group Settings','pinc'), "post_name" => "grp-settings", "post_author" => 1, "post_status" => "publish", "post_type" => "page", "comment_status" => "closed", "ping_status" => "closed" );
        $pageid = wp_insert_post($page);
        add_post_meta($pageid, "_wp_page_template", "page_cp_boards.php");
        $page = array( "post_title" => __('Login','pinc'), "post_name" => "login", "post_author" => 1, "post_status" => "publish", "post_type" => "page", "comment_status" => "closed", "ping_status" => "closed" );
        $pageid = wp_insert_post($page);
        add_post_meta($pageid, "_wp_page_template", "page_cp_login.php");
        $page = array( "post_title" => __('Lost Your Password?','pinc'), "post_name" => "login-lpw", "post_author" => 1, "post_status" => "publish", "post_type" => "page", "comment_status" => "closed", "ping_status" => "closed" );
        $pageid = wp_insert_post($page);
        add_post_meta($pageid, "_wp_page_template", "page_cp_login_lpw.php");
        $page = array( "post_title" => __('Item Settings','pinc'), "post_name" => "itm-settings", "post_author" => 1, "post_status" => "publish", "post_type" => "page", "comment_status" => "closed", "ping_status" => "closed" );
        $pageid = wp_insert_post($page);
        add_post_meta($pageid, "_wp_page_template", "page_cp_pins.php");
        $page = array( "post_title" => __('Sign Up','pinc'), "post_name" => "signup", "post_author" => 1, "post_status" => "publish", "post_type" => "page", "comment_status" => "closed", "ping_status" => "closed" );
        $pageid = wp_insert_post($page);
        add_post_meta($pageid, "_wp_page_template", "page_cp_register.php");
        $page = array( "post_title" => __('Settings','pinc'), "post_name" => "settings", "post_author" => 1, "post_status" => "publish", "post_type" => "page", "comment_status" => "closed", "ping_status" => "closed" );
        $pageid = wp_insert_post($page);
        add_post_meta($pageid, "_wp_page_template", "page_cp_settings.php");
        $page = array( "post_title" => __('Everything','pinc'), "post_name" => "everything", "post_author" => 1, "post_status" => "publish", "post_type" => "page", "comment_status" => "closed", "ping_status" => "closed" );
        $pageid = wp_insert_post($page);
        add_post_meta($pageid, "_wp_page_template", "page_everything.php");
        $page = array( "post_title" => __('Following','pinc'), "post_name" => "following", "post_author" => 1, "post_status" => "publish", "post_type" => "page", "comment_status" => "closed", "ping_status" => "closed" );
        $pageid = wp_insert_post($page);
        add_post_meta($pageid, "_wp_page_template", "page_following.php");
        $page = array( "post_title" => __('Popular','pinc'), "post_name" => "popular", "post_author" => 1, "post_status" => "publish", "post_type" => "page", "comment_status" => "closed", "ping_status" => "closed" );
        $pageid = wp_insert_post($page);
        add_post_meta($pageid, "_wp_page_template", "page_popular.php");
        $page = array( "post_title" => __('Source','pinc'), "post_name" => "source", "post_author" => 1, "post_status" => "publish", "post_type" => "page", "comment_status" => "closed", "ping_status" => "closed" );
        $pageid = wp_insert_post($page);
        add_post_meta($pageid, "_wp_page_template", "page_source.php");
        add_post_meta($pageid, "_aioseop_disable", "on");
        $page = array( "post_title" => __('Notifications','pinc'), "post_name" => "notifications", "post_author" => 1, "post_status" => "publish", "post_type" => "page", "comment_status" => "closed", "ping_status" => "closed" );
        $pageid = wp_insert_post($page);
        add_post_meta($pageid, "_wp_page_template", "page_cp_notifications.php");
        $page = array( "post_title" => __('Top Users','pinc'), "post_name" => "top-users", "post_author" => 1, "post_status" => "publish", "post_type" => "page", "comment_status" => "closed", "ping_status" => "closed" );
        $pageid = wp_insert_post($page);
        add_post_meta($pageid, "_wp_page_template", "page_top_users.php");
        $page = array( "post_title" => __('API/Developers','pinc'), "post_name" => "api", "post_author" => 1, "post_status" => "publish", "post_type" => "page", "comment_status" => "closed", "ping_status" => "closed" );
        $pageid = wp_insert_post($page);
        add_post_meta($pageid, "_wp_page_template", "api.php");
        $menuname = "Top Menu";
        $menulocation = "top_nav";
        $menu_exists = wp_get_nav_menu_object($menuname);
        if( !$menu_exists ) 
        {
            $menu_id = wp_create_nav_menu($menuname);
            $category_menu_id = wp_update_nav_menu_item($menu_id, 0, array( "menu-item-title" => __('Categories','pinc'), "menu-item-url" => "#", "menu-item-status" => "publish" ));
            wp_update_nav_menu_item($menu_id, 0, array( "menu-item-title" => __('Popular','pinc'), "menu-item-url" => home_url("/popular/"), "menu-item-status" => "publish", "menu-item-parent-id" => $category_menu_id ));
            wp_update_nav_menu_item($menu_id, 0, array( "menu-item-title" => __('Everything','pinc'), "menu-item-url" => home_url("/everything/"), "menu-item-status" => "publish", "menu-item-parent-id" => $category_menu_id ));
            if( !has_nav_menu($bpmenulocation) ) 
            {
                $locations = get_theme_mod("nav_menu_locations");
                $locations[$menulocation] = $menu_id;
                set_theme_mod("nav_menu_locations", $locations);
            }

        }
        update_option("sidebars_widgets", array(  ));
        $pinc_users = get_users("orderby=ID");
        foreach( $pinc_users as $user ) 
        {
            $board_parent_id = get_user_meta($user->ID, "_Board Parent ID", true);
            if( $board_parent_id == "" ) 
            {
                $board_id = wp_insert_term($user->ID, "board");
                update_user_meta($user->ID, "_Board Parent ID", $board_id["term_id"]);
                update_user_meta($user->ID, "pinc_user_notify_likes", "1");
                update_user_meta($user->ID, "pinc_user_notify_repins", "1");
                update_user_meta($user->ID, "pinc_user_notify_follows", "1");
                update_user_meta($user->ID, "pinc_user_notify_comments", "1");
            }

        }
        // predefined colors
        if (get_locale() == 'fa_IR') {
            wp_insert_term('آبی', 'color', array('slug' => 'blue',));
            wp_insert_term('قرمز', 'color', array('slug' => 'red',));
            wp_insert_term('بنفش', 'color', array('slug' => 'violet',));
            wp_insert_term('خاکستری', 'color', array('slug' => 'gray',));
            wp_insert_term('زرد', 'color', array('slug' => 'yellow',));
            wp_insert_term('سبز', 'color', array('slug' => 'green',));
            wp_insert_term('سفید', 'color', array('slug' => 'white',));
            wp_insert_term('سیاه', 'color', array('slug' => 'black',));
            wp_insert_term('صورتی', 'color', array('slug' => 'pink',));
            wp_insert_term('نارنجی', 'color', array('slug' => 'orange',));
            wp_insert_term('قهوه ای', 'color', array('slug' => 'brown',));
            wp_insert_term('کرم', 'color', array('slug' => 'moccasin',));
        } else {
            wp_insert_term('Blue', 'color', array('slug' => 'blue',));
            wp_insert_term('Red', 'color', array('slug' => 'red',));
            wp_insert_term('Violet', 'color', array('slug' => 'violet',));
            wp_insert_term('Gray', 'color', array('slug' => 'gray',));
            wp_insert_term('Yellow', 'color', array('slug' => 'yellow',));
            wp_insert_term('Green', 'color', array('slug' => 'green',));
            wp_insert_term('White', 'color', array('slug' => 'white',));
            wp_insert_term('Black', 'color', array('slug' => 'black',));
            wp_insert_term('Pink', 'color', array('slug' => 'pink',));
            wp_insert_term('Orange', 'color', array('slug' => 'orange',));
            wp_insert_term('Brown', 'color', array('slug' => 'brown',));
            wp_insert_term('Moccasin', 'color', array('slug' => 'moccasin',));
        }

        
        update_option("pinc_version", "1.6");
        add_action("admin_notices", "pinc_admin_notices");
    }
    else
    {
        if( floatval($pinc_version) <= 1.5 ) 
        {
            update_option("pinc_version", "1.6");
        }

    }

}
// Upgrade theme 
$pinc_version = get_option("pinc_version");
if( floatval($pinc_version) <= 1.6 ){
    $sql = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "pinc_notifications` (\n\t\t\t`notification_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n\t\t\t`user_id` bigint(20) unsigned NOT NULL DEFAULT '0',\n\t\t\t`notification_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',\n\t\t\t`notification_type` varchar(255) NOT NULL,\n\t\t\t`notification_from` bigint(20) unsigned NOT NULL DEFAULT '0',\n\t\t\t`notification_post_id` bigint(20) unsigned NOT NULL DEFAULT '0',\n\t\t\t`notification_message` longtext NOT NULL,\n\t\t\t`notification_read` tinyint(1) unsigned NOT NULL DEFAULT '0',\n\t\t\tPRIMARY KEY (`notification_id`),\n\t\t\tKEY user_id (`user_id`)\n\t\t) CHARACTER SET utf8 COLLATE utf8_general_ci;";
    require_once(ABSPATH . "wp-admin/includes/upgrade.php");
    dbDelta($sql);
    $page = array( "post_title" => __('API/Developers','pinc'), "post_name" => "api", "post_author" => 1, "post_status" => "publish", "post_type" => "page", "comment_status" => "closed", "ping_status" => "closed" );
    $pageid = wp_insert_post($page);
    add_post_meta($pageid, "_wp_page_template", "api.php");
    update_option("pinc_version", "1.7");
}

function pinc_admin_notices()
{
    echo "<div class=\"error fade\"><p><strong>[Important Notice] <img width=\"20\" src=\"http://skilledup.ir/pinkoob.png\"> Click <a href=\"" . admin_url("themes.php?page=theme_installation") . "\">" . "HERE</a> to complete the necessary installation steps required for Pinkoob to run smoothly." . "</strong></div>";
}

function pinc_setup_guide()
{
    if( function_exists("add_options_page") ) 
    {
        add_theme_page("Theme Installation", "Theme Installation", "edit_theme_options", "theme_installation", "pinc_setup_guide_page");
    }

}

function pinc_setup_guide_page()
{
    echo "<style type=\"text/css\">\n.wrap ol li { margin-bottom:30px; width: 520px; }\n.wrap ul li { margin:3px 0 0 15px;list-style-type:disc; }\n.wrap hr { border:none;border-top:1px dashed #aaa;height:0;margin:10px 0 0 0; }\n</style>\n<div class=\"wrap\">\n\t";
    screen_icon();
    echo "    <h2>Theme Installation</h2>\n\t<hr />\n    <table class=\"form-table\"><tr><th>\n\t\t<div style=\"background: #fcfcfc; border: 1px solid #eee; padding: 15px; max-width: 550px; font-weight: 400 !important\">\n\t\t\t<strong>[SERVER REQUIREMENTS]</strong><br><br>(if any of them are disabled, please contact your hosting provider)\n\t\t\t<ul>\n\t\t\t\t<li>PHP Extension: Curl\n\t\t\t\t";
    if( extension_loaded("curl") ) 
    {
        echo "\t\t\t\t<span style=\"color: green; font-weight: bold; font-style:italic;\">enabled</span>\n\t\t\t\t";
    }
    else
    {
        $error_extension = true;
        echo "\t\t\t\t<span style=\"color: red; font-weight: bold; font-style:italic;\">not enabled!</span>\n\t\t\t\t";
    }

    echo "\t\t\t\t</li>\n\n\t\t\t\t<li>PHP Extension: Dom\n\t\t\t\t";
    if( extension_loaded("dom") ) 
    {
        echo "\t\t\t\t<span style=\"color: green; font-weight: bold; font-style:italic;\">enabled</span>\n\t\t\t\t";
    }
    else
    {
        $error_extension = true;
        echo "\t\t\t\t<span style=\"color: red; font-weight: bold; font-style:italic;\">not enabled!</span>\n\t\t\t\t";
    }

    echo "\t\t\t\t</li>\n\n\t\t\t\t<li>PHP Extension: Mbstring\n\t\t\t\t";
    if( extension_loaded("mbstring") ) 
    {
        echo "\t\t\t\t<span style=\"color: green; font-weight: bold; font-style:italic;\">enabled</span>\n\t\t\t\t";
    }
    else
    {
        $error_extension = true;
        echo "\t\t\t\t<span style=\"color: red; font-weight: bold; font-style:italic;\">not enabled!</span>\n\t\t\t\t";
    }

    echo "\t\t\t\t</li>\n\n\t\t\t\t<li>PHP Extension: GD/Imagemagick\n\t\t\t\t";
    if( extension_loaded("gd") || extension_loaded("imagemagick") ) 
    {
        echo "\t\t\t\t<span style=\"color: green; font-weight: bold; font-style:italic;\">enabled</span>\n\t\t\t\t";
    }
    else
    {
        $error_extension = true;
        echo "\t\t\t\t<span style=\"color: red; font-weight: bold; font-style:italic;\">not enabled!</span>\n\t\t\t\t";
    }

    echo "\t\t\t\t</li>\n\n\t\t\t\t<li>WP-Content Directory Permission\n\t\t\t\t";
    if( is_writable(WP_CONTENT_DIR) ) 
    {
        echo "\t\t\t\t<span style=\"color: green; font-weight: bold; font-style:italic;\">writable</span>\n\t\t\t\t";
    }
    else
    {
        echo "\t\t\t\t<span style=\"color: red; font-weight: bold; font-style:italic;\">not writable</span>\n\t\t\t\t";
    }

    echo "\t\t\t\t</li>\n\n\t\t\t\t";
    if( $error_extension ) 
    {
        echo "\t\t\t\t<p><span style=\"color: red; font-weight: bold; font-style:italic;\">Alert:</span> Required php extension not enabled. Please check with your host to enable them.</p>\n\t\t\t\t";
    }

    echo "\n\t\t\t\t";
    if( !is_writable(WP_CONTENT_DIR) ) 
    {
        echo "\t\t\t\t<p><span style=\"color: red; font-weight: bold; font-style:italic;\">Alert:</span> WP-Content directory (";
        echo WP_CONTENT_DIR;
        echo ") not writeable. Please change directory permission to 755 or 777. If 777 works, check with your host if it's possible to work with 755, which is safer.</p>\n\t\t\t\t";
    }

    echo "\n\t\t\t\t";
    if( !$error_extension && is_writable(WP_CONTENT_DIR) ) 
    {
        echo "\t\t\t\t<br><p><strong>Server checklist passed! Please proceed with the steps below.</strong></p>\n\t\t\t\t";
    }

    echo "\t\t\t</ul>\n\t\t</div>\n<br><br>\n\t\t<ol style=\"font-weight: 400 !important;\">\n\t\t\t<li>\n\t\t\t\tVisit <strong><a href=\"";
    echo admin_url("options-general.php");
    echo "\" target=\"_blank\">Settings > General</a></strong> and set:\n\t\t\t\t<ul>\n\t\t\t\t\t<li>Membership = <strong>Anyone can register (ticked)</strong></li>\n\t\t\t\t\t<li>New User Default Role = <strong>Author</strong></li>\n\t\t\t\t</ul>\n\t\t\t</li>\n\t\t\t<hr style=\"border:none;border-top:1px solid #ccc;color\" /><br><br>\n\t\t\t<li>\n\t\t\t\tVisit <strong><a href=\"";
    echo admin_url("options-reading.php");
    echo "\" target=\"_blank\">Settings > Reading</a></strong> and set:\n\t\t\t\t<ul>\n\t\t\t\t\t<li>Blog pages show at most = <strong>20</strong></li>\n\t\t\t\t</ul>\n\t\t\t</li>\n\t\t\t<hr style=\"border:none;border-top:1px solid #ccc;color\" /><br><br>\n\t\t\t<li>\n\t\t\t\tVisit <strong><a href=\"";
    echo admin_url("options-media.php");
    echo "\" target=\"_blank\">Settings > Media</a></strong> and set:\n\t\t\t\t<ul>\n\t\t\t\t\t<li>Medium size: Max Width = <strong>420</strong>, Max Height = <strong>4096</strong></li>\n\t\t\t\t\t<li>Large size: Max Width = <strong>700</strong>, Max Height = <strong>4096</strong></li>\n\t\t\t\t</ul>\n\t\t\t</li>\n\t\t\t<hr style=\"border:none;border-top:1px solid #ccc;color\" /><br><br>\n\t\t\t<li>\n\t\t\t\tVisit <strong><a href=\"";
    echo admin_url("options-permalink.php");
    echo "\" target=\"_blank\">Settings > Permalinks</a></strong> and set:\n\t\t\t\t<ul>\n\t\t\t\t\t<li>Custom Structure = <strong>/pin/%postname%/</strong></li>\n\t\t\t\t</ul>\n\t\t\t</li>\n\t\t\t<hr style=\"border:none;border-top:1px solid #ccc;color\" /><br><br>\n\t\t\t<li>\n\t\t\t\tVisit <strong><a href=\"";
    echo admin_url("edit-tags.php?taxonomy=category");
    echo "\" target=\"_blank\">Posts > Categories</a></strong>\n\t\t\t\t<ul>\n\t\t\t\t\t<li>Add your preferred categories there</li>\n\t\t\t\t</ul>\n\t\t\t</li>\n\t\t\t<hr style=\"border:none;border-top:1px solid #ccc;color\" /><br><br>\n\t\t\t<li>\n\t\t\t\tVisit <strong><a href=\"";
    echo admin_url("nav-menus.php");
    echo "\" target=\"_blank\">Appearance > Menus</a></strong>\n\t\t\t\t<ul>\n\t\t\t\t\t<li>From \"<strong>Categories</strong>\" box, select the categories you created earlier and click \"<strong>Add to Menu</strong>\". Drag the newly added items slightly to the right by <strong>drag&drop</strong> method will include them in your menu.</li>\n\t\t\t\t</ul>\n\t\t\t</li>\n\t\t\t<hr style=\"border:none;border-top:1px solid #ccc;color\" /><br><br>\n\t\t\t<li>\n\t\t\t\tVisit <strong><a href=\"";
    echo admin_url("themes.php?page=pinc");
    echo "\" target=\"_blank\">Appearance > Theme Options</a></strong>\n\t\t\t\t<ul>\n\t\t\t\t\t<li>Change options based on your preferences</li>\n\t\t\t\t\t<li>Take a look at F.A.Q section</li>\n\t\t\t\t</ul>\n\t\t\t</li>\n\t\t\t<hr style=\"border:none;border-top:1px solid #ccc;color\" /><br><br>\n\t\t\t<li>\n\t\t\t\tVisit <strong><a href=\"";
    global $current_user;
    global $wp_rewrite;
    echo home_url("/" . $wp_rewrite->author_base . "/" . $current_user->data->user_nicename . "/");
    echo "\" target=\"_blank\">";
    echo home_url("/" . $wp_rewrite->author_base . "/" . $current_user->data->user_nicename . "/");
    echo "</a></strong>\n\t\t\t\t<ul>\n\t\t\t\t\t<li>If you face a 404 error, go to <a href=\"";
    echo admin_url("options-permalink.php");
    echo "\" target=\"_blank\">Settings > Permalinks</a> and simply click \"Save Changes\" once again. It should display your profile.</li>\n\t\t\t\t</ul>\n\t\t\t</li>\n\t\t\t<hr style=\"border:none;border-top:1px solid #ccc;color\" /><br><br>\n\t\t\t<li>\n\t\t\t\tEdit <strong>wp-config.php</strong>\n\t\t\t\t<ul>\n\t\t\t\t\t<li>Open wp-config.php file for editing (from Wordpress root directory) and search for: <strong><em>define('WP_DEBUG', false);</em></strong><br><br>Below this line, add the following line:<br><br> <em><strong>define('EMPTY_TRASH_DAYS', 0);</strong></em></li>\n\t\t\t\t</ul>\n\t\t\t</li>\n\t\t\t<hr style=\"border:none;border-top:1px solid #ccc;color\" /><br><br>\n\t\t\t<li>\n\t\t\t\tInstall <strong>WP Social Login</strong> plugin (optional - for Social Media login)\n\t\t\t\t<ul>\n\t\t\t\t\t<li>See <a href=\"";
    echo admin_url("themes.php?page=pinc#options-group-3");
    echo "\" target=\"_blank\">F.A.Q</a> section, under \"Recommended Plugins\" on how to do it</li>\n\t\t\t\t</ul>\n\t\t\t</li>\n\t\t\t<hr style=\"border:none;border-top:1px solid #ccc;color\" /><br><br>\n\n\t\t\t\t<font color=\"#428bca\" size=\"3\"><br><br><strong><u><img width=\"40\" src=\"http://skilledup.ir/pinkoob.png\">Enjoy Pinkoob</u>!</font><br><br>Or continue below to setup a sideblog, optionally.</strong><br><br><br>\n\n\t\t\t<br><br><br>\n\t\t\t<li>\n\t\t\t\tVisit <strong><a href=\"";
    echo admin_url("edit-tags.php?taxonomy=category");
    echo "\" target=\"_blank\">Posts > Categories</a></strong>\n\t\t\t\t<ul>\n\t\t\t\t\t<li>Add a new category, e.g <strong>Blog</strong></li>\n\t\t\t\t</ul>\n\t\t\t</li>\n\t\t\t<hr style=\"border:none;border-top:1px solid #ccc;color\" /><br><br>\n\t\t\t<li>\n\t\t\t\tVisit <strong><a href=\"";
    echo admin_url("themes.php?page=pinc");
    echo "\" target=\"_blank\">Appearance > Theme Options</a></strong>\n\t\t\t\t<ul>\n\t\t\t\t\t<li>On dropdown named \"<strong>Category For Blog</strong>\", select the blog category you just created</li>\n\t\t\t\t</ul>\n\t\t\t</li>\n\t\t\t<hr style=\"border:none;border-top:1px solid #ccc;color\" /><br><br>\n\t\t\t<li>\n\t\t\t\tVisit <strong><a href=\"";
    echo admin_url("nav-menus.php");
    echo "\" target=\"_blank\">Appearance > Menus</a></strong>\n\t\t\t\t<ul>\n\t\t\t\t\t<li>From \"<strong>Categories</strong>\" tab on the left, select the blog category you just created and click \"<strong>Add to Menu</strong>\"</li>\n\t\t\t\t</ul>\n\t\t\t</li>\n\t\t\t<hr style=\"border:none;border-top:1px solid #ccc;color\" /><br><br>\n\t\t\t<li>\n\t\t\t\tVisit <strong><a href=\"";
    echo admin_url("post-new.php");
    echo "\" target=\"_blank\">Posts > Add New</a></strong>\n\t\t\t\t<ul>\n\t\t\t\t\t<li>Create your post and make sure to select \"<strong>Blog</strong>\" category on the categories checkbox list</strong></li>\n\t\t\t\t</ul>\n\t\t\t</li>\n\t\t</ol>\n    </th></tr></table>\n</div>\n";
}

function remove_page_from_query_string($query_string)
{
    if( $query_string["pagename"] == "itm-settings" && isset($query_string["title"]) ) 
    {
        unset($query_string["title"]);
    }

    return $query_string;
}

function change_password($return, $user_id = 0)
{
    $return = true;
    return $return;
}



<?php
/*
Template Name: _pinkoob_api
*/
function pincAPI($id = 0 , string $size = "large-size-dl"):string {
    if($id == "rnd"){
        $argss = array( 
            'orderby' => 'rand',
            'posts_per_page' => '1'
        );
        $loop = new WP_Query( $argss );
        while ( $loop->have_posts() ) : $loop->the_post();
            $rndId = get_the_ID();
        endwhile;
        $url_image = wp_get_attachment_image_src(get_post_thumbnail_id($rndId), $size)[0];

    }else{
        $url_image = wp_get_attachment_image_src(get_post_thumbnail_id($id), $size)[0];
    }
    return $url_image;
}

if (isset($_GET["pin"]) && !empty($_GET) && is_user_logged_in()) {
    switch ($_GET["size"]) {
        case "small":
            $getAPI = ["url" => pincAPI($_GET["pin"], 'small-size-dl'), "size" => "Small"];
        break;
        case "medium":
            $getAPI = ["url" => pincAPI($_GET["pin"], 'medium-size-dl'), "size" => "Medium"];
        break;
        case "large":
            $getAPI = ["url" => pincAPI($_GET["pin"], "large-size-dl"), "size" => "Large"];
        break;
        case "raw":
            $getAPI = ["url" => pincAPI($_GET["pin"]), "size" => "Raw"];
        break;
        default:
            $getAPI = ["url" => pincAPI($_GET["pin"]), "size" => "Raw"];
    }
    echo json_encode($getAPI);
} else {
    get_header();
    ?>
    <div calss="container-fluid">
        <h3>The most powerful photo engine in the world.</h3>
        <p>Welcome to the Official Pinkoob API. Create with the largest open collection of high-quality photos. For free.</p>
        <h4>Try it</h4>
        <a href="<?php echo home_url( '/api/?pin=rnd&size=small' ); ?>"><?php echo home_url( '/api/?pin=rnd&size=small' ); ?></a>
        <p>in the exapmle above "pin" is the post ID and "size" can be "small", "medium", "large" and "raw".<br>
        "size" is optinal and its default value is "raw" if it's not set.
        </p>
    <?php if (!is_user_logged_in()){ ?>
        <span class="alert alert-info">You need to login or create Account to use API!</span>
    </div>
        <?php
    }
    get_footer();
}

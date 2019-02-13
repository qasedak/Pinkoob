<?php
define('WP_USE_THEMES', false); 
require('../../../wp-blog-header.php');
?>
<html>
<head>
<meta charset="UTF-8" />
<title>Add Board for Existing Users</title>
<link href="<?php echo get_template_directory_uri(); ?>/css/bootstrap.css" rel="stylesheet">
<style>
body {
padding: 30px;
}
</style>
</head>
<body>
<div class="jumbotron">
<?php
if (current_user_can('manage_options')) {
	global $wpdb;
	
	if ($_GET['catname']) {
		$args = array(
			'meta_query' => array(
				array(
				'key' => 'boardadded',
				'compare' => 'NOT EXISTS'
				)
			),
			'orderby'      => 'ID',
			'number'       => '20',
		 );
		$pinc_users = get_users($args);

		if (!empty($pinc_users)) {
			foreach ($pinc_users as $user) {
				$board_parent_id = get_user_meta($user->ID, '_Board Parent ID', true);
				$boards_name = explode(',', $_GET['catname']);
				$category_id = explode(',', $_GET['catid']);
				
				$count = 0;
					
				foreach($boards_name as $board_name) {
					$board_name = sanitize_text_field($board_name);
					
					$board_category = sanitize_text_field($category_id[$count]);
					if (!$board_category)
						$board_category = 1;
	
					$board_id = wp_insert_term (
						$board_name,
						'board',
						array(
							'description' => $board_category,
							'parent' => $board_parent_id,
							'slug' => $board_name . '__pincboard'
						)
					);
					if (!is_wp_error($board_id)) {
						echo $board_name . ' board added to User ID: ' . $user->ID . '<br />';
					} else {
						echo $board_name . ' board exists for User ID: ' . $user->ID . '. Skipped...<br />';
					}
					$count++;
				}
				
				delete_option("board_children");
				update_user_meta($user->ID, 'boardadded', '1');
			}
			echo '<br /><a class="btn btn-success" href="' . $_SERVER['REQUEST_URI'] . '"><strong>Click to continue next batch...</strong></a>';
		} else {
			$wpdb->query(
				"
				DELETE FROM " . $wpdb->prefix . "usermeta
				WHERE meta_key = 'boardadded'
				"
			);
			echo '<br /><span class="alert alert-success">Completed! Please re-enable user registration by checking "Anyone can register" <a href="' . admin_url('options-general.php') . '" target="_blank">here</a>.</span><br />';
			echo '<br /><br /><span class="alert alert-success">Also remember to update "Auto Create These Boards for New Users" theme option <a href="' . admin_url('themes.php?page=pinc') . '" target="_blank">here</a>.</span><br />';
			echo '<br /><br /><span class="alert alert-success">To add more boards, <a href="' . strtok($_SERVER['REQUEST_URI'], '?') . '">click here</a>.</span><br />';
		}
	} else {
	?>
		<h3>Add Board for Existing Users</h3>
		<p>Please temporarily disable user registration by unchecking "Anyone can register" <a href="<?php echo admin_url('options-general.php'); ?>" target="_blank">here</a></p>
		<form method="get" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
			<label>Board Name:</label> <input class="form-control" type="text" name="catname" id="catename" value="" placeholder="e.g board1, board2, board3" /><br/>
			<label>Category ID:</label> <input class="form-control" type="text" name="catid" id="cateid" value="" placeholder="8, 11, 22" /><br />
			<input class="btn btn-success" type="submit" value="Submit" style="font-weight: bold;" />
		</form>
	<?php
	}
} else {
	echo '<span class="alert alert-warning">Please login as Administrator first...</span>';	
}
?>
</div>
</body>
</html>
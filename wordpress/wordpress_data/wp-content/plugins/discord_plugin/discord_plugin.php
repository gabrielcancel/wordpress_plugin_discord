<?php
/*
Plugin Name: Discord Plugin
Description: Plugin qui va envoyer une notif discord pour chaque nouveau commentaire
Author: Gabriel CANCEL
Version: 1.0
*/

add_action( 'admin_menu', 'dp_add_admin_menu' );
add_action( 'admin_init', 'dp_settings_init' );
add_action('comment_post', 'dp_send_autorized_message', 10, 2);



function dp_add_admin_menu() { 
	// add plugin menu into wordpress discord
	add_menu_page( 'discord plugin', 'discord plugin', 'manage_options', 'discord_plugin', 'dp_options_page' );

}


function dp_settings_init() { 
	// display plugin settings into the plugin params
	register_setting( 'pluginPage', 'dp_settings' );

	add_settings_section(
		'dp_pluginPage_section', 
		__( 'Welcome to Discord Plugin', 'discord' ), 
		'dp_settings_section_callback', 
		'pluginPage'
	);

	add_settings_field( 
		'dp_api_key', 
		__( 'Enter your discord WebHook: ', 'discord' ), 
		'dp_api_key_render', 
		'pluginPage', 
		'dp_pluginPage_section' 
	);


}


function dp_api_key_render() { 
	// get webhook
	$options = get_option( 'dp_settings' );
	?>
	<input type='text' name='dp_settings[dp_api_key]' value='<?php echo $options['dp_api_key']; ?>'>
	<?php

}


function dp_settings_section_callback() { 

	echo __( '', 'discord' );

}


function dp_options_page() { 
		// option page
		?>
		<form action='options.php' method='post'>

			<?php
			settings_fields( 'pluginPage' );
			do_settings_sections( 'pluginPage' );
			submit_button();
			?>

		</form>
		<?php

}

function dp_send_message($message, $webhook, $level) {
    $timestamp = date("c", strtotime("now"));
	$color = "";

	// differentiation of colors according to the level of the result
	if ($level == 3) {
		$color = "#FF0000";
	} elseif ($level == 2) {
		$color = "#FFA600";
	} elseif ($level == 1 ) {
		$color = "#FFDC00";
	} else {
		$color = "#3EFF00";		
	};

	// data send to webhook
    $data = json_encode([
		"type" => "rich",
        'tts' => false,
        'embeds' => [[
			"title" => "PHP - Send message to Discord via Webhook",
            'description' => $message->comment_content . "\nauthor: " . $message->comment_author . "\nemail: " . $message->comment_author_email . "\nurl: " . $message->comment_author_url,
            'timestamp' => $timestamp,
            'color' => hexdec($color),
            
        ]]
    ]);
		
    $ch = curl_init($webhook);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_exec($ch);
    // Close the cURL handler
    curl_close($ch);
}

function dp_send_autorized_message($id, $is_admin) {
	$comment = get_comment($id);
	$content = $comment->comment_content;

	// execute python analyze with textblob
	$command = escapeshellcmd('python3 /var/www/html/wp-content/plugins/discord_plugin/main.py "' . $content . '"');
	$output = shell_exec($command);
	$level = comment_analyze($output);
	// if ($level == 3) {
	// 	mail_alert($comment);
	// }

    if (!$is_admin) {
		// send message when author is not an admin
        dp_send_message($comment, get_option('dp_settings')['dp_api_key'],$level);
		
    } 
}


function comment_analyze($content) {
	// processes the result of the analysis
	if($content < -0.7) {
		return 3;
	} else if ($content < -0.3) {
		return 2;
	} else if ($content < 0) {
		return 1;
	} else {
		return 0;
	}
}

// function mail_alert($comment) {
// 	$email = $comment->comment_author_email;
// 	$to = "\<" . $email . "\>";
// 	var_dump($to);
//     $subject = 'Warning Message';
//     $message = "Abusive content ! Your account will be blocked";
// 	$t = mail($to, $subject, $message);
// 	var_dump($t);
// }

?>
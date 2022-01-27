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



function dp_add_admin_menu(  ) { 

	add_menu_page( 'discord plugin', 'discord plugin', 'manage_options', 'discord_plugin', 'dp_options_page' );

}


function dp_settings_init(  ) { 

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


function dp_api_key_render(  ) { 

	$options = get_option( 'dp_settings' );
	?>
	<input type='text' name='dp_settings[dp_api_key]' value='<?php echo $options['dp_api_key']; ?>'>
	<?php

}


function dp_settings_section_callback(  ) { 

	echo __( '', 'discord' );

}


function dp_options_page(  ) { 

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

function dp_send_message($message, $webhook) {
    $timestamp = date("c", strtotime("now"));

    $data = json_encode([
        
        'tts' => false,
        'embeds' => [[
            'title' => "New Comment on the web-site",
            #'type' => "rich",
            'description' => $message->comment_content,
            'timestamp' => $timestamp,
            'color' => hexdec( "3366ff" ),
            
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
	var_dump($content);
	$command = escapeshellcmd('python3 /var/www/html/wp-content/plugins/discord_plugin/main.py "' . $content . '"');
	$output = shell_exec($command);
	var_dump($output);
	
    if (!$is_admin) {
        dp_send_message($comment, get_option('dp_settings')['dp_api_key']);
    } 
}
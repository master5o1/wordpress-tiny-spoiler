<?php
/*
Plugin Name: Tiny Spoiler Fork
Plugin URI: https://github.com/master5o1/wordpress-tiny-spoiler
Description: Modified Tony Spoiler to allow bbPress 2.0 and bbPress Post Toolbar integration.
Version: 0.3
Author: Jason Schwarzenberger
*/

add_shortcode('spoiler', 'spoiler_shortcode');

add_filter( 'bbp_get_reply_content', 'spoiler_bbpress_shortcode' );
add_filter( 'bbp_5o1_toolbar_add_items' , 'spoiler_add_to_post_toolbar' );

add_action('wp_head', 'insert_spoiler_css');
add_action('wp_footer', 'insert_spoiler_js');

function insert_spoiler_css()
{
	echo "<style type='text/css'>
	.spoiler {
		-webkit-border-radius: 3px;-khtml-border-radius: 3px;-moz-border-radius: 3px;-o-border-radius: 3px; border-radius: 3px;
		border: solid 1px #e5e5e5;
		padding: 0; margin: 1em;
		background-color: #f3f3f3;
	}
	.spoiler div.spoiler-closed,
	.spoiler div.spoiler-open	{
		-webkit-user-select: none;-khtml-user-select: none;-moz-user-select: none;-o-user-select: none; user-select: none;
		text-align: center; font-weight: bold; text-transform: lowercase; font-variant: small-caps;
		height: 1.70em; line-height: 1.70em; padding: 0; margin: 0;
		border: none; cursor: pointer;
	}
	.spoiler div.spoiler-open:hover,
	.spoiler div.spoiler-closed:hover {
		background-color: #f5f5f5;
	}
	.spoiler div.spoiler-content {
		background-color: #f9f9f9;
		line-height: 1.4em; padding: 0.5em 1.0em; margin: 0;
		border: none; border-top: solid 1px #e5e5e5;
		display: none; overflow: hidden;
	}
	</style>\n";
}

function insert_spoiler_js()
{
	echo <<<ENDJS
	<script type='text/javascript'>
	function tiny_spoiler( id, elem )
	{
		if ( document.getElementById( id ).style.display == 'block' )
		{
			document.getElementById( id ).style.display = 'none';
			elem.innerHTML = elem.innerHTML.replace('Hide', 'Show');
			elem.setAttribute('class', 'spoiler-closed');
		}
		else
		{
			document.getElementById( id ).style.display = 'block';
			elem.innerHTML = elem.innerHTML.replace('Show', 'Hide');
			elem.setAttribute('class', 'spoiler-open');
		}
	}
	</script>
ENDJS;
}

function replace_spoiler_tag( $content, $name )
{
	if (empty($content)) return $content; // nothing to hide so nothing to do.
	$addition = '';
	for ( $i = 0; $i < 10; $i++ )
		$addition .= chr( rand( ord('a'), ord('z') ) );
	$id = str_replace(' ', '', trim($name)) . $addition;
	$name = trim($name);
	$s = '<div class="spoiler">'
	.	'<div class="spoiler-closed" onclick="tiny_spoiler(\''.$id.'\', this)">Show ' . $name . '</div>'
	.	'<div class="spoiler-content" id="'.$id.'">'.do_shortcode(trim($content)).'</div>'
	.	'</div>';
	return $s;
}

function spoiler_bbpress_shortcode($content) {
	$shortcode_tags = array( 'spoiler' => 'spoiler_shortcode' );
	if (empty($shortcode_tags) || !is_array($shortcode_tags))
		return $content;
	$tagnames = array_keys($shortcode_tags);
	$tagregexp = join( '|', array_map('preg_quote', $tagnames) );
	$pattern = '(.?)\[('.$tagregexp.')\b(.*?)(?:(\/))?\](?:(.+?)\[\/\2\])?(.?)';
	return preg_replace_callback('/'.$pattern.'/s', 'do_shortcode_tag', $content);
}

function spoiler_add_to_post_toolbar($items) {
	$items[] = array(
		'action' => 'api_item',
		'inside_anchor' => '<img src="' . plugins_url( '/spoiler_btn.png', __FILE__ ) . '" title="Spoiler" alt="Spoiler" />',
		'data' => "function(stack){insertShortcode(stack, 'spoiler', []);}");
	return $items;
}

function spoiler_shortcode( $atts, $content )
{
	extract(shortcode_atts(array(
		'name' => 'spoiler'
	), $atts));
	return replace_spoiler_tag( $content, $name );
}

?>
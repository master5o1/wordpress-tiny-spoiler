<?php
/*
Plugin Name: Tiny Spoiler
Plugin URI: http://wordpress.org/extend/plugins/tiny-spoiler/
Description: Modified Tony Spoiler to allow bbPress 2.0.
Version: 999.0.2
Author: Tom Braider, master5o1
*/


/**
 * adds stylesheet
 */
function insert_spoiler_css()
{
	echo "<style type='text/css'>
	.spoiler { border: 1px #eee solid; background-color: #f3f3f3; padding: 0; margin: 2px auto;}
	.spoiler legend { width: 0em; height: 1.70em; padding: 0; margin: -23px -3px; border: none; }
	.spoiler legend input { width: 7.3em; display: inline-block; margin: -0.1em 1.3em auto -1.6em; padding: 0; background-color: #f5f5f5; border: solid 1px #e5e5e5; border-radius: 3px; -moz-border-radius: 3px; -webkit-border-radius: 3px; cursor: pointer; }
	.spoiler legend input.open,
	.spoiler legend input:hover { background-color: #f9f9f9; }
	.spoiler div { line-height: 1.4em; padding: 0; margin: -10px -4px -22px; display: none; overflow: hidden; }
	</style>\n";
}

add_action('wp_head', 'insert_spoiler_css');



/**
 * adds javascript
 */
function insert_spoiler_js()
{
	echo <<<ENDJS
	<script type='text/javascript'>
	function tiny_spoiler( id, elem )
	{
		if ( document.getElementById( id ).style.display == 'block' )
		{
			document.getElementById( id ).style.display = 'none';
			document.getElementById( id ).style.padding = '0';
			elem.value = elem.value.replace('Hide', 'Show');
			elem.setAttribute('class', '');
		}
		else
		{
			document.getElementById( id ).style.display = 'block';
			document.getElementById( id ).style.padding = '5px 0';
			elem.value = elem.value.replace('Show', 'Hide');
			elem.setAttribute('class', 'open');
		}
	}
	</script>
ENDJS;
}

add_action('wp_footer', 'insert_spoiler_js');



/**
 * creates spoiler code
 *
 * @param string $content spoiler content
 * @param string $name title
 * @return string spoiler code
 */
function replace_spoiler_tag( $content, $name )
{
	$caracteres = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
	$addition = '';
	for ( $i = 0; $i < 10; $i++ )
		$addition .= $caracteres[rand(0,25)];
	$id = str_replace(' ', '', $name).$addition;
	//id="'.$id.'_button" 
	$s = '<fieldset class="spoiler">
			<legend>
				<input type="button" onclick="tiny_spoiler(\''.$id.'\', this)" value="Show Spoiler" />
			</legend>
			<div id="'.$id.'">'.$content.'</div>
		</fieldset>';		
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

add_filter( 'bbp_get_reply_content', 'spoiler_bbpress_shortcode' );


function spoiler_add_to_post_toolbar($items) {
	$items[] = array( 'action' => 'api_item',
						 'inside_anchor' => '<img src="' . plugins_url( '/spoiler_btn.png', __FILE__ ) . '" title="Spoiler" alt="Spoiler" />',
						 'data' => "function(stack){insertShortcode(stack, 'spoiler', []);}");
	return $items;
}

add_filter( 'bbp_5o1_toolbar_add_items' , 'spoiler_add_to_post_toolbar' );

/**
 * parses parameters
 *
 * @param string $atts parameters
 * @param string $content spoiler content
 * @return unknown
 */
function spoiler_shortcode( $atts, $content )
{
	extract(shortcode_atts(array(
		'name' => 'spoiler'
	), $atts));
	return replace_spoiler_tag( $content, $name );
}

add_shortcode('spoiler', 'spoiler_shortcode');

?>
<?php
/*
Plugin Name: Tiny Spoiler
Plugin URI: http://www.tomsdimension.de/wp-plugins/tiny-spoiler
Description: [spoiler name="top secret"]shows/hides this text[/spoiler]
Version: 0.2
Author: Tom Braider
Author URI: http://www.tomsdimension.de
*/


/**
 * adds stylesheet
 */
function insert_spoiler_css()
{
	echo "<style type='text/css'>
	.spoiler { border: 1px #000 dashed; }
	.spoiler legend { padding-right: 5px; background: white;  }
	.spoiler legend input { width: 30px; }
	.spoiler div { margin: 0px; overflow: hidden; height: 0; }
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
	function tiny_spoiler( id )
	{
		if ( document.getElementById( id ).style.height == 'auto' )
		{
			document.getElementById( id ).style.height = 0;
			document.getElementById( id ).style.padding = 0;
			document.getElementById( id + '_button' ).value = '+';
		}
		else
		{
			document.getElementById( id ).style.height = 'auto';
			document.getElementById( id ).style.padding = '10px';
			document.getElementById( id + '_button' ).value = '-';
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
	$s = '<fieldset class="spoiler">
			<legend>
				<input type="button" onclick="tiny_spoiler(\''.$id.'\')" id="'.$id.'_button" value="+" />
				'.$name.'
			</legend>
			<div id="'.$id.'">'
				.$content.'
			</div>
		</fieldset>';		
	return $s;
}



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
		'name' => 'Spoiler'
	), $atts));
	return replace_spoiler_tag( $content, $name );
}

add_shortcode('spoiler', 'spoiler_shortcode');

?>
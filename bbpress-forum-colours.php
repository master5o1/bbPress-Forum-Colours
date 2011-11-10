<?php
/**
 * Plugin Name: bbPress Forum Colours
 * Plugin URI: http://master5o1.com/
 * Description: Forum colours shown before the topic title on topic listings pages.
 * Version: 0.1
 * Author: Jason Schwarzenberger
 * Author URI: http://master5o1.com/
 */
/*  Copyright 2011  Jason Schwarzenberger  (email : jason@master5o1.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
http://www.phpied.com/files/rgbcolor/rgbcolor.js
Might use this to parse some colours and validate the input.

Also, eventual modification could make it images and/or colours.
*/

add_action( 'bbp_theme_before_topic_title', array('bbp_forum_colours', 'before_topic_title') );
add_action( 'bbp_theme_before_forum_title', array('bbp_forum_colours', 'before_forum_title') );

add_action( 'bbp_forum_metabox', array('bbp_forum_colours', 'forum_attributes_metabox') );
add_action( 'bbp_forum_attributes_metabox_save', array('bbp_forum_colours', 'forum_attributes_metabox_save') );

add_filter( 'bbp_admin_forums_column_headers', array('bbp_forum_colours', 'forum_colour_columns') );
add_action( 'bbp_admin_forums_column_data', array('bbp_forum_colours', 'forum_colour_column'), 10, 2 );

// add_filter( 'bbp_admin_replies_column_headers', array('bbp_forum_colours', 'forum_colour_columns') );
// add_action( 'bbp_admin_replies_column_data', array('bbp_forum_colours', 'reply_colour_column'), 10, 2 );

// add_filter( 'bbp_admin_topics_column_headers', array('bbp_forum_colours', 'forum_colour_columns') );
// add_action( 'bbp_admin_topics_column_data', array('bbp_forum_colours', 'topic_colour_column'), 10, 2 );

class bbp_forum_colours {

	function forum_attributes_metabox( $forum_id ) {
		$colour = bbp_forum_colours::get_forum_colour( $forum_id );
		$js = "var that = document.getElementById('forum_colour_preview'); that.style.backgroundColor = this.value;";
		?>
		<hr />
		<p>
			<strong class="label">Colour:</strong>
			<input name="forum_colour_value" type="text" id="forum_colour_value" value="<?php echo $colour; ?>" onkeyup="<?php echo $js; ?>" onchange="<?php echo $js; ?>" />
			<span id="forum_colour_preview" style="width: 1.7em; height: 1.7em; vertical-align: middle; display: inline-block; border: solid 1px #ccc; background-color: <?php echo $colour; ?>; margin: 0 0 0 0; -moz-border-radius: 2px; -webkit-border-radius: 2px; border-radius: 2px;"></span>
		</p>
		<p>
			<small>Forum colour is used as a visual identifier other than the title of the forum.  It is displayed as a small box next to topic titles in the listings.</small><br />
			<small>Use values such as <code>#F00</code>, <code>#FF0000</code>, <code>red</code> or <code>rgb(255,0,0)</code>.</small><br />
			<small>Clear the text or make it <code>transparent</code> to unset the colour.</small>
		</p>
		<?php
	}
	
	function forum_attributes_metabox_save( $forum_id ) {
		update_post_meta($forum_id, 'bbp_forum_colours', $_POST['forum_colour_value']);
	}
	
	function get_forum_colour( $forum_id=0 ) {
		$colour = get_post_meta($forum_id, 'bbp_forum_colours', true);
		if ( empty($forum_id) || empty($colour) )
			return 'transparent';
		return $colour;
	}

	function before_title($forum_id) {
		$colour = bbp_forum_colours::get_forum_colour( $forum_id );
		$permalink = bbp_get_forum_permalink( $forum_id );
		$border = 'border: solid 1px ' . $colour . ';';
		if ( $colour == 'transparent' ) {
			$border = 'border: solid 1px #ccc; ';
		}
		echo '<a href="' . $permalink . '" style="width: 1.0em; height: 1.0em; vertical-align: middle; display: inline-block;' . $border . ' background-color: ' . $colour . '; margin: 0 0.25em 0 0; -moz-border-radius: 2px; -webkit-border-radius: 2px; border-radius: 2px;"></a>';
	}
	
	function before_topic_title() {
		bbp_forum_colours::before_title( bbp_get_topic_forum_id() );
	}
	
	function before_forum_title() {
		bbp_forum_colours::before_title( bbp_get_forum_id() );
	}
	
	function forum_colour_columns($columns) {
		$columns['bbp_forum_colour'] = __( 'Colour', 'bbp_forum_colours');
		return $columns;
	}
	
	function forum_column($forum_id) {
		$colour = bbp_forum_colours::get_forum_colour( $forum_id );
		$permalink = bbp_get_forum_permalink( $forum_id );
		$border = 'border: solid 1px ' . $colour . ';';
		if ( $colour == 'transparent' ) {
			$border = 'border: solid 1px #ccc; ';
		}
		echo '<span style="width: 3.0em; height: 3.0em; display: inline-block;' . $border . ' background-color: ' . $colour . '; margin: 0; -moz-border-radius: 2px; -webkit-border-radius: 2px; border-radius: 2px;"></span>';
	}
	
	function forum_colour_column($column, $forum_id) {
		if ($column != 'bbp_forum_colour') return;
		bbp_forum_colours::forum_column($forum_id);
	}
	
	function reply_colour_column($column, $reply_id) {
		if ($column != 'bbp_forum_colour') return;
		bbp_forum_colours::forum_column( bbp_get_reply_forum_id($reply_id) );
	}
	
	function topic_colour_column($column, $topic_id) {
		if ($column != 'bbp_forum_colour') return;
		bbp_forum_colours::forum_column( bbp_get_topic_forum_id($topic_id) );
	}

}
?>
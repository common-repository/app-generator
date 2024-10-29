<?php
/*
Plugin Name: App Generator
Plugin Script: appmaker.php
Description: Create free applictions for Android devices.
Version: 1.0
License: GPL
Author: app-generator.net
Author URI: http://app-generator.net

=== RELEASE NOTES ===
2013-05-22 - v1.0 - first version
*/

/* 
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
Online: http://www.gnu.org/licenses/gpl.txt
*/

add_action('init','my_feed');
function my_feed() {
add_feed('app-generator','ffs');
global $wp_rewrite;
$wp_rewrite->flush_rules();
}

function ffs() {
//FEEDAUSGABE
$numposts = get_option('wp_merq_appmaker_anzahl'); // number of posts in feed
$posts = query_posts('showposts='.$numposts.'&cat=');
$more = 1;

header('Content-Type: '.feed_content_type('rss-http').'; charset='.get_option('blog_charset'), true);
echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>';
?>

<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
	<?php do_action('rss2_ns'); ?>
>
<channel>
	<title><?php bloginfo_rss('name'); wp_title_rss(); ?></title>
	<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
	<link><?php bloginfo_rss('url') ?></link>
	<description><?php bloginfo_rss("description") ?></description>
	<lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
	<?php the_generator( 'rss2' ); ?>
	<language><?php echo get_option('rss_language'); ?></language>
	<sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
	<sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
	<?php do_action('rss2_head'); ?>
	<?php while( have_posts()) : the_post(); ?>

	<item>
		<title><?php the_title_rss(); ?></title>
		<link><?php the_permalink_rss(); ?></link>
		<comments><?php comments_link(); ?></comments>
		<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
		<dc:creator><?php the_author(); ?></dc:creator>
<?php the_category_rss(); ?>
		<guid isPermaLink="false"><?php the_guid(); ?></guid>
<?php if (get_option('rss_use_excerpt')) : ?>

		<description><![CDATA[<?php the_content() ?>]]></description>
<?php else : ?>

		<description><![CDATA[<?php the_content() ?>]]></description>
	<?php if ( strlen( $post->post_content ) > 0 ) : ?>

		<content:encoded><![CDATA[<?php the_content() ?>]]></content:encoded>
	<?php else : ?>

		<content:encoded><![CDATA[<?php the_excerpt_rss() ?>]]></content:encoded>
	<?php endif; ?>
<?php endif; ?>

		<wfw:commentRss><?php echo get_post_comments_feed_link(); ?></wfw:commentRss>
		<slash:comments><?php echo get_comments_number(); ?></slash:comments>
<?php rss_enclosure(); ?>
<?php do_action('rss2_item'); ?>

	</item>
	<?php endwhile; ?>

</channel>
</rss>
<?php
//FEEDAUSGABE ENDE
exit;
}


add_action('admin_menu', 'my_plugin_menu');

function my_plugin_menu() {
	add_options_page('Preferences', 'App Generator', 'manage_options', 'appmaker', 'my_plugin_options');
}

function my_plugin_options() {


    //must check that the user has the required capability 
    if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }


    if( $_POST['anzahl'] != '' ) {
	update_option('wp_merq_appmaker_anzahl',$_POST['anzahl']);


?>
<div class="updated"><p><strong><?php _e('Changes saved.', 'menu-start' ); ?></strong></p></div>
<?php

    }

if (get_option('wp_merq_appmaker_anzahl') == "") {
update_option('wp_merq_appmaker_anzahl',"20");
}	
	
if (get_option('wp_merq_appmaker') == "") {

$pool = "qwertzupasdfghkyxcvbnm23456789WERTZUPLKJHGFDSAYXCVBNM";
srand ((double)microtime()*1000000);
for($index = 0; $index < 6; $index++) { $pass_word .= substr($pool,(rand()%(strlen ($pool))), 1); }

update_option('wp_merq_appmaker',$pass_word);

}	
	
    // Now display the settings editing screen

    echo '<div class="wrap">';

    // header

    echo "<h2>" . __( 'Preferences: App Generator | app-generator.net', 'menu-start' ) . "</h2>";

    // settings form
    
?>
<h3>Welcome</h3>

Using this plugin you can create your own application for Android devices. The app includes inter alia the last article of your blog.<br /><br />
1. free registration at <a href="http://app-generator.net" target="_blank">app-generator.net</a><br />
2. create a new app at app-generator.net and put in the url that listed below (RSS FEED).<br />
3. the application ready - you could link the app here on your blog!<br /><br />
URL (RSS FEED): <b><?php echo network_site_url( '/' )."feed/app-generator/"; ?><br/><br/></b>

<form name="form1" method="post" action="">

<p>Number of posts that should be displayed in the app: 
<input type="text" name="anzahl" value="<?php echo get_option('wp_merq_appmaker_anzahl'); ?>" size="5">
</p>
<i>Hint: Changes are visible in the app after 30 minutes!</i>

<hr />

<p style="float:left;" class="submit">
<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
</p>

<p style="float:right;">
<a target="_blank" href="http://app-generator.net"><img width="200" src="http://app-generator.net/app-generator.png"></a>
</p>

</form>
</div>

<?php
}



//add_filter('comments_template', 'no_comments_on_page');
function no_comments_on_page( $file )
{
    if ( is_page() == false ) {
        $file = dirname( __FILE__ ) . '/file.php';
		
    }
    return $file;
}

?>
<?php
include_once(dirname(__FILE__)."/include/twitter.inc.php");
include_once(dirname(__FILE__)."/include/facebook.inc.php");
include_once(dirname(__FILE__)."/include/myspace.inc.php");

$sl_options = $sl->GetOptions();
?>

<script type='text/javascript' src='<?php bloginfo('url'); ?>/wp-content/plugins/socialite/js/scriptaculous/lib/prototype.js'></script>
<script type='text/javascript' src='<?php bloginfo('url'); ?>/wp-content/plugins/socialite/js/scriptaculous/src/scriptaculous.js'></script>
<script type='text/javascript' src='<?php bloginfo('url'); ?>/wp-content/plugins/socialite/js/scriptaculous/src/effects.js'></script>
<script type='text/javascript' src='<?php bloginfo('url'); ?>/wp-content/plugins/socialite/js/socialite.js'></script>

<?php if($_POST["action"] == "update") : ?>
<div id="message" class="updated fade" style="background-color: rgb(255, 251, 204);">
	<p>Socialite Options <strong>Saved</strong>.</p>
</div>
<?php endif; ?>

<div id="div_left">
	<form method="post">
	<?php wp_nonce_field('update-options'); ?>

	<div id="options_general">
	<?php include(dirname(__FILE__)."/options_general.inc.php"); ?>
	</div>

	<div id="options_twitter">
	<?php include(dirname(__FILE__)."/options_twitter.inc.php"); ?>
	</div>

	<div id="options_facebook">
	<?php include(dirname(__FILE__)."/options_facebook.inc.php"); ?>
	</div>
	
	<div id="options_myspace">
	<?php include(dirname(__FILE__)."/options_myspace.inc.php"); ?>
	</div>

	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="socialite[initialized]" value=1 />

	<p class="submit">
	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>

	</form>
</div> <!-- id=div_left -->
<div id="div_right">
	<table border=0 cellspacing=0 cellpadding=0 width=200>
	<tr>
		<td class="td_header">Donate Via Paypal</td>
	</tr>
	<tr>
		<td class="td_content">
			<div align="center">
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="hidden" name="hosted_button_id" value="3944609">
				<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
				<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
				</form>
			</div>

			<a href="http://www.gilfether.com/socialite" target="_blank">Socialite</a>
			is free and open source. Help keep it alive by donating as little or as much as you want.
			I'm a struggle software programmer and any little bit you can donate will go a long way to help!
			<br /><br />
			Thank You,<br />
			<a href="http://www.gilfether.com" target="_blank">Ryan Gilfether</a>
		</td>
	</tr>
	</table>

	<table border=0 cellspacing=0 cellpadding=0 width=200>
	<tr>
		<td class="td_header">Socialite</td>
	</tr>
	<tr>
		<td class="td_content">
			<img src="<?= get_bloginfo('siteurl'); ?>/wp-content/plugins/socialite/images/socialite-45x45.jpg" border=0 style="float: left; margin-right: 2px" />
			<a href="http://www.gilfether.com/socialite" target="_blank">Socialite</a> is an open source
			Wordpress plugin that allows you to publish your posts on Twitter, Facebook and MySpace.
			Please visit the <a href="http://www.gilfether.com/socialite" target="_blank">Official Socialite Homepage</a>
			for the latest updates and support questions.
			<br /><br />
			I do my best to answer all support questions, but unfortunately I have to develop this plugin in my spare
			time, so please be patient.
			<br /><br />
			<a href="http://www.gilfether.com">Ryan Gilfether</a>
		</td>
	</tr>
	</table>

	<table border=0 cellspacing=0 cellpadding=0 width=200>
	<tr>
		<td class="td_header">College-Pages.com</td>
	</tr>
	<tr>
		<td class="td_content">
			<div align="center">
				<a href="http://www.college-pages.com" target="_blank">
				<img src="<?php bloginfo('url'); ?>/wp-content/plugins/socialite/images/college-pages-logo-300x96.png" width="198" border=0 />
				</a>
			</div>

			<a href="http://www.college-pages.com" target="_blank">College-Pages.com</a> is a leading provider
			of online college education information. We have all the answers to your distance learning questions.
			Request free information from any college or university on our site. Read our daily news articles, and
			participate in our college education forums.
			<br /><br />
			What are you waiting for? <a href="http://www.college-pages.com">Get Started Today!</a>
			<p>
				<a href="http://twitter.com/collegepages" target="_blank"><img src="<?php bloginfo('url'); ?>/wp-content/plugins/socialite/images/large-grey-twitter.png" border=0 /></a>
				<br />
				<a href="http://www.facebook.com/pages/College-Pagescom/194249306160" target="_blank"><img src="<?php bloginfo('url'); ?>/wp-content/plugins/socialite/images/large-grey-facebook.png" border=0 /></a>
			</p>
		</td>
	</tr>
	</table>
</div> <!-- id=div_right -->

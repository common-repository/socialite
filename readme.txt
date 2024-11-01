=== Socialite ===
Contributors: brap
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3944609
Tags: publish,post,plugin,Twitter,Facebook,MySpace,social,social network,post to Twitter,post to Facebook,post to MySpace,publish to Twitter,publish to Facebook,publish to MySpace
Requires at least: 2.2
Tested up to: 2.9
Stable tag: 0.2.5.2

Publishes your Wordpress posts to Twitter, Facebook and MySpace.

== Description ==

Socialite allows your Wordpress posts to publish to Twitter, Facebook, and
MySpace. Each social networking site can be enabled or disabled for
publishing, and each is configured separately with their own options. Support
for Short URL services such as zz.gd and Tinyurl.com is also supported.

<a href="http://www.gilfether.com/socialite">Visit the Socialite Homepage for More Information</a>

A few notes about this plugin:

* <strong>IMPORTANT</strong>: Socialite requires PHP 5.0.0 or greater. The Facebook API was
written for PHP5, due to this fact, I wrote Socialite using PHP5 syntax. Furthermore PHP 4
was retired on Dec 31, 2007 and it won't be supported. <strong>If you are getting
<u>parse errors</u> when attempting to use Socialite, this is because you are not using
PHP5.</strong>

* <strong>IMPORTANT</strong>: MySpace does not have an official API that
supports posting to the MySpace blog. This means cURL had to be used to emulate
logging in and posting to the MySpace blog. IF YOU DO NOT HAVE THE PHP CURL
OPTIONS INSTALLED ON YOUR SERVER, THE MYSPACE MODULE WILL NOT WORK!<br />
Please read
<a href="http://www.php.net/manual/en/curl.setup.php">http://www.php.net/manual/en/curl.setup.php</a>
for more information

* <strong>IMPORTANT</strong>: If you are encountering problems posting to Facebook, please either
change the URL Shortening service you have selected, or uncheck the URL Shortening checkbox under
the Facebook options. Facebook blocks and filters some urls from posting to a Facebook Wall, this
includes some URL Shortening URLs, and prevents Socialite from posting to your Wall.

* The Twitter and Facebook modules are written using each networks official
API, so barring any mistakes on my part, both modules should be pretty stable.


== Installation ==

1. Socialite is distributed in zip compression format. Unpack the socialite.zip
file. A folder called socialite should be created.

2. Move the socialite folder into your Wordpress plugins directory, located at
wp-content/plugins. When you have done this, you should have a the folder
wp-content/plugins/socialite

3. Log into Wordpress as an administrator or as a user that can activate
Wordpress plugins. Go to the Plugins menu and activate the Socialite plugin.

4. As the administrator, go to the Settings Wordpress Menu,
and click on the Socialite menu option. Complete the Socialite Configuration
for all the options you want set, and click on the 'save' button at the bottom
of the screen.

5. Now if you are feeling generous, there is a Donation link on the right hand
side of the Socialite Configuration page. I'm a struggling free lance
programmer and develop and maintain this plugin in my spare time. Any amount
you can donate will help, even if its enough for me to pick up a value meal at
a fast food resturant so I can continue working on this plugin through the
night into the next morning! It also helps the server fee which hosts this
plugin.

== Frequently Asked Questions ==

= Do I have to use all three Social Networking sites at once? =

No. You can turn Twitter, Facebook, and MySpace on or off as you wish.

= Does the plugin publish the entire post, or an excerpt? =

Twitter only allows a one line story of 140 characters to be posted. This can
be configured by you.

Facebook will allow you to post the title, the link, and any additional text you
wish to add. This is configurable by you.

MySpace allows you to post anything you want, the full story, the excerpt, both,
or neither!

= Does the Facebook module allow me to post to my profile page or fan page? =

As of Socialite 0.2.4 Socialite now allows you to post to Facebook fan page, or
your personal Facebook page. This can be set up after entering your login information
for Facebook in the Socialite configuration screen. Please note that in order
to post to a page other than your personal profile page, you must be an admin of
the page. Also note that when you are following the steps to activate your
Facebook login for Socialite, the Facebook authentication steps will ask you which
pages you wish to enable Socialite on. It is important you enable Socialite on the page(s)
you intend to publish to, otherwise it will appear as though Socialite is not working.
You can always click the 'Reset Facebook Login' button in the Socialite configuration
screen to start the Facebook authentication process over if you made a mistake.

= Status updates and/or Links are not posting to the Facebook Wall =

It appears Facebook has implemented some kind of content filter. Some URLs are blocked
by Facebook with no rhyme or reason, this may include some URL Shortening URLs. If you
are having trouble posting to Facebook, try changing the URL Shortening Service you are
using or uncheck the Shorten URLs option under the Facebook Options in Socialite.

= Can I configure the way the published post looks on each site? =

Yes. Socialite has templates you set up in the Configuration screen that allows
you to set up how your post will look on each social networking site.

= What version of PHP will this plugin work with? =

PHP 5.0.0 and later is supported. PHP 4 will not work. If you are getting parse
errors when you attempt to use this plugin, it is because you are not using PHP5.

= Are there any special PHP modules I need installed? =

The MySpace module requires you to have cURL installed for PHP. Please read
<a href="http://www.php.net/manual/en/curl.setup.php">http://www.php.net/manual/en/curl.setup.php</a>
for more information.

The Twitter and Facebook modules do not require any additional PHP modules.


== Screenshots ==

1. The Socialite configuration screen in Wordpress 2.7


== License ==

Socialite is released under Version 3 of the GNU General Public License.
For more information please read
<a href="http://www.gnu.org/copyleft/gpl.html">http://www.gnu.org/copyleft/gpl.html</a>
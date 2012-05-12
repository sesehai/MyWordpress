=== WP-PostViews Plus ===
Contributors: Richer Yang
Tags: views,hits,counter,postviews,bot,user
Requires at least: 3.0.0
Tested up to: 3.3.2
Stable tag: 1.2.10

Enables You To Display How Many Times A Post Had Been Viewed By User Or Bot.

== Description ==
It can set that if count the registered member views OR views in index page.
To differentiate between USER and BOT is by HTTP_agent, and it can set at admin

== Installation ==
1. Upload 'postviews_plus' directory to the '/wp-content/plugins/' directory.
2. Activate the plugin 'WP-PostViews Plus' through the 'Plugins' menu in WordPress.
3. Place the show views function in your templates. [function reference](http://wwpteach.com/wp-postviews-plus/2 "function reference")

= Usage =
You need edit you theme to show the post views.
Add `<?php if(function_exists('the_views')) { the_views(); } ?>` to show the post views in your page.

== Screenshots ==
1. Using page
2. setting page
3. widget setting page

== Frequently Asked Questions ==
Please visit the [plugin page](http://wwpteach.com/wp-postviews-plus " ") with any questions.

== Changelog ==
Please move to [plugin change log](http://wwpteach.com/wp-postviews-plus/history " ")

=== Fullscreen Gallery ===
Contributors: CodeKitchen, markoheijnen, Felipe Seré
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=CQFB8UMDTEGGG
Tags: gallery, post, fullscreen
Requires at least: 3.6
Tested up to: 4.0
Stable tag: 0.2
License: GPLv2 or later

A simple plugin that adds a fullscreen endpoint to display the gallery images

== Description ==

At this moment it only adds a gallery endpoint to posts with only one theme. In the next couple of weeks I will be looking at more solutions and build themes around those. I will then also start building a settings page where you can select what the default theme is and build a metabox where you can select per post what the options should be.

A demo can be seen at https://fesere.de/. If you go to https://fesere.de/african-wildlife/ you see it's a gallery provided by the theme and gets extended by this plugin. So when you go to https://fesere.de/african-wildlife/?fullscreen you see the same images but without the rest of the site. The plugin is so build that it should easily integrate in your existing theme.

== Installation ==

1. Upload the folder `fullscreen-gallery` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Now go and watch this plugin by going to a post with a gallery and add `/fullscreen` to the url.

== Frequently Asked Questions ==

Nothing yet

== Screenshots ==

Nothing yet

== Changelog ==

= 0.2.0 ( 2014-09-14 ) =
* Add settings page
* Add prev/next button support
* Ability to hide the back button
* Ability to change the image size
* Smoother animation when CSS3 can be used
* Add 'fullscreen_gallery_args' filter
* Complete rewrite of template system
* Fix glitches in configuration

= 0.1.0 ( 2014-09-04 ) =
* First version 
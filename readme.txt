=== Plugin Name ===
Contributors: neilGurung
Plugin Name: Ezy Front Image Uploader
Plugin URI: http://www.wordpress.org/ezy-front-image-uploader
Tags: 1.0.0.1
Author URI: http://www.neil.com.np/
Author: Neil Gurung
Donate link: http://www.neil.com.np/
Requires at least: 3.0
Tested up to: 4.2.2
Stable tag: 1.0.0.1
Version: 1.0.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

What is Frontend Uploader?

This plugin is a simplest way for upload & attached images to posts or pages of your site.
The plugin uses a set of shortcodes to let you create highly customizable slider image & image upload options to your posts and pages.It’s that easy!


5 Steps to using Frontend Uploader

Install the plugin.
Insert the Ezy Frontend Image Uploader shortcode into a post. The basic form can be inserted using [ezy_upload uploadtype="s" multiple_image_upload = "0"], please visit the FAQs for more complex forms.
Users can attach images to post or pages by clicking attach button.
Users can also hide form by click hide button.
To display slider to all attached image into post or pages by [ezy_slider] shortcode.
Your user generated media is live.
Exploring Customizations

You can modify the submission form as needed, and have users submit posts. Please visit the FAQ page for more information.
This plugin can be applied to Posts, Pages, and Custom Post Types. You can enable this via Settings > Frontend Uploader Settings.
In addition to the WordPress whitelisted file types, this also supports uploading of Microsoft Office and Adobe files, as well as various video and audio files. You can enable these file types via Settings > Frontend Uploader Settings.
The plugin allows you to upload all filetypes that are whitelisted in WordPress. If you’d like to add more file types and are comfortable with theme development, there's a filter that'll allow you to add some exotic MIME-type.
You can also get recent attached id to posts or pages by using $ezyUpload->recentAttachIds as an array which you can used in any form.


== Installation ==

Upload ezy-front-image-uploader to the /wp-content/plugins/ directory
Activate the plugin through the 'Plugins' menu in WordPress
Use the following shortcode in post or page: [ezy_upload],[ezy_slider]
Moderate user posts in Posts 

== Upgrade Notice ==

Still working on it..

== Screenshots ==

Default media upload form

Dynamic media upload form

ezy slider

== Changelog ==

Initial release and well written readme

== Frequently Asked Questions ==

Shortcode parameters
Customizing Your Form with Shortcode Parameters

[ezy_upload] contains two attributes as an options.Both of them are explained as below:

uploadtype => It contains two options either using simple file upload by "s" or dynamic form
	      by providing "a".

multiple_image_upload => This attribute for multiple image/single image upload.

For eg: [ezy_upload uploadtype="s" multiple_image_upload = "0"]


If you will be going to use it on your form then after attaching images to posts
or pages.you can get all recent attached ids by using $ezyUpload->recentAttachIds
where $ezyUpload is the global variable.

== Donations ==

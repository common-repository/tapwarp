=== Plugin Name ===
Contributors: yoihito
Donate link: http://qaon.net/tap-warp-donation-page
Tags: photo, upload
Requires at least: 4.0
Tested up to: 4.5.3
Stable tag: trunk
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

TapWarp Plugin allows you upload photos from your mobile device to media library by a simple QR-code scan.

== Description ==

TapWarp is a simple, easy-to-use app for uploading pictures to websites.

It helps you accelerate picture uploading, since with TapWarp you no longer need to move pictures to computers, select them, and then upload them. TapWarp give you a one-stop solution. You take photos and then directly upload them to a website by simply scanning a QR code.

You can download WordPress plugin from the demo site.

With TapWarp, not only you can take photo and send them to websites efficiently, but you can also enlarge it, rotate it, translate it, so that you can view its very details clearly.

When do you need TapWarp?

Large community sites often have such apps, so you do not need TapWarp in this case. But if you own a website and need to upload images often, then TapWarp is your best friend.

For example, you have an e-commerce site, then you will often need to upload pictures of goods. Especially if you sell original goods or used goods, then you have to take photos by yourself, because no other people can prepare a set of uniform pictures and share with you. The process of upload is in fact quite tedious. According to requirements of different websites, you may need to transfer images to a computer, resize the images, then upload them to a specific page through a browser.

But if the website supports TapWarp API, it will become easy, you only need to scan a QR-code with TapWarp, images will be uploaded. Size, etc, can be encoded in QR codes, the app will automatically transform images and then upload them. Thus totally removes operations such as manual image edit. Meanwhile, websites can directly receive images within specs, remove the burden of further image processing.

TapWarp Plugin allows you upload photos from your mobile device to wordpress very easily.  It integrates into Media Library.  Your uploaded images will appear automatically in Media Library.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/tapwarp` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use the Settings->TapWarp screen to configure the plugin

== Frequently Asked Questions ==

= What are the steps to use TapWarp? =

1. You need to install the TapWarp app, which can be found on App Store or Google Play.
2. Take some photos by TapWarp app, or select photos from your gallery.
3. Open your wordpress Media Library.
4. Tap the send button, and scan the QR code on your display.
5. Wait several seconds, so that images are all sent.

Your uploaded pictures will gradually appear in you Media Library.
Note that you must log in as a user who can upload images to see images gradually appear in media library.

= What if I am asked to make a directory writable? =

It means that you may need to set permission of the directory to 775 or something like.

On *nix system, it is often a command: chmod 775 dirpath .

Refer to the following page for more information:
https://codex.wordpress.org/Changing_File_Permissions

== Screenshots ==

1. Media Library
2. Option Page

== Changelog ==

= 0.1.8 =

Support universal link.

= 0.1.7 =

Added text copy function.  Click the QR code to find it.

= 0.1.6 =

Added a loading icon, in case that the QR-code is not visible.
You blog name will be sent to the Tap Warp app, and can be displayed for distinguishing purpose.  (Supported by TapWarp app 1.1.3 or above.)

= 0.1.5 =

Fixed a layout problem.

= 0.1.4 =

Change location of local image pool.

= 0.1.3 =

Added a rotating now-loading icon.

= 0.1.2 =

Fixed some potential bug.
Removed some trash code.

= 0.1.1 =

This is the first version.

== Upgrade Notice ==

= 0.1.1 =
This is the first version.  So no upgrade.


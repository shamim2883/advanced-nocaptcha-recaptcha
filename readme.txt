=== Advanced noCaptcha & invisible Captcha ===
Contributors: shamim51
Tags: recaptcha,nocaptcha,invisible,no captcha,bot,spam,captcha,woocommerce captcha,woocommerce nocaptcha, woocommerce,widget,plugin,sidebar,shortcode,page,posts,comments,google,bbpress,multisite,multiple
Donate link: https://www.paypal.me/hasanshamim
Requires at least: 4.4
Tested up to: 4.9.7
Stable tag: 2.7
Requires PHP: 5.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Show noCaptchan or invisible captcha in Comment (after Comment textarea before submit button), CF7, bbpress, woocommerce, Login, Register, Lost & Reset Password.

== Description ==

Show noCaptcha or invisible captcha in Comment Form (after Comment textarea before submit button), Contact Form 7, bbPress, woocommerce, Login, Register, Lost Password, Reset Password. Also can implement in any other form easily.

* **Allow multiple captcha in same page.**
* **Allow conditional login captcha** (you can set after how many failed login attempts login captcha will show)

= Show noCaptcha on =

* Comment Form (after Comment textarea before submit button)
* WooCommerce
* Login
* Register
* Multisite User Signup
* Lost Password
* Reset Password
* Contact Form 7
* FEP Contact Form
* bbPress New topic
* bbPress reply to topic

= Options =

* Language can be changed
* Theme can be changed
* Size can be changed or set as invisible
* Badge location can be changed
* Error message can be changed
* Option to show/hide captcha for logged in users
* Captcha will show if javascript disabled also (optional)

= Privacy Notices =

* This plugin send IP address go Google for captcha verification. Please read [Google Privacy Policy](https://policies.google.com/).
* If you set "Show login Captcha after how many failed attempts" to more than 0(zero) then user hash from ip address will be stored in database. After successful login, hash of that ip address will be deleted. 

== Installation ==
1. Upload "advanced-nocaptcha-recaptcha" to the "/wp-content/plugins/" directory.
1. Activate the plugin through the "Plugins" menu in WordPress.
1. Go to plugin settings page for setup.


== Frequently Asked Questions ==

= Can i use this plugin to my language? =
Yes. this plugin is translate ready. But If your language is not available you can make one. If you want to help us to translate this plugin to your language you are welcome.

= Can i show multiple captcha in same page? =
Yes. You can show unlimited number of captcha in same page.

= How to set Invisible captcha? =
Make sure to obtain key for invisible captcha from google. Go to Dashboard > Settings > Advanced noCaptcha & invisible captcha > Size ( Set as Invisible )

= How to set captcha in contact form 7? =
To show noCaptcha use [anr_nocaptcha g-recaptcha-response]


== Screenshots ==

1. Captcha in comment form
2. Captcha in Contact Form 7
3. Captcha in WooCommerce (multiple in same page)
4. Captcha in Login Form
5. Captcha in Register Form
6. Captcha in Lost Password Form
7. Advanced noCaptcha reCaptcha Settings
8. Advanced noCaptcha reCaptcha Setup Instruction

== Changelog ==

= 2.7 =

* Fix: Settings page checkbox uncheck was not working.

= 2.6 =

* New: Show captcha after set failed login attempts (may not work if you use ajax based login form, fall back to show always).
* Fix: contact form 7 deprecated function use.

= 2.5 =

* New: Invisible captcha feature added.
* Fix: Show captcha error when login form loaded
* Move this plugin settings page under Settings

= 2.4 =

* Bug fix: WooCommerce lostpassword corrupted link

= 2.3 =

* Comment form captcha issue fixed.
* Captcha now wraped in anr_captcha_field div class.
* Comment form captcha p tag removed.

= 2.2 =

* Security update.
* WooCommerce checkout form issue fixed.

= 2.1 =

* Captcha in WooCommerce added (WooCommerce Login, Registration, Lost password, Reset password forms).
* Allow multiple captcha in same page.
* Text domain changed.
* Some minor bug fixed.

= 1.3 =

* New filter 'anr_same_settings_for_all_sites' added, Now same settings can be used for all sites in Multisite.
* Multisite User Signup Form added.
* Some bug fixed.

= 1.2 =

* Now captcha size can be changed.
* bbPress New topic added
* bbPress reply to topic added
* XMLRPC_REQUEST Check
* Some bug fixed.

= 1.1 =

* Initial release.

== Upgrade Notice ==

= 2.7 =

* Fix: Settings page checkbox uncheck was not working.

= 2.6 =

* New: Show captcha after set failed login attempts (may not work if you use ajax based login form, fall back to show always).
* Fix: contact form 7 deprecated function use.

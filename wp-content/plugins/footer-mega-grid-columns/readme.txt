=== Footer Mega Grid Columns ===
Tags: footer, footer widgets, footer widgets in grid, website footer, simple footer editor, mega footer, megafooter
Contributors: wponlinesupport, anoopranawat, pratik-jain
Requires at least: 3.5
Tested up to: 5.1.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Footer Mega Grid Columns - Register a footer widget area for your theme and allow you to add and display footer widgets in grid view with multiple columns

== Description ==
Is your footer stuck in the default "1 or 2 columns" that came with your theme?

Footer Mega Grid Columns is a free plugin which allows you to create footer areas in grid depending upon the requirement of your theme.

Footer Mega Grid Columns - Register a footer widget area for your theme and allow you to add and display footer widgets in grid view with multiple columns.

The site footer is a valuable piece of site real estate, often containing important lead generating items such as mailchimp and social. A well designed footer can be a tremendous benefit.

Check footer [DEMO](https://www.wponlinesupport.com/wp-plugin/footer-mega-grid-columns/) | [PRO DEMO](https://www.wponlinesupport.com/wp-plugin/footer-mega-grid-columns/) for additional information.

= How to display footer grid =
Add the following code in your footer.php 
<pre><code><?php if( function_exists('slbd_display_widgets') ) { echo slbd_display_widgets(); } ?></code></pre>

= Features =
* Add a Footer widget ie Footer Mega Grid Columns .
* Display all widgets in grid 1,2,3,4 etc under Footer Mega Grid Columns.
* Can be used with most of the themes.
* Third party widget can be added.

= How to install : =
[youtube https://www.youtube.com/watch?v=52Q0IHcnxVo] 


== Installation ==

1. Upload the 'footer-mega-grid-columns' folder to the '/wp-content/plugins/' directory.
2. Activate the "Footer Mega Grid Columns" list plugin through the 'Plugins' menu in WordPress.
3. Check you Widget section for widget name Footer Mega Grid Columns.
4. Add the following code in your footer.php file under <code><footer></code> tag.
<pre>
 if( function_exists('slbd_display_widgets') ) { echo slbd_display_widgets(); }
</pre>
= How to install : =
[youtube https://www.youtube.com/watch?v=52Q0IHcnxVo] 

== Frequently Asked Questions ==

= Footer is displaying in the full width. How to add in wrap? =

Yes. We have added a CSS class - 'footer-mega-col-wrap' and given a width 100%. You can take the class in your theme style.css file OR in custom css section.
Ues like this 
<code>.footer-mega-col-wrap{max-width:1100px;}</code>

== Screenshots ==

1. Widget
2. Footer with 3 col
3. Footer with 4 col

== Changelog ==

= 1.1.1 (28-10-2017) =
* [+] Added support for 5 columns

= 1.0.1 (31-01-2017) =
* [+] Added ::after and ::before to .footer-mega-col class
* [+] Added .footer-mega-col-wrap new class under footer-mega-col class

= 1.0 =
* Initial release.
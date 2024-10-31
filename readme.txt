=== Responsive Food and Drink Menu ===
Contributors: corporatezen222
Tags: food, drink, menu, responsive, restaurant, cafe, drinks, restaurant menu
Requires at least: 4.0
Tested up to: 5.0.2
Stable tag: 2.3
License: GPL3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.en.html

Quicky and easily create a responsive food or drink menu for your business, or use your existing PDF menus to display your menus in various layouts, in any location.

== Description ==
Quicky and easily create a responsive food or drink menu for your business, or use your existing PDF menus to display your menus in various layouts, in any location. 

== Installation ==
1. Upload the un-zipped directory to the "/wp-content/plugins/" directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Now you are ready to create your first menu by going to "Menus" -> "Add New"

== Usage ==
Once the plugin is activated, you will see "Menus" as an option on the admin side, along with "PDF Menus".

These two types of posts are slightly different:

Menus - This post type lets you create or re-create a menu from scratch. You can add sections such as Soups, Salads, Entrees, whatever you want. Each section can have an unlimited number of menu items with prices and descriptions. You can display this menu in various layouts as well - just change the "Default Layout" dropdown on the right.
If your menu is in PDF format, you can upload it via the "Optional PDF Menu" metabox. Uploading a PDF will create a button on the front end which will link to the PDF menu in a new tab.

PDF Menu - If your menu is already in PDF format, we can handle that as well! All you do is enter the title of the menu, upload your PDF, and choose an optional image to represent your menu. These PDF menus can also be displayed in multiple layouts.

== Frequently Asked Questions ==
Q: How do I display the menu on my website?
A: You accomplish this using shortcodes. Shortcodes are super handy tools that allow you to display complex content quickly and easily. You simply copy and paste the supplied shortcode anywhere you want - a post, a page, a widget, or anything else.

For non-PDF menus, use this shortcode where you choose an individual menu to display in your chosen layout: [display_menu p=1234] (REMEMBER TO USE YOUR ID IN PLACE OF "1234"). Remember to choose your layout on the edit page of the menu. The default is the "Classic" layout.
For PDF menus, use this shortcode to display all your menus where you choose a layout: [display_pdf_menus]

The default is layout 1 - Full Width, but all of these are vaild:
[display_pdf_menus]
[display_pdf_menus layout=1]
[display_pdf_menus layout=2]
[display_pdf_menus layout=3]

Q: What kinds of layouts are available?
A: Regular Menus have 3 layouts - A classic menu look, A menu displaying your menu's featured image, and a modern grid layout.

PDF Menus have 3 layouts too - A full width layout, a grid layout with circular images, and a grid layout with square images

Q: My menus are in PDF format, can I still use this plugin?
A: Yes! Use our "PDF Menu" post type to use your existing PDF menu on your website.

Q: What kind of settings or customizations are available?
A: Once the plugin is activated, from the dashboard, you can navigate to "Settings" -> "RFADM Display Options" to customize the menu display. This page lets you choose which HTML tags to use for each menu's various pieces. It is not recommended to change these unless you are familiar with HTML and your theme's CSS rules. In case you need them, the default values are:
Menu Title       - h3
Menu Header      - p
Section Title    - h4
Item Title       - p
Item Price       - h5
Item Description - h6
Menu Footer      - p

== Changelog ==
1.0 Initial Release

1.1 General bugfixes

1.2 Added item images and descriptions to the menu

1.3 Bugfixes and css updates to the UI on the admin side

2.0 Major update. New layouts, added a new post type, and new shortcode functionality. Added functionality for PDF menus. Made css and all code more theme friendly. Removed bootstrap code and used custom css code instead.


=== Multiple Term Selection Widget ===
Contributors: xDe6ug
Donate link: http://wiboo.fr/wordpress
Tags: widget, plugin, term, custom-post, taxonomy, sidebar, drill, search, criteria, drop-down
Requires at least: 3.4
Tested up to: 3.8
Stable tag: 1.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This widget makes it easy for your site users to drill down into your Wordpress custom-post taxonomies.

== Description ==

This plugin is an initiative of the **Wiboo Agency**. For more info [visit our website](http://wiboo.fr/wordpress "Wiboo").

= Description =

Turn your Wordpress custom-post taxonomies into a search powerhouse! This plugin gives you the ability to provide your users with a widget full of dropdowns based upon parent terms (criterias) and their sub-terms (options).

For example, if you have multiple countrie terms in a country parent term, you can search throw custom-post by selecting one of the countries.

You can use this plugin as a widget or in a content with a shortcode.

= Features =
* Custom post choice
* Taxonomy choice
* Terms selection (parents/criterias and children/options), can manually sort them
* Automatic addition of the new terms/options in the selection, or not, with alphabetical re-ordering or not
* Simple or multiple selection
* Narrow search (all of the criterias) or broaden search (any of the criterias)
* Let the user choose the search type
* Ordering of results by default or by title
* Generates complete URL
* Does a proper URL-Rewrite if permalinks are enabled
* Pagination
* Search without selection : display all or do nothing
* Can hide empty terms (ie: without related custom-posts)
* If the empty terms are hidden and after a narrow search, disable terms which give no results if added to the previous search
* The results are displayed as custom-post archive
* Can use jQuery plugin Select2 or not
* Can use a shortcode

Thanks to [Zackdesign's Multiple Category Selection Widget plugin](http://wp.zackdesign.biz/category-selection-widget/) which gives the basic idea.

== Installation ==

1. Upload the 'multiple-term-selection-widget' folder to the '/wp-content/plugins/ directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Create multiple terms and subterms in a taxinomy
4. Add as many instances of this widget you like to your sidebars or use a shortcode

== Changelog ==

= 1.0.2 =
* Improved code
* Documentation added
* Undefined index when there's not at least one parent term and one child term in the taxonomy: Bug fixed

= 1.0.1 =
* Bug fixed
* Improved code

= 1.0 =
* Ok go on

= 0.9 =
* Better ergonomy in back office
* Add sorting of the terms
* Add multiple selection
* Add alphabetical sorting
* Add jQuery Select2
* Add management of disabled terms after search

= 0.5 =
* First version

== Documentation ==

= Description =

Multiple Term Selection Widget allows you to add one (or more) search widget(s) on your site. The search looks for posts (or custom posts) according to the terms belonging to a taxonomy-related . The results are displayed on the archive page of posts (or custom posts). The taxonomy must be hierarchical.

= Principle =

In a hierarchical taxonomy (category or custom taxonomy - but not tags) related to posts (or  custom posts), you must create one or more parent terms whose names match one or more research themes (eg city, country, color, price, etc ...). Then, you have to create children terms as options of these themes.

At the creation of a widget instance, choose the post type, taxonomy, parent and children terms to be used. The plugin does the rest. When a user selects one or more terms and executes a search, the posts related to the chosen terms are displayed.

= Settings =

In the menu Settings > Multiple Term Selection Widget, you can manage the global settings of the plugin and specially those for the widget related to the shortcode.
In the Widgets menu, add an instance from Multi-Term-Selection and configure it.

= Widget settings =

* **Id**: widget id (automatic – unmodifiable)
* **Title**: widget title
* **Post Type**: select the post type on which to search (only post types with a hierarchical taxonomy are available)
* **Taxonomy**: select the taxonomy used to display search options (only hierarchical taxonomies related to the chosen post type are available)
* **Included Terms**: check the parent terms (research themes), and then check or uncheck the children terms (search options). The parent terms are draggabled, as the children term. If you uncheck then check a parent term, the children terms are all checked again and automatically alphabetically reordered
* **Automatically add new children terms**: if after creating an instance of the widget, new children terms of the used taxonomy  are added by a site manager, they can be automatically integrated into the selection of  children terms (yes), with replacement of terms in alphabetical order (yes with alphabetical reordering ), or may not be integrated (no)
* **Multi-selection**: you can select multiple options (children terms) simultaneously in the same theme (parent term). The search is performed on any of these options (broaden search)
* **Search Type**: you can let the user choose the type of search
* **Default Search Type**: research can be broaden so that at least one of the selected options in a theme is related to the posts displayed (any) or narrow so at least one of the selected options for each theme is related to posts displayed (all)
* **Blank Search Results**: if no option is selected and the search is launched, all posts can be displayed (all) or the current page is reloaded without changing anything (none)
* **Ordering**: the posts results can be displayed in the default order (by default) or be classified by title (by title)
* **Hide empty terms**: you can hide children terms (options) that are not related to any post. If you choose yes to this configuration and make narrow research, then the options that would not bring any result (no post matches the already selected options and the present option) are disabled (grayed out)
* **Submit Button Text**: submit button text

= Permalink =

*Default permalink form*:

'http://[home]/?post_type=[post type name]&taxonomy=[taxonomy name]&terms=[children terms ids (comma-separated options) from themes (semicolon separated)]&search_type=[search tyep]&order=[order]&paged=[pagination]'

*Custom permalink form*:

'http://[home]/[post_type]/[taxonomy]/[terms]/[children terms ids (comma-separated options) from themes (semicolon separated)]/search_type/[search type]/order/[order]/paged/[pagination]'
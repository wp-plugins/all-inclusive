=== Plugin Name ===
Contributors: markjaquith, mdawaffe (this should be a list of wordpress.org userid's)
Donate link: http://example.com/
Tags:  Post, posts, thumbnail, meta, image, images, picture pictures, integration, theme, themes, thumbnail, meta
Requires at least: 3.0.0
Tested up to: 3.0.1
Stable tag: trunk

The pluginplug-in, with two additional SQL queries, joins the original data sample $wp_query->posts all meta fields, all files and images.

== Description ==
The pluginplug-in, with two additional SQL queries, joins the original data sample $wp_query->posts all meta fields, all files and images, for each image the direct url to small, medium, and large image is formed. There’s no need to use functions the_post_thumbnail  and get_post_meta.
 = How it works =

So let’s say we go to the home page of our blog, WordPress gives us a list of the latest 10 records. Let me remind you these are already in the global object in the array $wp_query-> posts. Let’s begin from creating a list of ID (number of records to which they are stored in the database).

= Form the first SQL query: =
* $query = "SELECT $wpdb->posts.* FROM $wpdb->posts WHERE  $wpdb->posts.post_parent IN('21','29','30','35','38','42','46','48','49','55') AND $wpdb->posts.post_parent
   AND $wpdb->posts.post_type = 'attachment'";

A normal translation is «find all the child entries for entries with the numbers (’21 ‘, ’29′, ’30 ‘, ’35′, ’38 ‘, ’42′, ’46 ‘, ’48′, ’49 ‘, ’55 ‘)». And the type of records required to be «attachment». That is, in short, we find all the files (attachment) which we downloaded for our 10 entries.

Again we form a list of ID: the first 10 ID entries and add the ID of files (attachments). Attachments as ordinary records are stored in one table – wp_posts.

= Now we use the function update_meta_cache: =
* $all_meta = update_meta_cache('post', $ar_all_id);

$ar_all_id – here are the original recordings and attachments

So everything just gets in the cache and subsequent calls get_post_meta will derive value from the cache. The rest is a matter of technique – the data must be carefully arranged, not mixing anything up. All meta fields are added to the $post->meta array, and files into an $post->files array and files are immediately sorted by number order (when downloading it, you can specify the sort order).
In addition, for each file, if it is an image, the full path to the thumbnails is calculated: small (thumbnail), average (medium) and the complete picture (full).

[Home page](http://ili.com.ua/wordpress/all-inclusive-en.html#Works)
[Details](http://ili.com.ua/wordpress/all-inclusive-en.html#Details)  

== Installation ==

1. Upload `all-inclusive.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

= Access to metadata fields recording =
$post->meta['meta_name']
 = Get files =
* $post->files[0]->attachment_url Direct Link
* $post->files[0]->guid the first file parameter guid is the same direct link

= Get images and thumbnail =
* $post->files[0]->thumbnail First Image Thumbnail
* $post->files[1]->thumbnail second file is a miniature
* $post->files[2]->medium the third file is the average miniature
* $post->files[3]->full fourth file is a complete picture

[More PHP code Examples](http://ili.com.ua/wordpress/all-inclusive-en.html#Examples)




== Changelog ==
= 1.0 =
* Initial version
= 1.0.1 =
* Meta field get through function update_meta_cache()


== A brief Markdown Example ==
[Examples](http://ili.com.ua/wordpress/all-inclusive-en.html#Examples)
[Details](http://ili.com.ua/wordpress/all-inclusive-en.html#Details)
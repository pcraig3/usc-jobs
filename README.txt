=== USC Jobs ===
Contributors: pcraig3
Donate link: http://example.com/
Tags: comments, spam
Requires at least: 3.5.1
Tested up to: 3.6
Stable tag: 0.8.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Plugin creates a new 'Jobs' Custom Post Type -- expects the Admin Page Framework to be included.

== Description ==

Plugin creates a new 'Jobs' Custom Post Type -- expects the Admin Page Framework to be included.

List of features we want:

*   Create a Job.
*   List Jobs on the site.
*   Search, filter, whatever the jobs

== Installation ==

This section describes how to install the plugin and get it working.

== Frequently Asked Questions ==

= Give me a job. =

No.  Maybe once we're at 0.9.0


== Changelog ==

= 0.8.0 =
* Took bloody ages, but I think it's paid off.
* JavaScript (for those who have it, which is most people) takes out the sidebar widgets and does sorting and filtering.
* UI more or less finalized.

= 0.7.3 =
* Jobs Archive now works just about perfectly before we start mucking about with the JavaScript.

= 0.7.2 =
* Moved 'internship' to the list of positions rather than a top-level category.  Archive retrieval still works.

= 0.7.1 =
* Initial stab at a Jobs listing archive.  No JS or anything yet, and have to modify the USC_Jobs data structure a bit.

= 0.7.0 =
* First real template for westernusc (have to put it in the theme rather than the plugin), and no more job_description textarea

= 0.6.3 =
* Ripped out a bunch of crappy JS, and updated code for remuneration archives.

= 0.6.2 =
* I think we're done enough of it to move onward.  Not styled yet, but functionally it looks okay.

= 0.6.1 =
* Transferring it over is unbelievably painful.

= 0.6.0 =
* Filter JS is working pretty awesomely for testwestern site.  We'll see how badly it breaks on the westernusc one.

= 0.5.1 =
* YES!  Loading a filterjs list of usc_jobs post titles.

= 0.5.0 =
* Loading all my filter_js stuff, and figured out (without implementing it) how to get job posts as a JSON array.

= 0.4.5 =
* Used some rewrite rules and a bit of template wizardry to make more archives.

= 0.4.4 =
* Figured out the archive for my custom Departments Taxonomy.

= 0.4.3 =
* Getting the template for single USC Jobs has been revolutionized along the lines of Stephen Harris' content-modifying method.

= 0.4.2 =
* USC Jobs have a bad custom archive layout instead of the general one.

= 0.4.1 =
* USC Jobs have a bad layout instead of a horrendous one from before.

= 0.4.0 =
* USC Jobs are closer to finalized, after getting input from Chris Noble and Cassandra.
* Department Taxonomies now listed as checkboxes when creating a new Job
* Validation works, though perhaps not ideally.
* Cleaned up some of the PHP and the JS.

= 0.3.0 =
* USC Jobs are some resemblance of what a job post might look like.

= 0.2.0 =
* USC Jobs shows up for public / admin users.

= 0.1.0 =
* Trying to get a Custom Post built with the Advance Page Framework to show up.


== Updates ==

The basic structure of this plugin was cloned from the [WordPress-Plugin-Boilerplate](https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate) project.
This plugin supports the [GitHub Updater](https://github.com/afragen/github-updater) plugin, so if you install that, this plugin becomes automatically updateable direct from GitHub. Any submission to WP.org repo will make this redundant.

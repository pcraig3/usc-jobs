=== USC Jobs ===
Contributors: pcraig3
Donate link: http://example.com/
Tags: comments, spam
Requires at least: 3.6
Tested up to: 4.0
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Plugin creates a new 'Jobs' Custom Post Type -- expects the Admin Page Framework (and Collapseomatic) to be included.

== Description ==

Plugin creates a new 'Jobs' Custom Post Type -- expects the Admin Page Framework (and Collapseomatic) to be included.

List of features we want:

*   Create a Job.
*   List Jobs on the site.
*   Search, filter, whatever the jobs


== Frequently Asked Questions ==

= Give me a job. =

No.  Maybe once we're at 1.0.1


== Changelog ==

= 1.0.0 =
* Deleted the Department AdminPageFramework taxonomy because we just want a garden-variety one
* Says '1 Job' or '2 Jobs' now.  Woo.
* Losta comments

= 0.9.0 =
* Almost DONE!!
* * Updated media queries to match the Divi Theme's
* * Mock collapseomatic element for mobile devices
* * Global cache object and global timezone object initiated on plugin start
* * Patched up CSS for single USC Job
* * Removed a couple of the TODOs
* * Not strictly to do with this plugin, but it's using theme templates now instead of internal ones

= 0.8.3 =
* Added an accordion for Jobs filters when on mobile
* Took Tyler's suggestion and am now feeding usc_jobs from the $wp_query rather than the JSON API backend.
* * It's nowhere near as nice code, but it's WAY faster
* Only display posts whose apply_by_dates are before 11pm on a given day
* Added "position" field for paid positions
* Backend error messages work (as well as skirting around the APC cache thing)
* Cleaned up CSS.  Everything is blue what should be

= 0.8.2 =
* Added Tyler's stupid 'All' checkbox
* Cleaned up + commented public-filter.js (does all of the Javascript, basically)

= 0.8.1 =
* Bunch of small fixes -- merits a .1 update
* Icon font on single post types has been fixed (didn't work before in IE or Safari)
* JSON request headers now (hopefully) disable caching.  Modifications to Jobs should be instantly reflected on the pulled-in-by-JSON Jobs Archive
* Small JS fix reflects current number of jobs returned in some weird scenario where it might have been inaccurate
* Both the query and the JSON url are identical now, same post_per_page limits and both return by soonest apply_by_date

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

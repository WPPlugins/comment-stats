<?php
/*
Plugin Name: Comment Stats
Plugin URI: http://www.christianlittle.com
Description: Shows month-over-month statistics for all of your blog comments.  To see your stats, activate this plugin and then go to <a href="edit-comments.php?page=comment-stats/comment-stats.php">Comments -> Comment Stats</a>.
Version: 2
Author: Christian Little
Author URI: http://www.christianlittle.com
*/

/* -------------------------------- REVISION HISTORY -----------------------------------

One - Initial version of this plugin.  Don't expect much beyond the very basic
      components and functionality.  If you've downloaded this version of the 
      plugin I will not be able to provide any support at all.  Please wait until 
      later version of the plugin are released.  DATE: Sept 25, 2008
--------------------------------------------------------------------------------------- */

// HOOKS
add_action('admin_menu', 'mt_add_pages');

// HOOK FUNCTIONS

function mt_add_pages() {

  // Adds the Comment Stats as a submenu to the Comments menu
  add_submenu_page('edit-comments.php', __('Comment Stats'), 'Comment Stats', '8', __FILE__, 'mt_manage_page' );


}

function mt_manage_page() {

  echo "<div style=\"width: 1000px; padding-left: 10px;\" class=\"wrap\">
        <h2>Comment Statistics</h2>
<p>This page shows you various statistics about your comments for every month.  <font color=\"#FF0000\">Scroll down to the bottom of the page to see definitions for each column in the table</font>.  Please note that this plugin is in it's very early stages of development, as such there may be some bugs in the numbers.</p>
        <table class=\"widefat\"><thead><tr>
            <td rowspan=\"2\" style=\"width: 100px;\">Period</td>
            <td rowspan=\"2\"  style=\"width: 50px;\" align=\"center\">Approved<br />Comments</td>
            <td rowspan=\"2\"  style=\"width: 50px;\" align=\"center\">Posts<br />Discussed</td>
            <td colspan=\"4\" style=\"width: 100px;\">Commentator Statistics</td>
            <td rowspan=\"2\" style=\"width: 700px;\">Most Commented Post(s)</td>
       </tr><tr>
            <td align=\"center\">Names</td>
            <td align=\"center\">Emails</td>
            <td align=\"center\">URLs</td>
            <td align=\"center\">IPs</td>
       </tr></thead>";

  $query = "SELECT 
              date_format(comment_date, '%M, %Y') as period, 
              COUNT(*) as total,
              COUNT(DISTINCT(comment_post_ID)) as totalposts,
              COUNT(DISTINCT(comment_author)) as totalauthors,
              COUNT(DISTINCT(comment_author_email)) as totalemails,
              COUNT(DISTINCT(comment_author_url)) as totalurls,
              COUNT(DISTINCT(comment_author_IP)) as totalips
            FROM wp_comments
            GROUP BY period
            ORDER BY comment_date DESC
            ";
  $result = mysql_query($query);
  while($row = mysql_fetch_object($result)) {

    echo "<tr>
            <td>$row->period</td>
            <td align=\"center\">$row->total</td>
            <td align=\"center\">$row->totalposts</td>
            <td align=\"center\">$row->totalauthors</td>
            <td align=\"center\">$row->totalemails</td>
            <td align=\"center\">$row->totalurls</td>
            <td align=\"center\">$row->totalips</td>
          ";

    echo "<td style=\"font-size: x-small;\">";
    $popularquery = "SELECT 
                     wp_comments.comment_post_ID as commentid,
                     COUNT(*) AS count,
                     wp_posts.post_title as title
                     FROM wp_comments
                     LEFT JOIN wp_posts ON wp_comments.comment_post_ID = wp_posts.ID
                     WHERE date_format( comment_date, '%M, %Y' ) = \"$row->period\"
                     GROUP BY wp_comments.comment_post_ID
                     ORDER BY count DESC
                    ";
    //echo "$popularquery<br />";
    $popularresult = mysql_query($popularquery);
    while($poprow = mysql_fetch_object($popularresult)) {
      echo "$poprow->count | $poprow->title<br />";
    }
    echo "</td>";
    echo "</tr>";
  }

  echo "</table>
  <h3>Column Definitions</h3>
<p><strong>Period</strong> should be self-explanatory, it is the month and year for that particular row.  The table is always sorted by date, showing the most recent months first.</p>
<p><strong>Approved Comments</strong> shows the total number of comments that have been <font color=\"#00FF00\"><strong>APPROVED</strong></font> (which includes comments that have not been automatically blocked by a spam filter like Akismet).</p>
<p><strong>Posts Discussed</strong> shows you the total number of posts during this period that received at least 1 approved comment.</p>
<p><strong>Commentator Statistics</strong> shows you the unique number for each of the sub-items:<ul>
<li><strong>Names</strong>: Total number of unique names used</li>
<li><strong>Emails</strong>: Total number of unique email addresses used</li>
<li><strong>URLs</strong>: Total number of unique websites used</li>
<li><strong>IP's</strong>: Total number of unique IP addresses</li>
</ul></p>
<p><strong>Most Commented Post(s)</strong> lists all of your posts that received at least 1 comment.  The posts show here are listed by the number of comments received during that period (NOTE: It is common for a blog post to get comments for months after it is posted, as such if it shows 10 posts this month for a comment but there are 20 in total, look at previous months to see when the other comments arrived on this post).</p>
  </div>";

}



?>
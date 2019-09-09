<div class="wrap">
  <h1>No Password For Login</h1>
  <p>Select the page where the user can only visit.</p>
  <form method="post">
    <input type="hidden" name="npfl_form_update_pages" value="1">
    <?php
      global $wpdb;

      $result = $wpdb->get_results ( "
          SELECT *
          FROM  $wpdb->posts
              WHERE post_type = 'page'
      " );

      foreach ( $result as $page )
      {
        echo "<div style='width: 120px; float: left;'><input name='page_id' type='radio' value='{$page->ID}' " . ( $page_id == $page->ID ? 'checked' : '' ) . ">" . $page->post_title . "</div>";
      }
    ?>
    <div style="clear: both;">
      <br>
      <hr>
      <input type="submit" value="Update Pages">
    </div>
  </form>
</div>

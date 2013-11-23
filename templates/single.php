<?php
/**
 * The template for displaying a single research
 */

// Get template header
get_header(); ?>

<div class="row-fluid">

  <!-- Get sidebar -->
  <div class="span3">
    <?php get_sidebar(1); ?>
  </div>
  
  <!-- Main -->
  <div id="main" class="span6" role="main">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <article id="post-<?php the_ID(); ?>" role="article" itemscope itemtype="http://schema.org/BlogPosting">
      <!-- Header -->
      <header>
        <h1 class="h3 lead" itemprop="headline"><?php echo the_title(); ?></h1>
      </header>
      <!-- Content -->
      <section class="post-content" itemprop="articleBody">
        <?php the_content(); ?>
      </section>
    </article>
    <?php endwhile; ?>
    <?php endif; ?>
  </div>
  
  <!-- Get options-->
  <div class="span3">

    <?php 

      // Get the ID
      $post_id = get_the_ID();

    ?>

    <?php

      // RSS Link
      $rss = get_post_meta( $post_id, 'rp_rss_link', true );

      if ($rss) {
        $rss_options = array(
          'title' => 'Productos recientes',  // Title of the Widget
          'url' => esc_url($rss), // URL of the RSS Feed
          'items' => 10, // Number of items to be displayed
          'show_summary' => 0, // Show post excerpts?
          'show_author' => 0, // Set 1 to display post author
          'show_date' => 0 // Set 1 to display post dates
        );

        // Show the widget
        the_widget( 'WP_Widget_RSS', $rss_options );
      }

    ?>

  </div>

</div>

<!-- Get template footer -->
<?php get_footer(); ?>

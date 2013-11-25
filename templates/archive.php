<?php
/**
 * The template for displaying a research projects by archive
 */

// Get template header
get_header(); ?>

<div class="row-fluid">

  <!-- Get sidebar -->
  <div class="span3">
    <!-- Categories -->
    <div class="widget">
      <h4 class="widget-title h4 lead">
        <?php _e('Categories'); ?>
      </h4>
      <ul class="nav nav-tabs nav-stacked">
      <?php

        $current = get_query_var('term');
        $taxonomy = 'research_category';
        $categories = get_terms( $taxonomy, '' );

        if ($categories) {
          foreach ( $categories as $category ) {
            echo '<li '. ($current === $category->slug ? 'class="active"' : '') . '>';
            echo '<a href="' . esc_attr(get_term_link( $category, $taxonomy )) . '">' . $category->name . '</a>';
            echo '</li>';
          }
        }

      ?>
      </ul>
    </div>
  </div>
  
  <!-- Main -->
  <div id="main" class="span9" role="main">
    <?php if (have_posts()) : ?> 
      <?php while (have_posts()) : the_post(); ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
          <header class="entry-header">
            <h1 class="entry-title">
              <a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a>
              </h1>
          </header>

          <div class="entry-content">
            <?php the_content(); ?>
          </div>

          <footer class="entry-meta">
            <?php

              $categories = array();
              $categories_list = get_the_terms( get_the_ID(), 'research_category' );

              if (count($categories_list) > 0) {
                
                foreach ($categories_list as $key => $value) {
                  $categories[] = '<a href="/'. _x('projects/category', 'Slug URL (archive)', 'research-projects') . '/' . $value->slug .'">'. $value->name .'</a>';
                }

                echo "Archivada en: " . implode($categories, ', ');
              }

            ?>
          </footer>
        </article>

        <hr>

      <?php endwhile; ?>
    <?php endif; ?>
  </div>

</div>

<!-- Get template footer -->
<?php get_footer(); ?>

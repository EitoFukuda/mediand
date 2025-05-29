<?php
/**
 * Template Name: About medi& Page
 * 
 * @package medi& GENSEN Child
 */

get_header(); ?>

<div class="about-page">
    <!-- 上記HTMLの<body>内容をここに貼り付け -->
    <?php while (have_posts()) : the_post(); ?>
        <!-- WordPressのコンテンツを表示したい場合 -->
        <div class="wp-content" style="display: none;">
            <?php the_content(); ?>
        </div>
    <?php endwhile; ?>
</div>

<?php get_footer(); ?>
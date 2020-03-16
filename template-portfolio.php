<?php
/**
 * Template Name: Portfolio
 */

$context         = Timber::context();
$timber_post     = Timber::query_post();
$context['post'] = $timber_post;

$args = array(
  'post_type' => 'project',
  'posts_per_page' => -1,
  'post_status' => 'publish',
  'order' => 'DESC',
  'orderby' => 'date'
);

// $products = Timber::get_posts($args);
$projects = Timber::get_posts($args);


$context['projects'] = $projects;



Timber::render( 'templates/template-portfolio.twig', $context );


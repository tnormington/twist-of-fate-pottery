<?php
/**
 * Template Name: Front
 */

$context         = Timber::context();
$timber_post     = Timber::query_post();
$context['post'] = $timber_post;

$args = array(
  'post_type' => 'product',
  'posts_per_page' => 6,
  'post_status' => 'publish',
  'order' => 'DESC',
  'orderby' => 'date',
  'category__not_in' => array(149)
);

// $products = Timber::get_posts($args);
$products = Timber::get_posts($args);
$context['featured_products'] = array();

foreach($products as $product) {
  // $product->display_price = $produce
  // $p = wc_get_product($product->ID);
  $product->display_price = $product->get_price_html();
  // error_log(print_r($product, true));
  array_push($context['featured_products'], $product);
}

// $context['featured_products'] = $formatted_products;



Timber::render( 'templates/template-front.twig', $context );


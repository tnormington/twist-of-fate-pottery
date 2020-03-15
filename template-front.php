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
  'orderby' => 'date'
);

$products = Timber::get_posts($args);
$formatted_products = array();

foreach($products as $product) {
  // $product->display_price = $produce
  $p = wc_get_product($product->ID);
  $product->display_price = $p->get_price_html();
  // error_log(print_r($product, true));
  array_push($formatted_products, $product);
}

$context['featured_products'] = $formatted_products;



Timber::render( 'templates/template-front.twig', $context );


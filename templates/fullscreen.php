<?php

//get_header();

echo '<html><head>';
do_action('wp_head');

$post      = get_post();
$images    = get_post_galleries( $post, false );
$image_ids = wp_list_pluck( $images,'ids' );
$image_ids = explode(",", $image_ids[0]);

echo '<div id="slides"> <div class="slides-container">';

foreach ( $image_ids as $image_id ) {
	echo wp_get_attachment_image( $image_id, 'large' );
}

echo '</div></div>';

?>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		$('#slides').superslides({
			animation: "slide",
			scrollable: false,
			hashchange: true
		})
	});
</script>

<?php
echo '</body></html>';


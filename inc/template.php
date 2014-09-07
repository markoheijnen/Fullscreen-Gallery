<?php

$template = Fullscreen_Gallery::get_template();

if ( $template ) {
	$template->show_slider();
}
else {
	include get_404_template();
}
<?php
function getImagesByPostID($id = 0, $socialNetwork = false) {

    $featuredImage = wp_get_attachment_url(get_post_thumbnail_id($id));

    $page_data = get_page($id);

    $matches = array();

    if (!preg_match_all('%<img.*?src="(.*?)".*?/>%', $page_data->post_content, $matches) && !$featuredImage) {
        return false;
    }

    array_unshift($matches[1], $featuredImage);

    $upload_dir = wp_upload_dir();

    $rtrnArray = array();

    $i = 0;
    foreach ($matches[1] as $key => $img) {


        if ($img == false) {
            continue;
        }
        if (!$socialNetwork && substr($img, strrpos($img, '.')) != '.jpg') {
            continue;
        }

        $startPosition = strpos($img, 'uploads');

        $absolutePath = $upload_dir['basedir'] . substr($img, $startPosition + 7);

        $absolutePath = str_replace('\\', DIRECTORY_SEPARATOR, str_replace('/', DIRECTORY_SEPARATOR, $absolutePath));

        if (!$socialNetwork && !is_file($absolutePath)) {
            continue;
        }

        $i++;

        $rtrnArray[$key][0] = $img;
        $rtrnArray[$key][1] = $absolutePath;

        if ($i == 10) {
            break;
        }
    }

    return $rtrnArray;
}

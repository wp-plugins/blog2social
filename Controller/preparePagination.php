<?php

function preparePagination($numPosts, $postsPerPage, $currPage = 1, $adjacent = 3) {
    $pages = intval(ceil($numPosts / $postsPerPage));

    if ($pages == 0) {
        $pages = 1;
    }

    $paginationArray = array();
    for ($i = 1; $i <= $pages; $i++) {
        if ($i == $currPage) {
            $paginationArray[] = array('curr' => TRUE, 'shown' => TRUE);
        } elseif ($i >= ($currPage - $adjacent) && $i <= ($currPage + $adjacent)) {
            $paginationArray[] = array('curr' => FALSE, 'shown' => TRUE);
        } else {
            $paginationArray[] = array('curr' => FALSE, 'shown' => FALSE);
        }
    }

    $paginationArray[0]['shown'] = TRUE;
    $paginationArray[$pages - 1]['shown'] = TRUE;

    $paginationString = '<ul class="pagination">';
    unset($_GET['prgPage']);
    unset($_GET['filterSet']);
    $params = http_build_query($_GET);

    foreach ($paginationArray as $key => $val) {
        if ($val['curr'] === TRUE) {
            $paginationString .= '<li class="active"><span>' . ($key + 1) . '</span></li>';
        } elseif ($val['shown'] === TRUE) {
            $paginationString .= '<li><a href="?' . $params . '&prgPage=' . ($key + 1) . '">' . ($key + 1) . '</a></li>';
        } else {
            if (substr($paginationString, -42) !== '<li class="disabled"><span>...</span></li>') {
                $paginationString .= '<li class="disabled"><span>...</span></li>';
            }
        }
    }
    return $paginationString . '</ul>';
}

<?php

class B2SUtil {

    public static function getCustomDateFormat($dateTime = '0000-00-00 00:00:00', $lang = 'de') {
        if ($lang == 'de') {
            $ident = 'd.m.Y H:i';
            return date($ident, strtotime($dateTime)) . ' Uhr';
        } else {
            $ident = 'Y/m/d g:i a';
            //$ident = 'F j, Y, g:i a';
            return date($ident, strtotime($dateTime));
        }
    }

    public static function getCustomDateFormatShort($dateTime = '0000-00-00 00:00:00', $lang = 'de') {
        if ($lang == 'de') {
            $ident = 'd.m.Y';
            return date($ident, strtotime($dateTime));
        } else {
            $ident = 'Y/m/d';
            return date($ident, strtotime($dateTime));
        }
    }

    public static function getCustomDate($dateTime = '0000-00-00 00:00:00', $ident = '') {
        return date($ident, strtotime($dateTime));
    }

    public static function getTimeRemainingForToday($dateTime = '0000-00-00 00:00:00', $currentDate = '0000-00-00 00:00:00', $lang = 'de') {
        if (date('Ymd') == date('Ymd', strtotime($dateTime))) {
            $now = new DateTime($currentDate);
            $future_date = new DateTime($dateTime);
            $interval = $future_date->diff($now);

            if ((int) $interval->format("%h") >= 1) {
                if ($lang == 'de') {
                    return $interval->format("in %h") . ' Std. (Heute ' . date('H:i', strtotime($dateTime)) . ')';
                } else {
                    return $interval->format("in %h") . 'h (Today ' . date('g:i a', strtotime($dateTime)) . ')';
                }
            } else {
                if ($lang == 'de') {
                    return $interval->format("in %i") . ' Minuten';
                } else {
                    return $interval->format("in %i") . ' minutes';
                }
            }
        }
        return self::getCustomDateFormat($dateTime, $lang);
    }

    public static function getImagesByPostId($id = 0, $network = false) {

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
            if (!$network && substr($img, strrpos($img, '.')) != '.jpg') {
                continue;
            }

            $startPosition = strpos($img, 'uploads');

            $absolutePath = $upload_dir['basedir'] . substr($img, $startPosition + 7);

            $absolutePath = str_replace('\\', DIRECTORY_SEPARATOR, str_replace('/', DIRECTORY_SEPARATOR, $absolutePath));

            if (!$network && !is_file($absolutePath)) {
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

    public static function getNetworkListByCheck($data = array(), $imageUrl = '',$lang='de') {
        if (is_array($data) && !empty($data)) {
            $output = '<table class="table noborder">';
            foreach ($data as $networkId => $details) {
                foreach ($details as $c => $v) {
                    foreach ($v as $networkType => $networkName) {
                        $output.='<tr class="border-bottom">';
                        if (!empty($imageUrl)) {
                            $output.=' <td width="5%"><img src="' . $imageUrl . '/' . $networkId . '_flat.png"></td>';
                        }
                        $type = ($lang !='de' && strtolower(trim($networkType)) == 'profil') ? 'profile' : $networkType; 
                        $output.='<td width="95%"><div class="report-portal">' . $type . ' | ' . $networkName . '</div></td>';
                        $output.='</tr>';
                    }
                }
            }
            return $output;
        }
        return false;
    }

}

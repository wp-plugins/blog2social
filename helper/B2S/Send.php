<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Tools.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Util.php';

$actions = array('sentToNetwork');
if (!in_array(trim($_POST['action']), $actions) || empty($_POST['token'])) {
    echo json_encode(array('result' => 0));
    exit;
} else {
    call_user_func($_POST['action']);
}

function sentToNetwork() {
    if (isset($_POST['mandant_id']) && !empty($_POST['token']) && isset($_POST['token']) && (int) $_POST['blog_user_id'] > 0 && (int) $_POST['post_id'] > 0) {
        global $wpdb;

        $publishData = '';
        $schedData = '';
        $schedId = array();
        if (isset($_POST['network']) && !empty($_POST['network']) && is_array($_POST['network'])) {
            //Custom
            if (isset($_POST['sched_date_all']) && empty($_POST['sched_date_all'])) {
                foreach ($_POST['network'] as $key => $value) {
                    foreach ($value as $k => $v) {
                        if (isset($v['profil']['sched_date']) && !empty($v['profil']['sched_date']) && isset($_POST['check'][$key][$k]['profil'])) {
                            //SCHED
                            $wpdb->insert('b2s_filter', array('sched_network_date' => B2SUtil::getCustomDate(trim($v['profil']['sched_date'] . ':00'), 'Y-m-d H:i:s'),
                                'publishData' => addslashes(serialize(array($key => array($k => array('profil' => $_POST['check'][$key][$k]['profil']))))),
                                'post_id' => (int) $_POST['post_id'],
                                'blog_user_id' => (int) $_POST['blog_user_id']));
                            $v['profil']['sched_id'] = (int) $wpdb->insert_id;
                            array_push($schedId, $v['profil']['sched_id']);
                            $schedData['network'][$key][$k] = $v;
                        } else if (isset($v['page']['sched_date']) && !empty($v['page']['sched_date']) && isset($_POST['check'][$key][$k]['page'])) {
                            $wpdb->insert('b2s_filter', array('sched_network_date' => B2SUtil::getCustomDate(trim($v['page']['sched_date'] . ':00'), 'Y-m-d H:i:s'),
                                'publishData' => addslashes(serialize(array($key => array($k => array('page' => $_POST['check'][$key][$k]['page']))))),
                                'post_id' => (int) $_POST['post_id'],
                                'blog_user_id' => (int) $_POST['blog_user_id']));
                            $v['page']['sched_id'] = (int) $wpdb->insert_id;
                            array_push($schedId, $v['page']['sched_id']);
                            $schedData['network'][$key][$k] = $v;
                        } else if (isset($v['group']['sched_date']) && !empty($v['group']['sched_date']) && isset($_POST['check'][$key][$k]['group'])) {
                            $wpdb->insert('b2s_filter', array('sched_network_date' => B2SUtil::getCustomDate(trim($v['group']['sched_date'] . ':00'), 'Y-m-d H:i:s'),
                                'publishData' => addslashes(serialize(array($key => array($k => array('group' => $_POST['check'][$key][$k]['group']))))),
                                'post_id' => (int) $_POST['post_id'],
                                'blog_user_id' => (int) $_POST['blog_user_id']));
                            $v['group']['sched_id'] = (int) $wpdb->insert_id;
                            array_push($schedId, $v['group']['sched_id']);
                            $schedData['network'][$key][$k] = $v;
                        } else{
                            //PUBLISH
                            $publishData['network'][$key][$k] = $v;
                        }
                    }
                }
                //ALL SAVE SCHED    
            } else if (isset($_POST['sched_date_all']) && !empty($_POST['sched_date_all'])) {
                $checkData = (isset($_POST['check']) && !empty($_POST['check'])) ? addslashes(serialize($_POST['check'])) : '';
                $wpdb->insert('b2s_filter', array('sched_network_date' => B2SUtil::getCustomDate(trim($_POST['sched_date_all'] . ':00'), 'Y-m-d H:i:s'), 'publishData' => $checkData, 'post_id' => (int) $_POST['post_id'], 'blog_user_id' => (int) $_POST['blog_user_id']));
                $_POST['sched_id'] = (int) $wpdb->insert_id;
                array_push($schedId, $_POST['sched_id']);
                $schedData['network'] = $_POST['network'];
            }
        }

        unset($_POST['network']);
        unset($_POST['check']);
        
        if (!empty($schedData) || !empty($publishData)) {
            $result = json_decode(B2STools::sentToNetwork($_POST, $publishData, $schedData));
            if (is_object($result) && !empty($result) && isset($result->publish) && isset($result->sched)) {
                //PUBLISH!
                if ((int) $result->publish == 1 && (int) $result->sched == 0) {
                    $sqlCheckPost = $wpdb->prepare("SELECT `id` FROM `b2s_filter` WHERE `blog_user_id` = %s AND `post_id` = %s  AND `sched_network_date` = %s", trim($_POST['blog_user_id']), trim($_POST['post_id']),"0000-00-00 00:00:00");
                    $postEntry = $wpdb->get_var($sqlCheckPost);
                    $currentDateTime = date('Y-m-d H:i:s', current_time('timestamp'));
                    if ($postEntry == NULL || (int) $postEntry == 0) {
                        $wpdb->insert('b2s_filter', array('last_network_publish_date' => $currentDateTime, 'publishData' => addslashes($result->data), 'post_id' => (int) $_POST['post_id'], 'blog_user_id' => (int) $_POST['blog_user_id']));
                        $postEntry = $wpdb->insert_id;
                    } else {
                        if((int) $postEntry > 0){
                            $wpdb->update('b2s_filter', array('last_network_publish_date' => $currentDateTime, 'publishData' => addslashes($result->data)), array('id' => (int) $postEntry));
                        }
                    }
                    if ((int) $postEntry > 0) {
                        echo json_encode(array('result' => 1, 'network_id' => (int) $postEntry, 'error' => ''));
                        exit;
                    }
                }
                //SCHED!
                if ((int) $result->publish == 0 && (int) $result->sched == 1) {
                    echo json_encode(array('result' => 2, 'network_id' => 0, 'error' => ''));
                    exit;
                }

                //DELETE SCHED!
                if (is_array($schedId) && !empty($schedId)) {
                    foreach ($schedId as $sid) {
                        if ((int) $sid > 0) {
                            $wpdb->delete('b2s_filter', array('id' => (int) $sid));
                        }
                    }
                }

                echo json_encode(array('result' => 0, 'network_id' => 0, 'error' => $result->data));
                exit;
            }
        }

        //DELETE SCHED!
        if (is_array($schedId) && !empty($schedId)) {
            foreach ($schedId as $sid) {
                if ((int) $sid > 0) {
                    $wpdb->delete('b2s_filter', array('id' => (int) $sid));
                }
            }
        }
    }
    echo json_encode(array('result' => 0, 'network_id' => 0, 'error' => 'NO_DATA'));
    exit;
}

<?php

class B2SNetwork {

    public $data;
    public $lang;
    public $url;
    public $vocabulary;
    public $postData;
    public $image;
    public $postUrl;
    public $version;
    public $voeData;
    public $lastVoeDate;
    public $allowTitle = array(2, 3, 6, 8, 9);
    public $isComment = array(1);
    public $allowTag = array(4, 9);
    public $onlyImage = array(6, 7);
    public $allowNoImage = array(5, 8, 9);
    public $allowEditUrl = array(2, 8);
    public $limitCharacter = array(2 => 140, 3 => 600, 6 => 500, 8 => 420, 9 => 250);
    public $allowPreview = array(1);
    public $scheduleMinDate;
    public $scheduleMaxDate;

    function __construct($postData = '', $image = false, $voeData = array(), $lastVoeDate = '', $version = 0, $postUrl = '', $lang = 'de', $vocabulary = '', $url = '', $scheduleMinDate, $scheduleMaxDate) {
        $this->lang = $lang;
        $this->voeData = $voeData;
        $this->lastVoeDate = $lastVoeDate;
        $this->url = $url;
        $this->postUrl = $postUrl;
        $this->version = $version;
        $this->vocabulary = $vocabulary;
        $this->postData = $postData;
        $this->image = $image;
        $this->scheduleMinDate = $scheduleMinDate;
        $this->scheduleMaxDate = $scheduleMaxDate;
    }

    public function getItemHtml($data = array(), $networkCount = 0) {
        $this->data = $data;
        $profil_multi = (isset($data->profil_multi)) ? (int) $data->profil_multi : 0;
        $profilName = (empty($data->profil_name) && !isset($data->profil_name) && $profil_multi == 0) ? '' : '(' . $data->profil_name . ')';
        $pageName = (empty($data->page_name) || !isset($data->page_name)) ? '' : '(' . $data->page_name . ')';
        $groupName = (empty($data->group_name) || !isset($data->group_name)) ? '' : '(' . $data->group_name . ')';
        $disabledProfileCheckBox = '';
        $disabledProfileTextArea = '';
        $disabledProfileTextAreaClass = '';
        $disabledPageCheckBox = '';
        $disabledPageTextArea = '';
        $disabledPageTextAreaClass = '';
        $disabledGroupCheckBox = '';
        $disabledGroupTextArea = '';
        $disabledGroupTextAreaClass = '';
        $infoAuthProfil = '';
        $infoAuthPage = '';
        $infoAuthGroup = '';
        $limit = false;
        $limitValue = 0;
        $countCharacter = 0;
        $textareaLimit = '';
        $textareaLimitInfo = '';
        $textareaOnKeyUp = '';

        //Settings
        $expired_date = (isset($data->expired_date) && $data->expired_date != '0000-00-00') ? '<span class="network-auth-info"><img class="warning-image" src="' . $this->url . '/../assets/images/warning.png"> ' . $this->vocabulary['NETWORK_EXPIRED_DATE_TITLE'] . ' ' . B2SUtil::getCustomDateFormatShort($data->expired_date . ' 00:00:00', $this->lang) . '</span>' : '';
        $isRequiredTextarea = (in_array($this->data->id, $this->isComment)) ? '' : 'required="required"';
        $message = (in_array($this->data->id, $this->isComment)) ? '' : ((in_array($this->data->id, $this->allowTitle)) ? $this->postData->post_title : trim(strip_shortcodes(strip_tags($this->postData->post_content))));
        $infoImage = (in_array($this->data->id, $this->allowNoImage)) ? $this->vocabulary['NETWORK_NO_IMAGE_SUPPORT'] : '';
        $messageInfo = empty($infoImage) ? '' : '<small>' . $infoImage . '</small>';
        $infoAuth = '<span class="network-auth-info"><img class="' . strtolower($data->name) . '-warning warning-image" src="' . $this->url . '/../assets/images/warning.png"> ' . $this->vocabulary['NETWORK_NO_AUTH_TITLE'] . ' (<a href="admin.php?page=netzwerk">' . $this->vocabulary['NETWORK_NO_AUTH_LINK'] . '</a>)</span>';
        $infoVoe = (!empty($this->lastVoeDate) && in_array($this->data->id, $this->voeData)) ? '<br><span class="voe-info">' . $this->vocabulary['NETWORK_VOE_INFO'] . ' ' . B2SUtil::getCustomDateFormat($this->lastVoeDate, $this->lang) . '</span>' : '';
        $infoVersion = '<span class="network-version-info">' . $this->vocabulary['NETWORK_VERSION_UPGRADE'] . '</span>';
        $messagePlaceholder = (empty($message) && in_array($this->data->id, $this->isComment)) ? $this->vocabulary['NETWORK_COMMENT_TITLE'] : '';
        $content = '';

        //Limit
        foreach ($this->limitCharacter as $i => $valueLimit) {
            if ($i == $data->id) {
                $messageInfo.='<small>' . (!empty($messageInfo) ? ' | ' : '') . 'max ' . $valueLimit . ' ' . $this->vocabulary['NETWORK_CHARACTER_TITLE'] . '</small>';
                $limitValue = $valueLimit;
                $limit = true;
                continue;
            }
        }

        if ($limit !== false) {
            $countValue = $limitValue;
            if ($data->id == 2) { //Twitter
                $countValue = $limitValue - 24;
            }
            if ($data->id == 8) { //Xing -1 Leerzeichen
                $countValue = $limitValue - strlen($this->postUrl) - 1;
            }
            $countCharacter = (int) $countValue - strlen($message);
            if ($countCharacter <= 0) {
                $message = mb_substr($message, 0, (int) $countValue, "UTF-8");
            }
        }

        $box = strtolower($data->name) . '_box';

        $content .='<div class="networkItem">';

        //Profil
        if ($profil_multi == 0) { //Facebook
            $boxDisabeld = ((int) $networkCount >= 1) ? 'style="display:none;"' : '';
            $classElementBoxShow = $box . '_profil_' . $networkCount;
            $content .='<div class="box" id="' . $box . '_profil_' . $networkCount . '" ' . $boxDisabeld . '>';
            //Settings
            $disabledProfileCheckBox = (empty($profilName) || !empty($boxDisabeld)) ? 'disabled' : 'checked';
            //IsImage?
            if ($this->image === false && in_array($this->data->id, $this->onlyImage)) {
                $disabledProfileCheckBox = 'disabled';
                $infoAuthProfil = '<span class="network-auth-info"><img class="' . strtolower($data->name) . '-warning warning-image" src="' . $this->url . '/../assets/images/warning.png"> ' . $this->vocabulary['NETWORK_NO_IMAGE_TITLE'] . '</span>';
            }

            $disabledProfileTextArea = ($disabledProfileCheckBox != 'checked') ? 'disabled="disabled" readonly' : '';
            $disabeldProfileSchedButton = ($disabledProfileCheckBox != 'checked') ? 'disabled="disabled"' : '';
            $displaySchedInfoProfile = ($disabledProfileCheckBox != 'checked') ? 'style="display:none;"' : 'style="display:block;"';
            $disabledProfileTextAreaClass = (!empty($disabledProfileTextArea)) ? 'off' : '';
            $relProfileAuth = (empty($profilName)) ? 'noAuth' : 'isAuth';

            //Prüfung Pro Version
            if (empty($profilName)) {
                $infoAuthProfil = $infoAuth;
            } else {
                $infoAuthProfil = $expired_date;
            }


            if ($limit !== false) {
                $counterId = strtolower($data->name) . '-count-character-profil_'.$networkCount;
                $textareaLimitInfo = '<span class="pull-right limit-info"><span id="' . $counterId . '">' . (int) $countCharacter . '</span> ' . $this->vocabulary['NETWORK_CHARACTER_COUNT_TITLE'] . '</span>';
                $textareaOnKeyUp = (in_array($this->data->id, $this->allowEditUrl)) ? 'onkeyup="networkLimitAll(\'' . strtolower($data->name) . '\',\'' . $limitValue . '\',\'' . $networkCount . '\');"' : ' onkeyup="networkLimit(\'' . strtolower($data->name) . '\',\'profil\',\'' . $limitValue . '\',\'' . $networkCount . '\');"';
            }

            $content .='<div class="col-md-8"><div class="options ' . strtolower(str_replace('+', "", $data->name)) . '_check_profil">
                    <div class="pull-left options_text">
                        <div class="portal-name">' . $this->vocabulary['NETWORK_PROFIL_NAME'] . ' | ' . ucfirst($data->name) . ' <small>' . $profilName . '</small> ' . $infoAuthProfil . $infoVoe . '</div>
                    </div>
                    <div class="pull-right">
                    ' . $messageInfo . '
                    </div>
                </div>
                <div class="controls social_checkbox ' . strtolower($data->name) . '">
                    <div class="pull-left InfoBox">
                        <label class="checkbox" for="' . strtolower($data->name) . '">
                            <input id="' . strtolower(str_replace('+', "", $data->name)) . '_checkbox_profil_'.$networkCount.'" value="' . $data->name . '" name="check[' . $this->data->id . ']['.$networkCount.'][profil]" rel="' . $relProfileAuth . '" type="checkbox" ' . $disabledProfileCheckBox . ' class="check networkCheckBox '.strtolower(str_replace('+', "", $data->name)).'_check_box '.$classElementBoxShow.'_input" onclick="setProfile(\'' . strtolower(str_replace('+', "", $data->name)) . '\',\'' . $networkCount . '\');">
                              <img class="logo hidden-xs hidden-sm" src="' . $this->url . '/../assets/images/portale/' . $this->data->id . '_flat.png">';
            if (in_array($this->data->id, $this->allowPreview) && !empty($data->profil_name)) {
                $content.='<br><a id="profil" class="btn btn-link preview hidden-sm hidden-xs" name="fbPreview" href="#fbPreview" rel="fbView">' . $this->vocabulary['NETWORK_PREVIEW_TITLE'] . '</a>';
                $content.='<input type="hidden" id="fbPreviewUrl-profil" value="' . $data->profil_name . '">';
            }
            $content.='</label>
                    </div>';

            $content.='<div class="pull-left InputBox ' . strtolower(str_replace('+', "", $data->name)) . '_check_profil">
                        <textarea ' . $isRequiredTextarea . ' class="form-control '.$classElementBoxShow.'_input  '.strtolower(str_replace('+', "", $data->name)).'_textarea checkContent ' . $disabledProfileTextAreaClass . '" id="' . strtolower(str_replace('+', "", $data->name)) . '_textarea_profil_'.$networkCount.'" ' . $disabledProfileTextArea . ' ' . $textareaOnKeyUp . ' name="network[' . $this->data->id . ']['.$networkCount.'][profil][content]" ' . $textareaLimit . ' placeholder="' . $messagePlaceholder . '">' . $message . '</textarea>
                            ' . $textareaLimitInfo . '
                       </div>';

                                    if (in_array($this->data->id, $this->allowEditUrl)) {
                $disabeldUrl = (empty($profilName) ||!empty($boxDisabeld)) ? true : false;
                $urlLimit = ($limit !== false) ? ' onkeyup="networkLimitAll(\'' . strtolower($data->name) . '\',\'' . $limitValue . '\',\'' . $networkCount . '\');"' : '';
                $content.=$this->getUrlHtml(strtolower($data->name), $this->data->id, 'profil', $disabeldUrl, $urlLimit,$classElementBoxShow,$networkCount);
            }
            
            
            
            
            //Tags
            if (in_array($this->data->id, $this->allowTag)) {
                $disabeldTags = (empty($disabledProfileTextAreaClass)) ? true : false;
                $content.=$this->getTagsHtml(strtolower($data->name), $this->data->id, 'profil', $disabeldTags,$classElementBoxShow);
                $content.='<br clear="both">';
            }
            $content.='</div></div>';
            //SCHED
            $content.='<div class="col-md-4"><div class="options">';

            $content.='<input type="text" rel="' . strtolower(str_replace('+', "", $data->name)) . '_info_sched_profil_'.$networkCount.'"  value="" id="' . strtolower(str_replace('+', "", $data->name)) . '_input_sched_profil_'.$networkCount.'" class="b2sInputChangeSched '.$classElementBoxShow.'_input" ' . $disabeldProfileSchedButton . ' style="display:none;" name="network[' . $this->data->id . ']['.$networkCount.'][profil][sched_date]" data-field="datetime" data-min="' . $this->scheduleMinDate . '" data-max="' . $this->scheduleMaxDate . '">';
            $content.='<a href="#" rel="' . strtolower(str_replace('+', "", $data->name)) . '_input_sched_profil_'.$networkCount.'"  id="' . strtolower(str_replace('+', "", $data->name)) . '_button_sched_profil_'.$networkCount.'"  class="schedNetwork '.$classElementBoxShow.'_input btn btn-success btn-sm" ' . $disabeldProfileSchedButton . '>'.$this->vocabulary['NETWORK_BUTTON_SCHED_TITLE'].'</a>';
            $content.='<div class="networkSchedInfo '.$classElementBoxShow.'" ' . $displaySchedInfoProfile . ' id="' . strtolower(str_replace('+', "", $data->name)) . '_info_sched_profil_'.$networkCount.'">'.$this->vocabulary['NETWORK_SCHED_DESC_PUBLISH'].': <span>'.$this->vocabulary['NETWORK_SCHED_DESC_PUBLISH_NOW'].'</span></div>';
            if($this->data->id == 2 && $networkCount <=1){
                $content.='<a href="#" rel="' . strtolower(str_replace('+', "", $data->name)) . '_box_profil_'.($networkCount+1) .'"  id="' . strtolower(str_replace('+', "", $data->name)) . '_button_more_sched_profil_'.$networkCount .'"  class="moreSchedNetwork '.$classElementBoxShow.'_input btn btn-link btn-sm" ' . $disabeldProfileSchedButton . '>+ '.$this->vocabulary['NETWORK_BUTTON_MORE_SCHED_TITLE'].'</a>';
            }
            $content.='</div></div>';
            $content.='<br clear="both">';
            $content.='</div>';
        }

        //Page
        if (isset($data->page) && (int) $data->page == 1) {
            $boxDisabeld = ((int) $networkCount >= 1) ? 'style="display:none;"' : '';
            $content .='<div class="box" id="' . $box . '_page_' . $networkCount . '" ' . $boxDisabeld . '>';
            $disabledPageCheckBox = (empty($pageName) || (int) $this->version == 0 || !empty($boxDisabeld)) ? 'disabled="disabled"' : 'checked';
            $disabledPageTextArea = ($disabledPageCheckBox != 'checked') ? 'disabled="disabled" readonly' : '';
            $disabeldPageSchedButton = ($disabledPageCheckBox != 'checked') ? 'disabled="disabled"' : '';
            $displaySchedInfoPage = ($disabledPageCheckBox != 'checked') ? 'style="display:none;"' : 'style="display:block;"';
            $disabledPageTextAreaClass = (!empty($disabledPageTextArea)) ? 'off' : '';
            if (empty($pageName) || (int) $this->version == 0) {
                $infoAuthPage = ((int) $this->version == 0) ? $infoVersion : $infoAuth;
            }

            $relPageAuth = (empty($pageName) || (int) $this->version == 0) ? 'noAuth' : 'isAuth';

            if ($limit !== false) {
                $counterId = strtolower($data->name) . '-count-character-page';
                $textareaLimitInfo = '<span class="pull-right limit-info"><span id="' . $counterId . '">' . (int) $countCharacter . '</span> ' . $this->vocabulary['NETWORK_CHARACTER_COUNT_TITLE'] . '</span>';
                $textareaOnKeyUp = ' onkeyup="networkLimit(\'' . strtolower($data->name) . '\',\'page\',\'' . $limitValue . '\',\'""\');"';
            }

            $content .='<div class="col-md-8"><div class="options ' . strtolower(str_replace('+', "", $data->name)) . '_check_page">
                    <div class="pull-left options_text">
                        <div class="portal-name">' . $this->vocabulary['NETWORK_PAGE_NAME'] . ' | ' . ucfirst($data->name) . ' <small>' . $pageName . '</small> ' . $infoAuthPage . '</div>
                    </div>
                    <div class="pull-right">
                    ' . $messageInfo . '
                    </div>
                </div>
                <div class="controls social_checkbox ' . strtolower($data->name) . '">
                    <div class="pull-left InfoBox">
                        <label class="checkbox" for="' . strtolower($data->name) . '">
                            <input id="' . strtolower(str_replace('+', "", $data->name)) . '_checkbox_page" value="' . $data->name . '" ' . $disabledPageCheckBox . ' type="checkbox" rel="' . $relPageAuth . '" name="check[' . $this->data->id . ']['.$networkCount.'][page]" class="check networkCheckBox  '.strtolower(str_replace('+', "", $data->name)).'_check_box" onclick="setPage(\'' . strtolower(str_replace('+', "", $data->name)) . '\');">
                              <img class="logo hidden-sm hidden-xs" src="' . $this->url . '/../assets/images/portale/' . $this->data->id . '_flat.png">';
            if (in_array($this->data->id, $this->allowPreview) && !empty($data->page_name) && (int) $this->version > 0) {
                $content.='<br><a id="page" class="btn btn-link preview hidden-sm hidden-xs" name="fbPreview-page" href="#fbPreview" rel="fbView">' . $this->vocabulary['NETWORK_PREVIEW_TITLE'] . '</a>';
                $content.='<input type="hidden" id="fbPreviewUrl-page" value="' . $data->page_name . '">';
            }
            $content.='</label>
                    </div>
                    <div class="pull-left InputBox ' . strtolower(str_replace('+', "", $data->name)) . '_check_page">
                        <textarea ' . $isRequiredTextarea . ' class="form-control checkContent ' . $disabledPageTextAreaClass . '" id="' . strtolower(str_replace('+', "", $data->name)) . '_textarea_page" ' . $disabledPageTextArea . ' ' . $textareaOnKeyUp . ' name="network[' . $this->data->id . ']['.$networkCount.'][page][content]" placeholder="' . $messagePlaceholder . '">' . $message . '</textarea>
                        ' . $textareaLimitInfo . '
                    </div>
                </div>
             </div>';

            //SCHED
            $content.='<div class="col-md-4"><div class="options">';
            $content.='<input type="text" rel="' . strtolower(str_replace('+', "", $data->name)) . '_info_sched_page" value="" id="' . strtolower(str_replace('+', "", $data->name)) . '_input_sched_page" class="b2sInputChangeSched" ' . $disabeldPageSchedButton . ' style="display:none;" name="network[' . $this->data->id . ']['.$networkCount.'][page][sched_date]" data-field="datetime" data-min="' . $this->scheduleMinDate . '" data-max="' . $this->scheduleMaxDate . '">';
            $content.='<a href="#" rel="' . strtolower(str_replace('+', "", $data->name)) . '_input_sched_page"  id="' . strtolower(str_replace('+', "", $data->name)) . '_button_sched_page" class="schedNetwork btn btn-success btn-sm" ' . $disabeldPageSchedButton . '>'.$this->vocabulary['NETWORK_BUTTON_SCHED_TITLE'].'</a>';
            $content.='<div class="networkSchedInfo" ' . $displaySchedInfoPage . ' id="' . strtolower(str_replace('+', "", $data->name)) . '_info_sched_page">'.$this->vocabulary['NETWORK_SCHED_DESC_PUBLISH'].': <span>'.$this->vocabulary['NETWORK_SCHED_DESC_PUBLISH_NOW'].'</div>';
            $content.='</div></div>';
            $content.='<br clear="both">';
            $content.='</div>';
        }

        //Group
        if (isset($data->group) && (int) $data->group == 1) {
            $boxDisabeld = ((int) $networkCount >= 1) ? 'style="display:none;"' : '';
            $content .='<div class="box" id="' . $box . '_group_' . $networkCount . '" ' . $boxDisabeld . '>';
            $disabledGroupCheckBox = (empty($groupName) || (int) $this->version == 0 || !empty($boxDisabeld)) ? 'disabled="disabled"' : 'checked';
            $disabledGroupTextArea = ($disabledGroupCheckBox != 'checked') ? 'disabled="disabled" readonly' : '';
            $disabeldGroupSchedButton = ($disabledGroupCheckBox != 'checked') ? 'disabled="disabled"' : '';
            $displaySchedInfoGroup = ($disabledGroupCheckBox != 'checked') ? 'style="display:none;"' : 'style="display:block;"';
            $disabledGroupTextAreaClass = (!empty($disabledGroupTextArea)) ? 'off' : '';
            if (empty($groupName) || (int) $this->version == 0) {
                $infoAuthGroup = ((int) $this->version == 0) ? $infoVersion : $infoAuth;
            }

            $relGroupAuth = (empty($groupName) || (int) $this->version == 0) ? 'noAuth' : 'isAuth';

            if ($limit !== false) {
                $counterId = strtolower($data->name) . '-count-character-group';
                $textareaLimitInfo = '<span class="pull-right limit-info"><span id="' . $counterId . '">' . (int) $countCharacter . '</span> ' . $this->vocabulary['NETWORK_CHARACTER_COUNT_TITLE'] . '</span>';
                $textareaOnKeyUp = ' onkeyup="networkLimit(\'' . strtolower($data->name) . '\',\'group\',\'' . $limitValue . '\',\'""\');"';
            }

            $content .='<div class="col-md-8"><div class="options ' . strtolower(str_replace('+', "", $data->name)) . '_check_group">
                    <div class="pull-left options_text">
                        <div class="portal-name">' . $this->vocabulary['NETWORK_GROUP_NAME'] . ' | ' . ucfirst($data->name) . ' <small>' . $groupName . '</small> ' . $infoAuthGroup . '</div>
                    </div>
                   <div class="pull-right">
                    ' . $messageInfo . '
                    </div>
                </div>
                <div class="controls social_checkbox ' . strtolower($data->name) . '">
                    <div class="pull-left InfoBox">
                        <label class="checkbox" for="' . strtolower($data->name) . '">
                            <input id="' . strtolower(str_replace('+', "", $data->name)) . '_checkbox_group" value="' . $data->name . '" ' . $disabledGroupCheckBox . ' type="checkbox" rel="' . $relGroupAuth . '" name="check[' . $this->data->id . ']['.$networkCount.'][group]" class="check networkCheckBox" onclick="setGroup(\'' . strtolower(str_replace('+', "", $data->name)) . '\');">
                              <img class="logo hidden-sm hidden-xs" src="' . $this->url . '/../assets/images/portale/' . $this->data->id . '_flat.png">';
            if (in_array($this->data->id, $this->allowPreview) && !empty($data->group_name) && (int) $this->version > 0) {
                $content.='<br><a id="group" class="btn btn-link preview hidden-sm hidden-xs" name="fbPreview-group" href="#fbPreview" rel="fbView">' . $this->vocabulary['NETWORK_PREVIEW_TITLE'] . '</a>';
                $content.='<input type="hidden" id="fbPreviewUrl-group" value="' . $data->group_name . '">';
            }
            $content.='</label>
                    </div>
                    <div class="pull-left InputBox ' . strtolower(str_replace('+', "", $data->name)) . '_check_group">
                        <textarea ' . $isRequiredTextarea . ' class="form-control checkContent ' . $disabledGroupTextAreaClass . '" id="' . strtolower(str_replace('+', "", $data->name)) . '_textarea_group" ' . $disabledGroupTextArea . ' ' . $textareaOnKeyUp . ' name="network[' . $this->data->id . ']['.$networkCount.'][group][content]" placeholder="' . $messagePlaceholder . '">' . $message . '</textarea>
                           ' . $textareaLimitInfo . '
                    </div>
                </div>
             </div>';
            //SCHED
            $content.='<div class="col-md-4"><div class="options">';
            $content.='<input type="text" rel="' . strtolower(str_replace('+', "", $data->name)) . '_info_sched_group" id="' . strtolower(str_replace('+', "", $data->name)) . '_input_sched_group" value="" class="b2sInputChangeSched" ' . $disabeldGroupSchedButton . ' style="display:none;" name="network[' . $this->data->id . ']['.$networkCount.'][group][sched_date]" data-field="datetime" data-min="' . $this->scheduleMinDate . '" data-max="' . $this->scheduleMaxDate . '">';
            $content.='<a href="#" rel="' . strtolower(str_replace('+', "", $data->name)) . '_input_sched_group"  id="' . strtolower(str_replace('+', "", $data->name)) . '_button_sched_group" class="schedNetwork btn btn-success btn-sm" ' . $disabeldGroupSchedButton . '>'.$this->vocabulary['NETWORK_BUTTON_SCHED_TITLE'].'</a>';
            $content.='<div class="networkSchedInfo" ' . $displaySchedInfoGroup . ' id="' . strtolower(str_replace('+', "", $data->name)) . '_info_sched_group">'.$this->vocabulary['NETWORK_SCHED_DESC_PUBLISH'].': <span>'.$this->vocabulary['NETWORK_SCHED_DESC_PUBLISH_NOW'].'</div>';
            $content.='</div></div>';
            $content.='<br clear="both">';
            $content.='</div>';
        }


        $content.='</div>';



        return $content;
    }

    protected function getTagsHtml($network = '', $networkId = 0, $type = 'profil', $show = true,$classElementBoxShow='',$networkCount=0) {

        $tagBtn = ($show !== false) ? '' : 'style="display:none;"';
        $tagRemoveBtn = 'style="display:none;"';
        $tagsInput = ($show !== false) ? '' : 'disabled="disabled"';
        $tagsClass = ($show !== false) ? '' : 'off';

        $tags = '';
        $tags.='<div class="pull-left ' . $network . '_check network-tag-margin">
                           <br>
                            <div class="tag-title">
                                Tags
                            </div>
                            <div class="' . $network . '_check_tags tag-div-float">
                                <input class="' . $tagsClass . $classElementBoxShow . '  form-control ' . $network . '_tags network_tag" name="network[' . $networkId . ']['.$networkCount.'][' . $type . '][tag][]" value="" ' . $tagsInput . '>
                                    </div>
                                    <div id="' . $network . '-tag-div" class="pull-left tag-add-div" ' . $tagBtn . '>
                           <img id="' . $network . '-tag-btn" class="' . $network . '-remove-tag-btn" src="' . $this->url . '/../assets/images/removeTag.png" '.$tagRemoveBtn.' onclick="removeTag(\'' . $network . '\',\'' . $networkId . '\');" >
                           <img id="' . $network . '-tag-btn" src="' . $this->url . '/../assets/images/addTag.png" onclick="addTag(\'' . $network . '\',\'' . $networkId . '\');" >
                        </div>
                </div>';
        return $tags;
    }

    protected function getUrlHtml($network = '', $networkId = 0, $type = 'profil', $show = true, $urlLimit = '',$classElementBoxShow='',$networkCount=0) {
        $urlInput = ($show !== false) ? 'disabled="disabled"' : '';
        $urlClass = ($show !== false) ? 'off' : '';
        $url = '';
        $url.='<div class="pull-right ' . $network . '_check InputBox">
            <div class="url-title">Link</div>
                            <span class="' . $network . '_check_url">
                                <input id="' . $network . '_url_'.$networkCount.'" class="form-control '.$classElementBoxShow.'_input network_url ' . $urlClass . '" name="network[' . $networkId . ']['.$networkCount.'][' . $type . '][url]" ' . $urlInput . ' ' . $urlLimit . ' placeholder="' . $this->vocabulary['NETWORK_URL_PLACEHOLDER'] . '" value="' . $this->postUrl . '">
                            </span>
                </div>';
        return $url;
    }

}

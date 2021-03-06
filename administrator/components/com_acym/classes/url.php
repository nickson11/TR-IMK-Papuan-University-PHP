<?php
defined('_JEXEC') or die('Restricted access');
?><?php

class acymurlClass extends acymClass
{
    var $table = 'url';
    var $pkey = 'id';

    public function save($url)
    {
        if (empty($url)) {
            return false;
        }

        foreach ($url as $oneAttribute => $value) {
            if (empty($value)) {
                continue;
            }

            $url->$oneAttribute = strip_tags($value);
        }

        return parent::save($url);
    }

    public function getOneUrlById($id)
    {
        $query = 'SELECT * from #__acym_url WHERE id = '.intval($id);

        return acym_loadObject($query);
    }

    public function get($url)
    {
        $column = is_numeric($url) ? 'id' : 'url';
        $query = 'SELECT * FROM #__acym_url WHERE '.$column.' = '.acym_escapeDB($url).' LIMIT 1';

        return acym_loadObject($query);
    }

    public function getAdd($url)
    {
        $currentUrl = $this->get($url);
        if (empty($currentUrl->id)) {
            $currentUrl = new stdClass();
            $currentUrl->name = $url;
            $currentUrl->url = $url;
            $currentUrl->id = $this->save($currentUrl);

            if (empty($currentUrl->id)) {
                return;
            }
        }

        return $currentUrl;
    }

    public function getUrl($url, $mailid, $userid)
    {
        if (empty($url) || empty($mailid) || empty($userid)) return;

        static $allurls;

        $url = str_replace('&amp;', '&', $url);

        if (empty($allurls[$userid][0]) || $allurls[$userid][0] !== $url) {
            $currentUrl = $this->getAdd($url);

            $allurls[$userid][0] = $url;
            $allurls[$userid][1] = acym_frontendLink('fronturl&task=click&urlid='.$currentUrl->id.'&userid='.$userid.'&mailid='.$mailid);
        }

        return $allurls[$userid][1];
    }
}


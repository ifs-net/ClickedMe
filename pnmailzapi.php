<?php

/*
 * get plugins with type / title
 *
 * @param   $args['id']     int     optional, show specific one or all otherwise
 * @return  array
 */
function ClickedMe_mailzapi_getPlugins($args)
{
    // Load language definitions
    pnModLangLoad('ClickedMe','mailz');
    
    $plugins = array();
    // Add first plugin.. You can add more using more arrays
    $plugins[] = array(
        'pluginid'      => 1,   // internal id for this module
        'title'         => _CLICKEDME_LAST_VISITS,
        'description'   => _CLICKEDME_LAST_VISITS_DESCRIPTION,
        'module'        => 'mailz'
    );
    return $plugins;
}

/*
 * get content for plugins
 *
 * @param   $args['pluginid']       int         id number of plugin (internal id for this module, see getPlugins method)
 * @param   $args['params']         string      optional, show specific one or all otherwise
 * @param   $args['uid']            int         optional, user id for user specific content
 * @param   $args['contenttype']    string      h or t for html or text
 * @param   $args['last']           datetime    timtestamp of last newsletter
 * @return  array
 */
function ClickedMe_mailzapi_getContent($args)
{
    // Load language definitions
    pnModLangLoad('ClickedMe','mailz');

    switch ($args['pluginid']) {
        case 1:
            // Get Viewers
            $viewers = pnModAPIFunc('ClickedMe','user','getViewers',array('uid' => $args['uid'], 'amount' => 5));
            if ($args['contenttype'] == 't') {
                $output="\n";
                foreach ($viewers as $items) {
                    $output.="> ".$item['uname']."\n";
                }
                $output.="\n";
            } else {
                $render = pnRender::getInstance('ClickedMe');
                $render->assign('visitors', $viewers);
                $output = $render->display('clickedme_mailz_visitors.htm');
            }
            return $output;
            break;
        default:
            return 'wrong plugin id given';
    }

    // return emtpy string because we do not need anything to display in here...    
    return '';
}


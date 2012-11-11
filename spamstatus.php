<?php

/**
 * This plugin displays an icon showing the status
 * of spam status
 *
 * @version 0.0.1
 * @author Bodo Bellut
 * @mail bb@prima.de
 * @url ...
 * @copyright (c) 2012 Bodo Bellut
 *
 * based on
 * - dkimstatus by Julien vehent - julien@linuxwall.info
 * - markbuttons by Karl McMurdo - user xrxca on roundcubeforum.net
 * - message_highlight by Cor Bosman - roundcube@wa.ter.net
 * - markasjunk by Thomas Bruederli
 *
 * Changelog:
 *  20121109 - initial version
 */
class spamstatus extends rcube_plugin
{
    public $task = 'mail';
    function init()
    {
        $rcmail = rcmail::get_instance();
        if ($rcmail->action == 'show' || $rcmail->action == 'preview') {
            $this->add_hook('storage_init', array($this, 'storage_init'));
            $this->add_hook('message_headers_output', array($this, 'message_headers'));
        } else if ($rcmail->action == '') {
            // with enabled_caching we're fetching additional headers before show/preview
            $this->add_hook('storage_init', array($this, 'storage_init'));
        }
	$this->add_hook('template_container', array($this, 'html_output'));
        $this->add_texts('localization');
    }

    function storage_init($p)
    {
        $rcmail = rcmail::get_instance();
        $p['fetch_headers'] = trim($p['fetch_headers'].' ' . strtoupper('X-Spam-Flag'));
        return $p;
    }

    function image($image, $alt, $title)
    {
	    return '<img src="'
		    . $this->url($this->local_skin_path()) . '/images/'
		    . $image.'" alt="'.$this->gettext($alt).'" title="'.$this->gettext($alt).$title.'" /> ';
    }

    function message_headers($p)
    {

        /* 
        */
        if($p['headers']->others['x-spam-flag']){

            $results = $p['headers']->others['x-spam-flag'];

            if(preg_match("/no/", $results)) {
                $image = 'ham.png';
                $alt = 'Ham';
	    } else {
		$image = 'spam.png';
		$alt = 'Spam';
	    }
	} else {
                $image = 'ham.png';
                $alt = 'Ham';
	}

        if ($image && $alt) {
            $p['output']['from']['value'] = $this->image($image, $alt, $title) . $p['output']['from']['value'];
        }
        return $p;
    }

    function select_button($i) {
	    return("\n      <a title=\""
		    . Q($this->gettext('select' . $i))
		    . '" class="mybutton spam" href="#"'
		    . ' onclick="return rcmail.command(\'select-all\',\''
		    . $i . '\',this)"><img align="top" src="'
		    . $this->url($this->local_skin_path()) . '/images/' . $i
		    . '.png" /> </a>');
    }


    function html_output($p) {
	    if ($p['name'] == "listcontrols") {
		    $rcmail = rcmail::get_instance();
		    $skin_path = $this->url($this->local_skin_path()) . '/images/';
		    $r = $this->select_button('spam');
		    $p['content'] = $r . $p['content'];
	    }

	    return $p;
    }
}

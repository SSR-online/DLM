<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use stdClass;

class VideoBlock extends Block
{

    static $displayName = 'Video';

    public $childTypes = [
        'App\TextBlock',
        'App\QuestionBlock'
    ];

    public $configuration =[
        'mediasite_user' => [
            'friendly_name' => 'Mediasite user',
            'default' => '',
        ],
        'mediasite_password' => [
            'friendly_name' => 'Mediasite password',
            'default' => '',
        ],
        'mediasite_apikey' => [
            'friendly_name' => 'Mediasite API key',
            'default' => '',
        ],
        'mediasite_authenticate' => [
            'friendly_name' => 'Authenticate at MediaSite',
            'default' => true,
        ],
        'youtube_domains' => [
            'friendly_name' => 'Youtube Domains',
            'default' => [
                'www.youtube.com', 
                'youtube.com', 
                'youtube-nocookie.com',
                'www.youtube-nocookie.com',
                'youtu.be',
                'www.youtu.be'
            ]
        ]
        ,
        'mediasite_domains' => 
        [
            'friendly_name' => 'Mediasite Domains (use :id as a placeholder for video-ID)',
            'default' => [
                'media.ssr.nl',
                'ssr.mediasite.com'
            ]
        ],
        'vimeo_domains' => [
            'friendly_name' => 'Vimeo Domains',
            'default' => [
                'player.vimeo.com',
                'vimeo.com'
            ]
        ],
        'custom_url_youtube' => [
            'friendly_name' => 'Custom URL for YouTube videos (use :id as a placeholder for video-ID)',
            'default' => ''
        ],
        'custom_url_mediasite' => [
            'friendly_name' => 'Custom URLs for MediaSite videos (use :id as a placeholder for video-ID)',
            'default' => ''
        ],
        'custom_url_vimeo' => [
            'friendly_name' => 'Custom URLs for Vimeo videos (use :id as a placeholder for video-ID)',
            'default' => ''
        ]
    ];
    
    private $playertype = null; 

    public function process(Request $request) {
    	$this->url = $request->input('url');
        $this->caption = $request->input('caption');
    }

    public function is_interactive() {
        return ($this->get_playertype() == 'mediasite');
    }

    private function is_str_in_array($str, $array) {
        if(is_array($array)) {
            $imploded = implode(' ', $array);
        } else { 
            $imploded = $array;
        }
        if(strpos($imploded, $str)!==false) {
            return true;
        }
        return false;
    }

    private function get_playertype() {
        if($this->playertype == null) {
            if(empty($this->url)) { $this->playertype = false; }
            $url_parts = parse_url($this->url);
            if(!$url_parts) { return false; }

            if($this->is_str_in_array($url_parts['host'], $this->get_configuration('youtube_domains'))) {
                $this->playertype = 'youtube';
            } else if($this->is_str_in_array($url_parts['host'], $this->get_configuration('mediasite_domains'))) {
                $this->playertype = 'mediasite';
            } else if($this->is_str_in_array($url_parts['host'], $this->get_configuration('vimeo_domains'))) {
                $this->playertype = 'vimeo';
            }
        }
        return $this->playertype;
    }

    private function get_id_from_url_with_placeholder($domains, $url) {
        $id = null;
        $url_parts = parse_url($this->url);
        if($this->is_str_in_array(':id', $domains)) { 
            foreach($domains as $domain) {
                if(strpos($domain, $url_parts['host'])===false) { continue; }
                $parts = parse_url($domain);
                foreach($parts as $urlfrag=>$part) {
                    $startpos = strpos($part, ':id');
                    if($startpos!==false) {
                        if($urlfrag == 'query') {
                            $queryvars = null;
                            parse_str($part, $queryvars);
                            $param = array_search(':id', $queryvars);
                            parse_str($url_parts['query'], $queryvars);
                            $id = $queryvars[$param];
                        } elseif($urlfrag == 'path') {
                            $id = substr($url_parts[$urlfrag], $startpos);
                            $id = substr($id, 0, strpos($id, '/')); //remove later path components;
                        }
                    }
                }
            }
        }
        return $id;
    }

    public function get_id() {
        $id = null;
        if(empty($this->url)) { return false; }
        $url_parts = parse_url($this->url);
        if(!$url_parts) { return false; }
        if($this->get_playertype() == 'youtube') {
            $domains = $this->get_configuration('youtube_domains');
            if($id = $this->get_id_from_url_with_placeholder($domains, $this->url)) { return $id; }
            $id = $this->youtube_id($url_parts);
        } else if($this->get_playertype() == 'mediasite') {
            $domains = $this->get_configuration('mediasite_domains');
            if($id = $this->get_id_from_url_with_placeholder($domains, $this->url)) { return $id; }
            //If no :id was set, just use the last path component.
            $path_array = explode('/', $url_parts['path']);
            $id = array_pop($path_array);
        } else if($this->get_playertype() == 'vimeo') {
            $domains = $this->get_configuration('vimeo_domains');
            if($id = $this->get_id_from_url_with_placeholder($domains, $this->url)) { return $id; }
            $path_array = explode('/', $url_parts['path']);
            $id = array_pop($path_array);
        }
        return $id;
    }

    private function get_player() {
        $id = $this->get_id();
        if(empty($this->url)) { return false; }
        $url_parts = parse_url($this->url);
        if(!$url_parts) { return false; }
        $iframe = '<p>Geen herkende video-url gevonden.</p>';
        if($this->get_playertype() == 'youtube') {
            $iframe = $this->youtube_iframe($id);
        } else if($this->get_playertype() == 'mediasite') {
            $iframe = $this->mediasite_iframe($id);
        } else if($this->get_playertype() == 'vimeo') {
            $iframe = $this->vimeo_iframe($id);
        }

        return '<div class="video">' . $iframe . '</div>';
    }

    public function display() {
        return '<div class="video">' . $this->get_player() . '</div>';
    }

    private function youtube_id($url_parts) {
        if(strpos($url_parts['path'], 'embed')!==false) {
            $path_parts = explode('/', $url_parts['path']);
            $vid = array_pop($path_parts);
        } else if(array_key_exists('query', $url_parts)) {
          $query_vars = [];                
          parse_str($url_parts['query'], $query_vars);
          $vid = $query_vars['v'];
        } else {
            $path_parts = explode('/', $url_parts['path']);
            $vid = array_pop($path_parts);
        }
        return $vid;
    }

    private function get_youtube_url($id) {
        if($url = $this->get_configuration('custom_url_youtube')) {
            return str_replace(':id', $id, $url);
        }
        return 'https://www.youtube-nocookie.com/embed/'.$id.'?autoplay=0';
    }
    private function get_vimeo_url($id) {
        if($url = $this->get_configuration('custom_url_vimeo')) {
            return str_replace(':id', $id, $url);
        }
        return 'https://player.vimeo.com/video/'.$id;
    }

    private function get_mediasite_url($id) {
        if($url = $this->get_configuration('custom_url_mediasite')) {
            $url = str_replace(':id', $id, $url);
            if($this->get_configuration('mediasite_authenticate') == 'on') {
                $url .= '?authTicket='.$this->getAuthorizationTicket();
            }
            return $url;
        }
        $url_parts = parse_url($this->url);
        if(!$url_parts) {
            return "https://media.mediasite.com/Mediasite/Play/" . $id . '?authTicket='.$this->getAuthorizationTicket();
        } else {
            $url = 'https://' . $url_parts['host'] . '/Mediasite/Play/' . $id;
            if($this->get_configuration('mediasite_authenticate') == 'on') {
                $url .= '?authTicket='.$this->getAuthorizationTicket();
            }
            return $url;
        }
    }

    private function youtube_iframe($id) {
    	if(!$id) { return 'No valid video ID found'; }
    	$output = '<iframe class="player ytplayer" type="text/html"
  							src="'.$this->get_youtube_url($id).'"
  							frameborder="0">
  					    </iframe>';
  		return $output;
    }

    private function mediasite_iframe($id) {
    	if(!$id) { return 'No valid video ID found'; }
    	$output = '<div id="mediaplayer-'.$this->id.'" class="player" data-src="'.$this->get_mediasite_url($id).'"></div>';
      //   '<iframe class="player mediasiteplayer" type="text/html"
						// src="https://mijn.ssr.nl/local/ssrcontentsearch/view.php?resourceid='.$id.'"
						// frameborder="0">
				  //   </iframe>';
		return $output;
    }

    private function vimeo_iframe($id) {
    	if(!$id) { return; }
    	return '<iframe src="'.$this->get_vimeo_url($id).'" width="640" height="268" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
    }

    public function get_url() {
        $id = $this->get_id();
        return 'https://media.ssr.nl/Mediasite/Play/'.$id.'?authTicket='.$this->getAuthorizationTicket().'&playFrom=0&autostart=true ';
    }

    private function getAuthorizationTicket() {
        $ch = curl_init();
        $url = 'https://media.ssr.nl/Mediasite/api/v1/AuthorizationTickets/';
        // $url = 'https://mijn.ssr.test/local/ssrcontentsearch/authorize.php';

        $user = $this->get_configuration('mediasite_user'); //get_config('local_ssrcontentsearch','user');
        $password = $this->get_configuration('mediasite_password');
        $headers[] = 'sfapikey: '. $this->get_configuration('mediasite_apikey');
        $headers[] = 'Content-Type:application/json';

        // $payload = new stdClass();
        $user = $this->get_configuration('mediasite_user');
        $password = $this->get_configuration('mediasite_password');
        // $payload->apikey = $this->get_configuration('mediasite_apikey');
        // $payload->resourceid = $this->get_id();

        $payload = new stdClass();
        $payload->Username = $user;
        $payload->ResourceId = $this->get_id();
        $payload->MinutesToLive = 60;
        $payload = json_encode($payload);

        // Check the cache first.
        curl_setopt_array($ch, array(
            // set url, timeouts, encoding headers etc.
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => True,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_FOLLOWLOCATION => True,
            CURLOPT_POSTREDIR => 2,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ));

        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $user.':'.$password); //Your credentials goes here

        $response = curl_exec($ch);

        if(curl_errno($ch)) {
            echo curl_error($ch);
        }
        $responseObject = json_decode($response);

        if(is_object($responseObject) && property_exists($responseObject, 'TicketId')) {
            return $responseObject->TicketId;
        }
        return false;
    }

    public function hydrateFromImport($object) {
        $this->url = $object->url;
        $this->caption = $object->caption;
        return parent::hydrateFromImport($object);
    }
}
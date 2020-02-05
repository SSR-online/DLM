<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use IMSGlobal\LTI\ToolProvider;
use IMSGlobal\LTI\ToolProvider\DataConnector;
use App\Providers\LTIServiceProvider;
use Webpatser\Uuid\Uuid;

use App\Module;
use Log;

class LTIController extends Controller
{
	private $db_connector;

	public function __construct() {
		$db = new \PDO('mysql:dbname='.env('DB_DATABASE').';host='.env('DB_HOST'), env('DB_USERNAME'), env('DB_PASSWORD'));
		 
		$this->db_connector = DataConnector\DataConnector::getDataConnector('', $db);
	}

	private function domainFromUrl($url) {
		return parse_url($url, PHP_URL_HOST);
	}

	public function getConsumers() {
		return view('lti.consumers', ['consumers' => $this->db_connector->getToolConsumers()]);
	}

    public function postLogin(Request $request, Module $module = null) {
    	session(['launch_presentation_return_url' => $request->get('launch_presentation_return_url')]);
    	session(['tool_consumer_instance_name' => $request->get('tool_consumer_instance_name')]);
		$tool = new LTIServiceProvider($this->db_connector);
		$tool->module = $module;
		$tool->handleRequest();
		if($request->get('lti_message_type') == 'ContentItemSelectionRequest') {
    		return view('lti.select');
		}
		if($module) {
        	return redirect('/module/' . $module->id);
		}
        return redirect('/');
    }

    public function getConsumer( $consumerId = null ) {
		$consumer = new ToolProvider\ToolConsumer($consumerId, $this->db_connector);
    	return view('lti.editclient', [
    		'consumer' => $consumer,
    		'key' => $consumer->getKey()
    	]);
    }

    public function postConsumer(Request $request ) {
    	$domain = $this->domainFromUrl($request->get('domain'));
    	$consumer = new ToolProvider\ToolConsumer($domain, $this->db_connector);
		$consumer->name = $request->get('name');
		$consumer->secret = (string) Uuid::generate(4);
		$consumer->enabled = TRUE;
		$consumer->save();
		return redirect('/lti/consumers');
    }

    public function returnContentItem(Module $module) {
    	$item = new ToolProvider\ContentItem('LtiLinkItem');
		$item->setMediaType(ToolProvider\ContentItem::LTI_LINK_MEDIA_TYPE);
		$item->setTitle($module->title);
		$item->setText('');
		$item->setUrl(url('/lti/' . $module->id));
		$form_params['content_items'] = ToolProvider\ContentItem::toJson($item);
		$consumer = ToolProvider\ToolConsumer::fromRecordId(session('consumer_pk'), $this->db_connector);
		$form_params = $consumer->signParameters(session('return_url'), 'ContentItemSelection', session('lti_version'), $form_params);
		$page = ToolProvider\ToolProvider::sendForm(session('return_url'), $form_params);
		echo $page;
    }

    public function postOutcome( $grade ) {
    	if($grade > 1) {
    		$grade = $grade / 100; // Assume anything above 1 is on a scale of 1/100;
    	}
    	$resourcelink = ToolProvider\ResourceLink::fromRecordId(session('resourceLinkRecordId'), $this->db_connector);
    	$user = ToolProvider\User::fromResourceLink($resourcelink, Auth::user()->lti_userid);
        $outcome = new ToolProvider\Outcome( $grade );
        $ok = $resourcelink->doOutcomesService(ToolProvider\ResourceLink::EXT_WRITE, $outcome, $user);
    }
}

<?php

namespace App\Providers;

use IMSGlobal\LTI\ToolProvider;
use Illuminate\Support\Facades\Auth;
use IMSGlobal\LTI\ToolProvider\DataConnector;
use App\User;
use App\Role;
use Log;

class LTIServiceProvider extends ToolProvider\ToolProvider
{
    private $db_connector;
    public $module = null;

    function __construct() {
        $db = new \PDO('mysql:dbname='.env('DB_DATABASE').';host='.env('DB_HOST'), env('DB_USERNAME'), env('DB_PASSWORD'));
        $this->db_connector = DataConnector\DataConnector::getDataConnector('', $db);
        parent::__construct($this->db_connector);
    }

    private function guidv4($data)
    {
        assert(strlen($data) == 16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    
    // Handle incoming connections - use the user,
    // context and resourceLink properties of the class instance
    // to access the current user, context and resource link.
    function onLaunch() {
        if(!$this->module) { 
            $this->ok = false;
            $this->reason = 'Er is geen module geselecteerd om te openen.';
            Log::info('Failed LTI launch: No module selected - 1');
            return;
        }
        if(empty($this->user->email)) {
            $this->user->email = $this->guidv4(random_bytes(16)) . '@example.com';
            // $this->ok = false;
            // $this->reason = 'Er is geen e-mail-adres ontvangen. Om deze module te gebruiken moet het e-mail-adres van de gebruiker bekend zijn.';
            // return;
        }
        if(!$user = User::where('lti_userid', $this->user->ltiUserId)->where('lti_consumer', $this->consumer->getRecordId())->first()) {
            $user = new User();
            $user->email = $this->user->email;
            $user->name = $this->user->fullname;
            $user->password = bcrypt(str_random(20));
            $user->lti_consumer = $this->consumer->getRecordId();
            $user->lti_userid = $this->user->ltiUserId;
            $user->save();
        }
        $this->assignRoles($user);
        session(['sourceUrl' => $this->returnUrl]);
        session(['resourceLinkRecordId' => $this->resourceLink->getRecordId()]);
        Auth::login($user);
        Log::info('LTI launch succeeded with login - 2');
    }

    private function assignRoles($user) {
        $role_ids = [];
        $rolesInModule = $user->rolesInModule($this->module)->get();
        foreach($this->user->roles as $lti_role) {
            $role = $this->role($lti_role);
            //Keep module empty for admin roles, these are DLM (consumer??)-wide
            $module_id = (strpos($lti_role, 'administrator') === false) ? $this->module->id : null;
            $role_ids[$role->id] = ['module_id' => $module_id];
        }
        $user->rolesInModule($this->module)->sync($role_ids, false); //Sync roles, keep old ones
    }

    private function role($lti_role) {
        $role = Role::where('lti_identifier', $lti_role)->first();
        if(!$role) {
            $role = new Role();
            $role->lti_identifier = $lti_role;
            $role_segments = explode('/', $lti_role);
            $role->name = array_pop($role_segments);
            $role->save();
        }
        return $role;
    }

    function onContentItem() {
        session(['consumer_pk' => $this->consumer->getRecordId()]);
        session(['resource_pk' => NULL]);
        session(['isContentItem' => TRUE]);
        session(['return_url' => $this->returnUrl]);
    }

    function onError() {    
        Log::info('LTI onError was hit: message ' . $this->reason . ' - 3');
    }

    function returnScore( $score ) {
        if(empty($score)) { return; }
        if($score > 1) {
            $score = $score / 100; // Assume anything above 1 is on a scale of 1/100;
        }
        $resourcelink = ToolProvider\ResourceLink::fromRecordId(session('resourceLinkRecordId'), $this->db_connector);
        $user = ToolProvider\User::fromResourceLink($resourcelink, Auth::user()->lti_userid);
        $outcome = new ToolProvider\Outcome( $score );
        return $resourcelink->doOutcomesService(ToolProvider\ResourceLink::EXT_WRITE, $outcome, $user);
    }
}

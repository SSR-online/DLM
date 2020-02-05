<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Module;

class User extends Authenticatable
{
    use Notifiable;
    use Traits\HasSettings;

    protected $casts = [ 'settings' => 'array' ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function roles() {    
        return $this->belongsToMany('App\Role')->withTimestamps();
    }

    public function rolesInModule(Module $module) {
        return $this->belongsToMany('App\Role')->wherePivot('module_id', $module->id)->withTimestamps();
    }

    public function modules() {    
        return $this->belongsToMany('App\Module')->withPivot(['preferences']);
    }

    public function preferencesForModule(Module $module) {
        $module = $this->modules()->find($module->id);
        if(!$module) { return false; }
        return json_decode($this->modules()->find($module->id)->pivot->preferences);
    }

    public function moduleWithPreferences(Module $module) {
        return $this->modules()->where('module_id', $module->id)->first();
    }
}

<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

class PermissionParentGroup extends Model
{
    protected $table = 'permission_parent_group';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'parent_id', 'id');
    }
}

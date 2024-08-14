<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\PreventiveMaintenance
 *
 * @mixin Eloquent
 */
class PreventiveMaintenance extends Model
{
    use HasFactory;

    protected $table = 'preventive_maintenance';

    protected $guarded = [];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    public function subDepartment() 
    {
        return $this->belongsTo(Department::class, 'sub_department_id', 'id');
    }

    public function departmentLocation()
    {
        return $this->belongsTo(DepartmentLocation::class, 'department_location_id', 'id');
    }

    public function subDepartmentLocation()
    {
        return $this->belongsTo(DepartmentLocation::class, 'department_sub_location_id', 'id');
    }
}

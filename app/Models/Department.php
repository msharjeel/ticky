<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Storage;

/**
 * App\Models\Department
 *
 * @property int $id
 * @property string $name
 * @property int $all_agents
 * @property int $public
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|User[] $agent
 * @property-read int|null $agent_count
 * @method static Builder|Department newModelQuery()
 * @method static Builder|Department newQuery()
 * @method static Builder|Department query()
 * @method static Builder|Department whereAllAgents($value)
 * @method static Builder|Department whereCreatedAt($value)
 * @method static Builder|Department whereId($value)
 * @method static Builder|Department whereName($value)
 * @method static Builder|Department wherePublic($value)
 * @method static Builder|Department whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Department extends Model
{
    use HasFactory;

    //Users whome belongs to this department
    public function users()
    {
        return $this->HasMany(User::class, 'department_id', 'id');
    }

    public function agent(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_departments', 'department_id', 'user_id');
    }

    public function agents()
    {
        if (!$this->all_agents) {
            return $this->agent->all();
        }
        return User::whereIn('role_id', UserRole::where('dashboard_access', true)->pluck('id'))
            ->where('status', true)
            ->get();
    }

    public function subDepartments() 
    {
        return $this->HasMany(Department::class, 'parent_id', 'id');
    }

    public function subDepartmentsCount(): Int
    {
        return $this->HasMany(Department::class, 'parent_id', 'id')->count();
    }


    public function locations() 
    {
        return $this->HasMany(Location::class, 'id', 'department_id');
    }

    public function departmentLocation() 
    {
        return $this->HasMany(DepartmentLocation::class, 'department_id', 'id');
    }

    public function tickets()
    {
        return $this->HasMany(Ticket::class, 'department_id', 'id');
    }

    public function getLogo(): string
    {
        if (Storage::disk('public')->exists($this->logo)) {
            return Storage::disk('public')->url($this->logo);
        }        
        return 'gravatar';
    }

    public function getGravatar(): string
    {
        return 'https://www.gravatar.com/avatar/'.md5('department-'.$this->id.'-avatar').'?s=80&d=retro';
    }
}

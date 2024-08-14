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
 * App\Models\DepartmentLocation
 *
 * @property int $id
 * @property string $name
 * @property int $all_agents
 * @property int $public
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|User[] $agent
 * @property-read int|null $agent_count
 * @method static Builder|DepartmentLocation newModelQuery()
 * @method static Builder|DepartmentLocation newQuery()
 * @method static Builder|DepartmentLocation query()
 * @method static Builder|DepartmentLocation whereAllAgents($value)
 * @method static Builder|DepartmentLocation whereCreatedAt($value)
 * @method static Builder|DepartmentLocation whereId($value)
 * @method static Builder|DepartmentLocation whereName($value)
 * @method static Builder|DepartmentLocation wherePublic($value)
 * @method static Builder|DepartmentLocation whereUpdatedAt($value)
 * @mixin Eloquent
 */
class DepartmentLocation extends Model
{
    use HasFactory;

    protected $table = 'department_locations';

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
    
    public function parentLocation() 
    {
        return $this->belongsTo(Department::class, 'parent_id', 'id');
    }

    public function subLocations() 
    {
        return $this->HasMany(DepartmentLocation::class, 'parent_id', 'id');
    }

    public function locationCategory() 
    {
        return $this->belongsTo(LocationCategory::class, 'location_category', 'id');
    }

}

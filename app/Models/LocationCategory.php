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
 * App\Models\LocationCategory
 *
 * @property int $id
 * @property string $name
 * @property string $color
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Ticket[] $tickets
 * @property-read int|null $tickets_count
 * @method static Builder|LocationCategory newModelQuery()
 * @method static Builder|LocationCategory newQuery()
 * @method static Builder|LocationCategory query()
 * @method static Builder|LocationCategory whereColor($value)
 * @method static Builder|LocationCategory whereCreatedAt($value)
 * @method static Builder|LocationCategory whereId($value)
 * @method static Builder|LocationCategory whereName($value)
 * @method static Builder|LocationCategory whereUpdatedAt($value)
 * @mixin Eloquent
 */
class LocationCategory extends Model
{
    use HasFactory;

    protected $table = "location_category";
    protected $guarded = [];

}

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
 * App\Models\TicketType
 *
 * @property int $id
 * @property string $name
 * @property string $color
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Ticket[] $tickets
 * @property-read int|null $tickets_count
 * @method static Builder|TicketType newModelQuery()
 * @method static Builder|TicketType newQuery()
 * @method static Builder|TicketType query()
 * @method static Builder|TicketType whereColor($value)
 * @method static Builder|TicketType whereCreatedAt($value)
 * @method static Builder|TicketType whereId($value)
 * @method static Builder|TicketType whereName($value)
 * @method static Builder|TicketType whereUpdatedAt($value)
 * @mixin Eloquent
 */
class TicketType extends Model
{
    use HasFactory;

    protected $table = "ticket_types";
    protected $id = "id";

    public function tickets(): BelongsToMany
    {
        return $this->belongsToMany(Ticket::class, 'id','ticket_type');
    }
}

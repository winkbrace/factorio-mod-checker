<?php declare(strict_types=1);

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * This eloquent entity represents a Mod
 *
 * @property string $name
 * @property string $title
 * @property string $author
 * @property string $version
 * @property string $factorio_version
 * @property string $released_at
 *
 * @method static Builder|self newModelQuery()
 * @method static Builder|self newQuery()
 * @method static Builder|self query()
 */
final class Mod extends Model
{
    public $timestamps = false;
    protected $primaryKey = null;

    // This means whether the user has the mod enabled and can't be downloaded in the mods api of course.
    public bool $enabled = false;

    public function releasedAt() : string
    {
        return Carbon::parse($this->released_at)->format('Y-m-d H:i');
    }
}

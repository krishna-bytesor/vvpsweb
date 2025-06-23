<?php

namespace App\Models;

use App\Models\Traits\ModelHelpers;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model {
    use ModelHelpers;
}

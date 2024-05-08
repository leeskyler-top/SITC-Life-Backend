<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format("Y-m-d H:i:s");
    }

    public function taskCheckIns()
    {
        return $this->hasMany(TaskCheckIn::class);
    }

    public function semseter()
    {
        return $this->belongsTo(Semester::class, "semseter_id");
    }

    public function getStatusAttribute()
    {
        $now = now();
        if ($this->start_time > $now) {
            return 'waiting';
        } else if ($this->end_time <= $now) {
            return 'ended';
        } else {
            return 'started';
        }
    }
}

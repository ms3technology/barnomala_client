<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $fillable = ['option_key', 'option_value', 'value_type'];

    protected $appends = ['value'];

    /**
     * Get value casted to its type
     */
    public function getValueAttribute()
    {
        $value = $this->option_value;

        switch ($this->value_type) {
            case 'json':
            case 'array':
                return json_decode($value, true);
            case 'boolean':
                return (bool) $value;
            case 'integer':
                return (int) $value;
            default:
                return $value;
        }
    }

    /**
     * Set value attribute
     */
    public function setValueAttribute($value)
    {
        if (is_array($value) || is_object($value)) {
            $this->attributes['option_value'] = json_encode($value);
            $this->attributes['value_type'] = 'json';
        } elseif (is_bool($value)) {
            $this->attributes['option_value'] = (string) $value;
            $this->attributes['value_type'] = 'boolean';
        } elseif (is_int($value)) {
            $this->attributes['option_value'] = (string) $value;
            $this->attributes['value_type'] = 'integer';
        } else {
            $this->attributes['option_value'] = (string) $value;
            $this->attributes['value_type'] = 'string';
        }
    }
}

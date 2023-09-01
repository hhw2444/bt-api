<?php
namespace Root\BtApi\Entitys;

class BaseEntity
{
    public function __construct(...$value)
    {
        if (!empty($value)) {
            $param = $this->formatParams($value);
            foreach ($param as $key => $val) {
                if (property_exists($this, $key)) {
                    $this->$key = $val;
                }
            }
        }
    }

    public function toJson()
    {
        return json_encode($this);
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    protected function formatParams($value): array
    {
        if (isset($value[0])) {
            $value = $value[0];
        }
        if (! is_array($value)) {
            $value = ['value' => $value];
        }
        return $value;
    }
}
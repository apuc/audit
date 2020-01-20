<?php


namespace common\classes;


class ChartData
{
    public $size;
    public $loading_time;
    public $server_response_code;
    public $created_at;

    public function __construct($size, $loading_time, $server_response_code, $created_at)
    {
        $this->size = $size;
        $this->loading_time = $loading_time;
        $this->server_response_code = $server_response_code;
        $this->created_at = $created_at;
    }
}
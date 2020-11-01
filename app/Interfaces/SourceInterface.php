<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

Interface SourceInterface{

    public function setHeaders(array $headers);
    public function getHeaders() : array;
    public function setUrls(array $urls);
    public function getUrls() : array;
    public function setFrom($from);
    public function getFrom();
    public function setTo($to);
    public function getTo();
}

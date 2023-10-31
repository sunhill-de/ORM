<?php

namespace Sunhill\ORM\Managers;

class ObjectDataGenerator
{
        
    public function getUUID(): string
    {
        return $this->generateUUID();    
    }
    
    public function getUnixTime(): int
    {
        return time();
    }
    
    public function getDBTime(): string
    {
        return date("Y-m-d H:i:s");    
    }
    
    /**
     * Method taken from https://stackoverflow.com/questions/2040240/php-function-to-generate-v4-uuid
     * Generates a v4 uuid string and returns it
     */
    protected function generateUUID()
    {
        $data = random_bytes(16);
        
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));        
    }
    
    public function getUniqueID(int $digits = 10)
    {
        return substr(md5(uniqid()),0,$digits);
    }
}
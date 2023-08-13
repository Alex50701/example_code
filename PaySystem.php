<?php

namespace interfaces;

interface PaySystem
{
    /**
     * Возращает url на платежную страницу
     */
    public static function getUrl(array $array):string;

}
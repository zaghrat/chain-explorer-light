<?php

namespace App\Interface;

interface BlockcypherInterface
{
    function query(string $asset, string $address,  \DateTime $startTime, \DateTime $endTime): \Generator;
    function getData(string $asset, string $address, \DateTime $startTime, \DateTime $endTime, int $threshold);
}
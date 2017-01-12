<?php
namespace AppBundle\Entity;

abstract class SplitType
{
    const Payment = -1;
    const Even = 0;
    const YouOwe = 1;
    const TheyOwe = 2;
}
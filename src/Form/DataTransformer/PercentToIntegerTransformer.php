<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class PercentToIntegerTransformer implements DataTransformerInterface
{
    public function transform($percentInInt)
    {
        if(null === $percentInInt) {
            return;
        }

        $percentInDecimal = number_format(($percentInInt / 100), 2, ',', ' ');
        return $percentInDecimal;
    }

    public function reverseTransform($percentInDecimal)
    {
        if(null === $percentInDecimal){
            return;
        }
        $percentInInt = (int)($percentInDecimal * 100);
        return $percentInInt;
    }

}

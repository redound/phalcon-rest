<?php

namespace OA\Fractal;

class CustomSerializer extends \League\Fractal\Serializer\ArraySerializer
{
    public function collection($resourceKey, array $data)
    {

    	if ($resourceKey == 'parent'){

    		return $data;
    	}

        return array($resourceKey ?: 'data' => $data);
    }

    public function item($resourceKey, array $data)
    {

        if ($resourceKey == 'parent'){

            return $data;
        }

        return array($resourceKey ?: 'data' => $data);
    }
}

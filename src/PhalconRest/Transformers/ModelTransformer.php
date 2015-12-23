<?php

namespace PhalconRest\Transformers;

use Phalcon\Db\Column;
use Phalcon\Di;
use PhalconRest\Constants\Services;

class ModelTransformer extends \League\Fractal\TransformerAbstract
{
    const TYPE_INTEGER = Column::TYPE_INTEGER;
    const TYPE_DATE = Column::TYPE_DATE;
    const TYPE_VARCHAR = Column::TYPE_VARCHAR;
    const TYPE_DECIMAL = Column::TYPE_DECIMAL;
    const TYPE_DATETIME = Column::TYPE_DATETIME;
    const TYPE_CHAR = Column::TYPE_CHAR;
    const TYPE_TEXT = Column::TYPE_TEXT;
    const TYPE_FLOAT = Column::TYPE_FLOAT;
    const TYPE_BOOLEAN = Column::TYPE_BOOLEAN;
    const TYPE_DOUBLE = Column::TYPE_DOUBLE;
    const TYPE_TINYBLOB = Column::TYPE_TINYBLOB;
    const TYPE_BLOB = Column::TYPE_BLOB;
    const TYPE_MEDIUMBLOB = Column::TYPE_MEDIUMBLOB;
    const TYPE_LONGBLOB = Column::TYPE_LONGBLOB;
    const TYPE_BIGINTEGER = Column::TYPE_BIGINTEGER;
    const TYPE_JSON = Column::TYPE_JSON;
    const TYPE_JSONB = Column::TYPE_JSONB;


    protected $modelClass;

    protected $modelDataTypes;
    protected $modelColumnMap;
    protected $mmodelAttributes;


    public function getModelClass()
    {
        return $this->modelClass;
    }

    public function setModelClass($modelClass)
    {
        $this->modelClass = $modelClass;
    }

    /**
     * @return array Properties to be included in the response
     */
    protected function includedProperties()
    {
        $attributes =  $this->getModelAttributes();
        $columnMap = $this->getModelColumnMap();

        if(!is_array($columnMap)){
            return $attributes;
        }

        return array_map(function($attribute) use ($columnMap){

            return array_key_exists($attribute, $columnMap) ? $columnMap[$attribute] : $attribute;

        }, $attributes);
    }

    /**
     * @return array Properties to be excluded in the response
     */
    protected function excludedProperties()
    {
        return [];
    }

    protected function additionalFields($item)
    {
        return [];
    }

    /**
     * Returns keyMap to be used.
     * Keys are the model properties, values are the response keys
     *
     * @return array
     */
    protected function keyMap()
    {
        return [];
    }

    protected function typeMap()
    {
        return [];
    }


    protected function getFieldValue($item, $propertyName, $fieldName)
    {
        $dataType = array_key_exists($propertyName,
            $this->getModelDataTypes()) ? $this->getModelDataTypes()[$propertyName] : null;

        $value = $item->$propertyName;
        $typedValue = $value;

        switch ($dataType) {

            case self::TYPE_INTEGER:
            case self::TYPE_BIGINTEGER: {

                $typedValue = (int)$value;
                break;
            }

            case self::TYPE_DECIMAL:
            case self::TYPE_FLOAT: {

                $typedValue = (float)$value;
                break;
            }

            case self::TYPE_DOUBLE: {

                $typedValue = (double)$value;
                break;
            }

            case self::TYPE_BOOLEAN: {

                $typedValue = (bool)$value;
                break;
            }

            case self::TYPE_VARCHAR:
            case self::TYPE_CHAR:
            case self::TYPE_TEXT:
            case self::TYPE_BLOB:
            case self::TYPE_MEDIUMBLOB:
            case self::TYPE_LONGBLOB: {

                $typedValue = (string)$value;
                break;
            }

            case self::TYPE_DATE:
            case self::TYPE_DATETIME: {

                $typedValue = strtotime($value);
                break;
            }

            case self::TYPE_JSON:
            case self::TYPE_JSONB: {

                $typedValue = json_decode($value);
                break;
            }
        }

        return $typedValue;
    }


    public function transform($item)
    {
        $result = [];
        $keyMap = $this->keyMap();

        foreach ($this->getResponseProperties() as $property) {

            $fieldName = array_key_exists($property, $keyMap) ? $keyMap[$property] : $property;
            $result[$fieldName] = $this->getFieldValue($item, $property, $fieldName);
        }

        $combinedResult = array_merge($result, $this->additionalFields($item));

        return $combinedResult;
    }

    protected function getResponseProperties()
    {
        return array_diff($this->includedProperties(), $this->excludedProperties());
    }

    protected function getModelAttributes()
    {
        if (!$this->mmodelAttributes) {

            /** @var \Phalcon\Mvc\Model\MetaData $modelsMetaData */
            $modelsMetaData = Di::getDefault()->get(Services::MODELS_METADATA);

            $modelClass = $this->getModelClass();

            $this->mmodelAttributes = $modelsMetaData->getAttributes(new $modelClass);
        }

        return $this->mmodelAttributes;
    }

    protected function getModelDataTypes()
    {
        if (!$this->modelDataTypes) {

            /** @var \Phalcon\Mvc\Model\MetaData $modelsMetaData */
            $modelsMetaData = Di::getDefault()->get(Services::MODELS_METADATA);

            $modelClass = $this->getModelClass();
            $columnMap = $this->getModelColumnMap();

            $dataTypes = $modelsMetaData->getDataTypes(new $modelClass);

            $mappedDataTypes = [];

            if(is_array($columnMap)) {

                foreach ($dataTypes as $attributeName => $dataType) {

                    $mappedAttributeName = array_key_exists($attributeName, $columnMap) ? $columnMap[$attributeName] : $attributeName;
                    $mappedDataTypes[$mappedAttributeName] = $dataType;
                }
            }
            else {

                $mappedDataTypes = $dataTypes;
            }

            $this->modelDataTypes = array_merge($mappedDataTypes, $this->typeMap());
        }

        return $this->modelDataTypes;
    }

    protected function getModelColumnMap()
    {
        if (!$this->modelColumnMap) {

            /** @var \Phalcon\Mvc\Model\MetaData $modelsMetaData */
            $modelsMetaData = Di::getDefault()->get(Services::MODELS_METADATA);

            $modelClass = $this->getModelClass();

            $this->modelColumnMap = array_merge($modelsMetaData->getColumnMap(new $modelClass), $this->keyMap());
        }

        return $this->modelColumnMap;
    }
}
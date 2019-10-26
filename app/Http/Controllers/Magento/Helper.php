<?php


namespace App\Http\Controllers\Magento;


class Helper
{
    /**
     * Parse object based on $fieldsToParse and rename fields if needed with $fieldsToRename
     * @param $response
     * @param array $fieldsToParse
     * @param array $fieldsToRename
     * @return array
     */
    public static function getParsedData($response, array $fieldsToParse, array $fieldsToRename = [])
    {
        $parsedResponse = [];
        foreach ($fieldsToParse as $field) {
            if (isset($response->$field)) {
                if (array_key_exists($field, $fieldsToRename)) {
                    $parsedResponse[$fieldsToRename[$field]] = $response->$field;
                } else {
                    $parsedResponse[$field] = $response->$field;
                }
            }
        }

        return $parsedResponse;
    }

    public static function hasDifferences($data, $model)
    {
        if (empty($model)) {
            return true;
        }
        foreach ($data as $key => $value) {
            if ($model->$key != $value) {
                echo $key . $model->$key . PHP_EOL;
                return true;
            }
        }
        return false;
    }
}

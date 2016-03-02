<?php
namespace CsvViews\FieldHandlers;

use CsvViews\FieldHandlers\BaseFieldHandler;

class DefaultFieldHandler extends BaseFieldHandler
{
    /**
     * Method that renders default type field's value.
     * @param  mixed  $table   name or instance of the Table
     * @param  string $field   field name
     * @param  string $data    field data
     * @param  array  $options field options
     * @return string
     */
    public function renderValue($table, $field, $data, array $options)
    {
        $result = parent::renderValue($table, $field, $data, $options);
        $result .= $data;

        return $result;
    }
}

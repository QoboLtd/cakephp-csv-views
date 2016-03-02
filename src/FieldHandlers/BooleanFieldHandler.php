<?php
namespace CsvViews\FieldHandlers;

use CsvViews\FieldHandlers\BaseFieldHandler;

class BooleanFieldHandler extends BaseFieldHandler
{
    /**
     * Method that renders specified field's value based on the field's type.
     * @param  mixed  $table   name or instance of the Table
     * @param  string $field   field name
     * @param  string $data    field data
     * @param  array  $options field options
     * @return string
     */
    public function renderValue($table, $field, $data, array $options)
    {
        $result = parent::renderValue($table, $field, $data, $options);
        $result .= $data ? __('Yes') : __('No');

        return $result;
    }
}

<?php
namespace CsvViews\FieldHandlers;

use App\View\AppView;
use CsvViews\FieldHandlers\BaseFieldHandler;

class BooleanFieldHandler extends BaseFieldHandler
{
    /**
     * Method responsible for rendering field's input.
     * @param  mixed  $table   name or instance of the Table
     * @param  string $field   field name
     * @param  string $data    field data
     * @param  array  $options field options
     * @return string          field input
     */
    public function renderInput($table, $field, $data, array $options = [])
    {
        // load AppView
        $cakeView = new AppView();

        $fieldType = $options['fieldDefinitions']['type'];

        if (in_array($fieldType, array_keys($this->_fieldTypes))) {
            $fieldType = $this->_fieldTypes[$fieldType];
        }

        return $cakeView->Form->input($field, [
            'type' => $fieldType,
            'checked' => $data
        ]);
    }

    /**
     * Method that renders specified field's value based on the field's type.
     * @param  mixed  $table   name or instance of the Table
     * @param  string $field   field name
     * @param  string $data    field data
     * @param  array  $options field options
     * @return string
     */
    public function renderValue($table, $field, $data, array $options = [])
    {
        $result = $data ? __('Yes') : __('No');

        return $result;
    }
}

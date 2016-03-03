<?php
namespace CsvViews\FieldHandlers;

use App\View\AppView;
use CsvViews\FieldHandlers\FieldHandlerInterface;

class BaseFieldHandler implements FieldHandlerInterface
{
    /**
     * Method responsible for rendering field's input.
     * @param  mixed  $table   name or instance of the Table
     * @param  string $field   field name
     * @param  array  $options field options
     * @return string          field input
     */
    public function renderInput($table, $field, array $options = [])
    {
        // load html helper
        $cakeView = new AppView();
        $cakeView->loadHelper('Html');

        return $cakeView->Form->input($field);
    }

    /**
     * Method that renders default type field's value.
     * @param  mixed  $table   name or instance of the Table
     * @param  string $field   field name
     * @param  string $data    field data
     * @param  array  $options field options
     * @return string
     */
    public function renderValue($table, $field, $data, array $options = [])
    {
        $result = $data;

        return $result;
    }
}

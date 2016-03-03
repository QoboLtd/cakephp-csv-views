<?php
namespace CsvViews\FieldHandlers;

use App\View\AppView;
use CsvViews\FieldHandlers\FieldHandlerInterface;

class BaseFieldHandler implements FieldHandlerInterface
{
    /**
     * Csv field types respective input field types
     * @var array
     */
    protected $_fieldTypes = [
        'boolean' => 'checkbox',
        'text' => 'textarea',
        'string' => 'text',
        'uuid' => 'text',
        'integer' => 'number'
    ];

    /**
     * Method responsible for rendering field's input.
     * @param  mixed  $table   name or instance of the Table
     * @param  string $field   field name
     * @param  array  $options field options
     * @return string          field input
     */
    public function renderInput($table, $field, array $options = [])
    {
        // load AppView
        $cakeView = new AppView();

        $fieldType = $options['fieldDefinitions']['type'];

        if (in_array($fieldType, array_keys($this->_fieldTypes))) {
            $fieldType = $this->_fieldTypes[$fieldType];
        }

        return $cakeView->Form->input($field, [
            'type' => $fieldType
        ]);
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

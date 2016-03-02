<?php
namespace CsvViews\FieldHandlers;

interface FieldHandlerInterface
{
    /**
     * Method responsible for rendering field's value.
     * @param  mixed  $table   name or instance of the Table
     * @param  string $field   field name
     * @param  string $data    field data
     * @param  array  $options field options
     * @return string          field value
     */
    public function renderValue($table, $field, $data, array $options);
}

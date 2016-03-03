<?php
namespace CsvViews\FieldHandlers;

use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use CsvMigrations\ForeignKeysHandler;

class FieldHandlerFactory
{
    const DEFAULT_HANDLER_CLASS = 'Default';

    const HANDLER_SUFFIX = 'FieldHandler';

    const IMPLEMENTED_INTERFACE = 'FieldHandlerInterface';

    protected $_tableName;

    protected $_tableInstances = [];

    public function renderInput($model, $field, $options)
    {
    }

    /**
     * Method that renders specified field's value based on the field's type.
     * @param  mixed  $table   name or instance of the Table
     * @param  string $field   field name
     * @param  string $data    field data
     * @param  array  $options field options
     * @return string          list field value
     */
    public function renderValue($table, $field, $data, array $options = [])
    {
        // set table name
        if (is_object($table)) {
            $this->setTableName($table->alias());
        } else {
            $this->setTableName($table);
        }

        $tableInstance = $this->_setTableInstance($table);

        // get fields definitions
        $fieldsDefinitions = $tableInstance->getFieldsDefinitions();
        $fieldDefinitions = $fieldsDefinitions[$field];

        // add field definitions to options array
        $options['fieldDefinitions'] = $fieldDefinitions;

        // get appropriate field handler
        $handlerName = $this->_getHandlerByFieldType($fieldDefinitions['type'], true);

        if (!class_exists($handlerName)) {
            $handlerName = __NAMESPACE__ . '\\' . static::DEFAULT_HANDLER_CLASS . static::HANDLER_SUFFIX;
        }

        $interface = __NAMESPACE__ . '\\' . static::IMPLEMENTED_INTERFACE;
        if (!in_array($interface, class_implements($handlerName))) {
            throw new \RuntimeException($handlerName . ' does not implement ' . $interface);
        }

        $handler = new $handlerName;

        return $handler->renderValue($table, $field, $data, $options);
    }

    /**
     * Set table name
     * @param string $tableName table name
     * @return void
     */
    public function setTableName($tableName)
    {
        $this->_tableName = $tableName;
    }

    /**
     * Method that retrieves handler class name based on provided field type.
     * @param  string $type field type
     * @param  bool   $fqcn true to use fully-qualified class name
     * @return string       handler class name
     */
    protected function _getHandlerByFieldType($type, $fqcn = false)
    {
        if (false !== $pos = strpos($type, ':')) {
            $type = substr($type, 0, $pos);
        }

        $result = Inflector::classify($type) . static::HANDLER_SUFFIX;

        if ($fqcn) {
            $result = __NAMESPACE__ . '\\' . $result;
        }

        return $result;
    }

    /**
     * Method that adds specified table to the _tableInstances
     * array and returns the table's instance.
     * @param  mixed $table name or instance of the Table
     * @return object       instance of specified Table
     */
    protected function _setTableInstance($table)
    {
        // add table instance to _modelInstances array
        if (!in_array($this->_tableName, array_keys($this->_tableInstances))) {
            // get table instance
            if (!is_object($table)) {
                $table = TableRegistry::get($this->_tableName);
            }
            $this->_tableInstances[$this->_tableName] = $table;
        }

        return $this->_tableInstances[$this->_tableName];
    }
}

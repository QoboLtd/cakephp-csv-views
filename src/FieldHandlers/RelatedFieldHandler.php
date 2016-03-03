<?php
namespace CsvViews\FieldHandlers;

use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use Cake\View\View;
use CsvViews\FieldHandlers\BaseFieldHandler;

class RelatedFieldHandler extends BaseFieldHandler
{
    const FIELD_TYPE_PATTERN = 'related:';

    const LINK_ACTION = 'view';

    /**
     * Method that renders related field's value.
     * @param  mixed  $table   name or instance of the Table
     * @param  string $field   field name
     * @param  string $data    field data
     * @param  array  $options field options
     * @return string
     */
    public function renderValue($table, $field, $data, array $options = [])
    {
        $result = parent::renderValue($table, $field, $data, $options);
        // load html helper
        $cakeView = new View();
        $cakeView->loadHelper('Html');
        // get related table name
        $relatedName = $this->_getRelatedName($options['fieldDefinitions']['type']);
        // get related table's displayField value
        $displayFieldValue = $this->_getDisplayFieldValueByPrimaryKey(Inflector::camelize($relatedName), $data);
        // generate related record html link
        $result .= $cakeView->Html->link(
            h($displayFieldValue),
            ['controller' => $relatedName, 'action' => static::LINK_ACTION, $data]
        );

        return $result;
    }

    /**
     * Method that extracts list name from field type definition.
     * @param  string $type field type
     * @return string       list name
     */
    protected function _getRelatedName($type)
    {
        $result = str_replace(static::FIELD_TYPE_PATTERN, '', $type);

        return $result;
    }

    /**
     * Method that retrieves provided Table's displayField value,
     * based on provided primary key's value.
     * @param  mixed  $table      Table object or name
     * @param  sting  $value      query parameter value
     * @return string             displayField value
     */
    protected function _getDisplayFieldValueByPrimaryKey($table, $value)
    {
        if (!is_object($table)) {
            $table = TableRegistry::get($table);
        }
        $primaryKey = $table->primaryKey();
        $displayField = $table->displayField();

        $query = $table->find('all', [
            'conditions' => [$primaryKey => $value],
            'fields' => [$displayField],
            'limit' => 1
        ]);

        $result = $query->first();

        return $result->$displayField;
    }
}

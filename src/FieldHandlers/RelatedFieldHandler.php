<?php
namespace CsvViews\FieldHandlers;

use App\View\AppView;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use CsvViews\FieldHandlers\BaseFieldHandler;

class RelatedFieldHandler extends BaseFieldHandler
{
    /**
     * Field type match pattern
     */
    const FIELD_TYPE_PATTERN = 'related:';

    /**
     * Action name for html link
     */
    const LINK_ACTION = 'view';

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

        $relatedName = $this->_getRelatedName($options['fieldDefinitions']['type']);
        // get related table's displayField value
        $displayFieldValue = $this->_getDisplayFieldValueByPrimaryKey(Inflector::camelize($relatedName), $data);

        $input = $cakeView->Form->input($field, [
            'name' => $field . '_label',
            'id' => $field . '_label',
            'type' => 'text',
            'data-type' => 'typeahead',
            'readonly' => (bool)$data,
            'value' => $displayFieldValue,
            'data-name' => $field,
            'autocomplete' => 'off',
            'data-url' => '/api/' . $relatedName . '/lookup.json'
        ]);
        $input .= $cakeView->Form->input($field, ['type' => 'hidden', 'value' => $data]);

        return $input;
    }

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
        // load AppView
        $cakeView = new AppView();
        // get related table name
        $relatedName = $this->_getRelatedName($options['fieldDefinitions']['type']);
        // get related table's displayField value
        $displayFieldValue = $this->_getDisplayFieldValueByPrimaryKey(Inflector::camelize($relatedName), $data);
        // generate related record html link
        $result = $cakeView->Html->link(
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
        $result = '';

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

        $record = $query->first();

        if (!is_null($record)) {
            $result = $record->$displayField;
        }

        return $result;
    }
}

<?php
namespace CsvViews\FieldHandlers;

use Cake\Core\Configure;
use CsvViews\FieldHandlers\BaseFieldHandler;

class ListFieldHandler extends BaseFieldHandler
{
    const FIELD_TYPE_PATTERN = 'list:';

    /**
     * Field parameters
     * @var array
     */
    protected $_fieldParams = ['value', 'label', 'active'];

    /**
     * Method that renders list field's value.
     * @param  mixed  $table   name or instance of the Table
     * @param  string $field   field name
     * @param  string $data    field data
     * @param  array  $options field options
     * @return string
     */
    public function renderValue($table, $field, $data, array $options)
    {
        $result = parent::renderValue($table, $field, $data, $options);
        $listName = $this->_getListName($options['fieldDefinitions']['type']);
        $fieldOptions = $this->_getListFieldOptions($listName);

        if (!empty($fieldOptions[$data]['label'])) {
            $result .= h($fieldOptions[$data]['label']);
        } else {
            $result .= $data;
        }

        return $result;
    }

    /**
     * Method that extracts list name from field type definition.
     * @param  string $type field type
     * @return string       list name
     */
    protected function _getListName($type)
    {
        $result = str_replace(static::FIELD_TYPE_PATTERN, '', $type);

        return $result;
    }

    /**
     * Method that retrieves list field options.
     * @param string $listName list name
     * @return array
     */
    protected function _getListFieldOptions($listName)
    {
        $result = [];
        $path = Configure::readOrFail('CsvListsOptions.path') . $listName . '.csv';
        $listData = $this->_getCsvData($path);
        if (!empty($listData)) {
            $result = $this->_prepareListOptions($listData);
        }

        return $result;
    }

    /**
     * Method that retrieves csv file path from specified directory.
     * @param  string $path directory to search in
     * @return string       csv file path
     */
    protected function _getCsvFile($path)
    {
        $result = '';
        if (file_exists($path)) {
            foreach (new \DirectoryIterator($path) as $fileInfo) {
                if ($fileInfo->isFile() && 'csv' === $fileInfo->getExtension()) {
                    $result = $fileInfo->getPathname();
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Method that retrieves csv file data.
     * @param  string $path csv file path
     * @return array        csv data
     */
    protected function _getCsvData($path)
    {
        $result = [];
        if (file_exists($path)) {
            if (false !== ($handle = fopen($path, 'r'))) {
                while (false !== ($data = fgetcsv($handle, 0, ','))) {
                    $result[] = $data;
                }
                fclose($handle);
            }
        }

        return $result;
    }

    /**
     * Method that restructures list options csv data for better handling.
     * @param  array  $data csv data
     * @return array
     */
    protected function _prepareListOptions($data)
    {
        $result = [];
        $paramsCount = count($this->_fieldParams);

        foreach ($data as $row) {
            $colCount = count($row);
            if ($colCount !== $paramsCount) {
                throw new \RuntimeException(sprintf($this->_errorMessages[__FUNCTION__], $colCount, $paramsCount));
            }
            $field = array_combine($this->_fieldParams, $row);

            $result[$field['value']] = [
                'label' => $field['label'],
                'active' => (bool)$field['active']
            ];
        }

        return $result;
    }
}

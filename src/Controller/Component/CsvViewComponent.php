<?php
namespace CsvViews\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;

/**
 * CsvView component
 */
class CsvViewComponent extends Component
{

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    /**
     * Count of fields per row for panel logic
     */
    const PANEL_COUNT = 3;

    /**
     * Actions to arrange fields into panels
     */
    const PANEL_ACTIONS = ['add', 'edit', 'view'];

    /**
     * Error messages
     * @var array
     */
    protected $_errorMessages = [
        '_arrangePanels' => 'Field parameters count [%s] does not match required parameters count [%s]'
    ];

    /**
     * Called before the controller action. You can use this method to configure and customize components
     * or perform logic that needs to happen before each controller action.
     *
     * @param \Cake\Event\Event $event An Event instance
     * @return void
     * @link http://book.cakephp.org/3.0/en/controllers.html#request-life-cycle-callbacks
     */
    public function beforeFilter(\Cake\Event\Event $event)
    {
        if ('view' === $this->request->params['action']) {
            $this->_setAssociatedRecords($event, ['oneToMany']);
        }

        $path = Configure::readOrFail('CsvViews.path');
        $this->_setTableFields($event, $path);
    }

    /**
     * Method that retrieves specified Table's associated records and passes them to the View.
     * @param \Cake\Event\Event $event     An Event instance
     * @param array             $types     association type(s)
     * @param string            $tableName Table name to fetch associated records from
     * @return void
     */
    protected function _setAssociatedRecords(\Cake\Event\Event $event, array $types, $tableName = '')
    {
        $controller = $event->subject();
        // if not provided, get Table name from current controller
        if ('' === trim($tableName)) {
            $tableName = $controller->name;
        }

        $result = [];
        $table = TableRegistry::get($tableName);
        foreach ($table->associations() as $association) {
            if (in_array($association->type(), $types)) {
                // get associated records
                $assocName = $association->name();
                $query = $table->{$assocName}->find('all');
                $result[$assocName]['records'] = $query->all();

                // get associated index View csv fields
                $action = 'index';
                $path = Configure::readOrFail('CsvViews.path');
                $path .= Inflector::camelize($association->table()) . DS . $action . '.csv';
                $result[$assocName]['fields'] = $this->_getFieldsFromCsv($path, $action);
            }
        }

        $controller->set('csvAssociatedRecords', $result);
        $controller->set('_serialize', ['csvAssociatedRecords']);
    }

    /**
     * Method that passes csv defined Table fields to the View
     * @param \Cake\Event\Event $event An Event instance
     * @param  string           $path  file path
     * @return void
     */
    protected function _setTableFields(\Cake\Event\Event $event, $path)
    {
        $result = [];
        if (file_exists($path)) {
            $controller = $event->subject();
            $result = $this->_getFieldsFromCsv(
                $path . $this->request->controller . DS . $this->request->params['action'] . '.csv'
            );
        }

        $controller->set('fields', $result);
        $controller->set('_serialize', ['fields']);
    }

    /**
     * Method that gets fields from a csv file
     * @param  string $path   csv file path
     * @param  string $action action name
     * @return array          csv data
     */
    protected function _getFieldsFromCsv($path, $action = '')
    {
        if ('' === trim($action)) {
            $action = $this->request->params['action'];
        }
        $result = [];
        if (file_exists($path)) {
            $result = $this->_getCsvData($path);
            if (in_array($action, static::PANEL_ACTIONS)) {
                $result = $this->_arrangePanels($result);
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
     * Method that arranges csv fetched fields into panels.
     * @param  array  $data fields
     * @throws \RuntimeException when csv field parameters count does not match
     * @return array        fields arranged in panels
     */
    protected function _arrangePanels(array $data)
    {
        $result = [];

        foreach ($data as $fields) {
            $fieldCount = count($fields);
            if (static::PANEL_COUNT !== $fieldCount) {
                throw new \RuntimeException(
                    sprintf($this->_errorMessages[__FUNCTION__], $fieldCount, static::PANEL_COUNT)
                );

            }
            $panel = array_pop($fields);
            $result[$panel][] = $fields;
        }

        return $result;
    }
}

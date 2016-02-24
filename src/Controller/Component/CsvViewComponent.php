<?php
namespace CsvViews\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

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

        $this->_setTableFields($event);
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
                $assocName = $association->name();
                $query = $table->{$assocName}->find('all');
                $result[$assocName] = $query->all();
            }
        }

        $controller->set('associated', $result);
        $controller->set('_serialize', ['associated']);
    }

    /**
     * Method that passes csv defined Table fields to the View
     * @param \Cake\Event\Event $event An Event instance
     * @return void
     */
    protected function _setTableFields(\Cake\Event\Event $event)
    {
        $controller = $event->subject();
        $path = Configure::readOrFail('CsvViews.path');
        $result = $this->_getFieldsFromCsv(
            $path . $this->request->controller . DS . $this->request->params['action'] . '.csv'
        );

        $controller->set('fields', $result);
        $controller->set('_serialize', ['fields']);
    }

    /**
     * Method that gets fields from a csv file
     * @param  string $path csv file path
     * @return array        csv data
     */
    protected function _getFieldsFromCsv($path)
    {
        $result = [];
        if (file_exists($path)) {
            $result = $this->_getCsvData($path);
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
}

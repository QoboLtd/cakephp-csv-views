<?php
namespace CsvViews\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;

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
     * Method that gets fields from a csv file
     * @param  string $path csv file path
     * @return array        csv data
     */
    public function getFields($path)
    {
        $result = $this->_getCsvData($path);

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

<?php
use \Cake\Utility\Inflector;
use Cake\ORM\TableRegistry;


$defaultOptions = [
    'title' => null,
    'entity' => null,
    'fields' => [],
];
if (empty($options)) {
    $options = [];
}
$options = array_merge($defaultOptions, $options);

// Get plugin name
if (empty($options['plugin'])) {
    $options['plugin'] = $this->request->plugin;
}

// Get controller name
if (empty($options['controller'])) {
    $options['controller'] = $this->request->controller;
}
// Get title
if (empty($options['title'])) {
    $displayField = TableRegistry::get($options['controller'])->displayField();

    $options['title'] = $this->Html->link(
        Inflector::humanize(Inflector::underscore($options['controller'])),
        ['plugin' => $options['plugin'], 'controller' => $options['controller'], 'action' => 'index']
    );
    $options['title'] .= ' &raquo; ';
    $options['title'] .= $options['entity']->$displayField;
}
?>

<div class="row">
    <div class="col-xs-12">
        <div class="panel panel-default">
            <!-- Panel header -->
            <div class="panel-heading">
                <h3 class="panel-title"><?= $options['title'] ?></h3>
            </div>
            <table class="table table-striped" cellpadding="0" cellspacing="0">
                <?php
                    if (!empty($options['fields'])) {
                        foreach ($options['fields'] as $fields) {
                            if (!empty($fields)) {
                                echo '<tr>';
                                foreach ($fields as $field) {
                                    if ('' !== trim($field)) {
                                        echo '<td class="text-right"><strong>';
                                        echo __(Inflector::humanize($field)) . ':';
                                        echo '</strong></td>';
                                        echo '<td>';
                                        if (is_bool($options['entity']->$field)) {
                                            echo $options['entity']->$field ? __('Yes') : __('No');
                                        } else {
                                            echo h($options['entity']->$field);
                                        }
                                        echo '</td>';
                                    } else {
                                        echo '<td>&nbsp;</td>';
                                        echo '<td>&nbsp;</td>';
                                    }
                                }
                                echo '</tr>';
                            }
                        }
                    }
                ?>
            </table>
        </div>
    </div>
</div>



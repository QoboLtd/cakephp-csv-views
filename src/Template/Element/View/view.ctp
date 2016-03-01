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
        <h3><strong><?= $options['title'] ?></strong></h3>
        <?php
            if (!empty($options['fields'])) :
                foreach ($options['fields'] as $panelName => $panelFields) :
        ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <strong><?= Inflector::humanize($panelName); ?></strong>
                </h3>
            </div>
            <table class="table table-hover">
            <?php foreach ($panelFields as $subFields) : ?>
                <tr>
                <?php
                    foreach ($subFields as $field) :
                        if ('' !== trim($field)) :
                            $tableField = Inflector::humanize($field);
                            // foreign key fields
                            if (
                                !empty($csvForeignKeys) &&
                                in_array($field, array_keys($csvForeignKeys)) &&
                                ($csvForeignKeys[$field]->type() === 'manyToOne') &&
                                !empty($csvAssociatedRecords['manyToOne']) &&
                                in_array($field, array_keys($csvAssociatedRecords['manyToOne']))
                            ) {
                                $tableField = Inflector::singularize(
                                    Inflector::humanize($csvForeignKeys[$field]->table())
                                );
                                $tableValue = $this->Html->link(
                                    h($csvAssociatedRecords['manyToOne'][$field]), [
                                        'controller' => $csvForeignKeys[$field]->table(),
                                        'action' => 'view',
                                        $options['entity']->$field
                                    ]
                                );
                            } elseif (
                                !empty($csvListsOptions) &&
                                in_array($field, array_keys($csvListsOptions))
                            ) { // list fields
                                $tableValue = h($csvListsOptions[$field][$options['entity']->$field]['label']);
                            } else {
                                if (is_bool($options['entity']->$field)) {
                                    $tableValue = $options['entity']->$field ? __('Yes') : __('No');
                                } else {
                                    $tableValue = h($options['entity']->$field);
                                }
                            }
                        ?>
                    <td class="col-xs-3 text-right">
                        <strong><?= $tableField; ?>:</strong>
                    </td>
                    <td class="col-xs-3">
                        <?= $tableValue; ?>
                    </td>
                        <?php else : ?>
                    <td class="col-xs-3">&nbsp;</td>
                    <td class="col-xs-3">&nbsp;</td>
                    <?php endif; endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <?php endforeach; endif; ?>
    </div>
</div>

<?= $this->element('CsvViews.associated_records'); ?>
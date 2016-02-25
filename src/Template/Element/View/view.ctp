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
            if (!empty($options['fields'])) {
                foreach ($options['fields'] as $panelName => $panelFields) {
                    echo '<div class="panel panel-default">';
                    echo '<div class="panel-heading">';
                    echo '<h3 class="panel-title"><strong>' . Inflector::humanize($panelName) . '</strong></h3>';
                    echo '</div>';
                    echo '<table class="table table-hover">';
                    foreach ($panelFields as $subFields) {
                        echo '<tr>';
                        foreach ($subFields as $field) {
                            if ('' !== trim($field)) {
                                echo '<td class="col-xs-3 text-right"><strong>';
                                echo Inflector::humanize($field) . ':';
                                echo '</strong></td>';
                                echo '<td class="col-xs-3">';
                                if (is_bool($options['entity']->$field)) {
                                    echo $options['entity']->$field ? __('Yes') : __('No');
                                } else {
                                    echo h($options['entity']->$field);
                                }
                                echo '</td>';
                            } else {
                                echo '<td class="col-xs-3">&nbsp;</td>';
                                echo '<td class="col-xs-3">&nbsp;</td>';
                            }
                        }
                        echo '</tr>';
                    }
                    echo '</table>';
                    echo '</div>';
                }
            }
        ?>
    </div>
</div>

<?php
if (!empty($csvAssociatedRecords)) : ?>
<div class="row">
    <div class="col-xs-12">
        <h3><?= __('Associated Records'); ?></h3>
        <ul id="relatedTabs" class="nav nav-tabs" role="tablist">
<?php
    $active = 'active';
    foreach ($csvAssociatedRecords as $tabName => $assocData) :
?>
            <li role="presentation" class="<?= $active; ?>">
                <a href="#<?= $tabName; ?>" aria-controls="<?= $tabName; ?>" role="tab" data-toggle="tab">
                    <?= Inflector::humanize($assocData['table_name']); ?>
                </a>
            </li>
<?php
    $active = '';
    endforeach;
?>
        </ul>
        <div class="tab-content">
<?php
    $active = 'active';
    foreach ($csvAssociatedRecords as $tabName => $assocData) {
    ?>
            <div role="tabpanel" class="tab-pane <?= $active; ?>" id="<?= $tabName; ?>">
                <table class="table table-hover">
                    <thead>
                        <tr>
                        <?php foreach ($assocData['fields'] as $assocField) : ?>
                            <th><?= $this->Paginator->sort($assocField[0]); ?></th>
                        <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($assocData['records'] as $record) : ?>
                        <tr>
                        <?php foreach ($assocData['fields'] as $assocField) : ?>
                            <?php if ('' !== trim($record->$assocField[0])) : ?>
                            <td>
                            <?php
                                if (is_bool($record->$assocField[0])) {
                                    echo $record->$assocField[0] ? __('Yes') : __('No');
                                } else {
                                    if ('id' === $assocField[0]) {
                                        echo $this->Html->link(
                                            h($record->$assocField[0]), [
                                                'controller' => $assocData['table_name'],
                                                'action' => 'view',
                                                $record->$assocField[0]
                                            ]
                                        );
                                    } else {
                                        echo h($record->$assocField[0]);
                                    }
                                }
                            ?>
                            </td>
                            <?php else : ?>
                            <td>&nbsp;</td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php $active = '';
    }
?>
        </div>
    </div>
</div>
<?php endif; ?>

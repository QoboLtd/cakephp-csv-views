<?php
use \Cake\Utility\Inflector;

$defaultOptions = [
    'title' => null,
    'entity' => null,
    'fields' => [],
];
if (empty($options)) {
    $options = [];
}
$options = array_merge($defaultOptions, $options);

// Get title from the entity
if (empty($options['title'])) {
    $options['title'] = __(
        'Add {0}',
        Inflector::singularize(Inflector::humanize(Inflector::underscore($this->request->controller)))
    );
}
?>

<div class="row">
    <div class="col-xs-12">
        <?= $this->Form->create($options['entity']) ?>
        <fieldset>
            <legend><?= $options['title'] ?></legend>
            <?php
                if (!empty($options['fields'])) {
                    foreach ($options['fields'] as $panelName => $panelFields) {
                        echo '<div class="panel panel-default">';
                        echo '<div class="panel-heading">';
                        echo '<h3 class="panel-title"><strong>' . Inflector::humanize($panelName) . '</strong></h3>';
                        echo '</div>';
                        echo '<div class="panel-body">';
                        foreach ($panelFields as $subFields) {
                            echo '<div class="row">';
                            foreach ($subFields as $field) {
                                echo '<div class="col-xs-6">';
                                if ('' !== trim($field)) {
                                    // foreign key fields
                                    if (
                                        !empty($csvForeignKeys) &&
                                        in_array($field, array_keys($csvForeignKeys)) &&
                                        ($csvForeignKeys[$field]->type() === 'manyToOne')
                                    ) {
                                        $association = $csvForeignKeys[$field];
                                        // typeahead field
                                        echo $this->Form->input($field, [
                                            'name' => $field . '_label',
                                            'id' => $field . '_label',
                                            'type' => 'text',
                                            'data-type' => 'typeahead',
                                            'data-name' => $field,
                                            'autocomplete' => 'off',
                                            'data-url' => '/' . $association->table() . '/autocomplete.json'
                                        ]);
                                        // typeahead hidden field
                                        echo $this->Form->input($field, ['type' => 'hidden']);
                                    } elseif ( // list fields
                                        !empty($csvListsOptions) &&
                                        in_array($field, array_keys($csvListsOptions))
                                    ) {
                                        $options = [];
                                        foreach ($csvListsOptions[$field] as $k => $v) {
                                            if ($v['active']) {
                                                $options[$k] = $v['label'];
                                            }
                                        }
                                        echo '<div class="form-group">';
                                        echo $this->Form->label($field);
                                        echo $this->Form->select($field, $options, ['class' => 'form-control']);
                                        echo '</div>';
                                    } else {
                                        echo $this->Form->input($field);
                                    }
                                } else {
                                    echo '&nbsp;';
                                }
                                echo '</div>';
                            }
                            echo '</div>';
                        }
                        echo '</div>';
                        echo '</div>';
                    }
                }
            ?>
        </fieldset>
        <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-primary']) ?>
        <?= $this->Form->end() ?>
    </div>
</div>

<?php
// enable typeahead library if foreign keys exist
if (!empty($csvForeignKeys)) {
    echo $this->Html->script('CsvViews.bootstrap-typeahead.min.js', ['block' => 'scriptBottom']);
    echo $this->Html->script('CsvViews.typeahead', ['block' => 'scriptBottom']);
}
?>
<?php
use \Cake\Utility\Inflector;
use \CsvViews\FieldHandlers\FieldHandlerFactory;

$fhf = new FieldHandlerFactory();

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
        'Edit {0}',
        Inflector::singularize(Inflector::humanize(Inflector::underscore($this->request->controller)))
    );
}
?>

<div class="row">
    <div class="col-xs-12">
        <?= $this->Form->create($options['entity']); ?>
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
                                    echo $fhf->renderInput($this->name, $field, $options['entity']->$field);
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
// enable typeahead library
// @todo load these files only if foreign/related field exists
echo $this->Html->script('CsvViews.bootstrap-typeahead.min.js', ['block' => 'scriptBottom']);
echo $this->Html->script('CsvViews.typeahead', ['block' => 'scriptBottom']);
?>
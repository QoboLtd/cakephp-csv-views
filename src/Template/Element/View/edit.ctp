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
                    foreach ($options['fields'] as $fields) {
                        if (!empty($fields)) {
                            $width = 12 / count($fields);
                            echo '<div class="row">';
                            foreach ($fields as $field) {
                                echo '<div class="col-xs-' . $width . '">';
                                if ('' !== trim($field)) {
                                    echo $this->Form->input($field);
                                } else {
                                    echo '&nbsp;';
                                }
                                echo '</div>';
                            }
                            echo '</div>';
                        }
                    }
                }
            ?>
        </fieldset>
        <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-primary']) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
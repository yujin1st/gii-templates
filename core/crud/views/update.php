<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();

echo "<?php\n";
?>

use yii\helpers\Html;
use kartik\icons\Icon;

/* @var $this yii\web\View */
<?php if ($generator->formClass): ?>
    /* @var $model <?= ltrim($generator->formClass, '\\') ?> */
<?php else: ?>
    /* @var $model <?= ltrim($generator->modelClass, '\\') ?> */
<?php endif; ?>


$this->title = $model->title . ': ' . <?= $generator->generateString('Updating') ?>;
$this->params['breadcrumbs'][] = ['label' => <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-update">

    <h1><?= "<?= " ?>Html::encode($this->title) ?></h1>

    <?= "<?= " ?>$this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

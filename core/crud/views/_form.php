<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator \yujin1st\gii\core\crud\Generator */

/* @var $model \yii\db\ActiveRecord */
$model = new $generator->modelClass();
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
  $safeAttributes = $model->attributes();
}

echo "<?php\n";
?>

use yii\helpers\Html;
// use yii\widgets\ActiveForm;
use yii\bootstrap4\ActiveForm;
use kartik\icons\Icon;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */
/* @var $form ActiveForm */
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form">

  <?= "<?php " ?>$form = ActiveForm::begin([
  'layout' => 'horizontal',
  ]); ?>

  <?php foreach ($generator->getColumnNames() as $attribute) {
    if (in_array($attribute, $safeAttributes)) {
      echo "    <?= " . $generator->generateActiveField($attribute) . " ?>\n\n";
    }
  } ?>

    <div class="form-group">
        <div class="offset-sm-3 col-sm-9">
            <?= "<?= " ?> Html::submitButton(Icon::show('check') . ($model->model->isNewRecord ? 'Добавить' : 'Сохранить'),
                ['class' => $model->model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            <?= "<?= " ?> Html::a(Icon::show('arrow-left') . 'К списку', ['index'], ['class' => 'btn btn-default']) ?>
            <?= "<?php" ?> if (!$model->model->isNewRecord): ?>
            <?= "<?= " ?> Html::a(Icon::show('trash') . 'Удалить ', ['delete', 'id' => $model->model->id],
                    [
                        'class' => 'btn btn-danger',
                        'data' => ['confirm' => 'Вы действительно хотите удалить ?']
                    ]) ?>
            <?= "<?php endif; ?>" ?>

        </div>
    </div>



  <?= "<?php " ?>ActiveForm::end(); ?>

</div>

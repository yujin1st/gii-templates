<?php
/**
 * This is the template for generating CRUD search class of the specified model.
 */

use yii\helpers\StringHelper;


/* @var $this yii\web\View */
/* @var $generator \yujin1st\gii\core\crud\Generator */

$modelClass = StringHelper::basename($generator->modelClass);
$modelVariable = lcfirst($modelClass);
$formModelClass = StringHelper::basename($generator->formClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);
if ($modelClass === $searchModelClass) {
    $modelAlias = $modelClass . 'Model';
}
$rules = $generator->generateSearchRules();
$labels = $generator->generateSearchLabels();
$searchAttributes = $generator->getSearchAttributes();
$searchConditions = $generator->generateSearchConditions();

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->formClass, '\\')) ?>;

use yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use <?= ltrim($generator->modelClass, '\\') . (isset($modelAlias) ? " as $modelAlias" : "") ?>;

/**
 * <?= $formModelClass ?> represents the form behind the `<?= $generator->modelClass ?>`.
 */
class <?= $formModelClass ?> extends Model

{

/** @var <?= $generator->modelClass ?> */
public $model;

public $title;
public $order;
public $enabled;


/**
* <?= $generator->modelClass ?>Form constructor.
*
* @param <?= $generator->modelClass ?>|null $<?= $modelVariable ?>
* @param array $config
*/
public function __construct(?<?= $generator->modelClass ?> $<?= $modelVariable ?> = null, $config = [])
{
parent::__construct($config);
if ($<?= $modelVariable ?>) {
$this->model = $<?= $modelVariable ?>;
$this->loadAttributesFromModel();
} else {
$this->model = new <?= $generator->modelClass ?>([
'deleted' => 0,
]);
}

parent::__construct($config);
}


/**
* @inheritdoc
*/
public function scenarios()
{
return [
self::SCENARIO_DEFAULT => [
],
];
}

/**
* @inheritdoc
*/
public function rules()
{
return [
//insert from model
];
}


/**
*
*/
private function loadAttributesFromModel(): void
{
$this->title = $this->model->title;
$this->order = $this->model->order;
$this->enabled = $this->model->enabled;
}

/**
*
*/
private function populateAttributesToModel(): void
{
$this->model->title = $this->title;
$this->model->order = $this->order;
$this->model->enabled = $this->enabled;
}

/**
* @param $validate
*
* @return bool
*/
public function save($validate = true): bool
{
if ($validate && !$this->validate()) {
return false;
}
$this->populateAttributesToModel();
$this->model->save(false);


return true;
}

/**
* @inheritdoc
*/
public function attributeLabels(): array
{
return [
// insert from model
];
}


}

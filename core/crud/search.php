<?php
/**
 * This is the template for generating CRUD search class of the specified model.
 */

use yii\helpers\StringHelper;


/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$modelClass = StringHelper::basename($generator->modelClass);
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

namespace <?= StringHelper::dirname(ltrim($generator->searchModelClass, '\\')) ?>;

use yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use <?= ltrim($generator->modelClass, '\\') . (isset($modelAlias) ? " as $modelAlias" : "") ?>;

/**
 * <?= $searchModelClass ?> represents the model behind the search form about `<?= $generator->modelClass ?>`.
 */
class <?= $searchModelClass ?> extends <?= isset($modelAlias) ? $modelAlias : $modelClass ?>

{

/** @var bool Skip deleted records */
public $onlyActive = false;
/** @var bool Skip disabled records */
public $onlyEnabled = false;
/** @var bool Skip deleted records */
public $excludeDeleted = true;


/** @var int $page size */
public $pageSize = 20;
/** @var int */
public $page = null;
/** @var string */
public $order;

/** @var bool whether data were loaded */
public $hasData = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            <?= implode(",\n            ", $rules) ?>,
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param array|null $formName
     *
     * @return <?= $modelClass ?>Query
     */
    public function search($params, $formName = null)
    {
        $query = <?= isset($modelAlias) ? $modelAlias : $modelClass ?>::find();

        if ($this->onlyActive) $query->active();
        if ($this->onlyEnabled) $query->enabled();
        if ($this->excludeDeleted) $query->excludeDeleted();


        $this->hasData = $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $query;
        }

        // grid filtering conditions
        <?= implode("\n        ", $searchConditions) ?>

        return $query;
    }

/**
* @param array $params
* @param null $formName
* @return ActiveDataProvider
*/
public function searchDP($params = [], $formName = null): ActiveDataProvider
{
return $this->searchDPQuery($this->search($params, $formName));
}

/**
* @param $query <?= $modelClass ?>Query
* @return ActiveDataProvider
*/
public function searchDPQuery($query): ActiveDataProvider
{
return new ActiveDataProvider([
'query' => $query,
'pagination' => [
'pageSize' => $this->pageSize,
'page' => $this->page,
],
]);
}


}

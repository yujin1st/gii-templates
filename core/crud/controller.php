<?php
/**
 * @author Evgeniy Bobrov <yujin1st@gmail.com>
 * @copyright Digital-agency «Space crabs»
 * @link http://spacecrabs.ru <hello@spacecrabs.ru>
 */

/**
 * This is the template for generating a CRUD controller class file.
 */

use yii\db\ActiveRecordInterface;
use yii\helpers\StringHelper;


/* @var $this yii\web\View */
/* @var $generator \yujin1st\gii\core\crud\Generator */

$controllerClass = StringHelper::basename($generator->controllerClass);
$formClass = StringHelper::basename($generator->formClass);
$modelClass = StringHelper::basename($generator->modelClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);
if ($modelClass === $searchModelClass) {
    $searchModelAlias = $searchModelClass . 'Search';
}

/* @var $class ActiveRecordInterface */
$class = $generator->modelClass;
$pks = $class::primaryKey();
$urlParams = $generator->generateUrlParams();
$actionParams = $generator->generateActionParams();
$actionParamComments = $generator->generateActionParamComments();

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>;

use yii;
use <?= ltrim($generator->formClass, '\\') ?>;
use <?= ltrim($generator->modelClass, '\\') ?>;
<?php if (!empty($generator->searchModelClass)): ?>
use <?= ltrim($generator->searchModelClass, '\\') . (isset($searchModelAlias) ? " as $searchModelAlias" : "") ?>;
<?php else: ?>
use yii\data\ActiveDataProvider;
<?php endif; ?>
use <?= ltrim($generator->baseControllerClass, '\\') ?>;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * <?= $controllerClass ?> implements the CRUD actions for <?= $modelClass ?> model.
 */
class <?= $controllerClass ?> extends <?= StringHelper::basename($generator->baseControllerClass) . "\n" ?>
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],


            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['view', 'index', 'search'],
                        'allow' => true,
                        // 'roles' => [Access::VIEW],
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['create', 'update', 'delete'],
                        'allow' => true,
                        // 'roles' => [Access::UPDATE],
                        'roles' => ['@'],
                    ],
                ],
            ],

        ];
    }

    /**
     * Lists all <?= $modelClass ?> models.
     * @return mixed
     */
    public function actionIndex()
    {
<?php if (!empty($generator->searchModelClass)): ?>
        $searchModel = new <?= isset($searchModelAlias) ? $searchModelAlias : $searchModelClass ?>();
        $searchModel->onlyActive = true;
        $searchModel->excludeDeleted = true;
        $dataProvider = $searchModel->searchDP(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
<?php else: ?>
        $dataProvider = new ActiveDataProvider([
            'query' => <?= $modelClass ?>::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
<?php endif; ?>
    }

    /**
     * Displays a single <?= $modelClass ?> model.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return mixed
    * @throws NotFoundHttpException
    */
    public function actionView(<?= $actionParams ?>)
    {
        return $this->render('view', [
            'model' => $this->findModel(<?= $actionParams ?>),
        ]);
    }

    /**
     * Creates a new <?= $modelClass ?> model.
     * @return mixed
     */
    public function actionCreate()
    {

<?php if($generator->formClass): ?>
        $model = new <?= $formClass ?>();
<?php else: ?>
        $model = new <?= $modelClass ?>();
        $model->createUserId = Yii::$app->user->id;
        $model->updateUserId = Yii::$app->user->id;
        $model->order = 0;
        $model->enabled = 1;
<?php endif; ?>

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', <?= $generator->generateString($modelClass.' created') ?> );
            return $this->redirect(['view', <?= $urlParams ?>]);
            return $this->redirect(['update', <?= $urlParams ?>]);
        } 
        return $this->render('create', [
            'model' => $model,
        ]);



    }

    /**
     * Updates an existing <?= $modelClass ?> model.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
    * @throws NotFoundHttpException
     * @return mixed
     */
    public function actionUpdate(<?= $actionParams ?>)
    {
<?php if ($generator->formClass): ?>
        $model = new <?= $formClass ?>($this->findModel(<?= $actionParams ?>));
<?php else: ?>
        $model = $this->findModel(<?= $actionParams ?>);
        $model->updateUserId = Yii::$app->user->id;
<?php endif; ?>




        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', <?= $generator->generateString($modelClass . ' updated') ?>);
            return $this->redirect(['view', <?= $urlParams ?>]);
            return $this->redirect(['update', <?= $urlParams ?>]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing <?= $modelClass ?> model.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
    * @throws NotFoundHttpException
     * @return mixed
     */
    public function actionDelete(<?= $actionParams ?>)
    {
        $model = $this->findModel(<?= $actionParams ?>);
        if ($model->delete()){
          Yii::$app->session->setFlash('success', <?= $generator->generateString($modelClass . ' deleted') ?>);
        }

        return $this->redirect(['index']);
    }


    /**
     * Search for typeahead
     *
     * @param $term
     * @param bool $new
     * @return array
     */
    public function actionSearch($term, $new = false) {
        $data = [];
        $term = trim($term);
        /** @var <?= $modelClass ?>[] $models */
        $models = <?= $modelClass ?>::find()
          ->filterWhere(['like', 'title', $term])
          //->active()
          ->limit(10)->all();
        if ($models) foreach ($models as $model) {
          $data[] = [
            'value' => $model->title,
            'id' => $model->id,
          ];
        }

        if ($new && !$data) $data[] = [
          'value' => 'Add new',
          'address' => '',
          'id' => -1,
        ];

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $data;
    }



    /**
     * Finds the <?= $modelClass ?> model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return <?=                   $modelClass ?> the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(<?= $actionParams ?>)
    {
<?php
if (count($pks) === 1) {
    $condition = '$id';
} else {
    $condition = [];
    foreach ($pks as $pk) {
        $condition[] = "'$pk' => \$$pk";
    }
    $condition = '[' . implode(', ', $condition) . ']';
}
?>
        if (($model = <?= $modelClass ?>::findOne(<?= $condition ?>)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}

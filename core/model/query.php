<?php
/**
 * @author Evgeniy Bobrov <yujin1st@gmail.com>
 * @copyright Digital-agency «Space crabs»
 * @link http://spacecrabs.ru <hello@spacecrabs.ru>
 */

/**
 * This is the template for generating the ActiveQuery class.
 */

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */
/* @var $className string class name */
/* @var $modelClassName string related model class name */

$modelFullClassName = $modelClassName;
if ($generator->ns !== $generator->queryNs) {
    $modelFullClassName = '\\' . $generator->ns . '\\' . $modelFullClassName;
}

echo "<?php\n";
?>

namespace <?= $generator->queryNs ?>;
use <?= $modelFullClassName ?>
/**
 * This is the ActiveQuery class for [[<?= $modelFullClassName ?>]].
 *
 * @see <?= $modelFullClassName . "\n" ?>
 */
class <?= $className ?> extends <?= '\\' . ltrim($generator->queryBaseClass, '\\') . "\n" ?>
{


/**
* @param $enabled
* @return $this
*/
public function enabled($enabled = true): self{
return $this->andWhere([<?= $generator->modelClass ?>::tableName() . '.enabled' => (int)$enabled]);
}

/**
* Filter deleted
*
* @return $this
*/
public function excludeDeleted(): self {
return $this->andWhere([<?= $generator->modelClass ?>::tableName() . '.deleted' => 0]);
}

/**
* Only visible and active nodes
*
* @return $this
*/
public function active() {
return $this->enabled(true)->excludeDeleted();
}

/**
* Search by title
*
* @param $title
* @param bool $exact
* @return $this
*/
public function byTitle($title, $exact = true): self
{
if ($exact) {
return $this->andWhere([<?= $generator->modelClass ?>::tableName() . '.title' => $title]);
} else {
return $this->andWhere(['like', <?= $generator->modelClass ?>::tableName() . '.title', $title]);
}
}


/**
     * @inheritdoc
     * @return <?= $modelFullClassName ?>[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return <?= $modelFullClassName ?>|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}

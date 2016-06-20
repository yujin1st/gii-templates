<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $queryClassName string query class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */

echo "<?php\n";
?>

namespace <?= $generator->ns ?>;

use yii;

/**
 * This is the model class for table "<?= $generator->generateTableName($tableName) ?>".
 *
<?php foreach ($tableSchema->columns as $column): ?>
 * @property <?= "{$column->phpType} \${$column->name}\n" ?>
<?php endforeach; ?>
<?php if (!empty($relations)): ?>
 *
<?php foreach ($relations as $name => $relation): ?>
 * @property <?= $relation[1] . ($relation[2] ? '[]' : '') . ' $' . lcfirst($name) . "\n" ?>
<?php endforeach; ?>
<?php endif; ?>
 */
class <?= $className ?> extends <?= '\\' . ltrim($generator->baseClass, '\\') . "\n" ?>
{


    /**
     * @event Event an event that is triggered after a record is recovered.
     */
    const EVENT_AFTER_RECOVER = 'afterRecover';

    /** Active Record
    ******************************************************************** */

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '<?= $generator->generateTableName($tableName) ?>';
    }
<?php if ($generator->db !== 'db'): ?>

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('<?= $generator->db ?>');
    }
<?php endif; ?>

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return[
          self::SCENARIO_DEFAULT => [
<?php foreach ($labels as $name => $label): ?>
  <?= "'$name', " ?>
<?php endforeach; ?>
          ],
        ];
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [<?= "\n            " . implode(",\n            ", $rules) . ",\n        " ?>];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
<?php foreach ($labels as $name => $label): ?>
            <?= "'$name' => " . $generator->generateString($label) . ",\n" ?>
<?php endforeach; ?>
        ];
    }

  /** @inheritdoc */
  public function behaviors() {
    return [
      //[
      //  'class' => TimestampBehavior::className(),
      //  'createdAtAttribute' => 'createTime',
      //  'updatedAtAttribute' => 'updateTime',
      //],
    ];
  }

  /**
   * Safe delete

   * @return bool|int
   * @throws yii\db\Exception
   */
  protected function deleteInternal() {
    if (!$this->beforeDelete()) {
      return false;
    }

    $this->deleted = 1;
    $ok = $this->save(false);

    $this->setOldAttributes(null);
    $this->afterDelete();

    return $ok;
  }


  /**
   * Recover deleted item
   */
  public function recover() {
    $this->deleted = 0;
    $ok = $this->save(false);

    $this->trigger(self::EVENT_AFTER_RECOVER);
    return $ok;
  }


<?php if ($queryClassName): ?>
<?php
    $queryClassFullName = ($generator->ns === $generator->queryNs) ? $queryClassName : '\\' . $generator->queryNs . '\\' . $queryClassName;
    echo "\n";
?>
    /**
     * @inheritdoc
     * @return <?= $queryClassFullName ?> the active query used by this AR class.
     */
    public static function find()
    {
        return new <?= $queryClassFullName ?>(get_called_class());
    }
<?php endif; ?>

    /** Relations
    ******************************************************************** */

<?php foreach ($relations as $name => $relation): ?>

    /**
     * @return \yii\db\ActiveQuery
     * @return <?= $name ?>Query
     */
    public function get<?= $name ?>()
    {
        <?= $relation[0] . "\n" ?>
    }
<?php endforeach; ?>



    /** Properties
    ******************************************************************** */

    /** Main
    ******************************************************************** */

}

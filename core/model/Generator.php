<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yujin1st\gii\core\model;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Connection;
use yii\db\Schema;
use yii\db\TableSchema;
use yii\gii\CodeFile;
use yii\helpers\Inflector;
use yii\base\NotSupportedException;

/**
 * This generator will generate one or multiple ActiveRecord classes for the specified database table.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Generator extends \yii\gii\generators\model\Generator
{

    /**
     * @return array the generated relation declarations
     */
    protected function generateRelations()
    {
        if ($this->generateRelations === self::RELATIONS_NONE) {
            return [];
        }

        $db = $this->getDbConnection();

        $relations = [];
        foreach ($this->getSchemaNames() as $schemaName) {
            foreach ($db->getSchema()->getTableSchemas($schemaName) as $table) {
                $className = $this->generateClassName($table->fullName);
                foreach ($table->foreignKeys as $refs) {
                    $refTable = $refs[0];
                    $refTableSchema = $db->getTableSchema($refTable);
                    if ($refTableSchema === null) {
                        // Foreign key could point to non-existing table: https://github.com/yiisoft/yii2-gii/issues/34
                        continue;
                    }
                    unset($refs[0]);
                    $fks = array_keys($refs);
                    $refClassName = $this->generateClassName($refTable);

                    // Add relation for this table
                    $link = $this->generateRelationLink(array_flip($refs));
                    $relationName = $this->generateRelationName($relations, $table, $fks[0], false);
                    $relations[$table->fullName][$relationName] = [
                        "\$query =\$this->hasOne($refClassName::className(), $link); 
                        return \$query; ",
                        $refClassName,
                        false,
                    ];

                    // Add relation for the referenced table
                    $hasMany = $this->isHasManyRelation($table, $fks);
                    $link = $this->generateRelationLink($refs);
                    $relationName = $this->generateRelationName($relations, $refTableSchema, $className, $hasMany);
                    $relations[$refTableSchema->fullName][$relationName] = [
                        "\$query =\$this->" . ($hasMany ? 'hasMany' : 'hasOne') . "($className::className(), $link);
                        return \$query ;",
                        $className,
                        $hasMany,
                    ];
                }

                if (($junctionFks = $this->checkJunctionTable($table)) === false) {
                    continue;
                }

                $relations = $this->generateManyManyRelations($table, $junctionFks, $relations);
            }
        }

        if ($this->generateRelations === self::RELATIONS_ALL_INVERSE) {
            return $this->addInverseRelations($relations);
        }

        return $relations;
    }

  /**
   * Generates relations using a junction table by adding an extra viaTable().
   *
   * @param \yii\db\TableSchema the table being checked
   * @param array $fks obtained from the checkJunctionTable() method
   * @param array $relations
   * @return array modified $relations
   */
  private function generateManyManyRelations($table, $fks, $relations) {
    $db = $this->getDbConnection();

    foreach ($fks as $pair) {
      list($firstKey, $secondKey) = $pair;
      $table0 = $firstKey[0];
      $table1 = $secondKey[0];
      unset($firstKey[0], $secondKey[0]);
      $className0 = $this->generateClassName($table0);
      $className1 = $this->generateClassName($table1);
      $table0Schema = $db->getTableSchema($table0);
      $table1Schema = $db->getTableSchema($table1);

      $link = $this->generateRelationLink(array_flip($secondKey));
      $viaLink = $this->generateRelationLink($firstKey);
      $relationName = $this->generateRelationName($relations, $table0Schema, key($secondKey), true);
      $relations[$table0Schema->fullName][$relationName] = [
        "/** @var \$query {$relationName}Query */ \n
        \$query = \$this->hasMany($className1::className(), $link)->viaTable('"
        . $this->generateTableName($table->name) . "', $viaLink);
        return \$query; ",
        $className1,
        true,
      ];

      $link = $this->generateRelationLink(array_flip($firstKey));
      $viaLink = $this->generateRelationLink($secondKey);
      $relationName = $this->generateRelationName($relations, $table1Schema, key($firstKey), true);
      $relations[$table1Schema->fullName][$relationName] = [
        "/** @var \$query {$relationName}Query */ \n
        \$query = \$this->hasMany($className0::className(), $link)->viaTable('"
        . $this->generateTableName($table->name) . "', $viaLink); \n
        if (\$onlyActive)  \$query->active();
        return \$query; ",
        $className0,
        true,
      ];
    }

    return $relations;
  }


}

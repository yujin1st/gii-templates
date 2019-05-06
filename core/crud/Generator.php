<?php

namespace yujin1st\gii\core\crud;


use Yii;
use yii\db\ActiveRecord;
use yii\gii\CodeFile;

class Generator extends \yii\gii\generators\crud\Generator
{
    public $formClass;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [
                ['formClass'],
                'match',
                'pattern' => '/^[\w\\\\]*$/',
                'message' => 'Only word characters and backslashes are allowed.'
            ],
            [['formClass'], 'validateModelClass'],
            [
                ['formClass'],
                'match',
                'pattern' => '/Form$/',
                'message' => 'Form$ class name must be suffixed with "Form$".'
            ],

            ['formClass', 'safe']
        ]);
    }

    public function generate()
    {

        $files = parent::generate();

        if (!empty($this->formClass)) {
            $formModel = Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->formClass, '\\') . '.php'));
            $files[] = new CodeFile($formModel, $this->render('formClass.php'));
        }

        return $files;
    }

    /**
     * Generates URL parameters
     * @return string
     */
    public function generateUrlParams()
    {
        /* @var $class ActiveRecord */
        $class = $this->modelClass;
        $pks = $class::primaryKey();
        if (count($pks) === 1) {
            if (is_subclass_of($class, 'yii\mongodb\ActiveRecord')) {
                return "'id' => (string)\$model->{$pks[0]}";
            }
            if ($this->formClass) {
                return "'id' => \$model->model->{$pks[0]}";
            } else {
                return "'id' => \$model->{$pks[0]}";

            }
        }

        $params = [];
        foreach ($pks as $pk) {
            if (is_subclass_of($class, 'yii\mongodb\ActiveRecord')) {
                $params[] = "'$pk' => (string)\$model->$pk";
            } else {
                $params[] = "'$pk' => \$model->$pk";
            }
        }

        return implode(', ', $params);
    }

}

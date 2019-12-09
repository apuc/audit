<?php

namespace frontend\modules\externallinks\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ExternalLinks;

/**
 * ExternallinksSearch represents the model behind the search form of `common\models\ExternalLinks`.
 */
class ExternallinksSearch extends ExternalLinks
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'audit_id'], 'integer'],
            [['acceptor', 'anchor'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = ExternalLinks::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'audit_id' => $this->audit_id,
        ]);

        $query->andFilterWhere(['like', 'acceptor', $this->acceptor])
            ->andFilterWhere(['like', 'anchor', $this->anchor]);

        return $dataProvider;
    }
}

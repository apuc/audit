<?php

namespace frontend\modules\dns\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Dns;

/**
 * DnsSearch represents the model behind the search form of `common\models\Dns`.
 */
class DnsSearch extends Dns
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'ttl', 'site_id'], 'integer'],
            [['class', 'type', 'target', 'ip'], 'safe'],
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
        $query = Dns::find();

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
            'ttl' => $this->ttl,
            'site_id' => $this->site_id,
        ]);

        $query->andFilterWhere(['like', 'class', $this->class])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'target', $this->target])
            ->andFilterWhere(['like', 'ip', $this->ip]);

        return $dataProvider;
    }
}

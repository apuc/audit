<?php

namespace frontend\modules\audit\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Audit;

/**
 * AuditSearch represents the model behind the search form of `common\models\Audit`.
 */
class AuditSearch extends Audit
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'size', 'loading_time', 'created_at', 'url_id'], 'integer'],
            [['server_response_code'], 'safe'],
            [['google_indexing', 'yandex_indexing'], 'boolean'],
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
        $query = Audit::find();

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
            'size' => $this->size,
            'loading_time' => $this->loading_time,
            'created_at' => $this->created_at,
            'url_id' => $this->url_id,
            'google_indexing' => $this->google_indexing,
            'yandex_indexing' => $this->yandex_indexing,
        ]);

        $query->andFilterWhere(['like', 'server_response_code', $this->server_response_code]);

        return $dataProvider;
    }
}

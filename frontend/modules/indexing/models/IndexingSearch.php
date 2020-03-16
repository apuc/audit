<?php

namespace frontend\modules\indexing\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Indexing;

/**
 * IndexingSearch represents the model behind the search form of `common\models\Indexing`.
 */
class IndexingSearch extends Indexing
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'google_indexed_pages', 'site_id', 'iks', 'status_google', 'status_yandex', 'status_date_cache', 'status_indexing_pages', 'status_iks'], 'integer'],
            [['google_indexing', 'yandex_indexing'], 'boolean'],
            [['date_cache'], 'safe'],
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
        $query = Indexing::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC
                ]
            ]
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
            'google_indexing' => $this->google_indexing,
            'google_indexed_pages' => $this->google_indexed_pages,
            'yandex_indexing' => $this->yandex_indexing,
            'site_id' => $this->site_id,
            'iks' => $this->iks,
            'status_google' => $this->status_google,
            'status_yandex' => $this->status_yandex,
            'status_date_cache' => $this->status_date_cache,
            'status_indexing_pages' => $this->status_indexing_pages,
            'status_iks' => $this->status_iks,
        ]);

        $query->andFilterWhere(['like', 'date_cache', $this->date_cache]);

        return $dataProvider;
    }
}

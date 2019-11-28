<?php

namespace frontend\modules\url\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\url\models\Url;

/**
 * UrlSearch represents the model behind the search form of `frontend\modules\url\models\Url`.
 */
class UrlSearch extends Url
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'site_id'], 'integer'],
            [['url', 'dns'], 'safe'],
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
        $query = Url::find();

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
            'site_id' => $this->site_id,
        ]);

        $query->andFilterWhere(['like', 'url', $this->url])
            ->andFilterWhere(['like', 'dns', $this->dns]);

        return $dataProvider;
    }
}

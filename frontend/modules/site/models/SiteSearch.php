<?php

namespace frontend\modules\site\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Site;

/**
 * SiteSearch represents the model behind the search form of `common\models\Site`.
 */
class SiteSearch extends Site
{
    public $dns;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'creation_date', 'expiration_date', 'theme_id'], 'integer'],
            [['name', 'registrar', 'states', 'dns'], 'safe'],
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
        $query = Site::find();

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
            'creation_date' => $this->creation_date,
            'expiration_date' => $this->expiration_date,
            'theme_id' => $this->theme_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'registrar', $this->registrar])
            ->andFilterWhere(['like', 'states', $this->states]);

        return $dataProvider;
    }
}

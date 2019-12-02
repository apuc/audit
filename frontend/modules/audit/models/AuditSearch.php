<?php

namespace frontend\modules\audit\models;

use common\classes\Debug;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Audit;
use common\models\Url;

/**
 * AuditSearch represents the model behind the search form of `common\models\Audit`.
 */
class AuditSearch extends Audit
{
    public $url;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'size', 'loading_time', 'created_at'], 'integer'],
            [['server_response_code', 'url'], 'safe'],
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
        $query = Audit::find()->leftJoin('url', 'audit.url_id = url.id')->with('url');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC
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
            'size' => $this->size,
            'loading_time' => $this->loading_time,
            'created_at' => $this->created_at,
            'url_id' => $this->url_id,
            'google_indexing' => $this->google_indexing,
            'yandex_indexing' => $this->yandex_indexing,
            //'url.url' => $this->url,
        ]);
        $query->andFilterWhere(['like', 'url.url', $this->url]);
        $query->andFilterWhere(['like', 'server_response_code', $this->server_response_code]);

        return $dataProvider;
    }
}

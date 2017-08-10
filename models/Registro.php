<?php

namespace app\models;

use Yii;

class Registro extends \yii\db\ActiveRecord {

    public $registroID, $registroData, $registroDataSistema, $bicoRegistroBicoID, $bicoDescricao, $bicoRegistroValor, $bicoRegistroAtual, $bicoRegistroAnterior, $bicoRegistroRetorno, $bombaDescricao, $tipoCombustivelDescricao;

    public static function tableName() {
        return 'registro';
    }

    public function rules() {
        return [
            [['posto_id', 'data', 'status'], 'required', 'message' => 'Campo obrigatório'],
            [['posto_id', 'status'], 'integer'],
            [['data', 'data_sistema'], 'safe'],
            [['posto_id'], 'exist', 'skipOnError' => true, 'targetClass' => Posto::className(), 'targetAttribute' => ['posto_id' => 'posto_id']],
        ];
    }

    public function attributeLabels() {
        return [
            'registro_id' => 'Registro ID',
            'posto_id' => 'Posto ID',
            'data' => 'Data',
            'data_sistema' => 'Data Sistema',
            'status' => 'Situação',
        ];
    }

    public function getBicoRegistros() {
        return $this->hasMany(BicoRegistro::className(), ['registro_id' => 'registro_id']);
    }

    public function getPosto() {
        return $this->hasOne(Posto::className(), ['posto_id' => 'posto_id']);
    }

    public function getEstoque() {
        $qtdeCompraGasolina = ProdutoNegociacao::find()
                ->where(['negociacao_id' => 2, 'status' => 1, 'produto_id' => 1, 'posto_id' => Yii::$app->user->identity->posto_id])
                ->sum('qtde');

        $qtdeVendaGasolina = BicoRegistro::find()
                ->leftJoin('registro', 'bico_registro.registro_id = registro.registro_id')
                ->leftJoin('bico', 'bico_registro.bico_id = bico.bico_id')
                ->where(['bico.tipo_combustivel_id' => 1, 'registro.posto_id' => Yii::$app->user->identity->posto_id])
                ->sum('bico_registro.registro_atual - bico_registro.registro_anterior');

        $qtdeVendaGasolinaRetorno = BicoRegistro::find()
                ->leftJoin('registro', 'bico_registro.registro_id = registro.registro_id')
                ->leftJoin('bico', 'bico_registro.bico_id = bico.bico_id')
                ->where(['bico.tipo_combustivel_id' => 1, 'registro.posto_id' => Yii::$app->user->identity->posto_id])
                ->sum('bico_registro.retorno');

        $resultGasolina = ($qtdeCompraGasolina - $qtdeVendaGasolina) != NULL ? ($qtdeCompraGasolina - $qtdeVendaGasolina) + $qtdeVendaGasolinaRetorno : 0;

        $qtdeCompraDiesel = ProdutoNegociacao::find()
                ->where(['negociacao_id' => 2, 'status' => 1, 'produto_id' => 2, 'posto_id' => Yii::$app->user->identity->posto_id])
                ->sum('qtde');

        $qtdeVendaDiesel = BicoRegistro::find()
                ->leftJoin('registro', 'bico_registro.registro_id = registro.registro_id')
                ->leftJoin('bico', 'bico_registro.bico_id = bico.bico_id')
                ->where(['bico.tipo_combustivel_id' => 2, 'registro.posto_id' => Yii::$app->user->identity->posto_id])
                ->sum('bico_registro.registro_atual - bico_registro.registro_anterior');

        $qtdeVendaDieselRetorno = BicoRegistro::find()
                ->leftJoin('registro', 'bico_registro.registro_id = registro.registro_id')
                ->leftJoin('bico', 'bico_registro.bico_id = bico.bico_id')
                ->where(['bico.tipo_combustivel_id' => 2, 'registro.posto_id' => Yii::$app->user->identity->posto_id])
                ->sum('bico_registro.retorno');

        $resultDiesel = ($qtdeCompraDiesel - $qtdeVendaDiesel) != NULL ? ($qtdeCompraDiesel - $qtdeVendaDiesel) + $qtdeVendaDieselRetorno : 0;

        $result = ['Gasolina' => $resultGasolina, 'Diesel' => $resultDiesel];

        return $result;
    }

    public function deleteBicoRegistros() {
        $modelsBicoRegistro = BicoRegistro::findAll(['registro_id' => $this->registro_id]);
        foreach ($modelsBicoRegistro as $modelBicoRegistro) {
            $modelsValorSaida = ValorSaida::findAll(['bico_registro_id' => $modelBicoRegistro->bico_registro_id]);
            foreach ($modelsValorSaida as $modelValorSaida) {
                ValorSaida::findOne(['valor_saida_id' => $modelValorSaida->valor_saida_id])->delete();
            }
            BicoRegistro::findOne(['bico_registro_id' => $modelBicoRegistro->bico_registro_id])->delete();
        }
    }

    public function deleteRegistros() {
        $backup = '';
        
        $modelsRegistro = Registro::find()
                ->where(['>=', 'registro_id', $this->registro_id])
                ->andWhere(['posto_id' => $this->posto_id])
                ->all();
        
        $backup .= '<h4 class="modal-title" style="margin-bottom: 15px;">Realizado no dia ' . date('d/m/Y', strtotime($this->data_sistema)) . ' ás ' . date('H:i', strtotime($this->data_sistema)) . '</h4>';
        
        foreach ($modelsRegistro as $modelRegistro) {
            $backup .= '<table class="table table-striped table-bordered text-center">';
            $backup .= '<thead>';
            $backup .= '<tr><td colspan="8" class="text-bold text-uppercase">' . date('d/m/Y', strtotime($modelRegistro->data)) . '</td></tr>';
            $backup .= '<tr><td class="text-bold" style="vertical-align: middle;">Bomba</td><td class="text-bold" style="vertical-align: middle;">Bico</td><td class="text-bold" style="vertical-align: middle;">Produto</td><td class="text-bold" style="vertical-align: middle;">Valor #Litro</td><td class="text-bold" style="vertical-align: middle;">Registro Anterior</td><td class="text-bold" style="vertical-align: middle;">Registro Atual</td><td class="text-bold" style="vertical-align: middle;">Retorno</td><td class="text-bold" style="vertical-align: middle;">Quantidade #Litro</td></tr>';
            $backup .= '</thead>';
            $backup .= '<tbody>';
            $modelsBicoRegistro = BicoRegistro::findAll(['registro_id' => $modelRegistro->registro_id]);
            foreach ($modelsBicoRegistro as $modelBicoRegistro) {
                $backup .= '<tr><td>' . $modelBicoRegistro->bico->bomba->descricao . '</td><td>' . $modelBicoRegistro->bico->descricao . '</td><td>' . $modelBicoRegistro->bico->tipoCombustivel->descricao . '</td><td>R$ ' . number_format($modelBicoRegistro->valor, 2, ',', '.') . '</td><td>' . number_format($modelBicoRegistro->registro_anterior, 0, '.', '.') . '</td><td>' . number_format($modelBicoRegistro->registro_atual, 0, '.', '.') . '</td><td>' . number_format($modelBicoRegistro->retorno, 0, '.', '.') . '</td><td>' . number_format((($modelBicoRegistro->registro_atual - $modelBicoRegistro->registro_anterior) - $modelBicoRegistro->retorno), 0, '.', '.')  . '</td></tr>';
                $modelsValorSaida = ValorSaida::findAll(['bico_registro_id' => $modelBicoRegistro->bico_registro_id]);
                foreach ($modelsValorSaida as $modelValorSaida) {
                    ValorSaida::findOne(['valor_saida_id' => $modelValorSaida->valor_saida_id])->delete();
                }
                $backup .= '</tbody>';
                BicoRegistro::findOne(['bico_registro_id' => $modelBicoRegistro->bico_registro_id])->delete();
            }
            $backup .= '</table>';
            Registro::findOne(['registro_id' => $modelRegistro->registro_id])->delete();
        }
        
        $modelBackup = new Backup();
        $modelBackup->posto_id = $this->posto_id;
        $modelBackup->descricao = $backup;
        $modelBackup->save();
        
    }

    public function getBackup() {
        $backup = Backup::find()->where(['posto_id' => Yii::$app->user->identity->posto_id])->orderBy(['backup.backup_id' => SORT_DESC])->one()->descricao;
                
        return $backup != NULL ? $backup : 'Nenhum resultado encontrado.';
    }

}

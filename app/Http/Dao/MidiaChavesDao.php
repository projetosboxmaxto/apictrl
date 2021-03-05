<?php
namespace App\Http\Dao;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MidiaChavesDao{
    
    /*
          public static function garanteChaves($reg, $tabela)
        {
              
          
            $servidor = 1;

            $sequencial = 0; $sqlChave = "";
            $ano = date("Y");
            
            if ( $reg->ano == ""){
                $reg->ano = $ano;
            }
            if ( $reg->servidor == ""){
                $reg->servidor = $servidor;
            }
             if ( $reg->sequencial == ""){
                $reg->servidor = $servidor;
            }
           
            //Coluna ano..
            $sqlChave = " select sequencial from chaves where " .
                         " tabela='" . $tabela . "' and ".
                         " servidor = ". $reg->servidor.
                         " and ano = " . $reg->ano;
            
            if (dr.Table.Columns.Contains("sequencial"))
            {
                //Coluna ano..
                if (dr["sequencial"] == DBNull.Value)
                {
                    sqlChave = " select sequencial from chaves where " +
                         " tabela='" + dr.Table.TableName + "' and " +
                         " servidor = " + "1" +
                         " and ano = " + ano.ToString();

                    object seqatual = ConnAccess.executeScalar(this.conn, sqlChave);

                    //Se ele não encontrar a porcaria do registro pra matéria.. então forço
                    // que ele ache outra opçõa para substituir o registro.
                    if (dr.Table.TableName == "materia_radiotv_jornal" &&
                         (seqatual == null || seqatual == DBNull.Value))
                    {
                        sqlChave = " select max(sequencial) as sequencial from " + dr.Table.TableName.Trim() + " where ano =" + ano.ToString() +
                             " and servidor = " + servidor;

                        seqatual = ConnAccess.executeScalar(this.conn, sqlChave);

                    }
                    if (seqatual == null || seqatual == DBNull.Value)
                    {
                        seqatual = 0;
                    }
                    sequencial = Convert.ToInt32(seqatual) + 1;
                    dr["sequencial"] = sequencial;

                    dr["id"] = DBNull.Value;
                }
            }

            if (dr.Table.Columns.Contains("id"))
            {
                //Coluna ano..
                if (dr["id"] != DBNull.Value && dr["id"].ToString() != "0")
                {
                    return; //Já temos ID, não precisamos continuar a função..
                }
            }

            //Obtem a id, caso a tabela chaves contenha..
            sqlChave = " select id from chaves where " +
                                    " tabela='" + dr.Table.TableName.Trim() + "' and " +
                                    " servidor = " + servidor +
                                    " and ano = " + ano.ToString();

            object seq_id = ConnAccess.executeScalar(this.conn, sqlChave);

            DataRow drchave = ConnAccess.getNewRow(this.conn, "chaves");

            drchave.Table.TableName = "chaves";
            drchave["sequencial"] = sequencial;
            drchave["ano"] = ano;
            drchave["servidor"] = Convert.ToInt32(servidor);
            drchave["tabela"] = dr.Table.TableName;

            //Juntamos tudo para montar uma PK.
            string saida = servidor.ToString() +
                           ano.ToString() +
                           sequencial.ToString();

            ///Vamos tentar proteger isso aqui..
            ConnAccess.executeCommand(this.conn, "START TRANSACTION");
            if (seq_id == null || seq_id == DBNull.Value)
            {
                if (dr.Table.TableName == "materia_radiotv_jornal")
                {
                    object ultimaID = ConnAccess.executeScalar(this.conn, " select max(sequencial) from " + dr.Table.TableName + " where ano =" + ano.ToString() +
                         " and servidor = " + servidor);

                    if (ultimaID != null && ultimaID != DBNull.Value)
                        drchave["sequencial"] = Convert.ToInt32(ultimaID) + 1; ;


                }



                ConnAccess.Insert(this.conn, drchave,"id", true);
            }
            else
            {

                drchave["id"] = Convert.ToInt32(seq_id);
                ConnAccess.Update(this.conn,  drchave, "id");
            }

            ConnAccess.executeCommand(this.conn, "COMMIT");

            try
            {
                dr["id"] = Convert.ToInt32(saida);
            }
            catch (OverflowException exp)
            {
    */
    
}


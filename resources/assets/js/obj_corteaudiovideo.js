var obj_corteaudiovideo = {

       reprod_normal: true,
       em_play: false,

       pauseVideo: function () {

              var video_main = document.getElementById("video_main");
              video_main.pause();
              obj_corteaudiovideo.em_play = false;

       },

       onpause: function () {

              obj_corteaudiovideo.em_play = false;
       },

       getSelectEmissora: function () {

              var selects = document.getElementsByTagName("select");

              for (var i = 0; i < selects.length; i++) {
                     if (selects[i].id.indexOf("ddlEmissora") > -1) {
                            return selects[i];
                     }
              }

              return null;
       },



       setaVelocidade: function (val) {

              var video_main = obj_corteaudiovideo.getVideoTag();

              if (val == "1") {

                     video_main.playbackRate = 1.0;
              }
              if (val == "1.25") {

                     video_main.playbackRate = 1.25;
              }
              if (val == "1.5") {

                     video_main.playbackRate = 1.5;
              }
              if (val == "2") {

                     video_main.playbackRate = 2.0;
              }

       },

       teclaSpace: function (event) {

              if (obj_corteaudiovideo.em_play) {
                     obj_corteaudiovideo.pauseVideo();
              } else {

                     if (document.getElementById("hd_atual_video").value != "") {

                            var video_main = obj_corteaudiovideo.getVideoTag();
                            video_main.play();
                            obj_corteaudiovideo.em_play = true;

                     }
              }

       },

       getVideoTag: function () {

              var ars = document.getElementsByTagName("video");

              if (ars.length > 0) {
                     var video_main = ars[0];

                     return video_main;
              }
       },

       play: function (url, indx) {

              var ars = document.getElementsByTagName("video");

              // var video_main = document.getElementById("video_main");

              if (ars.length > 0) {
                     var video_main = ars[0];

                     if (video_main != null) {

                            video_main.src = url;
                     }
              }

              var ars2 = document.getElementsByTagName("tr");

              for (var i = 0; i < ars2.length; i++) {

                     if (ars2[i].id != null && ars2[i].id != undefined && ars2[i].id.indexOf("tr_arquivo") > -1) {

                            if (ars2[i].id == "tr_arquivo" + indx.toString()) {
                                   // ars2[i].className = "info";
                                   $("#" + ars2[i].id).addClass("info");
                            } else {

                                   //ars2[i].className = "";
                                   $("#" + ars2[i].id).removeClass("info");
                            }
                     }

              }
              var arps = url.split('/');

              var hd_atual_video = document.getElementById("hd_atual_video");
              hd_atual_video.value = arps[arps.length - 1];




       },

       setPlayVideo: function (obj) {
              // reprod_normal = true;
              obj_corteaudiovideo.em_play = true;


              var id_operador = $("#id_operador").val();
              var arquivo = $("#hd_atual_video").val();
              var id_emissora = obj_corteaudiovideo.getSelectEmissora().value;
              var acao = "salvar_historico";
              var tipo = "see";

              var indx = "0";

              var ars2 = document.getElementsByTagName("tr");

              for (var i = 0; i < ars2.length; i++) {
                     if (ars2[i].id != null && ars2[i].id != undefined && ars2[i].id.indexOf("tr_arquivo") > -1) {
                            if ($("#" + ars2[i].id).hasClass("info")) {
                                   indx = ars2[i].id.replace("tr_arquivo", "");
                            }

                     }
              }


              $.post("ajax.aspx", { id_operador: id_operador, arquivo: arquivo, acao: acao, tipo: tipo, id_emissora: id_emissora }, function (retorno) {

                     console.log(retorno);
                     $("#tr_arquivo" + indx.toString()).addClass("assistido");

              }).done(function () {

                     //$('[data-toggle="tooltip"]').tooltip(); 
                     //obj_corteaudiovideo.hidemouse(); 
                     //setaClickModal();
              });



       },


       reproduceVideo: function () {

              var video_main = obj_corteaudiovideo.getVideoTag();

              if (video_main.currentTime < parseInt(document.getElementById("rg_start").value) ||
                     video_main.currentTime > parseInt(document.getElementById("rg_end").value)) {

                     video_main.currentTime = parseInt(document.getElementById("rg_start").value);
              }
              obj_corteaudiovideo.reprod_normal = false;

              video_main.play();
              obj_corteaudiovideo.em_play = true;
       },

       catchTime: function (tipo) {

              var video_main = obj_corteaudiovideo.getVideoTag();

              if (tipo == "start") {
                     document.getElementById("rg_start").value = video_main.currentTime.toString();
                     obj_corteaudiovideo.sendTime(document.getElementById("rg_start"));
              }

              if (tipo == "end") {
                     document.getElementById("rg_end").value = video_main.currentTime.toString();
                     obj_corteaudiovideo.sendTime(document.getElementById("rg_end"));
              }
              // obj_corteaudiovideo.indicaDuracao();
       },
       sendTime: function (obj) {

              document.getElementById(obj.name.replace("rg_", "sp_")).value = obj_corteaudiovideo.segundoParaTexto(obj.value);
              // obj_corteaudiovideo.indicaDuracao();

              // obj_corteaudiovideo.setaHoraFim(obj );

       },

       changeTextTime: function (obj) {

              var segundos = obj_corteaudiovideo.ConverteTextoParaSegundos(obj.value);
              document.getElementById(obj.name.replace("sp_", "rg_")).value = segundos;

       },

       setaHoraFim: function (obj) {


              var f = document.forms[0];
              var txtDuracao = document.getElementById("txtDuracao");

              if (txtDuracao != undefined && txtDuracao.value != "" && f.txtHoraInicio.value != "") {

                     var dur = obj_corteaudiovideo.ConverteTextoParaSegundos(txtDuracao.value);
                     var ini = obj_corteaudiovideo.ConverteTextoParaSegundos(f.txtHoraInicio.value);

                     f.txtHoraFim.value = obj_corteaudiovideo.segundoParaTexto(dur + ini);
              }
       },


       indicaDuracao: function () {

              var txtDuracao = document.getElementById("txtDuracao");

              if (txtDuracao != null) {
                     var fim = parseInt(document.getElementById("rg_end").value);
                     var inicio = parseInt(document.getElementById("rg_start").value);


                     txtDuracao.value = obj_corteaudiovideo.segundoParaTexto(fim - inicio);


              }
       },

       setCurrentTime: function (obj) {
              document.getElementById("tx_currentTime").value = obj_corteaudiovideo.segundoParaTexto(obj.currentTime);



              if (this.reprod_normal) { ///Se a reprodução for pelo outro botão..

                     if (video_main.currentTime >= parseInt(document.getElementById("rg_end").value)) {
                            video_main.pause();
                     }

              }
       },

       loadControls: function (obj) {
              document.getElementById("sp_end").value = obj_corteaudiovideo.segundoParaTexto(obj.duration);
              document.getElementById("rg_end").max = obj.duration;
              document.getElementById("rg_start").max = obj.duration;

              document.getElementById("rg_end").value = obj.duration.toString();
              document.getElementById("rg_start").value = "0";

              document.getElementById("sp_start").value = obj_corteaudiovideo.segundoParaTexto(0);

       },

       segundoParaTexto: function (segundos) {

              var horas = parseInt(parseInt(segundos) / 3600);
              var minutos = parseInt((parseInt(segundos) % 3600) / 60);
              var seg = parseInt((parseInt(segundos) % 3600) % 60);

              var tempo = obj_corteaudiovideo.sTempo(horas) + ":" + obj_corteaudiovideo.sTempo(minutos) + ":" + obj_corteaudiovideo.sTempo(seg);

              return tempo;

       },

       sTempo: function (seg) {

              if (seg.toString().length == 1) {
                     return "0" + seg.toString();
              }
              return seg.toString();
       },

       ConverteTextoParaSegundos: function (tempo) {
              if (tempo == "")
                     tempo = "00:00:00";

              var ar_tm = tempo.split(':');
              var segundos = 0;

              //   Posição 0 e 1 são as horas -> converter para inteiro e multiplicar por 3600.
              /// Posição 3 e 4 são os minutos -> converter para inteiro e multiplicar por 60.
              /// As 2 ultimas casas são segundos -> apenas somar com o restante

              if (ar_tm.length > 2) {
                     segundos =
                            (parseInt(ar_tm[0]) * 3600) +
                            (parseInt(ar_tm[1]) * 60) +
                            (parseInt(ar_tm[2]));
              }
              else {
                     segundos =
                            (parseInt(ar_tm[0]) * 60) +
                            (parseInt(ar_tm[1]));
              }
              return segundos;


       },


       gerarCorteArquivo: function (e) {

              var f = document.forms[0];

              var hd_atual_video = document.getElementById("hd_atual_video");
              var hd_url_video = document.getElementById("hd_url_video");


              if (hd_atual_video.value == "") {
                     alert("Não há arquivo de vídeo ou mp3 selecionado!");
                     return;
              }

              if (hd_atual_video.value == "") {
                     alert("Não há arquivo de vídeo ou mp3 selecionado!");
                     return;
              }


              if (f.txt_nomeArquivo.value == "") {
                     alert("Informe o nome do arquivo!");
                     return;
              }


              var video_main = document.getElementById("video_main");
              var url_arquivo = video_main.src;

              hd_url_video.value = video_main.src;

              var rg_start = document.getElementById("rg_start");
              var rg_end = document.getElementById("rg_end");

              mouseloading.mouse_show(e);

              var f = document.forms[0];
              f.action = "ajax.aspx";
              f.acao.value = "cortar_audiovideo";
              f.id_emissora.value = obj_corteaudiovideo.getSelectEmissora().value;

              f.target = "frameCorte";
              f.submit();

       },



       hidemouse: function () {
              mouseloading.mouse_hide();

       },

       //span_msg  div_alerta
       //alert alert-success alert-dismissible
       setaAlerta: function (msg, tipo) {

              var span_msg = document.getElementById("span_msg");
              var div_alerta = document.getElementById("div_alerta");

              span_msg.innerHTML = msg;

              if (tipo == "sucess") {
                     div_alerta.className = "alert alert-success alert-dismissible";
              }

              if (tipo == "error") {
                     div_alerta.className = "alert alert-danger alert-dismissible";
              }
              if (tipo == "info") {
                     div_alerta.className = "alert alert-info alert-dismissible";
              }

              div_alerta.style.display = "";

       },

       getAllCheckBox: function (nom) {

              var checks = new Array();

              var inputs = document.getElementsByTagName("input");

              for (var i = 0; i < inputs.length; i++) {

                     if (inputs[i].type == "checkbox" && inputs[i].name.indexOf(nom) > -1) {

                            checks[checks.length] = inputs[i];
                     }

              }

              return checks;
       },

       juntarArquivos: function (evt) {
              var arq_total_files = document.getElementById("arq_total_files");

              var checks = this.getAllCheckBox("arq_");

              var nome_arquivos = "";

              var qt_checado = 0;
              for (var i = 0; i < checks.length; i++) {
                     if (checks[i].checked) {
                            qt_checado++;

                            if (nome_arquivos != "")
                                   nome_arquivos += ",";

                            nome_arquivos += checks[i].value;
                     }
              }

              if (qt_checado <= 1) {
                     alert("Selecione ao menos dois arquivos");

                     return false;
              }


              //  mouseloading.mouse_show(evt);

              $.colorbox({ iframe: true, innerWidth: 800, height: 450, href: "frameJuntarArquivos.aspx?nome_arquivos=" + nome_arquivos });


       },


       atualizarPasta_old: function (id_atualizar) {

              var obj = document.getElementById("id_operador");


              if (obj.value != "") {

                     $.getJSON("ajax.aspx?acao=listar_pasta&id_operador=" + obj.value, function (pastadados) {


                            var list = pastadados;
                            console.log(list);
                            var table = "<table class=\"table table-striped table-condensed\">";

                            for (var i = 0; i < list.length; i++) {
                                   var item = list[i];

                                   table += "<tr id='tr_arquivo" + i.toString() + "' >";
                                   table += "<td>";

                                   table += item.Nome;
                                   table += "</td>";

                                   table += "<td style='width: 30px'>" +
                                          "<a onclick=\"play('" + item.Nome + "', '" + i.toString() + "')\"><span class='glyphicon glyphicon-play-circle'></span></a>";

                                   table += "</td></tr>";
                            }

                            table += "</tbody></table>";

                            console.log(table);

                            $("#divListVideos").html(table);
                     });



              }




       },


       atualizarPasta: function (e) {

              var obj = document.getElementById("id_operador");

              mouseloading.mouse_show(e);

              var url = "ajaxPasta.aspx?id_operador=" + obj.id;

              var self = this;
              $.ajax({
                     url: url, context: self, success: function (pastadados) {

                            $("#divDataPasta").html(pastadados);

                            setaClickModal();

                     }
              }).done(function () {

                     $('[data-toggle="tooltip"]').tooltip();
                     obj_corteaudiovideo.hidemouse();
                     setaClickModal();
              });

       },




       MascaraHora: function (cnpj, e) {
              if (mascaraInteiro(cnpj, e) == false) {
                     event.returnValue = false;
              }
              return formataCampo(cnpj, '00:00:00', e);
       },


       alerta: function (msg) {
              swal({
                     title: 'Atenção!',
                     text: msg,
                     type: 'success',
                     confirmButtonText: 'OK'
              });

       },

       excluir_arquivo_audiovideo: function (obj, nome_arquivo) {

              var id_operador = $("#id_operador").val();
              var self = this;

              swal({
                     title: 'Atenção!',
                     text: 'Deseja realmente excluir o arquivo ' + nome_arquivo + '?',
                     type: 'warning',
                     confirmButtonText: 'Sim',
                     showCancelButton: true,
                     cancelButtonText: 'Não'
              }).then(function (isConfirm) {
                     if (isConfirm.value != undefined && isConfirm.value == true) {

                            var url = "ajax.aspx?acao=delete&arquivos=" + nome_arquivo + "&id_operador=" + id_operador;
                            /* $.get( "ajax.aspx?acao=delete&arquivos=" + nome_arquivo +"&id_operador="+ id_operador,
                                       function( pastadados ) {
                            
                            
                              }).done(function() {
                                   
                                      obj_corteaudiovideo.atualizarPasta();
                              }); */

                            $.ajax({ url: url, context: self }).done(function () {
                                   document.getElementById("a_refresh_folder").click();
                                   //obj_corteaudiovideo.atualizarPasta();
                            }).fail(function (response) {

                                   swal({
                                          title: 'Erro!',
                                          text: 'Não foi possível excluir o arquivo ' + nome_arquivo + '. Provavelmente o FFMEG esta em execução. Tente novamente mais tarde',
                                          type: 'error',
                                          confirmButtonText: 'OK'
                                   });

                            });

                     } else {


                     }
              });

       },
       localizaDia: function (obj, ev) {

              if (obj.value != "") {

                     var id_operador = $("#id_operador").val();
                     var id_emissora = $("#id_emissora").val();
                     mouseloading.mouse_show(ev);


                     $.getJSON("ajax.aspx?acao=sel_dia&url=" + obj.value + "&id_operador=" + id_operador, function (listVideos) {


                            obj_corteaudiovideo.carregaVideosDiv(listVideos);


                     });



              }

       },

       carregaVideosDiv: function (listVideos) {


              var table = "<table class=\"table table-striped table-bordered\"><tbody>";

              for (var i = 0; i < listVideos.length; i++) {
                     var item = listVideos[i];

                     table += "<tr id='tr_arquivo" + i.toString() + "' >";
                     table += "<td>";

                     table += item.Codigo;
                     table += "</td>";

                     table += "<td style='width: 30px'>" +
                            "<a style=\"cursor:pointer\" onclick=\"obj_corteaudiovideo.play('" + item.Nome + "', '" + i.toString() + "')\"><span class='glyphicon glyphicon-play-circle'></span></a>";

                     table += "</td></tr>";
              }

              table += "</tbody></table>";

              $("#divListVideos").html(table);




              obj_corteaudiovideo.localizaHistoricoAssistidos(listVideos);

       },

       localizaEmissora: function (obj, ev) {

              if (obj.value != "") {

                     var id_operador = $("#id_operador").val();
                     mouseloading.mouse_show(ev);
                     $.getJSON("ajax.aspx?acao=emissora&ddlEmissora=" + obj.value + "&id_operador=" + id_operador, function (emissoraDados) {


                            var listFormatos = emissoraDados.listFormatos;

                            var options = [];
                            for (var i = 0; i < listFormatos.length; i++) {

                                   options.push('<option value="',
                                          listFormatos[i].Codigo, '">',
                                          listFormatos[i].Nome, '</option>');
                            }


                            $("#selFormato").html(options.join(''));

                            var divFormato = document.getElementById("divFormato").style.display = "";

                            if (emissoraDados.modelo_streaming == "1") {
                                   $("#div_dia").hide();
                            }
                            if (emissoraDados.modelo_streaming == "2") {


                                   var listDias = emissoraDados.listDias;
                                   console.log("Dias?"); console.log(listDias);


                                   var options2 = [];
                                   for (var i = listDias.length - 1; i >= 0; i--) {

                                          options2.push('<option value="',
                                                 listDias[i].Codigo, '">',
                                                 listDias[i].Nome, '</option>');
                                   }


                                   $("#selDia").html(options2.join(''));

                                   $("#div_dia").show();
                            }

                            obj_corteaudiovideo.carregaVideosDiv(emissoraDados.listVideos);


                     });



              }
       },


       localizaFormato: function (obj, ev) {


              if (obj.value != "") {

                     mouseloading.mouse_show(ev);

                     var id_operador = $("#id_operador").val();
                     var id_emissora = $("#id_emissora").val();

                     $.getJSON("ajax.aspx?acao=sel_formato&url=" + obj.value + "&id_operador=" + id_operador +
                            "&id_emissora=" + id_emissora, function (listVideos) {




                                   var table = "<table class=\"table table-striped table-bordered\"><tbody>";

                                   for (var i = 0; i < listVideos.length; i++) {
                                          var item = listVideos[i];

                                          table += "<tr id='tr_arquivo" + i.toString() + "' >";
                                          table += "<td>";

                                          table += item.Codigo;
                                          table += "</td>";

                                          table += "<td style='width: 30px'>" +
                                                 "<a onclick=\"obj_corteaudiovideo.play('" + item.Nome + "', '" + i.toString() + "')\"><span class='glyphicon glyphicon-play-circle'></span></a>";

                                          table += "</td></tr>";
                                   }

                                   table += "</tbody></table>";

                                   $("#divListVideos").html(table);
                                   obj_corteaudiovideo.localizaHistoricoAssistidos(listVideos);
                            });
              }


       },


       localizaHistoricoAssistidos: function (listVideos) {

              //Vou mandar toda a lista para o sistema verificar quem já tem histórico assistido..

              var data = {
                     acao: "verifica_historico",
                     videos: JSON.stringify(listVideos),
                     id_operador: $("#id_operador").val(),
                     id_emissora: obj_corteaudiovideo.getSelectEmissora().value
              }
              console.log("vamos tentar buscar o histórico");
              console.log(data);

              $.post('ajax.aspx', data, function (listVideos) {
                     for (var i = 0; i < listVideos.length; i++) {
                            var item = listVideos[i];

                            var idtr = "tr_arquivo" + item.Codigo.toString();

                            if (document.getElementById(idtr) != null) {

                                   if ($("#" + idtr).hasClass("info")) {
                                          $("#" + idtr).removeClass("info");
                                          $("#" + idtr).addClass("assistido");
                                          $("#" + idtr).addClass("info");

                                   } else {

                                          $("#" + idtr).addClass("assistido");
                                   }
                            }

                     }


              }, 'json').done(function () {
                     console.log("sucesso!");
                     mouseloading.mouse_hide();
              })
                     .fail(function (jqXHR, textStatus, errorThrown) {

                            console.log("erro: " + jqXHR + " - txt: " + textStatus);
                            console.log(errorThrown);
                            mouseloading.mouse_hide();

                     });



       }




}

window.obj_corteaudiovideo = obj_corteaudiovideo;
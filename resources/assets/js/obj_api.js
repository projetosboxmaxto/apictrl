var obj_api = {


  get_menu_user: function (menu) {

    if (parseInt(window.K_USER_GROUP_ID) > 1) {

      console.log("Tentando obter o group id " + window.K_USER_GROUP_ID);
      var arr = JSON.parse(window.K_USER_MENU);
      var saida = [];

      console.log(arr);

      for (var y = 0; y < menu.length; y++) {

        if (!menu[y].menu) {
          saida[saida.length] = menu[y];
          break;
        }

        for (var i = 0; i < arr.length; i++) {
          var code = arr[i].code;
          if (menu[y].name == code) {
            saida[saida.length] = menu[y];
            break;
          }
        }
      }

      return saida;


    }

    return menu;
  },
  call_delete: function (tipo, id, fn_return) {


    var url = window.URL_API + tipo + "/" + id;

    $.ajax({
      type: "DELETE",
      url: url,
      contentType: "application/x-www-form-urlencoded",
      processData: true,
      data: {},
      success: function (retorno) {

        console.log(retorno);

        if (fn_return != null) {
          fn_return(retorno, tipo)
        }


      },
      error: function (xhr, status, p3, p4) {
        var err = "Error " + " " + status + " " + p3 + " " + p4;
        if (xhr.responseText && xhr.responseText[0] == "{")
          err = JSON.parse(xhr.responseText).Message;


        console.error(err);

      }
    }).fail(function (response) {
      console.log("Falha ao tentar obter dados");
      console.log(response);

      $("#div_error_api").html(response.responseText);
    });


  },

  call_midiaclip(tip, acao, method, fn_return) {


    var url = window.K_URL_SISTEMA_MIDIACLIP + "importacao/handlerMateriaRtv.aspx" + acao;

    if ( window.URL_API_INTEGRADOR  != null && window.URL_API_INTEGRADOR  != undefined && window.URL_API_INTEGRADOR != ""){
             url = window.URL_API_INTEGRADOR + "integrador" + acao;
    }

    if (tip == "api4") {
      var url = window.URL_API4 + acao;

      if ( window.URL_API_INTEGRADOR  != null && window.URL_API_INTEGRADOR  != undefined && window.URL_API_INTEGRADOR != ""){
                 url = window.URL_API_INTEGRADOR  + acao;
      }

      if (window.API4_EXTRA_PARAM != null && window.window.API4_EXTRA_PARAM != "") {

        if (url.indexOf("?") < 0) {
          url += "?" + window.API4_EXTRA_PARAM;
        } else {

          url += "&" + window.API4_EXTRA_PARAM;
        }


      }

    }
  console.log("call_midiaclip?", url );

        $.ajax({
          type: method,
          url: url,
          contentType: "application/x-www-form-urlencoded",
          processData: true,
          data: {},
          success: function (retorno) {

            console.log(retorno);

            if (fn_return != null) {
              fn_return(retorno)
            }


          },
          error: function (xhr, status, p3, p4) {
            var err = "Error " + " " + status + " " + p3 + " " + p4;
            if (xhr.responseText && xhr.responseText[0] == "{")
              err = JSON.parse(xhr.responseText).Message;


            console.error(err);

          }
        }).fail(function (response) {
          console.log("Falha ao tentar obter dados");
          console.log(response);

          $("#div_error_api").html(response.responseText);
        });


  },



  ConvertToFormData(p_data) {


    return new Promise((resolve) => {
      var formData = new FormData()

      var keys = Object.keys(p_data);

      for (var i = 0; i < keys.length; i++) {
        var key = keys[i];

        if (p_data[key] != null && p_data[key] != undefined) {
          formData.append(key, p_data[key]);
        }

        if (i == keys.length - 1) {
          resolve(formData);
        }
      }
    });


  },
  callFormData: function (tipo, method, data, fn_return) {



    let self = this;

    this.ConvertToFormData(data).then((formData) => {

      // console.log( formData );  return false;

      var url = window.URL_API + tipo;

      $.ajax({
        type: method,
        url: url,
        //contentType: "multipart/form-data",
        processData: false,
        data: formData,
        /* beforeSend: function (request) {
          request.setRequestHeader("Authorization", window.API_AUTHORIZATION);
          request.setRequestHeader("apiauth", window.API_MYAUTH);
        }, */

        headers: {
          "Authorization": window.API_AUTHORIZATION,
          "apiauth": window.API_MYAUTH,

        },
        success: function (retorno) {

          console.log(retorno);

          if (fn_return != null) {
            fn_return(retorno, tipo)
          }


        },
        error: function (xhr, status, p3, p4) {
          var err = "Error " + " " + status + " " + p3 + " " + p4;
          // if (xhr.responseText && xhr.responseText[0] == "{")
          //   err = JSON.parse(xhr.responseText).Message;


          console.error(err);
          console.error(JSON.parse(JSON.stringify(xhr)));

        }
      }).fail(function (response) {
        console.log("Falha ao tentar obter dados");
        console.log(response);

        $("#div_error_api").html(response.responseText);
      });

    });


  },
  call: function (tipo, method, data, fn_return) {


    var url = window.URL_API + tipo;


    $.ajax({
      type: method,
      url: url,
      /*beforeSend: function (request) {
        request.setRequestHeader("Authorization", window.API_AUTHORIZATION);
        request.setRequestHeader("apiauth", window.API_MYAUTH);
      }, */
      headers: {
        "Authorization": window.API_AUTHORIZATION,
        "apiauth": window.API_MYAUTH,

      },
      contentType: "application/x-www-form-urlencoded",
      processData: true,
      data: data,
      success: function (retorno) {

        //console.log(retorno);

        if (fn_return != null) {
          fn_return(retorno, tipo)
        }


      },
      error: function (xhr, status, p3, p4) {
        var err = "Error " + " " + status + " " + p3 + " " + p4;
        if (xhr.responseText && xhr.responseText[0] == "{")
          err = JSON.parse(xhr.responseText).Message;


        console.error(err);

      }
    }).fail(function (response) {
      console.log("Falha ao tentar obter dados");
      console.log(response);

      $("#div_error_api").html(response.responseText);
    });


  },
  serialize(obj) {

    var str = [];
    for (var p in obj)
      if (obj.hasOwnProperty(p) && obj[p] != null) {
        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
      }
    return str.join("&");


  }



}

window.obj_api = obj_api;
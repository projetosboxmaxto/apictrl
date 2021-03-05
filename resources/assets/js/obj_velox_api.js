var obj_velox_api = {

        load_cidades: function (fn_return){
         
             var url =  window.URL_VELOX_API +"city/all/" + window.PARTNER_CODE;
             var city = window.VELOX_CITY;
             
             
             var options = [];
                    $.getJSON(url, function(result) {
                        for (var i = 0; i < result.length; i++) {
                            
                            options.push({id:result[i].id, name: result[i].name } );
                            
                        }
                        
                        
                        
                        if ( fn_return != null ){
                            fn_return( options );
                        }    
                        
                        
                    });
                    
            
        },
        
        select_city: function(){
            
            var city_p =$("#g_sel_cidades").val();
            var ar = city_p.split('|');
            var city = ar[0];
            
               // if ( window.T_SLUG == "" || window.T_SLUG == "home"){
                    var url = "ajax.php?session_id=" + window.SessionID +
                            "&city="+city+"&acao=set_city";
                    
                    console.log("mudando a cidade " + url );
                    window.VELOX_CITY = city;
                    obj_velox_api.call(url, "get", {} , function(){ 
                    
                        if ( window.T_SLUG == "" || window.T_SLUG == "home"){
                            
                                      obj_velox_api.load_cartaz_alt();
                                      obj_velox_api.load_em_breve_alt();
                        } 
                        
                         if ( window.T_SLUG == "programacao" || window.T_SLUG == "em-breve"){
                             location.reload();
                         }
                    }
                    );
                    
                //}
            
        },

        get_url_open_movie( url ){

                  var final = "";

                  var ar = url.split('/');

                  final = "filme-"+ar[ar.length -2]+"-"+ar[ar.length -1];

                  return final;


        },

        load_movie: function ( id ){


           var url =  window.URL_VELOX_API +"movie/detail/"+id+
                                      "?partnerCode="+ window.PARTNER_CODE;  
               
               
                      $.getJSON(url, function(result) {

                        var urltmp = result.url.replace("https://","//");
                        console.log("trazendo dados do filme");
                        console.log(result);
                        console.log("url filme: " + urltmp );
                        console.log("Velox JS: " + window.K_VELOX_JS );



                        var HTML = '<div style="width: 100%; margin: 0 auto;">'+
                         '<iframe id="veloxframe" src=""'+ 
                        'width="100%" scrolling="NO" frameborder="0" height="500"></iframe>'+
                        '<script type="text/javascript" '+
                         ' src="'+window.K_VELOX_JS+'" '+
                        ' veloxframesrc="'+urltmp+'"></script></div> ';


                        //console.log( HTML );
  

                       //   $("#div_box_filme").html(HTML);
                      });


        },
        get_cartaz_list: function (fn_result){

               
               var city = window.VELOX_FIRST_CITY;
               var url =  window.VELOX_URL_API +"movie/scheduled/"+window.VELOX_PARTNER_CODE+"/"+city+"?getUrl=true&imgType=HOME" ;  
               

                var fn_result2 = function(res_txt) {
                    
                    console.log("RETURN?");
                    //console.log(result);
                    
                    var result = JSON.parse( res_txt );
                          
                          if ( fn_result != null ){
                              fn_result(result);
                          }
                         
                    };
               
               obj_velox_api.call(url, "GET", {}, fn_result);

        },
        
         load_cartaz_alt: function (){

               
               var city = window.VELOX_FIRST_CITY;
               var url =  window.VELOX_URL_API +"movie/scheduled/"+window.VELOX_PARTNER_CODE+"/"+city+"?getUrl=true&imgType=HOME" ;  
               //VELOX_URL_API VELOX_PARTNER_CODE VELOX_FIRST_CITY
              
               
                      $.getJSON(url, function(result) {
                          
                            var HTML =  "<div id=\"div_cartaz_carrosel\" class=\"box-carrossel-filmes\">" +
                                     "<div class=\"js-carrossel-filme owl-carousel owl-theme\">";
                                        for (var i = 0; i < result.length; i++) {
                                            
                                            
                                        var item = result[i];  //item.imgUrl
                                        //assets/img/img-capa-filme.jpg
                                            //target="_blank"
                                            //var urlencoded = encodeURI( item.url );
                                            var tmpurl = obj_velox_api.get_url_open_movie( item.url );

                                             HTML += '<a href="'+tmpurl+'" class="item"> '+
                                                     '<img class="img-responsive"  ' +
                                                     ' src="'+item.imgUrl+'" alt="'+item.name+'"> '+
                                                        ' <div class="hover"> '+
                                                            ' <h5>' + item.name + '</h5>' +
                                                            '<p>Em cartaz</p>'+
                                                       ' </div></a>';  //img-capa-filme.jpg
                                            
                                        }
                              
                             HTML += "</div></div>"; 
                             
                             $("#div_container_cartaz").html(HTML);
                             g_gallery_cartaz();
                    }).error(function() { 

                            $("#div_container_cartaz").html("Não foi possível carregar filmes para esta cidade. URL "+
                                      url +"" );

                     })      
                                     

                           /*
                           
                            */

                          
            
         },
         
         load_em_breve_list( fn_return ){
              
              
               var city = window.VELOX_FIRST_CITY;
               var url =  window.VELOX_URL_API +"movie/soon?partnerCode="+window.VELOX_PARTNER_CODE+"&cityCode="+city+
                       "&getUrl=true&imgType=HOME" ;  
               
              // url = "https://teste.veloxtickets.com/Portal/api/v1/movie/soon?partnerCode=CINEROXY&cityCode=Sao-Paulo&getUrl=true&imgType=HOME";
               
              // console.log("Em breve?: " + url );
               
                var fn_result = function(res_txt) {
                    
                    console.log("RETURN?");
                    //console.log(result);
                    
                    var result = JSON.parse( res_txt );
                          
                          if ( fn_return != null ){
                              fn_return(result);
                          }
                         
                    };
               
               obj_velox_api.call(url, "GET", {}, fn_result);
             
         },

         call: function(url, method, data , fn_return ){


                //var url =  window.URL_VELOX_API +  tipo; 
                
                
                var xmlhttp = new XMLHttpRequest();

                xmlhttp.onreadystatechange = function() {
                    if (xmlhttp.readyState == XMLHttpRequest.DONE) {   // XMLHttpRequest.DONE == 4
                       if (xmlhttp.status == 200) {
                           
                           if ( fn_return != null ){
                               fn_return( xmlhttp.responseText );
                           }
                           
                       }
                       else if (xmlhttp.status == 400) {
                           console.log("Falha ao tentar obter dados");
                            console.log('There was an error 400');
                       }
                       else {
                           console.log('something else other than 200 was returned');
                       }
                    }
                };

                xmlhttp.open(method, url, true);
                xmlhttp.send();
                
                 /*
                 $.ajax({
                              type: method,
                              url: url,
                              contentType: "application/x-www-form-urlencoded",
                              processData: true,
                              data: data,
                              success: function (retorno) {
                                  
                                  console.log(retorno);
                                 
                                   if (fn_return != null ){
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
                          });
                          */


     }



}


window.obj_velox_api = obj_velox_api;
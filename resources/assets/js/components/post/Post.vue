<template>
	<div>

     <section class="content-header">

<section class="col-lg-9">
      <h1>
        {{titulo_acao}}

      </h1>
      <ol class="breadcrumb" style="display: none">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Forms</a></li>
        <li class="active">General Elements</li>
      </ol>
    </section>
</section>

<section class="col-lg-3" style="padding-top: 30px">
        <a href="#" v-if="botao_voltar_visible"  v-on:click="botao_voltar">
           <i class="fa fa-arrow-left"></i> Voltar para lista
        </a> 
</section>
    <div class="col-lg-12">
					<div v-if="show_message == 'on' " class="alert alert-success">
					      {{message_text}}
					</div>
</div>

<section class="col-lg-9">

   <div class="box">
   <div class="box-body">
                <div class="form-group">
                  <label for="post_title">Título</label>
                   <input class="form-control" name="post_title" 
                     id="post_title" v-model="title"
              type="text" placeholder="Título" >
                </div>
              <div class="form-group" v-if="slug != undefined && slug.length > 0 ">
                  <label for="post_slug">URL (SLUG)</label>
                   <div id="post_slug" >{{slug | site_url}}</div>
                </div>

                  <div class="form-group" v-if="(post_type=='post' || (post_type=='page' && slug=='filmes-em-cartaz') )">
                <label for="post_subtitle">SubTítulo</label>
                   <input class="form-control" name="post_subtitle" 
                     id="post_subtitle" v-model="subtitle"
                           type="text" placeholder="Subtítulo" maxlength="300" >
                </div>

                 <div >
                   <span class="pull-right">
                     <button class="btn btn-default btn-sm"
                             v-on:click="onInsertMidia"
                     ><i class="fa fa-picture-o"></i> Adicionar mídia</button>

                   </span>
                   
                 </div>
                 <div class="form-group" >
                   <label for="post_content" >Conteúdo</label>
                    <span v-bind:style="style_span_loading()">  Carregando imagem.. <img v-bind:src="img_loading | base_url" /> 
                    </span>
                   <textarea class="form-control summernote" v-model="content" name="post_content" 
                     id="post_content"  style="height: 400px"></textarea> 

                     <span style="display: none">

                          <input type="file" name="file_summernote" id="file_summernote"
                                  class="file_summernote" v-on:change="call_upload($event)" />
                     </span>


                </div>

   </div>

   </div>



</section>
<section class="col-lg-3">

  <div class="box">
  	   <div class="box-header with-border">
              <h4 class="box-title">{{publicar_titulo}}</h4>
       </div>         


       <div class="box-body">
 

         <div v-if="publication != '' && status=='publish' ">
             <i class="fa fa-scroll"></i> {{publicar_passado}} em: {{publication | datetime_show}} 
         </div>

         <div v-if="status=='draft'  ">

              <span v-if="status=='draft' "> <i class="fa fa-eye"></i> Em Rascunho </span>
         </div>
          <div v-if="status=='inherit' ">
                       <i class="fa fa-edit"></i> Em Rascunho
         </div>
          <div v-if="status=='review' ">
                       <i class="fa fa-eye"></i> Em Revisão
         </div>

              	<div class="btn-group pull-right" >





                     <button type="submit" class="btn btn-default" 
                      v-if="mostra_botoes_rascunho"
                      v-bind:disabled="disableButton"
                      v-on:click="salvar('draft')">Salvar Rascunho</button> 
                       <button type="submit" 

                      v-if="mostra_botoes_rascunho"
                       class="btn btn-default"
                      v-bind:disabled="disableButton" style="display:none"
                      v-on:click="salvar('review')">Revisão</button> 
                     <button type="submit" class="btn btn-info" 
                      v-bind:disabled="disableButton"
                     v-on:click="salvar('publish')">{{publicar_titulo}}</button> 
                 </div> 
       </div> 
  </div>


  <div class="box" v-if="post_type=='post'">
       <div class="box-body">

                 <div class="form-group" >
                        <label for="post_data" >Data da Notícia</label>
                        <input type="text" class="form-control temData" 
                               name="post_data" id="post_data" maxlength="10"
                               onfocus="obj_editor.focus_data(this)"
                               onkeypress="return obj_editor.mascara(event,this,'##/##/####');"
                               style="width: 120px" v-model="data">
                   </div>
       </div>

  </div>



<category title="Categoria" v-bind:selecteds="categories"
     v-bind:show="show_post_widget"
 prefixo="cat_" api_path="teste"></category>

<tagpostjquery v-bind:show="show_post_widget"></tagpostjquery>



<upload-unico 
   v-if="id !='' && parseInt(id) > 0 && post_type=='post'"
v-bind:id_parent="id" title="Imagem Capa" ptype="p:capa"></upload-unico>

<upload-unico 
   v-if="id !='' && parseInt(id) > 0 && post_type=='page' "
v-bind:id_parent="id" title="Banner Interno" ptype="p:binterno"></upload-unico>


<upload-unico 
   v-if="id !='' && parseInt(id) > 0 && post_type=='page' && slug=='escola' "
v-bind:id_parent="id" title="Banner de Rodapé" ptype="p:rodape"></upload-unico>




<upload-unico 
   v-if="id !='' && parseInt(id) > 0 && post_type!='news'"
v-bind:id_parent="id" title="Imagem Redes Sociais" ptype="p:redesoc"></upload-unico>



<upload-unico 
   v-if="id !='' && parseInt(id) > 0 && post_type =='news'"
v-bind:id_parent="id" title="Imagem Background" ptype="p:backg"></upload-unico>

<email-last    
   v-if="post_type =='news'"></email-last>




<div class="panel-group" id="accordion" v-if="post_type=='page' || post_type=='post'">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" style="color: #333;" data-parent="#accordion" href="#collapse1">
        SEO Optimization</a>
      </h4>
    </div>
    <div id="collapse1" class="panel-collapse collapse" v-bind:style="style_collapse">
      <div class="panel-body" >
      	  
 <label for="post_description">Descrição</label>
                   <input type="text" class="form-control" v-model="description" name="post_description" 
                     id="post_description" maxlength="160" /> 
                     <b><span >{{description.length}}</span> caracteres. </b>
                     <i>A maioria dos motores de busca utilizam no máximo 160 caracteres para a descrição</i>
            
                <br>

 <label for="post_seo_keywords">Palavras-Chave</label>
                   <input type="text" class="form-control" v-model="seo_keywords" name="post_seo_keywords" 
                     id="post_seo_keywords" maxlength="300" /> 
                     <b><span >{{seo_keywords.length}}</span> caracteres. </b> descrição</i>



              <div style="display: none">
                <input type="checkbox" id="seo_noindex" v-model="seo_noindex" />
                 <label for="seo_noindex">NO INDEX esta página / post</label>
             </div>

               <div style="display: none">
                <input type="checkbox" id="seo_nofollow" v-model="seo_nofollow" />
                 <label for="seo_nofollow">NO FOLLOW esta página / post</label>
             </div>

              <div style="display: none">
                <input type="checkbox" id="seo_inactive" v-model="seo_inactive" />
                 <label for="seo_inactive">Desativar nesta página / publicação</label>
             </div>


      </div>
    </div>
  </div>
</div>

</section>

</div>
</template>


<script>
    export default {
      props: ['id_load', 'post_type', 'show_back_button','onBack', 'onSave'],
      /* props: {
          id_load: {
            type: Object,
            required: false
          },
          post_type: {
            type: String,
            required: false 
          },
          show_back_button: {
            type: String,
            required: false 
          },
          onBack: {
            type: Function,
            required: false 
          },
          onSave: {
            type: Function,
            required: false 
          }
        }, */
        data: function() {
            return {

              
              type: this.post_type != null ? this.post_type  : "post",
              title_str: "",
              titulo_acao: this.type == "post" ? "Novo " : "Nova",
              botao_voltar_visible: (this.show_back_button != null ?  this.show_back_button :  false ),
            	show_message: "off",
            	message_text: "",
            	message_type: "success",
            	interval_message: null,
            	interval_tags: null,
            	style_collapse: "display:none",

              show_post_widget: "0", //this.type =="post" ? "1" :"0",

              summernote : null,
              editor_summernote: null,

            	categories: [],
                items: [],
                title: '',
                description: '',
                content: '',
                slug: '',
                disableButton: false,
                editor_hab: false,
                id: '', 
                publication: '',
                subtitle: '',
                status: '',
                seo_noindex: false,
                seo_nofollow: false,
                seo_inactive: false,
                seo_keywords: '',

                onuploading: false,
                data: '',


                publicar_titulo: "Publicar",
                publicar_passado: "Publicado",
                mostra_botoes_rascunho: true,
                com_editor: true,

                img_loading: "loading.gif"
            }
        },
        beforeRouteUpdate(to) {

          console.log("Before route ");
          console.log( to.params );
          this.post_type = to.params.post_type;
          this.type =  to.params.post_type;
          this.id_load = to.params.id_load;
          this.id = to.params.id_load;
          this.load_component();

        },

        mounted() {

            if ( this.$router.currentRoute.name != null ){
              let new_route = this.$router.currentRoute.name;

              if ( new_route =="posts" || new_route =="home")
                new_route = "post";

              if ( new_route =="pages" )
                new_route = "page";

                 this.post_type = new_route; //   "page"
                 this.type =  new_route;
            }
                    //console.log("Current Router: "); console.log( this.$router.currentRoute );
                 this.load_component();
        },

        computed: {
         //   isFavorite() {
          //      return this.favorited;
          //  },
        },

        methods: {

          load_component(){


                    console.log("Chegou os dados aqui: " + this.type );
                    console.log("Props post type: " + this.post_type );
                    console.log("Id recebido : " + this.id_load );

                    this.title_str = obj_post.getTitle( this.type );

                     this.titulo_acao  = "Nova " + this.title_str;
                         // console.log(this.titulo_acao );

                     if ( this.type == "page"){

                            this.titulo_acao  = "Nova " + this.title_str;

                     
                          this.publicar_titulo = "Salvar";
                          this.publicar_passado=  "Salvo";
                          this.mostra_botoes_rascunho =  false;
                     }



                     if ( this.type == "news"){

                            this.titulo_acao  = "Nova " + this.title_str;

                     
                          this.publicar_titulo = "Salvar";
                          this.publicar_passado=  "Salvo";
                          this.mostra_botoes_rascunho =  false;
                     }
                   //  console.log("mounting " + this.type +" - " + this.title_str +" -- na msg: " + obj_post.getTitle( this.type ) );
                     
                     let self = this;
                       //$('.collapse').collapse();


                          if ( this.id_load == null || this.id_load == ""){

                            self.load_editor_html();

                            return;
                          }

                  this.titulo_acao = "Carregando "+this.title_str+".."
                  this.disableButton = true;

                          var url =  window.URL_API +"post/" + this.id_load; console.log("monted post url: " + url );

                          var data = new FormData();
               
                          $.ajax({
                                  type: "GET",
                                  url: url,
                                  contentType: false,
                                  processData: false,
                                  data: data,
                                  success: function (retorno) {

                                    var item = retorno.item;
                                      
                                      console.log("LOAD: ");
                                      console.log(retorno);

                                      if ( self.title_str != undefined ){
                                          self.titulo_acao = "EDITAR " + self.title_str.toUpperCase();
                                      }
                                     
                                      self.title = item.title;
                                      self.description = item.description;
                                      self.content = item.content;
                                      self.id = item.id;
                                      self.publication = item.publication;
                                      self.status = item.status;
                                      self.loadtags( retorno.tags );
                                      self.categories = retorno.categories_obj;
                                      self.slug = item.slug;
                                      self.data = obj_post.dataBR(  item.data );

                                      //self.seo_noindex = item.seo_noindex ==1;
                                      //self.seo_inactive = item.seo_inactive==1;
                                      //self.seo_nofollow = item.seo_nofollow==1;
                                      self.subtitle = item.subtitle;
                                      self.seo_keywords = item.seo_keywords != null ? item.seo_keywords: "";

                                      if ( self.slug == "programacao" || self.slug == "filmes-em-cartaz" ){
                                        self.com_editor = false;
                                      }

                                      $(document).ready(function() {
                                  
                                              self.load_editor_html();
                                              self.disableButton = false;
                                      });
                                      //self.loadcategories( retorno.categories );
                                      //self.items = retorno;
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
                                    
                                    alert("Erro!");
                                    $("#div_g_errors").html( response.responseText );
                              });



          },

          style_span_loading(){
                   if (this.onuploading){
                         return "";
                   }

                   return "display:none";

          },

        	botao_voltar(){
        		var self = this;

                 if ( this.onBack != null && this.onBack != undefined ){
                 	console.log("clique no voltar!");
                 	 this.onBack( self );
                 }
        	},

        	salvar (tipo ){

        		var tags = this.gettags();

        		this.show_message = "off";

        		if ( this.title == ""){
        			obj_alert.show("Atenção","Informe o título do POST",
        				"warning",function(){ $("#post_title").focus() } );
        			return false;
        		}

        		var categories = this.getcategories();

                  
                let self = this;
                var url =  window.URL_API +"post"; console.log("url: " + url );
                var method = "POST";

                if (this.id != null &&  this.id != ""){
                       method = "PUT"; url =  url + "/"+ this.id;
                }

                var txt_content = this.content;

                if ( self.editor_hab ){
                	 txt_content = obj_editor.getContent(); // $(".note-editable").html();
                }

                console.log("HTML recebido: " + $(".note-editable").html() );

                var data = {title: this.title, description: this.description, 
                	 content: txt_content, tags: tags, categories: categories, 
                	  post_type: this.post_type != null ? this.post_type : "post", status: tipo,
                	  seo_noindex: this.seo_noindex == true ? 1 : 0,
                	   seo_nofollow: this.seo_nofollow == true ? 1 : 0,
                	  seo_inactive: this.seo_inactive == true ? 1 : 0 ,
                     subtitle: this.subtitle, seo_keywords: this.seo_keywords,
                     data: $("#post_data").val() }


                  console.log("Enviado com o método "+ method + " - URL: " + url );
                  console.log( data );
     
                      $.ajax({
                              type: method,
                              url: url,
                              contentType: "application/x-www-form-urlencoded",
                              processData: true,
                              data: data,
                              success: function (retorno) {
                                  
                                  console.log(retorno);
                                   self.status = tipo;
                                   self.id = retorno.item.id;
                                   self.slug = retorno.item.slug;

                                   if ( self.onSave != null ){
                                      	self.onSave(retorno, tipo)
                                   }

                                   if ( tipo == "publish"){

                                   	    // obj_alert.show("Sucesso!","Post publicado com sucesso!", "success", null, 4000);
                                            
                                            var tmp_verb = "publicada";
                                            
                                            if ( self.post_type == "news"){
                                                 tmp_verb = "salva";
                                            }

                                   	      self.show_message = "on";
        			            	              self.message_text = self.title_str + " " + tmp_verb+" com sucesso!";
        			            	              self.message_type = "success";

        			            	              this.interval_message = setInterval(self.clear_message, 6000); 

        			            	              self.publication = retorno.item.publication;

                                   }else{

                                    var st_verbo = "Revisão salva";

                                   	if ( tipo == "draft"){
                                         st_verbo = "Rascunho salvo";
                                   	}

			            	               self.show_message = "on";
			            	               self.message_text =  st_verbo + " com sucesso!";
			            	               self.message_type = "success";

			            	               this.interval_message = setInterval(self.clear_message, 4000); 

                                   }
	            	              
                                  this.novo_item = "";
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
                                
                                
                                    alert("Erro!");
                                    $("#div_g_errors").html( response.responseText );
                          });
        	},

          call_upload(ev){
                 console.log("call upload"); console.log(ev);

                 let self = this;

                 window.obj_upload.uploadImagem( ev.target.files, "imgpost", self);
          },
          onReturnUpload (retorno, type ){

            console.log("Upload realizado. Recebido: ");
            console.log(retorno );

            obj_editor.returnUpload(retorno);

              // setaContent

            /*
            console.log("Voltei do upload ? tenho um editor do summernote salvo? ");
            console.log( this.editor_summernote  );


           
                     
                     for ( var i = 0; i < retorno.length; i++ ){
                             var item = retorno[i];

                             var node = document.createElement("img");
                                 node.src = item.url;

                                  if ( this.editor_summernote != null && this.editor_summernote != undefined ){

                                        this.editor_summernote.insertNode(node);

                                   }else{
                                         $('.summernote').summernote('insertNode', node);
                                   }

                             this.onuploading = false;

                     }

             */              
          },

          load_editor_html(){

            var self = this;

            if (!self.com_editor ){
              return;
            }

            //obj_editor.loadCalendar('#post_data');
            obj_editor.aplicar('#post_content', 400, self, ".file_summernote" );
            this.editor_hab = true;

            //obj_editor.setaContent()

             /*
            $('.temData').datepicker({
               dateFormat: 'dd/mm/yy',
               dayNames: ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'],
               dayNamesMin: ['D', 'S', 'T', 'Q', 'Q', 'S', 'S', 'D'],
               dayNamesShort: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
               monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
               monthNamesShort: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
               nextText: 'Proximo',
               prevText: 'Anterior'
            }); */

            /*
            var callbacks = {  onImageUpload: function(files, editor ) {
                                          // upload image to server and create imgNode...
                                          console.log("Summernote de upload? ");
                                          console.log( editor )
                                          //console.log( self.summernote );
                                         // console.log(files);
                                             self.editor_summernote = editor;
                                             self.onuploading = true;

                                             window.obj_upload.uploadImagem( files, "imgpost", self);
                                        }
                             };

                                  //    console.log("callbacks, editor html ");
                                  //    console.log(  callbacks);

             if ( self.summernote == null ){


              //$('.summernote').eq(1).summernote('destroy');

                   self.summernote =  $('.summernote').summernote(
                              {
                                placeholder: self.content,
                                height: 400,
                                callbacks:  callbacks
                              }
                    );

             }else {

                     self.summernote.summernote("code", self.content);

             }
               

                  console.log("tenho summernote ui? ");
              */


               /*
                     // summernote.image.upload
                    $('.summernote').on('summernote.image.upload', function(we, files) {
                      // upload image to server and create imgNode...
                      console.log("UPload realizado!");
                      console.log( files );
                     // $summernote.summernote('insertNode', imgNode);
                    });        
               */

          },
          onInsertMidia(){
              console.log("OnInsert Midia");
              console.log(window.URL_BASE +"midiamodal"  );
              //console.log($("#dialog"));
              //console.log($(this.$el));

              window.jQuery.colorbox({iframe: true, 
                        href: window.URL_BASE +"midiamodal", innerWidth: 900, innerHeight: 700});
                   // window.jQuery.colorbox.open({ href: "https://www.google.com", iframe: true});
                      //$('.summernote').summernote('editor.insertText', 'hello world');

                   /*
                  jQuery("#dialog").dialog({
                      autoOpen: false,
                      modal: true,
                      height: 600,
                      open: function(ev, ui){
                               $('#myIframe').attr('src','http://www.jQuery.com');
                            }
                  }); */

          },
        	clear_message(){
                 this.show_message = "off";
                 clearInterval(this.interval_message);
        	},

            getcategories (){
                     
                     var ids = $(".chk_category:checked").map(
                                  function () {return this.value;}).get().join(",");

                     return ids;

            },

            gettags(){

                   var tags = [];

                   $('.tagify').find('tag').each(
                              
                   	function(){
						  tags[tags.length] =  $(this).text();
					});

                   return tags.join(",") ;

            },

            loadtags(txt){

                 var itens = txt.split(",");
                 var self = this;
                // console.log( $.fn.tagify );

                // console.log("tagFY existe? "); console.log(  $.fn.tagify );

                 if ( txt != ""  ){

                 	if ( $.fn.tagify != null && $.fn.tagify != undefined && this.show_post_widget == "1"){

                          $.fn.tagify.removeAllTags();
                          $.fn.tagify.addTags( itens  );

                 	}

                 	if (window.Gtagify  != null && window.Gtagify  != undefined && this.show_post_widget == "1"){
                         
                 		 console.log("Vou tentar carregar o TagiFy");

                         var fn_func = function(){

                         	 window.Gtagify.removeAllTags();
                             window.Gtagify.addTags( itens  );

                             console.log("temos interval? ");
                             console.log( self.interval_tags );

                             clearInterval(self.interval_tags);
                         }
			             this.interval_tags = setInterval(fn_func, 500); 
                 		

                 	}else {
                 		console.log("Tagigy ainda não carregado!");
                 	}
                         
                  }


            },

            loadcategories(txt){

                  var ar = txt.split(',');

                  for ( var i = 0; i < ar.length; i++ ){

                  	if (ar[i] == null || ar[i] == "")
                  		 continue;

                  	 $("#cat_chk_" + ar[i] ).attr('checked');
                  }


                   
            },



            ischecked( id ){

                if ( this.selecteds != null && this.selecteds != undefined ){
                               for ( var i = 0; i < this.selecteds.length; i++ ){
                                   if ( this.selecteds[i].id == id ){
                                       return true;
                                   }
                               }
                }

                return false;

            },

            get_nameid(id){

                return this.prefixo + id;
            },

            checkboxid( id ){
                return this.prefixo + "chk_" + id;
            },

            adicionar(){

              var txt_id = this.get_nameid("novo_item");

              console.log( "item digitado: " + this.novo_item  +" - ID: " + txt_id );

                if ( this.novo_item == "" ){
                      $("#" + txt_id ).focus();
                }else{

                   let self = this;

                   var url =  window.URL_API +"terms"; console.log("url: " + url );

                   var data = new FormData();
                   data.append("type", "category");
                   data.append("name", this.novo_item);
     
                      $.ajax({
                              type: "POST",
                              url: url,
                              contentType: false,
                              processData: false,
                              data: data,
                              success: function (retorno) {
                                  
                                  if ( retorno.code == "2"){
                                    alert(retorno.msg);
                                  }

                                  if ( retorno.code=="3"){
                                      if ( self.selecteds == null )
                                          self.selecteds = [];

                                       self.selecteds.unshift(retorno.item); 
                                  }

                                  if ( retorno.code=="1"){

                                     if ( self.selecteds == null )
                                          self.selecteds = [];

                                         self.selecteds.unshift(retorno.item);
                                         self.items.unshift(retorno.item);

                                  }

                                  this.novo_item = "";
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





                }


            },

        
        }
    }
</script>
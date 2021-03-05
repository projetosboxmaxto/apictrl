<template>
    <div class="box" v-bind:style="display_template">

            <div class="box-header with-border">
              <h3 class="box-title">Tags</h3>

              <div class="box-tools pull-right" style="display:none">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
              </div>
              <!-- /.box-tools -->
            </div>

  <div class="box-body">
     <input name='tags3' placeholder='Write some tags' pattern='^[A-Za-z_ ]{1,15}$'>
  </div>

    </div>

</template>

<script>
 export default {
        props: ['show'],

        data: function() {
            return {

                items: [],
                display_template: (this.show != null && this.show == "0" ? "display:none" : "")
            }
        },

        methods: {
              loadtagify(){

                var transformaTag =  function ( value ){
                    if( value == 'shit' )
                    return 's✲✲t';
                }

                var self = this;

                var input = document.querySelector('input[name=tags3]'),

                    tagify = new Tagify(input, {
                        delimiters          : ",|",  // add new tags when a comma or a space character
                        suggestionsMinChars : 3,
                        maxTags             : 100,
                        blacklist           : [],
                        keepInvalidTags     : true,  // do not remove invalid tags (but keep them marked as invalid)
                        whitelist           : self.items,
                        transformTag        : transformaTag
                    });

                    window.Gtagify = tagify;


                tagify.on('add', function(e){
                    console.log(e.detail)
                });

                tagify.on('invalid', function(e){
                    console.log(e, e.detail);
                });



              }
        },

        mounted() {

                let self = this;

                var url =  window.URL_API +"terms?type=post_tag"; console.log("url: " + url );

                var data = new FormData();
                data.append("type", "post_tag");
     
                $.ajax({
                        type: "GET",
                        url: url,
                        contentType: false,
                        processData: false,
                        data: data,
                        success: function (retorno) {
                            
                            var items = [];

                            for ( var i  =0; i < retorno.length; i++ ){
                                   items[items.length] = retorno[i].name;
                            }

                            self.items = items;


                            self.loadtagify();
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
      }

</script>


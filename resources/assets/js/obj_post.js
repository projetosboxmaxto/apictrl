var obj_post = {
    
    getTitle: function (type){
        
        if ( type == "post")
            return "Notícia";
        
        if ( type == "page")
            return "Página";


        if ( type == "news")
            return "Newsletter";

        return "Notícia ";
    },
    
    getNewLabel: function (type){
        
        //if ( type == "post")
            return "Nova";
        
    },

    dataBR: function (value){

        if ( value == null || value == undefined || value == "")
            return "";

           var ar = value.split(' ');
           var pedaco_data = ar[0].split('-');

           var data_saida = pedaco_data[2] + "/"+ pedaco_data[1] +"/" + pedaco_data[0];

           return data_saida;
    }
       
}

window.obj_post = obj_post;
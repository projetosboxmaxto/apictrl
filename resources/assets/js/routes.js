

import Vue from 'vue'
import VueRouter from 'vue-router'
import Alert from './components/alert'


import Category from './components/post/Category'
import TagPostJquery from './components/post/TagPostJquery'
import Post from './components/post/Post'
import Page from './components/post/Page'
import PostList from './components/post/PostList'
import MenuApp from './components/Menu'
import EventoList from './components/eventos/EventosList'
import EventoForm from './components/eventos/EventosForm'
import EventosTranscricao from './components/eventos/EventosTranscricao'
import EventosArquivosList from './components/eventos_arquivos/EventosArquivosList'
import EventosArquivosForm from './components/eventos_arquivos/EventosArquivosForm'


import EventosArquivosPalavrasList from './components/eventos_arquivos_palavras/EventosArquivosPalavrasList'
import EventosArquivosPalavrasForm from './components/eventos_arquivos_palavras/EventosArquivosPalavrasForm'


import MateriaRascunhoForm from './components/materia_rascunho/MateriaRascunhoForm';
import MateriaRascunhoList from './components/materia_rascunho/MateriaRascunhoList';

import MateriaRascunho from './components/materia_rascunho/ListRascunho';
import MateriaSalva from './components/materia_rascunho/ListSalvas';


import AgrupamentoNotificacoesForm from './components/agrupamento_notificacoes/AgrupamentoNotificacoesForm'
import AgrupamentoNotificacoesList from './components/agrupamento_notificacoes/AgrupamentoNotificacoesList'
import AgrupamentoNotificacoesList2 from './components/agrupamento_notificacoes/List2'
import Tinder from './components/agrupamento_notificacoes/Tinder'
import TinderList from './components/agrupamento_notificacoes/TinderList.vue'


import SearchQueriesForm from './components/search_queries/Form'
import SearchQueriesList from './components/search_queries/SearchQueriesList'


import ClienteConfiguracaoList from './components/cliente_configuracao/ClienteConfiguracaoList.vue'
import ClienteConfiguracaoForm from './components/cliente_configuracao/ClienteConfiguracaoForm.vue'

import ElasticQueriesListCadTable from './components/elastic_queries/ElasticQueriesListCadTable.vue'

import SearchQueriesListCadTable from './components/search_queries/SearchQueriesListCadTable.vue'

Vue.component('search_queries_form', SearchQueriesForm);
Vue.component('search_queries_list', SearchQueriesList);
Vue.component('cliente_configuracao_list', ClienteConfiguracaoList);
Vue.component('cliente_configuracao_form', ClienteConfiguracaoForm);


Vue.component('search_queries_list_cad_table', SearchQueriesListCadTable);
//import EventosArquivosVisualizar from './components/eventos_arquivos/EventosArquivosVisualizar'


/*
import PageNewsletter from './components/news/PageNewsletter'
import NewsList from './components/post/NewsList'
import PostSimples from './components/post/PostSimples'
import PagesList from './components/post/PagesList'
import ListCodeFilme from './components/filmes/ListCodeFilme'
//import FilmeEmBreve from './components/filmes/EmBreve'
import FilmeEmBreve2 from './components/filmes/EmBreveNovo'
import FilmeBack from './components/filmes/FilmeBack'
import EmCartaz from './components/filmes/EmCartaz'


import BreveAjuste from './components/post/breve_ajuste'

import NoticiasList from './components/post/NoticiasList'
import ImageList from './components/images/ImageList'
import ImageForm from './components/images/ImageForm'
import ImageSelect from './components/images/ImageSelect'
import Upload from './components/images/upload'
import UploadUnico from './components/images/UploadUnico'
import Imagebanner from './components/images/banner'


import EmailList from './components/news/EmailList'
import EmailForm from './components/news/EmailForm'
import EmailLast from './components/news/EmailLast'

import BannerTopo from './components/images/banner_topo'
import BannerParceiros from './components/images/banner_parceiros'
import BannerDetail from './components/images/banner-detail'
import BannerPrincipal from './components/images/banner_principal'
import BannerModal from './components/images/banner_modal'



import BottomSite from './components/configs/BottomSite'
import BottomSiteList from './components/configs/BottomSiteList'

import ModalBiblioteca from './components/post/ModalBiblioteca'


import UserForm from './components/users/UserForm'
import UserList from './components/users/UserList'


import ConfColor from './components/configs/ConfColor'
import Configuracao from './components/configs/Configuracao'

import UserGroupForm from './components/user_group/UserGroupForm'
import UserGroupList from './components/user_group/UserGroupList'
*/

import App from './components/App'



Vue.component('alert', Alert);
Vue.component('category', Category);
Vue.component('tagpostjquery', TagPostJquery);
Vue.component('post', Post);
Vue.component('postlist', PostList);
Vue.component('MenuApp', MenuApp);
Vue.component('eventos_list', EventoList);
Vue.component('eventos_form', EventoForm);
Vue.component('eventos_transcricao', EventosTranscricao);


Vue.component('eventos_arquivos_form', EventosArquivosForm);
Vue.component('eventos_arquivos_palavras_form', EventosArquivosPalavrasForm);


Vue.component('materia_rascunho_form', MateriaRascunhoForm);
Vue.component('materia_rascunho_list', MateriaRascunhoList);

Vue.component('agrupamento_notificacoes_form', AgrupamentoNotificacoesForm);
Vue.component('agrupamento_notificacoes_list', AgrupamentoNotificacoesList);


//Vue.component('eventos_arquivos_visualizar', EventosArquivosVisualizar);


/* Vue.component('newslist', NewsList);
Vue.component('noticias-list', NoticiasList);
Vue.component('post-simples', PostSimples);

Vue.component('imagelist', ImageList);
Vue.component('imageform', ImageForm);
Vue.component('imageselect', ImageSelect);

Vue.component('upload', Upload);
Vue.component('upload-unico', UploadUnico);
Vue.component('imagebanner', Imagebanner);
Vue.component('modalbiblioteca', ModalBiblioteca);
Vue.component('banner-detail', BannerDetail);
Vue.component('banner-principal', BannerPrincipal);
Vue.component('banner-modal', BannerModal);

Vue.component('filme-code', ListCodeFilme);
Vue.component('filme-back', FilmeBack);

  
Vue.component('bottom-site', BottomSite);
Vue.component('bottom-site-list', BottomSiteList);



Vue.component('user-form', UserForm);
Vue.component('user-list', UserList);
Vue.component('conf-color', ConfColor);
Vue.component('configuracao', Configuracao);

 
Vue.component('email-list', EmailList);
Vue.component('email-form', EmailForm);
Vue.component('email-last', EmailLast);
Vue.component('page', Page);
Vue.component('page-newsletter', PageNewsletter);

Vue.component('user_group_form', UserGroupForm);
Vue.component('user_group_list', UserGroupList );
Vue.component('breve_ajuste', BreveAjuste );

*/


var base_path = window.URL_BASE; //"/julio_cineroxy_lav54/painel/public/"; "/julio_cineroxy_lav54/painel2/";
//
var routes_geral = [

    {
        path: base_path + 'notificacoes3',
        name: 'notificacoes3',
        component: AgrupamentoNotificacoesList2,
        title: "Notificações",
        menu: true,
        icon: "fa fa-bell"
    },
    {
        path: base_path + 'notificacoes2',
        name: 'notificacoes2',
        component: AgrupamentoNotificacoesList,
        title: "Notificações (antigo)",
        menu: false,
        icon: "fa fa-bell"
    },
    {
        path: base_path + 'tinder',
        name: 'tinder',
        component: Tinder,
        title: "Tinder",
        menu: true,
        icon: "fa fa-fire"
    },
    {
        path: base_path + 'tinderlist',
        name: 'tinderlist',
        component: TinderList,
        title: "Notificações Aprovadas",
        menu: true,
        icon: "fa fa-puzzle-piece"
    },
    



    {
        path: base_path + 'notificacoes',
        name: 'notificacoes',
        component: EventosArquivosPalavrasList,
        title: "Notificações  (por palavra)",
        menu: false,
        icon: "fa fa-bell"
    },

    {
        path: base_path + 'programas',
        name: 'programas',
        component: EventoList,
        title: "Programas",
        menu: true,
        icon: "fa fa-tv"
    },


    {
        path: base_path + 'recortes',
        name: 'recortes',
        component: EventosArquivosList,
        title: "Recortes",
        menu: true,
        icon: "fa fa-cut"
    },


    {
        path: base_path + 'materias',
        name: 'materias',
        component: MateriaRascunhoList,
        title: "Matérias",
        menu: false,
        icon: "fa fa-cubes"
    },

    {
        path: base_path + 'materias_rascunho',
        name: 'materias_rascunho',
        component: MateriaRascunho,
        title: "Matérias - Rascunhos",
        menu: true,
        icon: "fa fa-cubes"
    },




    {
        path: base_path + 'materias_salvas',
        name: 'materias_salvas',
        component: MateriaSalva,
        title: "Matérias - Salvas",
        menu: true,
        icon: "fa fa-database"
    },


    {
        path: base_path + 'configurar',
        name: 'configurar',
        component: ClienteConfiguracaoList,
        title: "Configurar Busca",
        menu: true,
        icon: "fa fa-cogs"
    },


    {
        path: base_path,
        name: 'home',
        component: AgrupamentoNotificacoesList, //EventosArquivosPalavrasList
        title: "Programas",
        menu: false,
    },

];

//var routes_user = obj_api.get_menu_user( routes_geral );    

const router = new VueRouter({
    mode: 'history',
    routes: routes_geral,
});



$(document).ready(function () {
    const app = new Vue({
        el: '#app',
        components: { App },
        router,
    });

});

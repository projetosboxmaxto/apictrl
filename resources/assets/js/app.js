
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
require('./obj_upload');
require('./obj_alert');
require('./library/obj_editor.js');
require('./library/obj_datatable.js');
require('./jquery.colorbox');
require('./obj_post.js');
require('./obj_api.js');
require('./obj_velox_api.js');
require('./obj_mask.js');
require('./obj_corteaudiovideo.js');
require('./library/obj_programa.js');



//window.Vue = require('vue');
import Vue from 'vue'
import VueRouter from 'vue-router'


Vue.use(VueRouter);


//import VueTagsInput from '@johmun/vue-tags-input';

Vue.filter('datetime_show', function (value) {
    if (!value) return ''
    if (value.indexOf("-") <= 0) return value;

    value = value.toString();

    var ar = value.split(' ');
    var pedaco_data = ar[0].split('-');

    var data_saida = pedaco_data[2] + "/" + pedaco_data[1] + "/" + pedaco_data[0] + " " + ar[1];

    return data_saida;
});

Vue.filter('date_show', function (value) {
    if (!value) return ''
    if (value.indexOf("-") <= 0) return value;

    value = value.toString();

    var ar = value.split(' ');
    var pedaco_data = ar[0].split('-');

    var data_saida = pedaco_data[2] + "/" + pedaco_data[1] + "/" + pedaco_data[0];

    return data_saida;
});


Vue.filter('base_url', function (value) {

    return window.URL_BASE + value;
});

Vue.filter('template_url', function (value) {

    return window.URL_TEMPLATE_BASE + value;
});

Vue.filter('site_url', function (value) {
    return window.location.origin + window.URL_BASE_SITE + value;
});


//Vue.config.productionTip = true;


//Vue.component('tagpost', 
// require('./components/TagPost.vue'));

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */


require('./routes.js');
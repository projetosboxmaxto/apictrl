var obj_programa = {

    get_last_apresentador: function (id_programa) {
        if (window.sessionStorage.getItem("form_id_programa") != null) {
            if (window.sessionStorage.getItem("form_id_programa").toString() == id_programa.toString()) {
                return window.sessionStorage.getItem("form_id_apresentador");
            }

        }

        return null;
    },
    set_last_apresentador: function (id_programa, id_apresentador) {

        window.sessionStorage.setItem("form_id_programa", id_programa);
        window.sessionStorage.setItem("form_id_apresentador", id_apresentador);

    }
}

window.obj_programa = obj_programa;
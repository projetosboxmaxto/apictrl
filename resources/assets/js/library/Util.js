export default {

    getListToSelect(list, field_value, field_text) {

        var lst = [];

        for (var i = 0; i < list.length; i++) {

            var item = list[i];

            lst[i] = {
                value: item[field_value],
                label: item[field_text]
            };

        }

        return lst;
    },
    dateToBR(value) {
        if (!value) return ''
        if (value.indexOf("-") <= 0) return value;

        value = value.toString();

        var ar = value.split(' ');
        var pedaco_data = ar[0].split('-');

        var data_saida = pedaco_data[2] + "/" + pedaco_data[1] + "/" + pedaco_data[0];

        return data_saida;
    },
    dateToBd(date) {
        if (date == null) {
            return null;
        }
        var mes = date.getMonth() + 1;
        return date.getFullYear().toString() + "-" + mes.toString() + "-" + date.getDate().toString();

    },

    dateHourToBd(date) {
        if (date == null) {
            return null;
        }
        var mes = date.getMonth() + 1;
        return date.getFullYear().toString() + "-" + mes.toString() + "-" + date.getDate().toString() + " " +
            date.getHours().toString().padStart(2, "0") + ":" +
            date.getMinutes().toString().padStart(2, "0");

    },
    BrDateToUS(value) {
        //mes dia e ano
        if (value == null || value == undefined) {
            return "";
        }

        if (value.indexOf("/") < 0) {
            return "";
        }

        var ar = value.split("/");
        return ar[2] + "-" + ar[1] + "-" + ar[0];
    },

    BrDateToJavascript(value) {
        //mes dia e ano
        if (value == null || value == undefined) {
            return "";
        }

        if (value.indexOf("/") < 0) {
            return "";
        }

        var ar = value.split("/");
        return ar[1] + " " + ar[0] + " " + ar[2];
    },

    ApiDateToJavascript(value) {
        //mes dia e ano
        var arp = value.split(" ");

        var ar = arp[0].split("-");
        return ar[1] + " " + ar[2] + " " + ar[0];
    },

    getHourFromDate(date) {
        if (date == null)
            return "";

        return date.getHours().toString().padStart(2, "0") + ":" + date.getMinutes().toString().padStart(2, "0");
    },

    alerta(swal, tit, msg, fn_final) {
        // on select we show the Sweet Alert modal with an input
        swal.fire({
            title: tit,
            html: msg,
            showCancelButton: false,
            confirmButtonClass: "md-button md-success",
            cancelButtonClass: "md-button md-danger",
            buttonsStyling: false
        }).then(() => {
            /*  var eventData;
              var eventTitle = $("#md-input").val();
              if (eventTitle) {
                  eventData = {
                      title: eventTitle,
                      start: start,
                      end: end
                  };
                  $calendar.fullCalendar("renderEvent", eventData, true); // stick? = true
              }
              $calendar.fullCalendar("unselect"); */

            if (fn_final != null) {
                fn_final(tit);
            }
        });
    }


}
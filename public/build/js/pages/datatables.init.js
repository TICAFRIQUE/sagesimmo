/*
Template Name: Velzon - Admin & Dashboard Template
Author: Themesbrand
Website: https://Themesbrand.com/
Contact: Themesbrand@gmail.com
File: datatables init js
*/

function initializeTables() {
    // let example = new DataTable('#example',);
    let example = new DataTable("#example", {
        pageLength: 50, // Par défaut 50 lignes affichées
        lengthMenu: [
            [50, 100],
            [50, 100],
        ], // Options de pagination
    });

    let scrollVertical = new DataTable("#scroll-vertical", {
        scrollY: "210px",
        scrollCollapse: true,
        paging: false,
    });

    let scrollHorizontal = new DataTable("#scroll-horizontal", {
        scrollX: true,
    });

    let alternativePagination = new DataTable("#alternative-pagination", {
        pagingType: "full_numbers",
    });

    //fixed header
    let fixedHeader = new DataTable("#fixed-header", {
        fixedHeader: true,
    });

    //modal data data tables
    let modelDataTables = new DataTable("#model-datatables", {
        responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.modal({
                    header: function (row) {
                        var data = row.data();
                        return "Details for " + data[0] + " " + data[1];
                    },
                }),
                renderer: $.fn.dataTable.Responsive.renderer.tableAll({
                    tableClass: "table",
                }),
            },
        },
    });

    //buttons examples
    // if ($.fn.DataTable.isDataTable("#buttons-datatables")) {
    //     $("#buttons-datatables").DataTable().destroy();
    // }
    // let buttonsDataTables = new DataTable("#buttons-datatables", {
    //     dom: "Bfrtip",
    //     responsive: true,
    //     buttons: ["copy", "csv", "excel", "print", "pdf"],
    // });

    if ($.fn.DataTable.isDataTable("#buttons-datatables")) {
        $("#buttons-datatables").DataTable().destroy();
    }

    let buttonsDataTables = new DataTable("#buttons-datatables", {
        dom: "Bfrtip",
        responsive: true,
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json",
        },
        buttons: [
            { extend: "copy", text: '<i class="bi bi-clipboard"></i> Copier' },
            { extend: "csv", text: '<i class="bi bi-filetype-csv"></i> CSV' },
            {
                extend: "excel",
                text: '<i class="bi bi-file-earmark-excel"></i> Excel',
            },
            {
                extend: "pdf",
                text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
            },
            { extend: "print", text: '<i class="bi bi-printer"></i> Imprimer' },
        ],
        drawCallback: function (settings) {
            // Utilise la variable globale routeName définie dans la page
            if (
                typeof window.routeName !== "undefined" &&
                typeof delete_row === "function"
            ) {
                delete_row(window.routeName);
            }
        },
    });

    //buttons examples
    let ajaxDataTables = new DataTable("#ajax-datatables", {
        ajax: "build/json/datatable.json",
    });

    var t = $("#add-rows").DataTable();
    var counter = 1;

    $("#addRow").on("click", function () {
        t.row
            .add([
                counter + ".1",
                counter + ".2",
                counter + ".3",
                counter + ".4",
                counter + ".5",
                counter + ".6",
                counter + ".7",
                counter + ".8",
                counter + ".9",
                counter + ".10",
                counter + ".11",
                counter + ".12",
            ])
            .draw(false);

        counter++;
    });

    // Automatically add a first row of data
    $("#addRow").trigger("click");
}

document.addEventListener("DOMContentLoaded", function () {
    initializeTables();
});

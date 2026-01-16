// function delete_row(route){
//     $('.delete').on("click", function(e) {
//         e.preventDefault();
//         var Id = $(this).attr('data-id');
//         var url = '/admin/'+ route +'/delete/'
//         Swal.fire({
//             title: 'Etes-vous sûr(e) de vouloir supprimer ?',
//             text: "Cette action est irréversible!",
//             icon: 'warning',
//             showCancelButton: true,
//             confirmButtonText: 'Supprimer!',
//             cancelButtonText: 'Annuler',
//             customClass: {
//                 confirmButton: 'btn btn-primary w-xs me-2 mt-2',
//                 cancelButton: 'btn btn-danger w-xs mt-2',
//             },
//             buttonsStyling: false,
//             showCloseButton: true
//         }).then((result) => {
//             if (result.isConfirmed) {
//                 $.ajax({
//                     type: "GET",
//                     url:  url+ Id,
//                     dataType: "json",
//                     // data: {
//                     //     _token: '{{ csrf_token() }}',

//                     // },
//                     success: function(response) {
//                         if (response.status == 200) {
//                             Swal.fire({
//                                 title: 'Supprimé!',
//                                 text: 'Suppression effectuée avec succès.',
//                                 icon: 'success',
//                                 customClass: {
//                                     confirmButton: 'btn btn-primary w-xs mt-2',
//                                 },
//                                 buttonsStyling: false
//                             })

//                             $('#row_' + Id).remove();
//                             location.reload();
//                         }
//                     }
//                 });
//             }
//         });
//     });
// }




function delete_row(route) {
    $(document)
        .off("click", ".delete")
        .on("click", ".delete", function (e) {
            e.preventDefault();
            var Id = $(this).attr("data-id");
            var url = "/admin/" + route + "/delete/";

            Swal.fire({
                title: "Etes-vous sûr(e) de vouloir supprimer ?",
                text: "Cette action est irréversible!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Supprimer!",
                cancelButtonText: "Annuler",
                customClass: {
                    confirmButton: "btn btn-primary w-xs me-2 mt-2",
                    cancelButton: "btn btn-danger w-xs mt-2",
                },
                buttonsStyling: false,
                showCloseButton: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "DELETE",
                        url: url + Id,
                        dataType: "json",
                        success: function (response) {
                            if (response.status == 200) {
                                Swal.fire({
                                    title: "Supprimé!",
                                    text: "Suppression effectuée avec succès.",
                                    icon: "success",
                                    customClass: {
                                        confirmButton:
                                            "btn btn-primary w-xs mt-2",
                                    },
                                    buttonsStyling: false,
                                });
                                $("#row_" + Id).remove();
                                location.reload();
                            }
                        },
                    });
                }
            });
        });
}
